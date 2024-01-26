<?php
require_once dirname(__DIR__, 1) . '/entity/Employee.php';
require_once dirname(__DIR__, 1) . '/utils/ChromePhp.php';

class EmployeeAccessor
{
    private $getAllStatementString = "SELECT e.*, s.name AS siteName, p. permissionLevel as permissionLevel
    FROM employee e
    JOIN site s ON e.siteID = s.siteID
    JOIN posn p ON e.positionID = p.positionID
    ;";
    private $getByIDStatementString = "SELECT e.*, s.name AS siteName FROM employee e JOIN site s ON e.siteID = s.siteID WHERE e.employeeID = :employeeID;";
    private $deleteStatementString = "UPDATE employee SET active = 0 WHERE employeeID = employeeID;";
    private $insertStatementString = "INSERT INTO employee (`employeeID`, `PositionID`, `username`, `Password`, `FirstName`, `LastName`, `Email`, `active`, `siteID`, `locked`)
    VALUES (
        :employeeID,
        (SELECT positionID FROM posn WHERE permissionLevel = :permissionLevel),
        :username,
        :password,
        :firstName,
        :lastName,
        :email,
        :active,
        (SELECT siteID FROM site WHERE name = :siteName),
        :locked
    );";
    private $updateStatementString = "UPDATE employee
    SET PositionID = (SELECT positionID FROM posn WHERE permissionLevel = :permissionLevel),
        username = :username,
        password = :password,
        FirstName = :firstName,  
        LastName = :lastName,    
        Email = :email,          
        active = :active,        
        siteID = (SELECT siteID FROM site WHERE name = :siteName),        
        locked = :locked         
    WHERE employeeID = :employeeID;";

    private $getAllStatement = null;
    private $getByIDStatement = null;
    private $deleteStatement = null;
    private $insertStatement = null;
    private $updateStatement = null;

    /**
     * Creates a new instance of the accessor with the supplied database connection.
     * 
     * @param PDO $conn - a database connection
     */
    public function __construct($conn)
    {
        if (is_null($conn)) {
            throw new Exception("no connection");
        }

        $this->getAllStatement = $conn->prepare($this->getAllStatementString);
        if (is_null($this->getAllStatement)) {
            throw new Exception("bad statement: '" . $this->getAllStatementString . "'");
        }

        $this->getByIDStatement = $conn->prepare($this->getByIDStatementString);
        if (is_null($this->getByIDStatement)) {
            throw new Exception("bad statement: '" . $this->getByIDStatementString . "'");
        }

        $this->deleteStatement = $conn->prepare($this->deleteStatementString);
        if (is_null($this->deleteStatement)) {
            throw new Exception("bad statement: '" . $this->deleteStatementString . "'");
        }

        $this->insertStatement = $conn->prepare($this->insertStatementString);
        if (is_null($this->insertStatement)) {
            throw new Exception("bad statement: '" . $this->getAllStatementString . "'");
        }

        $this->updateStatement = $conn->prepare($this->updateStatementString);
        if (is_null($this->updateStatement)) {
            throw new Exception("bad statement: '" . $this->updateStatementString . "'");
        }
    }

    /**
     * Gets all of the employees.
     * 
     * @return Employee[] array of Employee objects
     */
    public function getAllEmployees()
    {
        $results = [];

        try {
            $this->getAllStatement->execute();
            $dbresults = $this->getAllStatement->fetchAll(PDO::FETCH_ASSOC);

            foreach ($dbresults as $r) {
                $employeeID = $r['employeeID'];
                $permissionLevel = $r['permissionLevel'];
                $username = $r['username'];
                $password = $r['Password'];
                $firstName = $r['FirstName'];
                $lastName = $r['LastName'];
                $email = $r['Email'];
                $active = $r['active'];
                $siteName = $r['siteName'];
                $locked = $r['locked'];

                $obj = new Employee($employeeID, $permissionLevel, $username, $password, $firstName, $lastName, $email, $active, $siteName, $locked);
                array_push($results, $obj);
            }
        } catch (Exception $e) {
            ChromePhp::log($e->getMessage());
            $results = [];
        } finally {
            if (!is_null($this->getAllStatement)) {
                $this->getAllStatement->closeCursor();
            }
        }

        return $results;
    }

    /**
     * Gets the  employee with the specified ID.
     * 
     * @param Interger $id the ID of the employee to retrieve 
     * @return Employee Employee object with the specified ID, or NULL if not found
     */
    private function getEmployeeByID($id)
    {
        $result = null;

        try {
            $this->getByIDStatement->bindParam(":employeeID", $id);
            $this->getByIDStatement->execute();
            $dbresults = $this->getByIDStatement->fetch(PDO::FETCH_ASSOC); // not fetchAll

            if ($dbresults) {
                $employeeID = $dbresults['employeeID'];
                $permissionLevel = $dbresults['permissionLevel'];
                $username = $dbresults['username'];
                $password = $dbresults['Password'];
                $firstName = $dbresults['FirstName'];
                $lastName = $dbresults['LastName'];
                $email = $dbresults['Email'];
                $active = $dbresults['active'];
                $siteName = $dbresults['siteName'];
                $locked = $dbresults['locked'];

                $result = new Employee($employeeID, $permissionLevel, $username, $password, $firstName, $lastName, $email, $active, $siteName, $locked);

            }
        } catch (Exception $e) {
            $result = null;
        } finally {
            if (!is_null($this->getByIDStatement)) {
                $this->getByIDStatement->closeCursor();
            }
        }

        return $result;
    }

