
<!-- toastify -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>





<!-- check if there is any message to display -->
<?php

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = "green";
    isset($_SESSION['message_type']) && $message_type = $_SESSION['message_type'];


    echo "<span class='message' style='color: $message_type;'>$message</span>";


    echo "<script>
            Toastify({
                text: '$message',
                duration: 8000,
                close: true,
                gravity: 'bottom',
                position: 'right',
                backgroundColor: '$message_type',
            }).showToast();
        </script>";


    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}

?>