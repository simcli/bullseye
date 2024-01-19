<?php
require_once dirname(__DIR__, 1) . '/db/ConnectionManager.php';
require_once dirname(__DIR__, 1) . '/utils/Constants.php';
require_once dirname(__DIR__, 1) . '/utils/ChromePhp.php';

session_start();

// Initialize the ConnectionManager
$connectionManager = new ConnectionManager(Constants::$MYSQL_CONNECTION_STRING, Constants::$MYSQL_USERNAME, Constants::$MYSQL_PASSWORD);

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read and decode the JSON data from the request body
    $requestData = json_decode(file_get_contents("php://input"));

    // Validate the presence of username
    if (isset($requestData->username)) {
        // Fetch employee details with permission level
        $query = "
        UPDATE employee
        SET locked = 1
        WHERE username = :username;
    ";

        $stmt = $connectionManager->getConnection()->prepare($query);
        $stmt->bindParam(':username', $requestData->username);
        $stmt->execute();

        $affectedRows = $stmt->rowCount();

        if ($affectedRows === 1) {
            // User locked successfully
            sendResponse(200, true, "Your account has been locked due to too many incorrect login attempts. Please contact your administrator at admin@bullseye.ca for assistance.");
            exit();
        } else {
            // No records were updated, possibly because the username didn't match any existing records
            // or the account has already been locked
            sendResponse(401, null, "Invalid username and/or password. Please contact your Administrator admin@bullseye.ca for assistance");
            exit();
        }
    } else {
        // Invalid request, missing username
        sendResponse(400, null, "Invalid username and/or password. Please contact your Administrator admin@bullseye.ca for assistance");
        exit();
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

