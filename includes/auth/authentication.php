<?php

include_once(__DIR__ . "/../config/app.php");

// 1. Include your controllers
include_once(SITE_ROOT . "/controllers/LoginController.php");
include_once(SITE_ROOT . "/controllers/RegisterController.php");





function checkIfUserIsLoggedIn()
{
    // login controller logic
    $loginAuth = new LoginController();
    // check if user is already logged in
    $checkLogin = $loginAuth->isUserAlreadyLoggedIn();

    if ($checkLogin) {
        redirect("already logged in", "green", site_url("dashboard"));
    } else {
        return false;
    }
}







// register logic
if (isset($_POST["register_btn"])) {


    $fullname = validate_input($db->conn, $_POST["name"]);
    $matric_number = validate_input($db->conn, $_POST["matric_number"]);
    $password = validate_input($db->conn, $_POST["password"]);
    $confirm_password = validate_input($db->conn, $_POST["confirm_password"]);

    // registration controller logic
    $register = new RegisterController();

    $confirm_if_password_match = $register->confirmPassword($password, $confirm_password);
    if (!$confirm_if_password_match) {
        // rederect function exist in controller
        redirect("password and confirm password do not match", "red", site_url("register.php"));
        exit();
    }

    // check if user already exists , using function from controller
    $user_exists = $register->checkUserExists($matric_number);
    if ($user_exists) {
        redirect("User with this matric number already exists", "red", site_url("register.php"));
        exit();
    }

    // PHP handles the salt automatically behind the scenes.
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);


    // after all validation, 
    // proceed to register the user
    $register_query = $register->registration($fullname, $matric_number, $hashed_password);
    if (!$register_query) {
        redirect("Registration Failed", "red", site_url("register.php"));
        exit();
    }

    // redirect to authenticator page after successful registration
    redirect("Registration Successful", "green", site_url("dashboard"));
}







// login logic
if (isset($_POST["login_btn"])) {
    session_start();

    $matric_number = validate_input($db->conn, $_POST["matric_number"]);
    $password = validate_input($db->conn, $_POST["password"]);

    // login controller logic
    $loginAuth = new LoginController();

    // check if user exists and password is correct
    $checkLogin = $loginAuth->checkLoginCredentialsAndLogin($matric_number, $password);
    if (!$checkLogin) {
        redirect("Could not find user", "red", site_url("login.php"));
        exit;
    }

    redirect("Logged in successfully", "green", site_url("dashboard"));
}







// logout logic
if (isset($_POST["logout_btn"])) {

    $loginAuth = new LoginController();
    $checkLoggedOut = $loginAuth->logout();

    if (!$checkLoggedOut) {
        redirect("Could not log you out", "red", site_url("dashboard"));
        exit;
    }

    // redirect to login page
    redirect("You have been logged out", "green", site_url("login.php"));

}