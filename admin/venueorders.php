<?php
session_start();
require '../config.php';

// Check if the user is logged in as an admin (user_status = 3)
if (!isset($_SESSION['user_id']) || $_SESSION['user_status'] != 3) {
    // Redirect to the login page if not logged in as an admin
    header("Location: ../login.php");
    exit();
}

// Retrieve all venue orders
$stmt = $conn->prepare("SELECT vo.id, vo.booking_date, v.venueName, v.venueLocation, u.username FROM venue_orders AS vo INNER JOIN venues AS v ON vo.venue_id = v.id INNER JOIN users AS u ON vo.user_id = u.id");
$stmt->execute();
$result = $stmt->get_result();

// Close the prepared statement
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Venue Orders</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Admin Venue Orders</h2>
    <table>
        <tr>
            <th>Order ID</th>
            <th>Booking Date</th>
            <th>Venue Name</th>
            <th>Location</th>
            <th>User</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['booking_date']; ?></td>
            <td><?php echo $row['venueName']; ?></td>
            <td><?php echo $row['venueLocation']; ?></td>
            <td><?php echo $row['username']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <a href="../logout.php">Logout</a>
</body>
</html>
