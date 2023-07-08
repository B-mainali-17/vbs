<?php
session_start();
require '../config.php';

// Check if the user is logged in as an admin (user_status = 3)
if (!isset($_SESSION['user_id']) || $_SESSION['user_status'] != 3) {
    // Redirect to the login page if not logged in as an admin
    header("Location: ../login.php");
    exit();
}

// Retrieve all renters
$stmt = $conn->prepare("SELECT id, fullname, email, username FROM users WHERE user_status = 1");
$stmt->execute();
$result = $stmt->get_result();

// Close the prepared statement
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Renters</title>
    <style>
        /* CSS code for table styling */
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
    <h2>Admin Renters</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Username</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['fullname']; ?></td>
            <td><?php echo $row['email']; ?></td>
            <td><?php echo $row['username']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <a href="../logout.php">Logout</a>
</body>
</html>
