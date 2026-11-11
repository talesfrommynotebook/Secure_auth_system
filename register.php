
<!-- navbar -->
<?php include 'includes/components/navbar.php'; ?>

<!-- authentication -->
<?php include 'includes/auth/authentication.php'; ?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <!-- custom css -->
    <link rel="stylesheet" href="assets/css/auth_pages.css">
</head>

<body>


    <section class="hero-section">
        <div class="container">
            <div class="hero-container">
                <a href="<?= site_url('index.php') ?>"><img src="assets/img/logo.png" alt="Hero Image"></a>
                <br>
                <h1>Welcome to <span>Oaustech</span></h1>

                <!-- show message -->
                <?php include 'includes/components/showMessageAndToast.php'; ?>

                <div class="form-container">
                    <!-- Pass event into the function -->
                    <form class="form" action="includes/auth/authentication.php" method="post">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="matric_number" class="form-label">Matric Number</label>
                            <input type="text" class="form-control" id="matric_number" name="matric_number" aria-describedby="matricHelp" placeholder="CSC/DE/25/..." required>
                            <div id="matricHelp" class="form-text">We'll never share your matric number with anyone else.</div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <button type="submit" name="register_btn" class="btn btn-primary">Create Account</button>
                    </form>
                </div>

                <br>
                <div class="form-footer">
                    <span>Already have an account?</span>
                    <a href="./login.php">Login</a>
                </div>
            </div>
        </div>
    </section>

    <!-- footer -->
    <?php include 'includes/components/footer.php'; ?>

    <!-- scripts -->
    <script src="./assets/js/authentication.js"></script>

        
</body>
</html>