<?php
// include authentication logic
include '../includes/auth/authentication.php';

// include Authentication controller for the 2fa setup logic
include_once SITE_ROOT . '/controllers/AuthenticationController.php';
$authenticated = new AuthenticationController();

$userDetails = $authenticated->getAuthUserDetails();
$hasPermanentSecret = !empty($userDetails['google_secret']);

$qrCodeUrl = '';
if (!$hasPermanentSecret) {
    $qrCodeUrl = $authenticated->getUser2faVerificationCode();
}

// handle the form submission for 2FA verification
if (isset($_POST['verify_btn'])) {
    $inputedCode = trim($_POST['verification_code']);
    $secretToVerify = $hasPermanentSecret ? $userDetails['google_secret'] : $_SESSION['temp_google_secret'];
    $authenticated->verifyUser2faCode($inputedCode, $secretToVerify);
}

// navbar
include SITE_ROOT . '/includes/components/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication | OAUSTECH Secure Portal</title>
    <!-- custom css -->
    <link rel="stylesheet" href="<?php echo site_url('assets/css/styles.css'); ?>">
    <link rel="stylesheet" href="<?php echo site_url('assets/css/verify_setup.css'); ?>">
</head>

<body>

    <div class="display-message">
        <?php include SITE_ROOT . '/includes/components/showMessageAndToast.php'; ?>
    </div>

    <section class="hero-section">
        <div class="container">
            <div class="hero-container card">
                <img src="<?php echo site_url('assets/img/logo.png'); ?>" alt="OAUSTECH Logo">
                <br>
                <h1>2FA Authentication Check</h1>

                <?php if (!$hasPermanentSecret): ?>
                    <div class="setup-steps">
                        <p><strong>Step 1:</strong> Install <em>Google Authenticator on your mobile
                                device.</p>
                        <p><strong>Step 2:</strong> Open your app and scan the QR code below.</p>
                    </div>
                    <div class="qr-code">
                        <img src="<?php echo $qrCodeUrl; ?>" alt="OAUSTECH 2FA QR Code">
                    </div>
                <?php endif; ?>

                <div class="verify" style="margin-top: 1.5rem;">
                    <form action="" method="POST">
                        <div class="form-group">
                            <label for="verification_code">Enter 6-Digit Authentication Code</label>
                            <input type="text" name="verification_code" id="verification_code" placeholder="000000"
                                maxlength="6" autocomplete="one-time-code" autofocus required>
                            <button type="submit" name="verify_btn" class="btn-success">Verify & Proceed</button>
                        </div>
                    </form>
                </div>

                <div class="security-badge">
                    Protected by OAUSTECH ICT
                </div>
            </div>
        </div>
    </section>

    <!-- footer -->
    <?php include SITE_ROOT . '/includes/components/footer.php'; ?>

</body>

</html>