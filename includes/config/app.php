<?php

session_start();

// define database connection parameters
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define("DB_PASSWORD", "");
define("DB_NAME", "secure_auth_db");
// define the base URL of the application
define("SITE_URL", "http://localhost/Php_Projects/secure_auth_system/");

include_once("DatabaseConnection.php");
$db = new DatabaseConnection();

function site_url($slug)
{
    return SITE_URL . $slug;
}


function validate_input($conn, $input)
{
    return mysqli_real_escape_string($conn, trim($input));
}
