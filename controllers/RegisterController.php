<?php

    include("./includes/config/app.php");

    class RegisterController {

        public $conn;

        public function __construct(){
            $db = new DatabaseConnection();
            $this->conn = $db->conn;
            echo " register controlleer triggered";
        }

    }

?>