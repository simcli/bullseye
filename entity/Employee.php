<?php

class Employee implements JsonSerializable
{
    private $employeeID;
    private $permissionLevel;
    private $username;
    private $password;
    private $firstName;
    private $lastName;
    private $email;
    private $active;
    private $siteName;
    private $locked;

    public function __construct($employeeID, $permissionLevel, $username, $password, $firstName, $lastName, $email, $active, $siteName, $locked)
    {
        // Check validation or other conditions when needed
        $this->employeeID = $employeeID;
        $this->permissionLevel = $permissionLevel;
        $this->username = $username;
        $this->password = $password;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->active = $active;
        $this->siteName = $siteName;
        $this->locked = $locked;
    }

    public function getEmployeeID()
    {
        return $this->employeeID;
    }

    public function getPermissionLevel()
    {
        return $this->permissionLevel;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function isActive()
    {
        return $this->active;
    }

    public function getSiteName()
    {
        return $this->siteName;
    }

    public function isLocked()
    {
        return $this->locked;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
// end class Employee
