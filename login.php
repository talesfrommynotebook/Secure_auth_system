<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/auth_pages.css">
 </head>

<body>

    <!-- navbar -->
    <?php include 'includes/components/navbar.php'; ?>

    <section class="hero-section">
        <div class="container">
            <div class="hero-container">
                <a href="index.php"><img src="assets/img/logo.png" alt="Hero Image"></a>
                <br>
                <h1>Login to <span>Account</span></h1>

                <div class="form-container">
                    <form action="includes/auth/authentication.php" method="post" class="form">
                        <div class="mb-3">
                            <label for="matric_number" class="form-label">Matric Number</label>
                            <input type="text" class="form-control" id="matric_number" aria-describedby="matricHelp" placeholder="CSC/DE/25/...">
                            <div id="matricHelp" class="form-text">We'll never share your matric number with anyone else.</div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password">
                        </div> 
                        <input type="submit" name="login_btn" class="btn btn-primary" value="Login">

                    </form>
                </div>

                <br>
                <div class="form-footer">
                    <span>Don't have an account?</span>
                    <a href="./register.php" class="">Create Account</a>


                </div>
            </div>
        </div>
    </section>

    <!-- footer -->
    <?php include 'includes/components/footer.php'; ?>
</body>

</html>