<?php
require '../config.php';

// Function to get the list of booked venues with user's name and total number of people
function getBookedVenues()
{
    global $conn;
    $query = "SELECT vo.id, v.venueName, u.fullname, vo.no_of_people, vo.verification_status
              FROM venue_orders vo
              INNER JOIN venues v ON vo.venue_id = v.id
              INNER JOIN users u ON vo.user_id = u.id";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        return array();
    }
}

// Function to update verification status of a venue
function updateVerificationStatus($orderId, $status)
{
    global $conn;
    $status = (int)$status;
    $query = "UPDATE venue_orders SET verification_status = $status WHERE id = $orderId";
    $conn->query($query);
}

// Check if the form is submitted for verification status update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["order_id"]) && isset($_POST["verification_status"])) {
        $orderId = $_POST["order_id"];
        $verificationStatus = $_POST["verification_status"];
        updateVerificationStatus($orderId, $verificationStatus);
    }
}

$bookedVenues = getBookedVenues();
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

        th,
        td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        select {
            padding: 5px;
        }

        input[type="submit"] {
            padding: 5px 10px;
        }
    </style>
</head>

<body>
    <h1>List of Booked Venues</h1>
    <table>
        <tr>
            <th>Venue Name</th>
            <th>User's Name</th>
            <th>Total No. of People</th>
            <th>Action</th>
        </tr>
        <?php foreach ($bookedVenues as $booking) : ?>
            <tr>
                <td><?php echo $booking['venueName']; ?></td>
                <td><?php echo $booking['fullname']; ?></td>
                <td><?php echo $booking['no_of_people']; ?></td>
                <td>
                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <input type="hidden" name="order_id" value="<?php echo $booking['id']; ?>">
                        <select name="verification_status">
                            <option value="0" <?php if (isset($_POST["verification_status"]) && $_POST["verification_status"] == 0) echo 'selected'; ?>>Pending</option>
                            <option value="1" <?php if (isset($_POST["verification_status"]) && $_POST["verification_status"] == 1) echo 'selected'; ?>>Approve</option>
                        </select>
                        <input type="submit" value="Submit">
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>
