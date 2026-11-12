<?php
//import authentication logic
include_once "../includes/auth/authentication.php";

// restrict pages to authenticated usrs only 
include_once SITE_ROOT . '/controllers/AuthenticationController.php';
$authenticated = new AuthenticationController();
$users_data = $authenticated->getAuthUserDetails();

// navbar
include SITE_ROOT . '/includes/components/navbar.php';

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <!-- custom css -->
    <link rel="stylesheet" href="assets/css/index.css">
</head>

<body>


    <section class="hero-section Py-2">
        <div class="container">
            <div class="hero-container">
                <br>
                <h2>My Profile</h2>
                <?php include SITE_ROOT . '/includes/components/showMessageAndToast.php'; ?>
            </div>
        </div>
    </section>

    <section class="user-details ">
        <div class="container">
            <div class="user-details-info-container card p-4">
                <div class="each-details-box">
                    <h5>Fullname : </h5> <span><?= $users_data["fullname"] ?></span>
                </div>
                <div class="each-details-box">
                    <h5>Matric Number : </h5> <span><?= $users_data["matric_number"] ?></span>
                </div>
                <div class="each-details-box">
                    <h5>Registration Date : </h5> <span><?= date('d-m-y', strtotime($users_data["created_at"])) ?></span>
                </div>
            </div>
        </div>
    </section>

    <br>

    <!-- footer -->
    <?php include SITE_ROOT . '/includes/components/footer.php'; ?>


</body>


<style>
    .each-details-box {
        display: flex;
        align-items: center;
        gap: 20px;
    }
</style>

</html>