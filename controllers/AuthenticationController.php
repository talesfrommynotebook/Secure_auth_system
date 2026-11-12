<?php

include_once(__DIR__ . "/../includes/config/app.php");


class AuthenticationController
{

    public $conn;

    public function __construct()
    {
        $db = new DatabaseConnection();
        $this->conn = $db->conn;
        $this->checkIsLoggedIn();
    }


    private function checkIsLoggedIn()
    {
        if (isset($_SESSION["authenticated"])) {
            // User is logged in
            return true;
        } else {
            // User is not logged in
            redirect("You need to login to access this page", "red", site_url("login.php"));
            return false;
        }
    }


    public function getAuthUserDetails()
    {
        $checkIfLoggedIn = $this->checkIsLoggedIn();

        if (!$checkIfLoggedIn) {
            return false;
        }

        // since user is logged in , we fetch his details from session
        $user_id = $_SESSION["auth_user"]["id"];

        $getUserDataQuery = "SELECT * users WHERE id='$user_id'";
        $result = $this->conn->query($getUserDataQuery);

        if ($result->num_rows > 0 ){

        }
        else{
            redirect("", "", site_url(""));
        }
    }
}


$authenticated = new AuthenticationController();
