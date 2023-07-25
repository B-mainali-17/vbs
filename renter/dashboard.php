<?php
session_start();
require '../config.php';

// Check if the user is logged in as a renter
if (!isset($_SESSION['user_id']) || $_SESSION['user_status'] != 1) {
    header("Location: login.php");
    exit();
}

?>
<h2>This is renter dashboard</h2>

<p><a href="addvenue.php">Add venues</a></p>
<p><a href="myvenues.php">My Venues</a></p>
<p><a href="checkbookings.php">Check Bookings</a></p>

<p><a href="../logout.php">Logout</a></p>