    /**
     * Does an employee exist (with the same ID)?
     * 
     * @param Employee $employee the employee to check
     * @return boolean true if the employee exists; false if not
     */
    public function employeeExists($employee)
    {
        return $this->getEmployeeByID($employee->getEmployeeID()) !== null;
    }

    /**
     * Deletes a  employee.
     * 
     * @param Employee $employee an object whose ID is EQUAL TO the ID of the employee to delete
     * @return boolean indicates whether the employee was deleted
     */
    public function deleteEmployee($employee)
    {
        if (!$this->employeeExists($employee)) {
            return false;
        }

        $success = false;
        $employeeID = $employee->getEmployeeID(); // only the ID is needed

        try {
            $this->deleteStatement->bindParam(":employeeID", $employeeID);
            $success = $this->deleteStatement->execute(); // this doesn't mean what you think it means
            $success = $success && $this->deleteStatement->rowCount() === 1;
        } catch (PDOException $e) {
            $success = false;
        } finally {
            if (!is_null($this->deleteStatement)) {
                $this->deleteStatement->closeCursor();
            }
        }
        return $success;
    }

    /**
     * Inserts a  employee into the database.
     * 
     * @param Employee $employee an object of type Employee
     * @return boolean indicates if the employee was inserted
     */
    public function insertEmployee($employee)
    {
        if ($this->employeeExists($employee)) {
            return false;
        }

        $success = false;

        $employeeID = $employee->getEmployeeID();
        $permissionLevel = $employee->getPermissionLevel();
        $username = $employee->getUsername();
        $password = $employee->getPassword();
        $firstName = $employee->getFirstName();
        $lastName = $employee->getLastName();
        $email = $employee->getEmail();
        $active = $employee->isActive();
        $siteName = $employee->getSiteName();
        $locked = $employee->isLocked();

        try {
            $this->insertStatement->bindParam(":employeeID", $employeeID);
            $this->insertStatement->bindParam(":permissionLevel", $permissionLevel);
            $this->insertStatement->bindParam(":username", $username);
            $this->insertStatement->bindParam(":password", $password);
            $this->insertStatement->bindParam(":firstName", $firstName);
            $this->insertStatement->bindParam(":lastName", $lastName);
            $this->insertStatement->bindParam(":email", $email);
            $act = $active == true ? 1 : 0;
            $this->insertStatement->bindParam(":active", $act);
            $this->insertStatement->bindParam(":siteName", $siteName);
            $loc = $locked == true ? 1 : 0;
            $this->insertStatement->bindParam(":locked", $loc);

            $success = $this->insertStatement->execute(); // this doesn't mean what you think it means
            $success = $this->insertStatement->rowCount() === 1;
        } catch (PDOException $e) {
            $success = false;
        } finally {
            if (!is_null($this->insertStatement)) {
                $this->insertStatement->closeCursor();
            }
        }
        return $success;
    }

    /**
     * Updates a  employee in the database.
     * 
     * @param Employee $employee an object of type Employee, the new values to replace the database's current values
     * @return boolean indicates if the employee was updated
     */
    public function updateEmployee($employee)
    {
        if (!$this->employeeExists($employee)) {
            return false;
        }

        $success = false;

        $employeeID = $employee->getEmployeeID();
        $permissionLevel = $employee->getPermissionLevel();
        $username = $employee->getUsername();
        $password = $employee->getPassword();
        $firstName = $employee->getFirstName();
        $lastName = $employee->getLastName();
        $email = $employee->getEmail();
        $active = $employee->isActive();
        $siteName = $employee->getSiteName();
        $locked = $employee->isLocked();

        try {
            $this->insertStatement->bindParam(":employeeID", $employeeID);
            $this->insertStatement->bindParam(":permissionLevel", $permissionLevel);
            $this->insertStatement->bindParam(":username", $username);
            $this->insertStatement->bindParam(":password", $password);
            $this->insertStatement->bindParam(":firstName", $firstName);
            $this->insertStatement->bindParam(":lastName", $lastName);
            $this->insertStatement->bindParam(":email", $email);
            $act = $active == true ? 1 : 0;
            $this->insertStatement->bindParam(":active", $act);
            $this->insertStatement->bindParam(":siteName", $siteName);
            $loc = $locked == true ? 1 : 0;
            $this->insertStatement->bindParam(":locked", $loc);

            $success = $this->insertStatement->execute(); // this doesn't mean what you think it means
            $success = $this->insertStatement->rowCount() === 1;
        } catch (PDOException $e) {
            $success = false;
        } finally {
            if (!is_null($this->updateStatement)) {
                $this->updateStatement->closeCursor();
            }
        }
        return $success;
    }
}
// end class UserAccessor
