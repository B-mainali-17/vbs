<?php
session_start();
require '../config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Query the database to fetch the user's approved and unverified bookings separately
$query_approved = "SELECT venues.venueName, venues.venueLocation, venue_orders.booking_date
                   FROM venue_orders
                   INNER JOIN venues ON venue_orders.venue_id = venues.id
                   WHERE venue_orders.user_id = $user_id AND  venue_orders.verification_status = 1";

$query_unverified = "SELECT venues.venueName, venues.venueLocation, venue_orders.booking_date
                     FROM venue_orders
                     INNER JOIN venues ON venue_orders.venue_id = venues.id
                     WHERE venue_orders.user_id = $user_id AND venue_orders.verification_status = 0";


$result_approved = $conn->query($query_approved);
$result_unverified = $conn->query($query_unverified);

// Fetch all the approved bookings as an associative array
$bookings_approved = array();
if ($result_approved->num_rows > 0) {
    while ($row = $result_approved->fetch_assoc()) {
        $bookings_approved[] = $row;
    }
}

// Fetch all the unverified bookings as an associative array
$bookings_unverified = array();
if ($result_unverified->num_rows > 0) {
    while ($row = $result_unverified->fetch_assoc()) {
        $bookings_unverified[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>My Bookings</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <h1>Approved Bookings</h1>
    <?php if (count($bookings_approved) > 0) : ?>
        <table>
            <tr>
                <th>Venue Name</th>
                <th>Venue Location</th>
                <th>Booking Date</th>
            </tr>

            <?php foreach ($bookings_approved as $booking) : ?>
                <tr>
                    <td><?php echo $booking['venueName']; ?></td>
                    <td><?php echo $booking['venueLocation']; ?></td>
                    <td><?php echo $booking['booking_date']; ?></td>
                </tr>
            <?php endforeach; ?>

        </table>
    <?php else : ?>
        <p>No approved bookings found.</p>
    <?php endif; ?>

    <h1>Unverified Bookings</h1>
    <?php if (count($bookings_unverified) > 0) : ?>
        <table>
            <tr>
                <th>Venue Name</th>
                <th>Venue Location</th>
                <th>Booking Date</th>
            </tr>

            <?php foreach ($bookings_unverified as $booking) : ?>
                <tr>
                    <td><?php echo $booking['venueName']; ?></td>
                    <td><?php echo $booking['venueLocation']; ?></td>
                    <td><?php echo $booking['booking_date']; ?></td>
                </tr>
            <?php endforeach; ?>

        </table>
    <?php else : ?>
        <p>No unverified bookings found.</p>
    <?php endif; ?>

    <?php
    // Close the database connection
    $conn->close();
    ?>

    <!-- Add any other content or navigation links as needed -->

</body>

</html>
