<?php
session_start();
require '../config.php';

// Check if the user is logged in as an admin (user_status = 3)
if (!isset($_SESSION['user_id']) || $_SESSION['user_status'] != 3) {
    // Redirect to the login page if not logged in as an admin
    header("Location: ../login.php");
    exit();
}

// Function to approve a venue
function approveVenue($venue_id, $conn) {
    $stmt = $conn->prepare("UPDATE venues SET approval_status = 1 WHERE id = ?");
    $stmt->bind_param("i", $venue_id);
    $stmt->execute();
    $stmt->close();
}

// Retrieve all venues registered by renters
$stmt = $conn->prepare("SELECT v.id, v.venueName, v.venueLocation, v.maxCapacity, v.minCapacity, v.venueImage, u.username FROM venues AS v INNER JOIN users AS u ON v.user_id = u.id WHERE u.user_status = 1");
$stmt->execute();
$result = $stmt->get_result();

// Close the prepared statement
$stmt->close();

// Handle venue approval
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['approve_venue'])) {
    $venue_id = $_POST['venue_id'];
    approveVenue($venue_id, $conn);
    // Refresh the page to update the list of venues
    header("Location: venue.php");
    exit();
}

// Function to update the approval status of a venue
function updateApprovalStatus($venue_id, $approval_status, $conn) {
    $stmt = $conn->prepare("UPDATE venues SET verification_status = ? WHERE id = ?");
    $stmt->bind_param("ii", $approval_status, $venue_id);
    $stmt->execute();
    $stmt->close();
}

// Retrieve all venues registered by renters along with their approval status
$stmt = $conn->prepare("SELECT v.id, v.venueName, v.venueLocation, v.maxCapacity, v.minCapacity, v.venueImage, v.verification_status, u.username FROM venues AS v INNER JOIN users AS u ON v.user_id = u.id WHERE u.user_status = 1");
$stmt->execute();
$result = $stmt->get_result();

// Close the prepared statement
$stmt->close();

// Handle venue approval update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_approval'])) {
    $venue_id = $_POST['venue_id'];
    $approval_status = $_POST['approval_status'];
    updateApprovalStatus($venue_id, $approval_status, $conn);
    // Refresh the page to update the list of venues
    header("Location: venue.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Venues</title>
    <link rel="stylesheet" href="style.css">
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
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['venueName']; ?></td>
            <td><?php echo $row['venueLocation']; ?></td>
            <td><?php echo $row['maxCapacity']; ?></td>
            <td><?php echo $row['minCapacity']; ?></td>
            <td><img src="<?php echo $row['venueImage']; ?>" alt="Venue Image" width="100"></td>
            <td><?php echo $row['username']; ?></td>
            <td>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <input type="hidden" name="venue_id" value="<?php echo $row['id']; ?>">
        <select name="approval_status">
            <option value="1" <?php echo $row['verification_status'] == 1 ? 'selected' : ''; ?>>Approve</option>
            <option value="0" <?php echo $row['verification_status'] == 0 ? 'selected' : ''; ?>>Disable</option>
        </select>
        <input type="submit" name="update_approval" value="Update">
    </form>
</td>
        </tr>
        <?php endwhile; ?>
    </table>
    <a href="../logout.php">Logout</a>
</body>
</html>
