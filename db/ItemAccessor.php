<?php
require_once dirname(__DIR__, 1) . '/entity/Item.php';
require_once dirname(__DIR__, 1) . '/utils/ChromePhp.php';

class ItemAccessor
{
    private $getAllStatementString = "SELECT * FROM item;";
    private $getByIDStatementString = "SELECT * FROM item WHERE itemID = :itemID;";
    private $updateStatementString = "UPDATE item SET imagePath = :imagePath, description = :description, notes = :notes WHERE itemID = :itemID;";

    private $getAllStatement = null;
    private $getByIDStatement = null;
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

        $this->updateStatement = $conn->prepare($this->updateStatementString);
        if (is_null($this->updateStatement)) {
            throw new Exception("bad statement: '" . $this->updateStatementString . "'");
        }
    }

    /**
     * Gets all items.
     * 
     * @return Item[] array of Item objects
     */
    public function getAllItems()
    {
        $results = [];

        try {
            $this->getAllStatement->execute();
            $dbresults = $this->getAllStatement->fetchAll(PDO::FETCH_ASSOC);

            foreach ($dbresults as $r) {
                $itemID = $r['itemID'];
                $imagePath = $r['imagePath'];
                $description = $r['description'];
                $notes = $r['notes'];

                $obj = new Item($itemID, $imagePath, $description, $notes);
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
     * Gets the item with the specified ID.
     * 
     * @param int $id the ID of the item to retrieve 
     * @return Item|null Item object with the specified ID, or NULL if not found
     */
    public function getItemByID($id)
    {
        $result = null;

        try {
            $this->getByIDStatement->bindParam(":itemID", $id);
            $this->getByIDStatement->execute();
            $dbresult = $this->getByIDStatement->fetch(PDO::FETCH_ASSOC);

            if ($dbresult) {
                $itemID = $dbresult['itemID'];
                $imagePath = $dbresult['imagePath'];
                $description = $dbresult['description'];
                $notes = $dbresult['notes'];

                $result = new Item($itemID, $imagePath, $description, $notes);
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
     * Updates an item in the database.
     * 
     * @param Item $item an object of type Item, containing updated data
     * @return boolean indicates if the item was updated
     */
    public function updateItem($item, $fileData, $uploadDirectory)
    {
        if (!$this->itemExists($item)) {
            return false;
        }

        $success = false;

        $itemID = $item->getItemID();
        $description = $item->getDescription();
        $notes = $item->getNotes();

        // Check if a new image file is uploaded
        if (!empty($fileData['tmp_name'])) {
            $imagePath = $this->uploadImage($fileData, $uploadDirectory);
        } else {
            // Keep the existing image path if no new file is uploaded
            $imagePath = $item->getImagePath();
        }

        try {
            $this->updateStatement->bindParam(":itemID", $itemID);
            $this->updateStatement->bindParam(":imagePath", $imagePath);
            $this->updateStatement->bindParam(":description", $description);
            $this->updateStatement->bindParam(":notes", $notes);

            $success = $this->updateStatement->execute(); // this doesn't mean what you think it means
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

    /**
     * Uploads an image file to the server and returns the path to the uploaded file.
     * 
     * @param array $fileData the file data uploaded by the user
     * @param string $uploadDirectory the directory where uploaded images will be stored
     * @return string the path to the uploaded image file
     */
    private function uploadImage($fileData, $uploadDirectory)
    {
        $uploadedFilePath = '';

        // Check if the file is uploaded successfully
        if (move_uploaded_file($fileData['tmp_name'], $uploadDirectory . '/' . $fileData['name'])) {
            $uploadedFilePath = $uploadDirectory . '/' . $fileData['name'];
        }

        return $uploadedFilePath;
    }

    /**
     * Checks if an item exists in the database.
     * 
     * @param Item $item the item to check
     * @return boolean true if the item exists; false if not
     */
    public function itemExists($item)
    {
        return $this->getItemByID($item->getItemID()) !== null;
    }
}
// end class ItemAccessor

