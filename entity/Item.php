<?php

class Item implements JsonSerializable
{
    private $itemID;
    private $imagePath;
    private $description;
    private $notes;

    public function __construct($itemID, $imagePath, $description, $notes)
    {
        // Check validation or other conditions when needed
        $this->itemID = $itemID;
        $this->imagePath = $imagePath;
        $this->description = $description;
        $this->notes = $notes;
    }

    public function getItemID()
    {
        return $this->itemID;
    }

    public function getImagePath()
    {
        return $this->imagePath;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getNotes()
    {
        return $this->notes;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
// end class Item
?>
