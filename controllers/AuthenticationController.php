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
        // Update activity timestamp on every page load
        $this->updateUserActivity();

        // Check timeouts SECOND 
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

        // 1. Check if a temporary secret already exists in the session for this setup flow
        if (empty($_SESSION['temp_google_secret'])) {
            // Generate a new secret and store it safely in the session
            $_SESSION['temp_google_secret'] = $tfa->createSecret();
        }

        $secret = $_SESSION['temp_google_secret'];

        // Generate the QR code URL using the session secret and user's full name/matric
        return $tfa->getQRCodeImageAsDataUri($user['fullname'], $secret);
    }

    public function verifyUser2faCode($userInputCode, $sessionSecret)
    {
        $tfa = new TwoFactorAuth(new QRServerProvider(), "Oaustech 2FA Verification");

        // Verify the code against the temporary session secret
        if (!$tfa->verifyCode($sessionSecret, $userInputCode)) {
            redirect("Invalid verification code. Please try again.", "red", site_url("dashboard/verify-setup.php"));
            return false;
        }

        $user_id = $this->conn->real_escape_string($this->getAuthUserDetails()['id']);
        $escapedSecret = $this->conn->real_escape_string($sessionSecret);

        // NOW that they have proven they scanned it, save the secret permanently and set verified to 1
        $updateQuery = "UPDATE users SET google_secret = '$escapedSecret', is_2fa_verified = 1 WHERE id = '$user_id'";

        if (!$this->conn->query($updateQuery)) {
            redirect("Error saving verification details in database", "red", site_url("dashboard/verify-setup.php"));
            return false;
        }

        // Clear the temporary session secret since it's now safely in the database
        unset($_SESSION['temp_google_secret']);


        writeActivityLog($_SESSION["auth_user"]["matric_number"], $_SESSION["auth_user"]["fullname"], "Successfully verified their account.");
        redirect("Your account has been successfully verified!", "green", site_url("dashboard/index.php"));
        return true;
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

        $current_time = date('Y-m-d H:i:s');

        // First, check if user already has an active or old session row
        $checkActiveSessionQuery = "SELECT * FROM `user_sessions` WHERE `user_id`='$user_id'";
        $checkActiveSessionResult = $this->conn->query($checkActiveSessionQuery);

        if ($checkActiveSessionResult->num_rows > 0) {
            // Reset the timers completely on a fresh login so it doesn't trigger 30-min timeout falsely!
            $updateSessionQuery = "UPDATE `user_sessions` SET 
            `session_status` = '$session_status', 
            `ip_address` = '$ip_address', 
            `user_agent` = '$user_agent', 
            `last_activity` = '$current_time', 
            `session_life_cycle_refresh_time` = '$current_time'
            WHERE `user_id` = '$user_id'";

            $updateSessionResult = $this->conn->query($updateSessionQuery);
            writeActivityLog($_SESSION["auth_user"]["matric_number"], $_SESSION["auth_user"]["fullname"], "Session successfully Updatted.");
            return $updateSessionResult ? true : false;
        }

        // If no session row exists, create a brand new one with fresh timestamps
        $query = "INSERT INTO `user_sessions` (`user_id`, `session_status`, `ip_address`, `user_agent`, `last_activity`, `session_life_cycle_refresh_time`, `created_at`) 
              VALUES ('$user_id', '$session_status', '$ip_address', '$user_agent', '$current_time', '$current_time', '$current_time')";

        $result = $this->conn->query($query);


        writeActivityLog($_SESSION["auth_user"]["matric_number"], $_SESSION["auth_user"]["fullname"], "Session successfully created.");
        return $result ? true : false;
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
        if (isset($_SESSION["authenticated"])) {
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

            if ($inactiveSeconds > INACTIVITY_SECONDS_LIMIT_SETTINGS) {
                $this->destroyUserSession($user_id);
                redirect("Session expired due to inactivity.", "orange", site_url("login.php"));
                exit();
            }
        }

        // 30 Minutes Absolute Session Limit
        if (!empty($session['session_life_cycle_refresh_time'])) {
            $sessionStartTime = strtotime($session['session_life_cycle_refresh_time']);
            $totalSessionSeconds = $currentTime - $sessionStartTime;

            if ($totalSessionSeconds > ABSOLUTE_SECONDS_LIMIT_SETTINGS) {
                $this->destroyUserSession($user_id);
                redirect("Your 30-minute session has expired.", "red", site_url("login.php"));
                exit();
            }
        }
    }



    private function destroyUserSession($user_id)
    {
        $session_status = 'expired';
        $last_activity = date('Y-m-d H:i:s');
        $current_time = date('Y-m-d H:i:s');

        // Fetch user details before clearing session
        $userQuery = $this->conn->query("SELECT fullname, matric_number FROM users WHERE id = '$user_id' LIMIT 1");
        if ($userQuery && $userQuery->num_rows > 0) {
            $uData = $userQuery->fetch_assoc();
            writeActivityLog($uData['matric_number'], $uData['fullname'], "Session expired due to inactivity or 30-minute limit.");
        }

        // Update session table log status to expired
        $this->conn->query("UPDATE user_sessions SET session_status='$session_status', last_activity='$last_activity', session_life_cycle_refresh_time='$current_time' WHERE user_id='$user_id'");

        // Unverify user's 2FA status so they must verify again on next login
        $this->conn->query("UPDATE users SET is_2fa_verified = 0 WHERE id = '$user_id'");


        // destroy and clear session data
        session_unset();
        session_destroy();
        redirect("Your session has expired. Please login again.", "orange", site_url("login.php"));
        exit();
    }





    // Calculate the exact remaining seconds for both timers for frontend display
    public function getSessionTimeoutsRemaining()
    {
        $sessionDetails = $this->getSessionDetails();
        $currentTime = time();

        // Hard Limit (30 minutes = 1800 seconds)
        $hardLimitRemaining = ABSOLUTE_SECONDS_LIMIT_SETTINGS;
        if ($sessionDetails && !empty($sessionDetails['session_life_cycle_refresh_time'])) {
            $elapsedHard = $currentTime - strtotime($sessionDetails['session_life_cycle_refresh_time']);
            $hardLimitRemaining = max(0, ABSOLUTE_SECONDS_LIMIT_SETTINGS - $elapsedHard);
        }

        // Inactivity Limit (10 minutes = 600 seconds)
        $idleLimitRemaining = INACTIVITY_SECONDS_LIMIT_SETTINGS;
        if ($sessionDetails && !empty($sessionDetails['last_activity'])) {
            $elapsedIdle = $currentTime - strtotime($sessionDetails['last_activity']);
            $idleLimitRemaining = max(0, INACTIVITY_SECONDS_LIMIT_SETTINGS - $elapsedIdle);
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