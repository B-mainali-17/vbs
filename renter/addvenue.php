<?php
session_start();
require '../config.php';

// Check if the user is logged in as a renter
if (!isset($_SESSION['user_id']) || $_SESSION['user_status'] != 1) {
    header("Location: login.php");
    exit();
}

// Define variables to hold form input values
$venueName = $venueLocation = $maxCapacity = $minCapacity = $venueImage = '';

// Define variable to hold error messages
$errorMessage = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form input values
    $venueName = $_POST['venueName'];
    $venueLocation = $_POST['venueLocation'];
    $maxCapacity = $_POST['maxCapacity'];
    $minCapacity = $_POST['minCapacity'];

    // Validate form inputs (perform additional validation as per your requirements)
    if (empty($venueName) || empty($venueLocation) || empty($maxCapacity) || empty($minCapacity)) {
        $errorMessage = "Please fill in all the required fields.";
    } elseif (!is_numeric($maxCapacity) || !is_numeric($minCapacity)) {
        $errorMessage = "Capacity values should be numeric.";
    } else {
        // Upload venue image file
        if (isset($_FILES['venueImage'])) {
            $file = $_FILES['venueImage'];
            $fileName = $file['name'];
            $fileTmpName = $file['tmp_name'];
            $fileSize = $file['size'];
            $fileError = $file['error'];

            // Check if the file was uploaded without any error
            if ($fileError === 0) {
                // Specify the directory to which the file will be uploaded
                $uploadDirectory = 'uploads/';
                $uploadedFilePath = $uploadDirectory . $fileName;

                // Move the uploaded file to the specified directory
                move_uploaded_file($fileTmpName, $uploadedFilePath);
                $venueImage = $uploadedFilePath;
            } else {
                $errorMessage = "Error uploading the image file.";
            }
        }

        if (empty($errorMessage)) {
            // Insert the venue details into the database
            $stmt = $conn->prepare("INSERT INTO venues (venueName, venueLocation, maxCapacity, minCapacity, venueImage, user_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiiii", $venueName, $venueLocation, $maxCapacity, $minCapacity, $venueImage, $_SESSION['user_id']);
            $stmt->execute();
            $stmt->close();

            // Redirect the user to a success page or any desired page after adding the venue
           echo "venue added";
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Venue</title>
</head>
<body>
    <h2>Add Venue</h2>
    <?php if (!empty($errorMessage)): ?>
        <p><?php echo $errorMessage; ?></p>
    <?php endif; ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
        <label for="venueName">Venue Name:</label>
        <input type="text" name="venueName" required value="<?php echo $venueName; ?>"><br><br>
        
        <label for="venueLocation">Venue Location:</label>
        <input type="text" name="venueLocation" required value="<?php echo $venueLocation; ?>"><br><br>
        
        <label for="maxCapacity">Max Capacity:</label>
        <input type="number" name="maxCapacity" required value="<?php echo $maxCapacity; ?>"><br><br>
        
        <label for="minCapacity">Min Capacity:</label>
        <input type="number" name="minCapacity" required value="<?php echo $minCapacity; ?>"><br><br>
        
        <label for="venueImage">Venue Image:</label>
        <input type="file" name="venueImage" accept="image/*"><br><br>
        
        <input type="submit" value="Add Venue">
    </form>
</body>
</html>
