<?php
require_once dirname(__DIR__, 1) . '/entity/Player.php';

class PlayerAccessor
{
    private $getAllStatementString = "select * from player";
    private $getByIDStatementString = "select * from player where playerID = :playerID";
    private $insertStatementString = "insert INTO player values (:playerID, :teamID, :firstName, :lastName, :hometown, :provinceCode)";
    private $updateStatementString = "update player set firstName = :firstName, lastName = :lastName, hometown = :hometown, provinceCode = :provinceCode where playerID = :playerID";
    //To delete a player, records on game table should be deleted first
    private $deleteGameStatementString = "delete from game where playerID = :playerID";
    private $deleteStatementString = "delete from player where playerID = :playerID";

    private $getAllStatement = null;
    private $getByIDStatement = null;
    private $insertStatement = null;
    private $updateStatement = null;
    private $deleteGameStatement = null;
    private $deleteStatement = null;

    /**
     * Creates a new instance of the accessor with the supplied database connection.
     * 
     * @param PDO $conn - a database connection
     */
    public function __construct($conn)
    {
        ChromePhp::log("Player accessor executing");

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

        $this->insertStatement = $conn->prepare($this->insertStatementString);
        if (is_null($this->insertStatement)) {
            throw new Exception("bad statement: '" . $this->insertStatementString . "'");
        }

        $this->updateStatement = $conn->prepare($this->updateStatementString);
        if (is_null($this->updateStatement)) {
            throw new Exception("bad statement: '" . $this->updateStatementString . "'");
        }

        $this->deleteGameStatement = $conn->prepare($this->deleteGameStatementString);
        if (is_null($this->deleteGameStatement)) {
            throw new Exception("bad statement: '" . $this->deleteGameStatementString . "'");
        }

        $this->deleteStatement = $conn->prepare($this->deleteStatementString);
        if (is_null($this->deleteStatement)) {
            throw new Exception("bad statement: '" . $this->deleteStatementString . "'");
        }
    }

    /**
     * Gets all menu item categories.
     * 
     * @return array MenuItemCategory objects, possibly empty
     */
    public function getAllPlayers()
    {
        $result = [];

        try {
            $this->getAllStatement->execute();
            $dbresults = $this->getAllStatement->fetchAll(PDO::FETCH_ASSOC);

            foreach ($dbresults as $r) {
                $playerID = $r['playerID'];
                $teamID = $r['teamID'];
                $firstName = $r['firstName'];
                $lastName = $r['lastName'];
                $hometown = $r['hometown'];
                $provinceCode = $r['provinceCode'];

                $obj = new Player($playerID,$teamID,$firstName,$lastName,$hometown,$provinceCode);
                array_push($result, $obj);
            }
        } catch (Exception $e) {
            $result = [];
        } finally {
            if (!is_null($this->getAllStatement)) {
                $this->getAllStatement->closeCursor();
            }
        }

        return $result;
    }


/*Delete player functions*/
public function deletePlayer($player)
{
    ChromePhp::log("DeletePlayer executing in PlayerAccessor.php");
    ChromePhp::log("Player number".$player->getPlayerID());
    if (!$this->itemExists($player)) {
        return false;
    }

    $success = false;
    $playerID = $player->getPlayerID();

    try {
        $this->deleteGameStatement->bindParam(":playerID", $playerID);
        $this->deleteStatement->bindParam(":playerID", $playerID);
        $success = $this->deleteGameStatement->execute();
        $success = $this->deleteStatement->execute();
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
 * Does a Player exist (with the same ID)?
 * 
 * @param Player $player to check
 * @return boolean true if player exists; false if not
 */
public function itemExists($player)
{
    return $this->getItemByID($player->getPlayerID()) !== null;
}

/**
 * Gets the player with the specified ID.
 * 
 * @param Integer $playerID the ID of the player to retrieve 
 * @return Player player object with the specified playerID, or NULL if not found
 */
private function getItemByID($playerID)
{
    $result = null;

    try {
        $this->getByIDStatement->bindParam(":playerID", $playerID);
        $this->getByIDStatement->execute();
        $dbresults = $this->getByIDStatement->fetch(PDO::FETCH_ASSOC);

        if ($dbresults) {
            $playerID = $dbresults['playerID'];
            $teamID = $dbresults['teamID'];
            $firstName = $dbresults['firstName']; // Assuming there's a player name in the database
            $lastName = $dbresults['lastName'];
            $hometown = $dbresults['hometown'];
            $provinceCode = $dbresults['provinceCode'];
            // Create a Player
            $result = new Player($playerID, $teamID, $firstName, $lastName, $hometown, $provinceCode);
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
 * Inserts a player into the database.
 * 
 * @param Player $player an object of type Player
 * @return boolean indicates if the player was inserted
 */
public function insertPlayer($player)
{
    if ($this->itemExists($player)) {
        return false;
    }

    $success = false;

    $playerID = $player->getPlayerID();
    $teamID = $player->getTeamID();
    $firstName = $player->getFirstName();
    $lastName = $player->getLastName();
    $hometown = $player->getHometown();
    $provinceCode = $player->getProvinceCode();

    try {
        $this->insertStatement->bindValue(':playerID', $playerID);
        $this->insertStatement->bindValue(':teamID', $teamID);
        $this->insertStatement->bindValue(':firstName', $firstName);
        $this->insertStatement->bindValue(':lastName', $lastName);
        $this->insertStatement->bindValue(':hometown', $hometown);
        $this->insertStatement->bindValue(':provinceCode', $provinceCode);
        $success = $this->insertStatement->execute();
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
 * Updates a player in the database.
 * 
 * @param Player $player an object of type Player
 * @return boolean indicates if the player was updated
 */
public function updatePlayer($player)
{
    if (!$this->itemExists($player)) {
        return false;
    }

    $success = false;

    $playerID = $player->getPlayerID();
    //$teamID = $player->getTeamID();
    $firstName = $player->getFirstName();
    $lastName = $player->getLastName();
    $hometown = $player->getHometown();
    $provinceCode = $player->getProvinceCode();

    try {
        //$this->updateStatement->bindValue(':playerID', $playerID);
        //$this->updateStatement->bindValue(':teamID', $teamID);
        $this->updateStatement->bindValue(':firstName', $firstName);
        $this->updateStatement->bindValue(':lastName', $lastName);
        $this->updateStatement->bindValue(':hometown', $hometown);
        $this->updateStatement->bindValue(':provinceCode', $provinceCode);
        $this->updateStatement->bindValue(':playerID', $playerID);
        $success = $this->updateStatement->execute();
        $success = $this->updateStatement->rowCount() === 1;
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
// end class MenuItemCategoryAccessor
