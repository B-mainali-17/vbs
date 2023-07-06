<?php
session_start();

// Destroy the session and unset all session variables
session_destroy();
$_SESSION = [];

// Redirect the user to the login page
header("Location: login.php");
exit();
?>
