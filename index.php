

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <!-- custom css -->
    <link rel="stylesheet" href="assets/css/index.css">
</head>

<body>

    <!-- navbar -->
    <?php include 'includes/components/navbar.php'; ?>

    <section class="hero-section">
        <div class="container">
            <div class="hero-container">
                <img src="assets/img/logo.png" alt="Hero Image">
                <br>
                <h1>Welcome to <span>Oaustech</span></h1>
                <p>lore ipsum dolor sit amet</p>
                <div class="btn-holder">
                    <a href="./login.php" class="btn btn-primary login-btn">Login</a>
                    <a href="./register.php" class="btn btn-danger register-btn">Create Account</a>
                </div>
            </div>
        </div>
    </section>

    <!-- footer -->
    <?php include 'includes/components/footer.php'; ?>


</body>

</html>