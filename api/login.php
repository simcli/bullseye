<?php
require_once dirname(__DIR__, 1) . '/db/ConnectionManager.php';
require_once dirname(__DIR__, 1) . '/utils/Constants.php';
require_once dirname(__DIR__, 1) . '/utils/ChromePhp.php';

session_start();

// Initialize the ConnectionManager
$connectionManager = new ConnectionManager(Constants::$MYSQL_CONNECTION_STRING, Constants::$MYSQL_USERNAME, Constants::$MYSQL_PASSWORD);
//sendResponse(400, null, $_SERVER['REQUEST_METHOD']);

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Read and decode the JSON data from the request body
    $requestData = json_decode(file_get_contents("php://input"));
    //sendResponse(401,$requestData->username,"TEST");
    // Validate the presence of username and password
    if (isset($requestData->username) && isset($requestData->password)) {

        // Fetch employee details with permission level
        $query = "
        SELECT
    e.Password,
    e.FirstName,
    e.LastName,
    p.permissionLevel,
    s.name AS location
    FROM
        employee e
    JOIN
        posn p ON e.PositionID = p.PositionID
    JOIN
        site s ON e.siteID = s.siteID
    WHERE
    e.username = :username
    AND e.active = 1
    AND e.locked = 0;
    ";

        $stmt = $connectionManager->getConnection()->prepare($query);

        $stmt->bindParam(':username', $requestData->username);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            //Compare sent password to hashed password
            if (password_verify($requestData->password, $result['Password'])) {
                //Employee found, respond with success and employee details
                sendResponse(200, $result, null);
                exit();
            } else {
                // Invalid credentials or employee not active/locked
                sendResponse(401, null, "Invalid username and/or password verification.");
                exit();
            }
        } else {
            // Invalid request, missing username or password
            sendResponse(401, null, "Invalid username and/or password. Please contact your Administrator admin@bullseye.ca for assistance");
            exit();
        }
    } else {
        // Invalid request, missing username or password
        sendResponse(400, null, "Invalid request. Missing username or password");
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
