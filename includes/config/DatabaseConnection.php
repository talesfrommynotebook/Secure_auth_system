<?php

class DatabaseConnection
{

    public $conn;

    // database connection constructor, so we can use new DatabaseConnection() 
    // to create a new instance of the class and automatically establish a connection to the database.
    public function __construct()
    {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        // echo "Connected successfully";
        $this->conn = $conn;
    }

}
?>