<?php
require_once dirname(__DIR__, 1) . '/db/ConnectionManager.php';
require_once dirname(__DIR__, 1) . '/utils/Constants.php';

// Initialize the ConnectionManager
$connectionManager = new ConnectionManager(Constants::$MYSQL_CONNECTION_STRING, Constants::$MYSQL_USERNAME, Constants::$MYSQL_PASSWORD);

// Check if the request is a PUT request

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {

    if (isset($_GET['employeeid'])) {
        // Read and decode the JSON data from the request body
        $requestData = json_decode(file_get_contents("php://input"));

        // Update the permission level 
        $query = "
            UPDATE employee
            SET PositionID = (SELECT positionID FROM posn WHERE permissionLevel = :permissionLevel)
            WHERE employeeID = :employeeID;
        ";

        try {
            $stmt = $connectionManager->getConnection()->prepare($query);
            $stmt->bindParam(':employeeID', $requestData->employeeID);
            $stmt->bindParam(':permissionLevel', $requestData->permissionLevel);
            $stmt->execute();

            $affectedRows = $stmt->rowCount();

            if ($affectedRows === 1) {
                // Password reset successful
                sendResponse(200, true, null);
                exit();
            } else {
                // Invalid request, missing username or new password
                sendResponse(400, null, "Invalid request. Bad Object or No Change");
                exit();
            }
        } catch (Exception $e) {
            sendResponse(500, null, "ERROR " . $e->getMessage());
            exit();
        }
    } else {
        // Bulk updates not implemented.
        sendResponse(405, null, "bulk UPDATEs not allowed");
    }
}



// Invalid request method or other cases
sendResponse(405, null, "Invalid request method");

// Close the database connection
$connectionManager->closeConnection();

function sendResponse($statusCode, $data, $error)
{
    header("Content-Type: application/json");
    http_response_code($statusCode);
    $resp = ['data' => $data, 'error' => $error];
    echo json_encode($resp, JSON_NUMERIC_CHECK);
}

