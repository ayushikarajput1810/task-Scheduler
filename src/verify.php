<?php
require_once 'functions.php';

if (isset($_GET['email']) && isset($_GET['code'])) {
    $email = $_GET['email'];
    $code = $_GET['code'];

    $success = verifySubscription($email, $code);

    if ($success) {
        echo "<h2>Email verified successfully! 🎉</h2>";
    } else {
        echo "<h2>Verification failed. Invalid or expired code.</h2>";
    }
} else {
    echo "<h2>Invalid request.</h2>";
}

