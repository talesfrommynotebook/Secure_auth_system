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

        include_once(__DIR__ . "/AuthenticationController.php");
        $authController = new AuthenticationController();
        // create a user session log on login
        $createUserSessionLog = $authController->createUserSessionLog($_SESSION['auth_user']);
        if (!$createUserSessionLog) {
            redirect("Could not create user session log", "red", site_url("login.php"));
            exit;
        }

        return true;
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

            // update user session to inactie on logout
            include_once(__DIR__ . "/AuthenticationController.php");
            $authController = new AuthenticationController();
            $session_status = 'inactive';
            $last_activity = date('Y-m-d H:i:s');
            $updateSession = $authController->updateUserSessionLogOnLogout($_SESSION['auth_user']['user_id'], $session_status, $last_activity);
            if (!$updateSession) {
                redirect("Could not update user session log on logout", "red", site_url("dashboard"));
                exit;
            }


            // unverify users 2fa status on logout first, so that they will be prompted to verify again on next login
            $unverifyUser2fa = $this->unverifyUser2fa($_SESSION["auth_user"]["user_id"]);
            if (!$unverifyUser2fa) {
                redirect("Could not unverify user 2fa status", "red", site_url("dashboard"));
                exit;
            }

            // destroy the session
            unset($_SESSION["authenticated"]);
            unset($_SESSION["auth_role"]);
            unset($_SESSION["auth_user"]);

            return true;
        }
    }







    public function unverifyUser2fa($user_id)
    {
        $updateVerificationStatusQuery = "UPDATE users SET is_2fa_verified=0 WHERE id = '$user_id'";
        $result = $this->conn->query($updateVerificationStatusQuery);
        if (!$result) {
            redirect("Error updating verification status in database", "red", site_url("dashboard/verify-setup.php"));
        }

        return $result;
    }
}