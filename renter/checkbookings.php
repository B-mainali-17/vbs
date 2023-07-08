<?php
session_start();
require '../config.php';

// Check if the user is logged in as a renter
if (!isset($_SESSION['user_id']) || $_SESSION['user_status'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Retrieve the user's bookings from the database
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT v.id, v.venueName, v.venueLocation, vo.booking_date FROM venues v JOIN venue_orders vo ON v.id = vo.venue_id WHERE v.user_id = ? ORDER BY vo.booking_date");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($venue_id, $venueName, $venueLocation, $booking_date);
$bookings = [];
while ($stmt->fetch()) {
    $bookings[] = [
        'venue_id' => $venue_id,
        'venueName' => $venueName,
        'venueLocation' => $venueLocation,
        'booking_date' => $booking_date
    ];
}
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Check Bookings</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
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
    <h2>My Bookings</h2>
    <?php if (!empty($bookings)): ?>
        <table>
            <thead>
                <tr>
                    <th>Venue Name</th>
                    <th>Venue Location</th>
                    <th>Booking Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?php echo $booking['venueName']; ?></td>
                        <td><?php echo $booking['venueLocation']; ?></td>
                        <td><?php echo $booking['booking_date']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No bookings found.</p>
    <?php endif; ?>
</body>
</html>
