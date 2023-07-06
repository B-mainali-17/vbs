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

    // Prepare and execute the SQL query to retrieve the user data from the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Store user information in session variables
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['user_status'] = $row['user_status'];

            // Redirect the user based on their user_status
            switch ($row['user_status']) {
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
                    $errorMessage = "Error: Invalid user status";
                    break;
            }
        } else {
            $errorMessage = "Error: Invalid password";
        }
    } else {
        $errorMessage = "Error: Invalid username";
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
    <?php if (!empty($errorMessage)) : ?>
        <p><?php echo $errorMessage; ?></p>
    <?php endif; ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <label for="username">Username:</label>
        <input type="text" name="username" required value="<?php echo $username; ?>"><br><br>
        
        <label for="password">Password:</label>
        <input type="password" name="password" required><br><br>
        
        <input type="submit" value="Login">
    </form>
    <a href="register.php">Register Now</a>
</body>
</html>
