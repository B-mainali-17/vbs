<?php
session_start();
require '../config.php';

// Check if the user is logged in as a booker
if (!isset($_SESSION['user_id']) || $_SESSION['user_status'] != 0) {
    header("Location: ../login.php");
    exit();
}

// Check if the venue ID is provided in the URL
if (!isset($_GET['venue_id'])) {
    header("Location: checkvenues.php");
    exit();
}

$venue_id = $_GET['venue_id'];

// Retrieve the venue details from the database
$stmt = $conn->prepare("SELECT id, venueName, venueLocation, maxCapacity, minCapacity FROM venues WHERE id = ?");
$stmt->bind_param("i", $venue_id);
$stmt->execute();
$stmt->bind_result($venue_id, $venueName, $venueLocation, $maxCapacity, $minCapacity);
$stmt->fetch();
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Perform booking logic here
    // Retrieve form input values and process the booking request
    // Redirect to a success page or perform any other necessary actions
    // Example code: 
    // $booking_date = $_POST['booking_date'];
    // $user_id = $_SESSION['user_id'];
    // ...
    // Insert the booking into the venue_orders table or perform the necessary operations
    // ...

    // Redirect the user to a success page or any desired page after booking
  echo "Venue Booked";
      exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Venue</title>
</head>
<body>
    <h2>Book Venue - <?php echo $venueName; ?></h2>
    <p>Venue Location: <?php echo $venueLocation; ?></p>
    <p>Capacity: <?php echo $minCapacity; ?> - <?php echo $maxCapacity; ?></p>

    <h3>Booking Form</h3>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?venue_id=<?php echo $venue_id; ?>" method="POST">
        <label for="booking_date">Booking Date:</label>
        <input type="date" name="booking_date" required><br><br>

        <!-- Additional form fields as needed -->

        <input type="submit" value="Book">
    </form>
</body>
</html>
