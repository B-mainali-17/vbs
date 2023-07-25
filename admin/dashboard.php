<?php
session_start();
require '../config.php';

// Check if the user is logged in as an admin (user_status = 3)
if (!isset($_SESSION['user_id']) || $_SESSION['user_status'] != 3) {
    // Redirect to the login page if not logged in as an admin
    header("Location: ../login.php");
    exit();
}
?>
<link rel="stylesheet" href="style.css">
<h2>This is admin dashboard</h2>
<p><a href="bookers.php">Booker/user list</a></p>
<p><a href="renters.php">Renter list</a></p>
<p><a href="venue.php">Check Venues</a></p>

<p><a href="../logout.php">Logout</a></p>
