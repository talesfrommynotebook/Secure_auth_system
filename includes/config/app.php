<?php

session_start();
// Points to the project root folder ("secure_auth_system/")
define('SITE_ROOT', dirname(__DIR__, 2)); 

// define database connection parameters
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define("DB_PASSWORD", "");
define("DB_NAME", "secure_auth_db");
// define the base URL of the application
define("SITE_URL", "http://localhost/Php_Projects/secure_auth_system/");


// 2. Use SITE_ROOT to include DatabaseConnection reliably from anywhere
include_once(__DIR__ . "/DatabaseConnection.php");
$db = new DatabaseConnection();

function site_url($slug)
{
    return SITE_URL . $slug;
}


function validate_input($conn, $input)
{
    return mysqli_real_escape_string($conn, trim($input));
}


// function to redirect user
function redirect($message, $message_display_color, $location)
{
    $_SESSION['message'] = $message;
    $_SESSION['message_display_color'] = $message_display_color;
    header("Location: $location");
    exit();
}