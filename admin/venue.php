<?php
session_start();
require '../config.php';

// Check if the user is logged in as an admin (user_status = 3)
if (!isset($_SESSION['user_id']) || $_SESSION['user_status'] != 3) {
    // Redirect to the login page if not logged in as an admin
    header("Location: ../login.php");
    exit();
}

// Retrieve all venues registered by renters
$stmt = $conn->prepare("SELECT v.id, v.venueName, v.venueLocation, v.maxCapacity, v.minCapacity, v.venueImage, u.username FROM venues AS v INNER JOIN users AS u ON v.user_id = u.id WHERE u.user_status = 1");
$stmt->execute();
$result = $stmt->get_result();

// Close the prepared statement
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Venues</title>
    <style>
        table {
  border-collapse: collapse;
  width: 100%;
}

th, td {
  border: 1px solid #ddd;
  padding: 8px;
  text-align: left;
}

th {
  background-color: #f2f2f2;
  font-weight: bold;
}

tr:nth-child(even) {
  background-color: #f9f9f9;
}

tr:hover {
  background-color: #f5f5f5;
}

    </style>
</head>
<body>
    <h2>Admin Venues</h2>
    <table>
        <tr>
            <th>Venue Name</th>
            <th>Location</th>
            <th>Max Capacity</th>
            <th>Min Capacity</th>
            <th>Image</th>
            <th>Renter Username</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['venueName']; ?></td>
            <td><?php echo $row['venueLocation']; ?></td>
            <td><?php echo $row['maxCapacity']; ?></td>
            <td><?php echo $row['minCapacity']; ?></td>
            <td><img src="<?php echo $row['venueImage']; ?>" alt="Venue Image" width="100"></td>
            <td><?php echo $row['username']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <a href="../logout.php">Logout</a>
</body>
</html>
