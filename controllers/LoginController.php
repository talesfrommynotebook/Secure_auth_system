<?php

include("../../includes/config/app.php");


class LoginController
{

    public $conn;

    public function __construct()
    {
        $db = new DatabaseConnection();
        $this->conn = $db->conn;
    }


    public function loginUser($matric_number, $password)
    {
        return true;
    }

    public function checkLoginCredentials($matric_number, $password)
    {
        $query = "SELECT * from users WHERE matric_number = $matric_number";
        $result = $this->conn->query($query);

        // if user exist , check if pw is correct
        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            if ($data->password != $password) {
                echo "password is incorrect";
                return false;
            }

            // if pw is correct , authenticate user
            $this->userAuthentication($data);

            return true;
        } else {
            return false;
        }
    }


    public function userAuthentication($data)
    {
        $_SESSION['authenticated'] = true;
        $_SESSION['auth_role'] = $data["role"];
        $_SESSION['auth_user'] = [
            'user_id' => $data["id"],
            'fullname' => $data["fullname"],
            'matric_number' => $data["matric_number"],
        ];
    }


    public function isUserAlreadyLoggedIn()
    {
        if (isset($_SESSION["authenticated"]) === true) {
            redirect("authenticated", "green", site_url("authenticator_page.php"));
            return true;
        } else {
            return false;
        }
    }
}

?>