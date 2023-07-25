<?php
session_start();
require 'config.php';

// Retrieve the list of venues from the database where verification_status is 1
$stmt = $conn->prepare("SELECT * FROM venues WHERE verification_status = 1");
$stmt->execute();
$result = $stmt->get_result();
$venues = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Redirect to checkvenue.php if the venue ID is set
if (isset($_GET['id'])) {
  $venueID = $_GET['id'];
  header("Location: booker/checkvenues.php?id=$venueID");
  exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Venue Booking System</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .venue-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
    }

    .venue {
      flex-basis: 23%;
      border: 1px solid #ccc;
      padding: 10px;
      text-align: center;
      margin-bottom: 20px;
    }

    .venue img {
      width: 100%;
      height: auto;
    }
    /* body {
    background-image: url("images/venue.jpg");
    background-repeat: no-repeat;
    background-size: cover;
  } */
  </style>
</head>
<body>
  <header>
    <div class="header">
      <div class="logo">
        <a href="">

      <img src="images/" alt=""></a>
      </div>
      <div class="login-signup">
        <a href="login.php"><button>Login</button></a>
        <a href="register.php"><button>Sign Up</button></a>
      </div>
    </div>
  </header>

  <div class="content">
    <!-- Your venue booking system content goes here -->
  </div>
  
  <div class="sidebar">
    <div class="search-button">
      <input type="text" placeholder="Venue Name">
      <input type="text" placeholder="Location">
      <button>Search</button>
    </div>
    <ul>
      <li>Home</li>
      <li>Venues</li>
      <li>Bookings</li>
      <li>About Us</li>
    </ul>
  </div>

  <h2>Venue Listing</h2>
  <div class="venue-container">
    <?php foreach ($venues as $venue): ?>
      <div class="venue">
        <h3><?php echo $venue['venueName']; ?></h3>
        <p>Location: <?php echo $venue['venueLocation']; ?></p>
        <p>Capacity: <?php echo $venue['minCapacity']; ?> - <?php echo $venue['maxCapacity']; ?></p>
        <?php if (!empty($venue['venueImage'])): ?>
          <img src="<?php echo $venue['venueImage']; ?>" alt="Venue Image" width="300">
        <?php endif; ?>

        <br>
        <a href="booker/checkvenues.php?id=<?php echo $venue['id']; ?>">View Details</a>
      </div>
      <br>
    <?php endforeach; ?>
  </div>

  <div class="footer">
    <p>Email: info@venuehub.com</p>
    <p>Location: 123 Main Street, City, Country</p>
    <p>Contact us: +1 123-456-7890</p>
  </div>
</body>
</html>
