<?php
session_start();
require '../config.php';

// Check if the user is logged in as a renter
if (!isset($_SESSION['user_id']) || $_SESSION['user_status'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Retrieve the renter's venues and the number of bookings for each venue from the database
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT v.id, v.venueName, COUNT(vo.id) AS bookings_count FROM venues v LEFT JOIN venue_orders vo ON v.id = vo.venue_id WHERE v.user_id = ? GROUP BY v.id, v.venueName");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($venue_id, $venueName, $bookings_count);
$venues = [];
while ($stmt->fetch()) {
    $venues[] = [
        'venue_id' => $venue_id,
        'venueName' => $venueName,
        'bookings_count' => $bookings_count
    ];
}
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Venues</title>
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
    <h2>My Venues</h2>
    <?php if (!empty($venues)): ?>
        <table>
            <thead>
                <tr>
                    <th>Venue Name</th>
                    <th>Number of Bookings</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($venues as $venue): ?>
                    <tr>
                        <td><?php echo $venue['venueName']; ?></td>
                        <td><?php echo $venue['bookings_count']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No venues found.</p>
    <?php endif; ?>
</body>
</html>
