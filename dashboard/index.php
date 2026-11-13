<?php
//import authentication logic
include_once "../includes/auth/authentication.php";
include_once SITE_ROOT . '/controllers/AuthenticationController.php';

$authenticated = new AuthenticationController();
$users_data = $authenticated->getAuthUserDetails();
$checkIfUserIsVerified = $authenticated->checkIfUserIsVerified();

$sessionDetails = $authenticated->getSessionDetails();

// Get the timers cleanly from the controller!
$timers = $authenticated->getSessionTimeoutsRemaining();
$hardLimitRemaining = $timers['hard_limit'];
$idleLimitRemaining = $timers['idle_limit'];

// navbar
include SITE_ROOT . '/includes/components/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome To Your Dashboard</title>
    <link rel="stylesheet" href="assets/css/index.css">
</head>

<body>

    <section class="hero-section P-5">
        <div class="container">
            <div class="hero-container">
                <br>
                <h1>Welcome To Your Dashboard</h1>

                <?php include SITE_ROOT . '/includes/components/showMessageAndToast.php'; ?>
                <p>Last activity time : <?= $sessionDetails["last_activity"] ?></p>

                <div class="session-timers card p-2 mb-3" style="font-size: 14px;">
                    <span>⏳ Hard Limit (30m): <strong id="hard-timer">00:00</strong></span>
                    <span>💤 Inactivity (10m): <strong id="idle-timer">00:00</strong></span>
                </div>
            </div>
        </div>
    </section>




    <script src="./assets/js/session-timeout.js"></script>
    <script>
        let absoluteTimeRemaining = <?php echo $hardLimitRemaining; ?>;
        let idleTimeRemaining = <?php echo $idleLimitRemaining; ?>;
        display_timeout(absoluteTimeRemaining, idleTimeRemaining) 
    </script>



    <?php include SITE_ROOT . '/includes/components/footer.php'; ?>
</body>

</html>