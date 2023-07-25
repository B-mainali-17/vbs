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
    <style>
        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(50px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f2f2f2;
            font-family: Arial, sans-serif;
        }

        .container {
            width: 400px;
            background-color: #fff;
            border-radius: 10px;
            padding: 40px;
            animation: fadeIn 0.5s ease-in-out;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="password"] {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        input[type="submit"] {
            padding: 10px;
            background-color: #4caf50;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .errorMessage {
            color: red;
            margin-bottom: 10px;
        }

        .registerLink {
            text-align: center;
        }

        .registerLink a {
            color: #4caf50;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login Form</h2>
        <?php $username = isset($username) ? $username : ''; ?>
        <?php if (!empty($errorMessage)): ?>
            <p class="errorMessage"><?php echo $errorMessage; ?></p>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" required value="<?php echo $username; ?>">
            
            <label for="password">Password:</label>
            <input type="password" name="password" required>
            
            <input type="submit" value="Login">
        </form>
        <div class="registerLink">
            <a href="register.php">Register</a>
        </div>
    </div>
</body>
</html>
