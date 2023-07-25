<?php
session_start();
require '../config.php';

// Check if the user is logged in as a booker
if (!isset($_SESSION['user_id']) || $_SESSION['user_status'] != 0) {
    header("Location: ../login.php");
    exit();
}

// Retrieve venues with verification_status = 1 from the database
$stmt = $conn->prepare("SELECT id, venueName, venueLocation, maxCapacity, minCapacity FROM venues WHERE verification_status = 1");
$stmt->execute();
$stmt->bind_result($venue_id, $venueName, $venueLocation, $maxCapacity, $minCapacity);
$venues = [];
while ($stmt->fetch()) {
    $venues[] = [
        'venue_id' => $venue_id,
        'venueName' => $venueName,
        'venueLocation' => $venueLocation,
        'maxCapacity' => $maxCapacity,
        'minCapacity' => $minCapacity
    ];
}
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Check Venues</title>
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

        .book-now-button {
            padding: 5px 10px;
            background-color: #4caf50;
            color: white;
            border: none;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h2>Available Venues</h2>
    <?php if (!empty($venues)): ?>
        <table>
            <thead>
                <tr>
                    <th>Venue Name</th>
                    <th>Venue Location</th>
                    <th>Max Capacity</th>
                    <th>Min Capacity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($venues as $venue): ?>
                    <tr>
                        <td><?php echo $venue['venueName']; ?></td>
                        <td><?php echo $venue['venueLocation']; ?></td>
                        <td><?php echo $venue['maxCapacity']; ?></td>
                        <td><?php echo $venue['minCapacity']; ?></td>
                        <td>
                            <a class="book-now-button" href="bookvenue.php?venue_id=<?php echo $venue['venue_id']; ?>">Book Now</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No venues found.</p>
    <?php endif; ?>
</body>
</html>
