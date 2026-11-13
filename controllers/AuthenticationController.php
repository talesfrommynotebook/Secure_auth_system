<?php

include_once __DIR__ . "/../includes/config/app.php";

// include the RobThree TwoFactorAuth library
use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\Providers\Qr\QRServerProvider; // if using library's provider

class AuthenticationController
{

    public $conn;

    public function __construct()
    {
        $db = new DatabaseConnection();
        $this->conn = $db->conn;
        $this->checkIsLoggedIn();
        $this->updateUserActivity();
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


    public function checkIfUserIsVerified()
    {
        // Query the database to check if the user is verified
        $user = $this->getAuthUserDetails();

        if ($user['is_2fa_verified'] == 0) {
            redirect("Please complete your 2FA verification", "orange", site_url("dashboard/verify-setup.php"));
            return false;
        }

        return true;
    }

    public function getUser2faVerificationCode()
    {
        // using the default Qr Code provider from the library
        $tfa = new TwoFactorAuth(new QRServerProvider(), "Oaustech 2FA Verification");

        // Query the database to check if the user is verified
        $user = $this->getAuthUserDetails();

        // If the user does not have a secret, generate one and store it in the database
        if (empty($user["google_secret"])) {
            $secret = $tfa->createSecret();

            // Store the secret in the database for the user
            $user_id = $user["id"];
            $updateSecretInUserDBQuery = "UPDATE users SET google_secret= '$secret' WHERE id = '$user_id'";
            $result = $this->conn->query($updateSecretInUserDBQuery);

            if (!$result) {
                redirect("Error storing secret in database", "red", site_url("dashboard/verify-setup.php"));
            }

        } else {
            // If the user already has a secret, return it
            $secret = $user['google_secret'];
        }

        // generate the QR code URL for the user to scan with their authenticator app
        // 2. Generate the QR code data URI using the persistent secret
        $qrCodeUrl = $tfa->getQRCodeImageAsDataUri($user['matric_number'], $secret);

        return $qrCodeUrl;
    }

    public function verifyUser2faCode($userInputCode, $secret)
    {
        $tfa = new TwoFactorAuth(new QRServerProvider(), "Oaustech 2FA Verification");
        $code_is_valid = $tfa->verifyCode($secret, $userInputCode);
        if (empty($code_is_valid)) {
            redirect("Invalid verification code. Please try again.", "red", site_url("dashboard/verify-setup.php"));
            return false;
        }

        echo "Verification code is valid!";
        // update the user's record in the database to mark them as verified
        $user_id = $this->getAuthUserDetails()['id'];
        $updateVerificationStatusQuery = "UPDATE users SET is_2fa_verified=1 WHERE id = '$user_id'";
        $result = $this->conn->query($updateVerificationStatusQuery);
        if (!$result) {
            redirect("Error updating verification status in database", "red", site_url("dashboard/verify-setup.php"));
        }

        redirect("Your account has been successfully verified!", "green", site_url("dashboard/index.php"));
        return $result;
    }



    // function to create a user session log
    public function createUserSessionLog($data)
    {

        if ($_SESSION['authenticated'] != true) {
            return false;
        }

        $user_id = $data['user_id'];
        $session_status = 'active';
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];

        $last_activity = date('Y-m-d H:i:s');
        $created_at = date('Y-m-d H:i:s');

        // first , check if user already has an active session
        $checkActiveSessionQuery = "SELECT * FROM `user_sessions` WHERE `user_id`='$user_id'";
        $checkActiveSessionResult = $this->conn->query($checkActiveSessionQuery);

        if ($checkActiveSessionResult->num_rows > 0) {
            // User already has a session just update the session status to active and update the last activity time
            $updateSessionQuery = "UPDATE `user_sessions` SET `session_status`='$session_status', `ip_address`='$ip_address', `user_agent`='$user_agent', `last_activity`='$last_activity', `created_at`='$created_at' WHERE `user_id`='$user_id'";
            $updateSessionResult = $this->conn->query($updateSessionQuery);
            if (!$updateSessionResult) {
                return false;
            }
            return true;
        }

        // if user does not have an active session, create a new session log
        $query = "INSERT INTO `user_sessions`(`user_id`, `session_status`, `ip_address`, `user_agent`, `last_activity`, `created_at`) VALUES ('$user_id', '$session_status', '$ip_address', '$user_agent', '$last_activity', '$created_at')";
        $result = $this->conn->query($query);

        if (!$result) {
            return false;
        }

        return true;
    }



    // function to update user session log
    public function updateUserSessionLogOnLogout($user_id, $session_status, $last_activity)
    {
        $query = "UPDATE `user_sessions` SET `session_status`='$session_status', `last_activity`='$last_activity' WHERE `user_id`='$user_id'";
        $result = $this->conn->query($query);

        if (!$result) {
            return false;
        }

        return true;
    }


    // update user activity timestamp on every page load
    private function updateUserActivity()
    {
        if (isset($_SESSION["auth_user"]["user_id"])) {
            $user_id = $_SESSION["auth_user"]["user_id"];
            // Updates the timestamp for their active sessions
            $this->conn->query("UPDATE user_sessions SET last_activity = NOW() WHERE user_id = '$user_id'");
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

        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            return $data;
        } else {
            redirect("error getting users data", "red", site_url("login.php"));
        }
    }



}