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


    // restrict pages to authenticated users only from authentication controller
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


    // get the logged in users details
    public function getAuthUserDetails()
    {
        $checkIfLoggedIn = $this->checkIsLoggedIn();

        if (!$checkIfLoggedIn) {
            return false;
        }

        // since user is logged in , we fetch his details from session
        $user_id = $_SESSION["auth_user"]["user_id"];

        $getUserDataQuery = "SELECT * FROM users WHERE id='$user_id' LIMIT 1";
        $result = $this->conn->query($getUserDataQuery);

        if ($result->num_rows > 0 ){
            $data = $result->fetch_assoc();
            return $data;
        }
        else{
            redirect("error getting users data", "red", site_url("login.php"));
        }
    }
}


