<?php
session_start();
require '../config.php';

// Check if the user is logged in as a booker
if (!isset($_SESSION['user_id']) || $_SESSION['user_status'] != 0) {
    header("Location: ../login.php");
    exit();
}?>
<h2>This is booker dashboard</h2>

<p><a href="checkvenues.php">Check Venues</a></p>
<p><a href="bookings.php">My bookings</a></p>

<p><a href="../logout.php">Logout</a></p>