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
        // FIX 1: Update activity FIRST so the timestamp is current before checking timeouts
        $this->updateUserActivity();

        // FIX 2: Check timeouts SECOND
        $this->checkSessionTimeouts();
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
        $tfa = new TwoFactorAuth(new QRServerProvider(), "Oaustech 2FA Verification");
        $user = $this->getAuthUserDetails();

        if (empty($user["google_secret"])) {
            $secret = $tfa->createSecret();

            $user_id = $this->conn->real_escape_string($user["id"]);
            $secret_escaped = $this->conn->real_escape_string($secret);

            $updateQuery = "UPDATE users SET google_secret = '$secret_escaped' WHERE id = '$user_id'";
            if (!$this->conn->query($updateQuery)) {
                redirect("Error storing secret in database", "red", site_url("dashboard/verify-setup.php"));
            }
        } else {
            $secret = $user['google_secret'];
        }

        return $tfa->getQRCodeImageAsDataUri($user['fullname'], $secret);
    }








    public function verifyUser2faCode($userInputCode, $secret)
    {
        $tfa = new TwoFactorAuth(new QRServerProvider(), "Oaustech 2FA Verification");

        if (!$tfa->verifyCode($secret, $userInputCode)) {
            redirect("Invalid verification code. Please try again.", "red", site_url("dashboard/verify-setup.php"));
            return false;
        }

        $user_id = $this->conn->real_escape_string($this->getAuthUserDetails()['id']);
        $updateQuery = "UPDATE users SET is_2fa_verified = 1 WHERE id = '$user_id'";

        if (!$this->conn->query($updateQuery)) {
            redirect("Error updating verification status in database", "red", site_url("dashboard/verify-setup.php"));
        }

        redirect("Your account has been successfully verified!", "green", site_url("dashboard/index.php"));
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




    public function getSessionDetails()
    {

        if ($_SESSION['authenticated'] != true) {
            return false;
        }

        $user_id = $_SESSION["auth_user"]["user_id"];

        $query = "SELECT * FROM `user_sessions` WHERE `user_id` = '$user_id' LIMIT 1";
        $result = $this->conn->query($query);

        if (!$result) {
            return false;
        }

        $data = $result->fetch_assoc();

        return $data;
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
            $current_time = date('Y-m-d H:i:s'); // Use PHP time consistently
            $this->conn->query("UPDATE user_sessions SET last_activity = '$current_time' WHERE user_id = '$user_id'");
        }
    }






    public function checkSessionTimeouts()
    {
        if (!isset($_SESSION["auth_user"]["user_id"])) {
            return;
        }

        $user_id = $_SESSION["auth_user"]["user_id"];
        $query = "SELECT * FROM user_sessions WHERE user_id = '$user_id' LIMIT 1";
        $result = $this->conn->query($query);

        if (!$result || $result->num_rows === 0) {
            return;
        }

        $session = $result->fetch_assoc();
        $currentTime = time();

        // 10 Minutes Inactivity Check
        if (!empty($session['last_activity'])) {
            $lastActivityTime = strtotime($session['last_activity']);
            $inactiveSeconds = $currentTime - $lastActivityTime;

            // If user is inactive for more than 600 seconds (10 minutes)
            if ($inactiveSeconds > 600) {
                $this->destroyUserSession($user_id);
                redirect("Session expired due to inactivity.", "orange", site_url("login.php"));
            }
        }

        // 30 Minutes Absolute Session Limit
        if (!empty($session['created_at'])) {
            $sessionStartTime = strtotime($session['created_at']);
            $totalSessionSeconds = $currentTime - $sessionStartTime;

            // If total session time is more than 1800 seconds (30 minutes)
            if ($totalSessionSeconds > 1800) {
                $this->destroyUserSession($user_id);
                redirect("Your 30-minute session has expired.", "red", site_url("login.php"));
            }
        }
    }

    private function destroyUserSession($user_id)
    {
        $session_status = 'expired';
        $last_activity = date('Y-m-d H:i:s');

        // Update session table log
        $this->conn->query("UPDATE user_sessions SET session_status='$session_status', last_activity='$last_activity' WHERE user_id='$user_id'");

        // Clear PHP session data
        unset($_SESSION['authenticated']);
        unset($_SESSION['auth_role']);
        unset($_SESSION['auth_user']);

        unset($_SESSION['last_activity']);
        unset($_SESSION['created_at']);
    }




    
    // Calculate the exact remaining seconds for both timers for frontend display
    public function getSessionTimeoutsRemaining()
    {
        $sessionDetails = $this->getSessionDetails();
        $currentTime = time();

        // Hard Limit (30 minutes = 1800 seconds)
        $hardLimitRemaining = 1800;
        if ($sessionDetails && !empty($sessionDetails['created_at'])) {
            $elapsedHard = $currentTime - strtotime($sessionDetails['created_at']);
            $hardLimitRemaining = max(0, 1800 - $elapsedHard);
        }

        // Inactivity Limit (10 minutes = 600 seconds)
        $idleLimitRemaining = 600;
        if ($sessionDetails && !empty($sessionDetails['last_activity'])) {
            $elapsedIdle = $currentTime - strtotime($sessionDetails['last_activity']);
            $idleLimitRemaining = max(0, 600 - $elapsedIdle);
        }

        return [
            'hard_limit' => $hardLimitRemaining,
            'idle_limit' => $idleLimitRemaining
        ];
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
