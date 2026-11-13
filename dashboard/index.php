<?php
//import authentication logic
include_once "../includes/auth/authentication.php";

include_once SITE_ROOT . '/controllers/AuthenticationController.php';

$authenticated = new AuthenticationController();
$users_data = $authenticated->getAuthUserDetails();
$checkIfUserIsVerified = $authenticated->checkIfUserIsVerified();

// navbar
include SITE_ROOT . '/includes/components/navbar.php';

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome To Your Dashboard</title>
    <!-- custom css -->
    <link rel="stylesheet" href="assets/css/index.css">
</head>

<body>


    <section class="hero-section P-5">
        <div class="container">


            <div class="hero-container">
                <br>
                <h1>Welcome To Your Dashboard</h1>

                <?php include SITE_ROOT . '/includes/components/showMessageAndToast.php'; ?>
                <p>lore ipsum dolor sit amet</p>

            </div>
        </div>
    </section>

    <!-- footer -->
    <?php include SITE_ROOT . '/includes/components/footer.php'; ?>


</body>

</html>