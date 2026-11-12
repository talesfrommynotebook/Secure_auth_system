<?php



class LoginController
{

    public $conn;

    public function __construct()
    {
        $db = new DatabaseConnection();
        $this->conn = $db->conn;
    }


    public function checkLoginCredentialsAndLogin($matric_number, $password)
    {
        $query = "SELECT * FROM users WHERE matric_number = '$matric_number'";
        $result = $this->conn->query($query);

        // if user exist , check if pw is correct
        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            if (!password_verify($password, $data['password'])) {
                redirect('password is not correct', 'red', site_url('login.php'));
                return false;
            }

            // if pw is correct , authenticate user
            $this->loginUser($data);

            return true;
        } else {
            return false;
        }
    }




    public function loginUser($data)
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
            return true;
        } else {
            return false;
        }
    }



    public function logout()
    {
        if (isset($_SESSION["authenticated"]) == true) {
            // destroy the session
            unset($_SESSION["authenticated"]);
            unset($_SESSION["auth_role"]);
            unset($_SESSION["auth_user"]);

            return true;
        }

    }
}
