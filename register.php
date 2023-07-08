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
$fullname = $email = $username = $password = $confirmpassword = '';

// Define variable to hold registration success message
$successMessage = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmpassword = $_POST['confirmpassword'];

    if ($password !== $confirmpassword) {
        $errorMessage = "Error: Passwords do not match";
    } else {
        // Hash the password for security
        $hashedPassword = md5($password);

        // Check if the user is a Booker or Venue Renter
        $userStatus = ($_POST['usertype'] == 'renter') ? 1 : 0;

        // Function to generate a unique verification code
        function generateVerificationCode() {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $code = '';
            $length = 10;

            for ($i = 0; $i < $length; $i++) {
                $index = rand(0, strlen($characters) - 1);
                $code .= $characters[$index];
            }

            return $code;
        }

        // Generate a verification code
        $verificationCode = generateVerificationCode();
        // Send the verification email
        $to = $_POST['email']; // Specify the recipient's email address
        $subject = "Email Verification";
        $message = "Please click the following link to verify your email address:\n\n";
        $message .= "http://localhost/vbs/verify.php?code=" . urlencode($verificationCode);
        $headers = "From: your_email@example.com";

        // Uncomment the line below to send the email
        mail($to, $subject, $message, $headers);

        // Prepare and execute the SQL query to insert the user data into the database
        $stmt = $conn->prepare("INSERT INTO users (fullname, email, username, password, user_status, verification_code, verification_status) VALUES (?, ?, ?, ?, ?, ?, 0)");
        $stmt->bind_param("ssssss", $fullname, $email, $username, $hashedPassword, $userStatus, $verificationCode);
        $stmt->execute();

        // Redirect to email verification page
        header("Location: email_verification.html");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registration Form</title>
</head>
<body>
    <h2>Registration Form</h2>
    <?php if (!empty($errorMessage)): ?>
        <p><?php echo $errorMessage; ?></p>
    <?php endif; ?>
    <?php if (!empty($successMessage)): ?>
        <p><?php echo $successMessage; ?></p>
    <?php endif; ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <label for="fullname">Full Name:</label>
        <input type="text" name="fullname" required value="<?php echo $fullname; ?>"><br><br>
        
        <label for="email">Email:</label>
        <input type="email" name="email" required value="<?php echo $email; ?>"><br><br>
        
        <label for="username">Username:</label>
        <input type="text" name="username" required value="<?php echo $username; ?>"><br><br>
        
        <label for="password">Password:</label>
        <input type="password" name="password" required><br><br>
        
        <label for="confirmpassword">Confirm Password:</label>
        <input type="password" name="confirmpassword" required><br><br>
        
        <label for="usertype">User Type:</label>
        <select name="usertype">
            <option value="booker">Booker</option>
            <option value="renter">Venue Renter</option>
        </select><br><br>
        
        <input type="submit" value="Register">
    </form>
    <a href="login.php">Login</a>
</body>
</html>
