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

    <!-- navbar -->
    <?php include 'includes/components/navbar.php'; ?>

    <section class="hero-section">
        <div class="container">


            <div class="hero-container">
                <img src="assets/img/logo.png" alt="Hero Image">
                <br>
                <h1>Welcome To Your Dashboard</h1>

                <?php include 'includes/components/showMessageAndToast.php'; ?>
                <p>lore ipsum dolor sit amet</p>

            </div>
        </div>
    </section>

    <!-- footer -->
    <?php include 'includes/components/footer.php'; ?>


</body>

</html>