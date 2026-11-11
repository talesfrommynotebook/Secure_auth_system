<?php

include("../../includes/config/app.php");


class LoginController{

    public $conn;

    public function __construct(){
        $db = new DatabaseConnection();
        $this->conn = $db->conn;
    }


    public function loginUser($matric_number, $password){
        return true;
    }

    public function checkLoginCredentials($matric_number, $password){

    }
}

?>