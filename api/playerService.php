<?php
require_once dirname(__DIR__, 1) . '/db/ConnectionManager.php';
require_once dirname(__DIR__, 1) . '/db/PlayerAccessor.php';
require_once dirname(__DIR__, 1) . '/entity/Player.php';
require_once dirname(__DIR__, 1) . '/utils/Constants.php';
require_once dirname(__DIR__, 1) . '/utils/ChromePhp.php';

/*
 * Important Note:
 * 
 * $_GET will contain the item ID, even if the caller uses POST in the AJAX call. 
 * Why? Because the router (.htaccess) converts the URL from 
 *     menuService/items/N
 * to
 *     menuService.php?itemid=N
 * The syntax "?key=value" is interpreted as a GET parameter and is therefore
 * stored in the $_GET array.
 */

$method = $_SERVER['REQUEST_METHOD'];

try {
    $cm = new ConnectionManager(Constants::$MYSQL_CONNECTION_STRING, Constants::$MYSQL_USERNAME, Constants::$MYSQL_PASSWORD);
    $pla = new PlayerAccessor($cm->getConnection());

    if ($method === "GET") {
        doGet($pla);
    } else if ($method === "POST") {
        doPost($pla);
    } else if ($method === "DELETE") {
        doDelete($pla);
    } else if ($method === "PUT") {
        doPut($pla);
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

function doGet($pla)
{
    // individual
    if (isset($_GET['itemid'])) {
        sendResponse(405, null, "individual GETs not allowed");
    }
    // collection
    else {
        try {
            $results = $pla->getAllPlayers();
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

function doDelete($pla)
{
    ChromePhp::log("doDelete executing in PlayerService.php");

    if (isset($_GET['playerid'])) {
        $playerID = $_GET['playerid'];
        // Only the ID of the item matters for a delete,
        ChromePhp::log("playerid received is" . $playerID);
        $playerObj = new Player($playerID, 1, "", "", "", "NB");

        // delete the object from DB
        $success = $pla->deletePlayer($playerObj);
        if ($success) {
            sendResponse(200, $success, null);
        } else {
            sendResponse(404, null, "could not delete player - it does not exist");
        }
    } else {
        // Bulk deletes not implemented.
        sendResponse(405, null, "bulk DELETEs not allowed");
    }
}

function doPost($pla)
{
    if (isset($_GET['playerid'])) {
        // The details of the item to insert will be in the request body.
        $body = file_get_contents('php://input');
        $contents = json_decode($body, true);

        try {
            // Create a Player object
            $playerObj = new Player(
                $contents['playerID'],
                $contents['teamID'],
                $contents['firstName'],
                $contents['lastName'],
                $contents['hometown'],
                $contents['provinceCode']
            );

            // Add the object to DB
            $success = $pla->insertPlayer($playerObj);
            if ($success) {
                sendResponse(201, $success, null);
            } else {
                sendResponse(409, null, "Could not add Player - already exists");
            }
        } catch (Exception $e) {
            sendResponse(400, null, $e->getMessage());
        }
    } else {
        // Bulk inserts not implemented.
        sendResponse(405, null, "Bulk INSERTs not allowed.");
    }
}

function doPut($pla)
{
    if (isset($_GET['playerid'])) {
        // The details of the item to update will be in the request body.
        $body = file_get_contents('php://input');
        $contents = json_decode($body, true);

        try {
            // Create a Player object
            $playerObj = new Player(
                $contents['playerID'],
                $contents['teamID'],
                $contents['firstName'],
                $contents['lastName'],
                $contents['hometown'],
                $contents['provinceCode']
            );

            // Update the object in DB
            $success = $pla->updatePlayer($playerObj);
            if ($success) {
                sendResponse(200, $success, null);
            } else {
                sendResponse(404, null, "Could not update Player - does not exist");
            }
        } catch (Exception $e) {
            sendResponse(400, null, $e->getMessage());
        }
    } else {
        // Bulk updates not implemented.
        sendResponse(405, null, "Bulk UPDATEs not allowed.");
    }
}

function sendResponse($statusCode, $data, $error)
{
    header("Content-Type: application/json");
    http_response_code($statusCode);
    $resp = ['data' => $data, 'error' => $error];
    echo json_encode($resp, JSON_NUMERIC_CHECK);
}
