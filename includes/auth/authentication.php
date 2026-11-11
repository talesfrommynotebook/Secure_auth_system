<?php

        

    // register logic
    if (isset($_POST["register_btn"])) {

    
        // import the RegisterController class
        include("../../controllers/RegisterController.php");
        
        
        $fullname = validate_input($db->conn, $_POST["name"]);
        $matric_number = validate_input($db->conn, $_POST["matric_number"]);
        $password = validate_input($db->conn, $_POST["password"]);
        $confirm_password = validate_input($db->conn, $_POST["confirm_password"]);

        // registration controller logic
        $register = new RegisterController();

        $confirm_if_password_match = $register->confirmPassword($password, $confirm_password);
        if (!$confirm_if_password_match) {
            // rederect function exist in controller
            $register->redirect("password and confirm password do not match","red", "../../register.php");
            exit();
        }

        // check if user already exists , using function from controller
        $user_exists = $register->checkUserExists($matric_number);
        if ($user_exists) {
            $register->redirect("User with this matric number already exists", "red", "../../register.php");
            exit();
        }

        // PHP handles the salt automatically behind the scenes.
        $password = password_hash($password, PASSWORD_DEFAULT);
        
        
        // after all validation, 
        // proceed to register the user
        $register_query = $register->registration($fullname, $matric_number, $password);
        if (!$register_query) {
            $register->redirect("Registration Failed", "red", "../../register.php");
            exit();
        }

        // redirect to authenticator page after successful registration
        $register->redirect("Registration Successful", "green", "../../authenticator_page.php");

    }






    // login logic
    if (isset($_POST["login_btn"])) {
        
        // import the LoginController class
        include("../../controllers/LoginController.php");

        $matric_number = validate_input($db->conn, $_POST["matric_number"]);
        $password = validate_input($db->conn, $_POST["password"]);

        // login controller logic
        $login = new LoginController();

        // check if user exists and password is correct

        echo "Login button clicked. Form data: " . $matric_number . ", " . $password;
    }
?>