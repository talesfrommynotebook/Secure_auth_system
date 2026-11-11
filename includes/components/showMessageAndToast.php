
<!-- toastify -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>





<!-- check if there is any message to display -->
<?php

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    // default toast color is green if not set
    $message_display_color = "green";
    isset($_SESSION['message_display_color']) && $message_display_color = $_SESSION['message_display_color'];


    echo "<span class='message' style='color: $message_display_color;'>$message</span>";


    echo "<script>
            Toastify({
                text: '$message',
                duration: 8000,
                close: true,
                gravity: 'bottom',
                position: 'right',
                backgroundColor: '$message_display_color',
            }).showToast();
        </script>";


    unset($_SESSION['message']);
    unset($_SESSION['message_display_color']);
}

?>