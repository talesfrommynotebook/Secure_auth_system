<?php

    // include 'config/app.php';
    include("../config/app.php");

    // register logic
    if (isset($_POST["register_btn"])) {
        $fullname = validate_input($db->conn, $_POST["name"]);
        $matric_number = validate_input($db->conn, $_POST["matric_number"]);
        $password = validate_input($db->conn, $_POST["password"]);
        $confirm_password = validate_input($db->conn, $_POST["confirm_password"]);
        
        echo "<br>";
        echo "Register button clicked. Form data: " . $fullname . ", " . $matric_number;
    }






    // login logic
    if (isset($_POST["login_btn"])) {
        $formData = $_POST["login_btn"];

        echo "Login button clicked. Form data: " . $formData;
    }
?>