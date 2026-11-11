<?php


    // register logic
    if (isset($_POST["register_btn"])) {
        
        // include 'config/app.php';
        include("../config/app.php");
        // import the RegisterController class
        include("./controllers/RegisterController.php");
        
        $fullname = validate_input($db->conn, $_POST["name"]);
        $matric_number = validate_input($db->conn, $_POST["matric_number"]);
        $password = validate_input($db->conn, $_POST["password"]);
        $confirm_password = validate_input($db->conn, $_POST["confirm_password"]);

        // registration controller logic
        $register = new RegisterController();
        
        echo "<br>";
        echo "Register button clicked. Form data: " . $fullname . ", " . $matric_number;
    }






    // login logic
    if (isset($_POST["login_btn"])) {
        $formData = $_POST["login_btn"];

        echo "Login button clicked. Form data: " . $formData;
    }
?>