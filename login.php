<?php
session_start();
require 'config.php';

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    // Redirect the user to the appropriate dashboard based on their user_status
    switch ($_SESSION['user_status']) {
        case 0:
            header("Location: booker/dashboard.php");
            exit();
        case 1:
            header("Location: renter/dashboard.php");
            exit();
        case 3:
            header("Location: admin/dashboard.php");
            exit();
        default:
            // If the user_status is not recognized, redirect to the login page
            header("Location: login.php");
            exit();
    }
}

// Define variables to hold form input values
$username = $password = '';

// Define variable to hold login error message
$errorMessage = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hash the password for comparison
    $hashedPassword = md5($password);

    // Prepare and execute the SQL query to retrieve the user data
    $stmt = $conn->prepare("SELECT id, user_status, verification_status FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $hashedPassword);
    $stmt->execute();
    $stmt->store_result();

    // Check if the user exists and the password is correct
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $user_status, $verification_status);
        $stmt->fetch();

        // Check the verification status
        if ($verification_status == 1) {
            // Set the user_id and user_status in the session
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_status'] = $user_status;

            // Redirect the user to the appropriate dashboard based on their user_status
            switch ($user_status) {
                case 0:
                    header("Location: booker/dashboard.php");
                    exit();
                case 1:
                    header("Location: renter/dashboard.php");
                    exit();
                case 3:
                    header("Location: admin/dashboard.php");
                    exit();
                default:
                    // If the user_status is not recognized, redirect to the login page
                    header("Location: login.php");
                    exit();
            }
        } else {
            $errorMessage = "Error: Your email is not verified yet. Please check your inbox and verify your email.";
        }
    } else {
        $errorMessage = "Error: Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Form</title>
</head>
<body>
    <h2>Login Form</h2>
    <?php if (!empty($errorMessage)): ?>
        <p><?php echo $errorMessage; ?></p>
    <?php endif; ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <label for="username">Username:</label>
        <input type="text" name="username" required value="<?php echo $username; ?>"><br><br>
        
        <label for="password">Password:</label>
        <input type="password" name="password" required><br><br>
        
        <input type="submit" value="Login">
    </form>
    <a href="register.php">Register</a>
</body>
</html>
