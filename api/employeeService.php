<?php
require_once dirname(__DIR__, 1) . '/db/ConnectionManager.php';
require_once dirname(__DIR__, 1) . '/db/EmployeeAccessor.php';
require_once dirname(__DIR__, 1) . '/entity/Employee.php';
require_once dirname(__DIR__, 1) . '/utils/Constants.php';
require_once dirname(__DIR__, 1) . '/utils/ChromePhp.php';

/*
 * Important Note:
 * 
 * $_GET will contain the employee ID, even if the caller uses POST in the AJAX call. 
 * Why? Because the router (.htaccess) converts the URL from 
 *     bullseye/employees/N
 * to
 *     employeeService.php?employeeid=N
 * The syntax "?key=value" is interpreted as a GET parameter and is therefore
 * stored in the $_GET array.
 */

$method = $_SERVER['REQUEST_METHOD'];

try {
    $cm = new ConnectionManager(Constants::$MYSQL_CONNECTION_STRING, Constants::$MYSQL_USERNAME, Constants::$MYSQL_PASSWORD);
    $ema = new EmployeeAccessor($cm->getConnection());

    if ($method === "GET") {
        doGet($ema);
    } else if ($method === "POST") {
        doPost($ema);
    } else if ($method === "DELETE") {
        doDelete($ema);
    } else if ($method === "PUT") {
        doPut($ema);
    } else {
        sendResponse(405, null, "method not allowed");
    }
} catch (Exception $e) {
    sendResponse(500, null, "ERROR " . $e->getMessage());
} finally {
    if (!is_null($cm)) {
        $cm->closeConnection();
    }
}

function doGet($ema)
{
    // individual
    if (isset($_GET['employeeid'])) {
        sendResponse(405, null, "individual GETs not allowed");
    }
    // collection
    else {
        try {
            $results = $ema->getAllEmployees();
            if (count($results) > 0) {
                $results = json_encode($results, JSON_NUMERIC_CHECK);
                sendResponse(200, $results, null);
            } else {
                sendResponse(404, null, "could not retrieve employees");
            }
        } catch (Exception $e) {
            sendResponse(500, null, "ERROR " . $e->getMessage());
        }
    }
}

function doDelete($ema)
{
    if (!isset($_GET['employeeid'])) {
        // Bulk deletes not implemented.
        sendResponse(405, null, "bulk DELETEs not allowed");
    } else {
        try {
            $employeeID = $_GET['employeeid'];

            // Only the ID of the employee matters for a delete,
            // but the accessor expects an object, 
            // so we need a dummy object.

            //being numbers or strings does not matter
            $EmployeeObj = new Employee($employeeID, "dummy", "dummy", "dummy", "dummy", "dummy", "dummy", 0, "dummy", 0);

            // delete the object from DB
            $success = $ema->deleteEmployee($EmployeeObj);
            if ($success) {
                sendResponse(200, $success, null);
            } else {
                sendResponse(404, null, "could not delete employee - it does not exist");
            }
        } catch (Exception $e) {
            sendResponse(500, null, "ERROR " . $e->getMessage());
        }
    }
}

// aka CREATE
function doPost($ema)
{
    if (isset($_GET['employeeid'])) {
        // The details of the employee to insert will be in the request body.
        $body = file_get_contents('php://input');
        $contents = json_decode($body, true);

        try {
            // create a Employee object
            $EmployeeObj = new Employee(
                $contents['employeeID'],
                $contents['permissionLevel'],
                $contents['username'],
                $contents['password'],
                $contents['firstName'],
                $contents['lastName'],
                $contents['email'],
                $contents['active'],
                $contents['siteName'],
                $contents['locked']
            );

            // add the object to DB
            $success = $ema->insertEmployee($EmployeeObj);
            if ($success) {
                sendResponse(201, $success, null);
            } else {
                sendResponse(409, null, "could not insert employee - it already exists");
            }
        } catch (Exception $e) {
            sendResponse(400, null, $e->getMessage());
        }
    } else {
        // Bulk inserts not implemented.
        sendResponse(405, null, "bulk INSERTs not allowed");
    }
}

// aka UPDATE
function doPut($ema)
{
    if (isset($_GET['employeeid'])) {
        // The details of the employee to update will be in the request body.
        $body = file_get_contents('php://input');
        $contents = json_decode($body, true);

        $hashedPassword = password_hash($contents['password'], PASSWORD_BCRYPT);

        try {
            // create a Employee object
            $EmployeeObj = new Employee(
                $contents['employeeID'],
                $contents['permissionLevel'],
                $contents['username'],
                $hashedPassword,
                $contents['firstName'],
                $contents['lastName'],
                $contents['email'],
                $contents['active'],
                $contents['siteName'],
                $contents['locked']
            );
            
            // update the object in the  DB
            $success = $ema->updateEmployee($EmployeeObj);
            
            if ($success) {
                sendResponse(200, $success, null);
            } else {
                sendResponse(404, null, "could not update employee - it does not exist");
            }
        } catch (Exception $e) {
            sendResponse(400, null, $e->getMessage());
        }
    } else {
        // Bulk updates not implemented.
        sendResponse(405, null, "bulk UPDATEs not allowed");
    }
}

function sendResponse($statusCode, $data, $error)
{
    header("Content-Type: application/json");
    http_response_code($statusCode);
    $resp = ['data' => $data, 'error' => $error];
    echo json_encode($resp, JSON_NUMERIC_CHECK);
}
