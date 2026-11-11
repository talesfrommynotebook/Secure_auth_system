<?php

    include("./includes/config/app.php");

?>


<link rel="stylesheet" href="assets/css/styles.css">
<!-- bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">


<!-- navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="index.php">Oaustech Secure Auth</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('index.php') ?>">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('login.php') ?>">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('register.php') ?>">Register</a>
                </li>
            </ul>
        </div>
    </div>
</nav>