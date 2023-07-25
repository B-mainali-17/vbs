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
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Venue not found.";
    exit();
}

$row = $result->fetch_assoc();
$venue_id = $row['id'];
$venueName = $row['venueName'];
$venueLocation = $row['venueLocation'];
$maxCapacity = $row['maxCapacity'];
$minCapacity = $row['minCapacity'];

$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve form input values
    $booking_date = $_POST['booking_date'];
    $no_of_people = $_POST['no_of_people'];

    // Check if the booking date is not a previous date
    if (strtotime($booking_date) < strtotime(date("Y-m-d"))) {
        echo "Cannot book a previous date.";
        exit();
    }

    // Check if the number of people exceeds the maximum capacity
    if ($no_of_people > $maxCapacity) {
        echo "Please enter a number of people less than or equal to the maximum capacity.";
        exit();
    }

    // Check if the number of people falls below the minimum capacity
    if ($no_of_people < $minCapacity) {
        echo "Please enter a number of people greater than or equal to the minimum capacity.";
        exit();
    }

    // Check if the venue is already booked on the selected date
    $stmt = $conn->prepare("SELECT id FROM venue_orders WHERE venue_id = ? AND booking_date = ?");
    $stmt->bind_param("is", $venue_id, $booking_date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "Venue is already booked on the selected date.";
        exit();
    }

    $stmt->close();

    // Retrieve the user ID from the session
    $user_id = $_SESSION['user_id'];

    
// Check if the booker has previously booked the venue
$stmt = $conn->prepare("SELECT id, booking_date, no_of_people FROM venue_orders WHERE venue_id = ? AND user_id = ?");
$stmt->bind_param("ii", $venue_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$booking_info = $result->fetch_assoc();
$stmt->close();

if ($booking_info) {
    $booking_id = $booking_info['id'];

    // Update the booking date and number of people in the venue_orders table
    $stmt = $conn->prepare("UPDATE venue_orders SET booking_date = ?, no_of_people = ? WHERE id = ?");
    $stmt->bind_param("ssi", $booking_date, $no_of_people, $booking_id);
    $stmt->execute();
    $stmt->close();

    echo "Booking updated.";
} else {
    // Insert a new booking into the venue_orders table with verification_status set to 0
    $verification_status = 0; // Set the verification status to 0
    $stmt = $conn->prepare("INSERT INTO venue_orders (venue_id, user_id, booking_date, no_of_people, verification_status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisii", $venue_id, $user_id, $booking_date, $no_of_people, $verification_status);
    $stmt->execute();
    $stmt->close();

    echo "Booking created.";
}

$conn->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Book Venue</title>
</head>
<body>
    <h2>Book Venue - <?php echo htmlspecialchars($venueName); ?></h2>
    <p>Venue Location: <?php echo htmlspecialchars($venueLocation); ?></p>
    <p>Capacity: <?php echo $minCapacity; ?> - <?php echo $maxCapacity; ?></p>

    <h3>Booking Form</h3>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?venue_id=<?php echo $venue_id; ?>" method="POST">
        <label for="booking_date">Booking Date:</label>
        <input type="date" name="booking_date" required><br><br>

        <label for="no_of_people">Number of People:</label>
        <input type="number" name="no_of_people" required><br><br>

        <!-- Additional form fields as needed -->

        <input type="submit" value="Book Now">
    </form>
</body>
</html>
