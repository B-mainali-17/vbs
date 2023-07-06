<?php
session_start();
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['code'])) {
    $verificationCode = $_GET['code'];

    // Check if the verification code exists in the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE verification_code = ?");
    $stmt->bind_param("s", $verificationCode);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Update the verification status to 1
        $stmt = $conn->prepare("UPDATE users SET verification_status = 1 WHERE verification_code = ?");
        $stmt->bind_param("s", $verificationCode);
        $stmt->execute();

        // Redirect the user to the appropriate dashboard based on their user_status
        switch ($user['user_status']) {
            case 0:
                header("Location: booker/dashboard.php");
                break;
            case 1:
                header("Location: renter/dashboard.php");
                break;
            case 3:
                header("Location: admin/dashboard.php");
                break;
            default:
                header("Location: login.php");
                break;
        }
        exit();
    } else {
        echo "Invalid verification code.";
    }
}
?>
