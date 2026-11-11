<?php

    // register logic
    if (isset($_POST["register_btn"])) {
        $formData = $_POST["register_btn"];

        echo "Register button clicked. Form data: " . $formData;
    }

    // login logic
    if (isset($_POST["login_btn"])) {
        $formData = $_POST["login_btn"];

        echo "Login button clicked. Form data: " . $formData;
    }
?>