<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.html");
    exit();
}

// Database connection
$conn = new mysqli("", "", "", "");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user info
$email = $_SESSION['email'];
$sql = "SELECT name, email FROM usersignup WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="./images/favicon.ico">
  <title>Dashboard - NSTU Academic Services</title>
  <link rel="stylesheet" href="./dashboard.css">
</head>

<body>

  <!-- ===== HEADER ===== -->
  <header class="dashboard-header">
    <div class="logo">
      <a href="./services.php"><img src="./images/logo.png" alt="NSTU Logo"></a>
      <h1>NOAKHALI SCIENCE AND TECHNOLOGY UNIVERSITY</h1>
    </div>

    <div class="user-info">
      <img src="./images/user.png" alt="User Avatar" class="user-avatar">
      <span><?php echo htmlspecialchars($user['name']); ?></span>
    </div>
  </header>

  <!-- ===== SIDEBAR ===== -->
  <aside class="sidebar">
    <ul>
      <li><a href="./services.php"  class="active">🏠 Dashboard</a></li>
      <li><a href="./applicationgenerator.php">📝 Application Generator</a></li>
      <li><a href="./payment_update.php">💳 Payment Status</a></li>
      <li><a href="./edit_profile.php">⚙️ Edit Profile</a></li>
      <li><a href="./logout.php" class="logout-btn">🚫 Logout</a></li>
    </ul>
  </aside>

  <!-- ===== MAIN CONTENT ===== -->
  <main class="dashboard-main">
    <div class="service-container">
      <h2>Online Academic Services</h2>

      <button class="service-btn" onclick="window.location.href='applicationgenerator.php?type=মার্কশীট'">
        Apply for Certificate / Marksheet
      </button>

      <button class="service-btn" onclick="window.location.href='applicationgenerator.php?type=প্রত্যয়ন পত্র'">
        Apply for Transcript
      </button>

    </div>
  </main>

  <!-- ===== FOOTER ===== -->
  <footer class="footer">
    <div class="footer-bottom">
      <p>© <?php echo date("Y"); ?> NSTU Academic Services</p>
    </div>
  </footer>

</body>
</html>
