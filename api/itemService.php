<?php
require_once dirname(__DIR__, 1) . '/db/ConnectionManager.php';
require_once dirname(__DIR__, 1) . '/db/ItemAccessor.php';
require_once dirname(__DIR__, 1) . '/entity/Item.php';
require_once dirname(__DIR__, 1) . '/utils/Constants.php';
require_once dirname(__DIR__, 1) . '/utils/ChromePhp.php';

$method = $_SERVER['REQUEST_METHOD'];
//ChromePhp::log(var_dump($_FILES));
try {
    $cm = new ConnectionManager(Constants::$MYSQL_CONNECTION_STRING, Constants::$MYSQL_USERNAME, Constants::$MYSQL_PASSWORD);
    $ia = new ItemAccessor($cm->getConnection());

    if ($method === "GET") {
        doGet($ia);
    } else if ($method === "PUT") {
        doPut($ia);
    } else {
        sendResponse(405, null, "Method not allowed");
    }
} catch (Exception $e) {
    sendResponse(500, null, "ERROR " . $e->getMessage());
} finally {
    if (!is_null($cm)) {
        $cm->closeConnection();
    }
}

function doGet($ia)
{
    // individual
    if (isset($_GET['itemid'])) {
        sendResponse(405, null, "individual GETs not allowed");
    }
    // collection
    else {
        try {
            $results = $ia->getAllItems();
            if (count($results) > 0) {
                $results = json_encode($results, JSON_NUMERIC_CHECK);
                sendResponse(200, $results, null);
            } else {
                sendResponse(404, null, "could not retrieve items");
            }
        } catch (Exception $e) {
            sendResponse(500, null, "ERROR " . $e->getMessage());
        }
    }
}

function doPut($ia)
{
    if (isset($_GET['itemid'])) {
        $body = file_get_contents('php://input');
        $contents = json_decode($body, true);

        try {
            $item = new Item(
                $contents['itemID'],
                $contents['imagePath'],
                $contents['description'],
                $contents['notes']
            );

            //get the file data
            $fileData = $_FILES['name'];
            //set the directory
            $uploadDirectory = "images";

            $success = $ia->updateItem($item, $fileData, $uploadDirectory);
            if ($success) {
                sendResponse(200, $success, null);
            } else {
                sendResponse(404, null, "Could not update item - it does not exist");
            }
        } catch (Exception $e) {
            sendResponse(400, null, $e->getMessage());
        }
    } else {
        sendResponse(405, null, "PUT requests must include itemID");
    }
}

function sendResponse($statusCode, $data, $error)
{
    header("Content-Type: application/json");
    http_response_code($statusCode);
    $resp = ['data' => $data, 'error' => $error];
    echo json_encode($resp, JSON_NUMERIC_CHECK);
}

