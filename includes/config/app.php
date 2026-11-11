<?php

    // define database connection parameters
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define("DB_PASSWORD", "");
    define("DB_NAME", "secure_auth_db");
    // define the base URL of the application
    define("BASE_URL", "http://localhost/Php_Projects/Secure%20Auth");

    include_once("DatabaseConnection.php");
    $db = new DatabaseConnection();    


?>