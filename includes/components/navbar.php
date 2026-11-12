<?php

// include("./includes/config/app.php");

?>


<link rel="stylesheet" href="assets/css/styles.css">
<!-- bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- toastify -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">


<!-- navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="index.php"><img src="<?= site_url('assets/img/logo.png') ?>" alt=""></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('index.php') ?>">Home</a>
                </li>

                <!-- if user is authenticated -->
                <!-- show user profile dropdown with logout btn -->
                <?php
                if (isset($_SESSION["authenticated"])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Welcome <?php echo $_SESSION["auth_user"]["fullname"]; ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="p-2"><a href="<?= site_url('dashboard/profile.php') ?>">Profile</a></li>
                            <li class="p-2">
                                <form action="" method="post">
                                    <button type="submit" name="logout_btn" class="btn btn-danger">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>

                    <!-- else if user is not authenticated show login & register links  -->
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= site_url('login.php') ?>">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= site_url('register.php') ?>">Register</a>
                    </li>


                <?php endif; ?>

            </ul>
        </div>
    </div>
</nav>