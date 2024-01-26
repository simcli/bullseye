<?php
require_once dirname(__DIR__, 1) . '/db/ConnectionManager.php';
require_once dirname(__DIR__, 1) . '/utils/Constants.php';

// Initialize the ConnectionManager
$connectionManager = new ConnectionManager(Constants::$MYSQL_CONNECTION_STRING, Constants::$MYSQL_USERNAME, Constants::$MYSQL_PASSWORD);

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read and decode the JSON data from the request body
    $requestData = json_decode(file_get_contents("php://input"));

    // Validate the presence of username and new password
    if (isset($requestData->username) && isset($requestData->newPassword)) {
        // Hash the new password using bcrypt
        $hashedPassword = password_hash($requestData->newPassword, PASSWORD_BCRYPT);

        // Update the password in the database
        $query = "
            UPDATE employee
            SET Password = :hashedPassword
            WHERE username = :username;
        ";

        $stmt = $connectionManager->getConnection()->prepare($query);
        $stmt->bindParam(':username', $requestData->username);
        $stmt->bindParam(':hashedPassword', $hashedPassword);
        $stmt->execute();

        $affectedRows = $stmt->rowCount();

        if ($affectedRows === 1) {
            // Password reset successful
            sendResponse(200, true, "Password reset successful");
            exit();
        } else {
            // Invalid username or other error
            sendResponse(401, null, "Invalid username or password reset failed");
            exit();
        }
    } else {
        // Invalid request, missing username or new password
        sendResponse(400, null, "Invalid request. Missing username or new password");
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

