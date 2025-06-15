<?php
require_once 'functions.php';

if (isset($_GET['email'])) {
    $email = urldecode($_GET['email']);
    unsubscribeEmail($email);

    echo "<h2>You have been unsubscribed successfully. 🙌</h2>";
} else {
    echo "<h2>Invalid unsubscribe request.</h2>";
}

