<?php

class DatabaseConnection{

    public $conn;

    public function __construct(){
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        if($conn->connect_error){
            die("Connection failed: " . $conn->connect_error);
        }
        echo "Connected successfully";
        $this->conn = $conn;
    }

}
?>