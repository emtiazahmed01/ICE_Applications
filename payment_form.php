<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['email']) || !isset($_SESSION['allow_payment']) || $_SESSION['allow_payment'] !== true) {
    header("Location: ./services.php");
    exit;
}

// Database connection
$conn = new mysqli("", "", "", "");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_SESSION['email'];

// Fetch user info
$sql = "SELECT name FROM usersignup WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

$user = $result->fetch_assoc() ?? ['name' => ''];

$stmt->close();
$conn->close();

// Retrieve application type & amount from session
$type = $_SESSION['payment_type'] ?? '';
$amount = $_SESSION['payment_amount'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="./images/favicon.ico">
  <title>Payment Form - NSTU Academic Services</title>
  <link rel="stylesheet" href="./dashboard.css">
  <style>
    body {
      font-family: "Poppins", Arial, sans-serif;
      margin: 0;
      background: #f3f4f6;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    .dashboard-main {
      margin-left: 230px;
      padding: 40px 30px;
      flex: 1;
    }

    .form-container {
      max-width: 500px;
      margin: 0 auto;
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      padding: 30px;
    }

    h2, h3 { text-align: center; color: #1a237e; }

    .form-group { margin-bottom: 20px; }
    label { display: block; margin-bottom: 6px; font-weight: 600; color: #333; }
    input, select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 15px; }
    input:focus, select:focus { border-color: #1a237e; outline: none; box-shadow: 0 0 5px rgba(26, 35, 126, 0.2); }

    .btn {
      width: 100%;
      padding: 12px;
      background: #1a237e;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: 0.3s;
    }
    .btn:hover { background: #3949ab; }

    .logout { margin-top: 15px; text-align: center; }
    .logout a { color: #ef4444; text-decoration: none; font-weight: bold; }
  </style>
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
      <li><a href="./services.php">🏠 Dashboard</a></li>
      <li><a href="./applicationgenerator.php"  class="active">📝 Application Generator</a></li>
      <li><a href="./payment_update.php">💳 Payment Status</a></li>
      <li><a href="./edit_profile.php">⚙️ Edit Profile</a></li>
      <li><a href="./logout.php" class="logout-btn">🚫 Logout</a></li>
    </ul>
  </aside>

  <!-- ===== MAIN CONTENT ===== -->
  <main class="dashboard-main">
    <div class="form-container">
      <h2>Payment Form</h2>
      <h3>Application Type: <?= htmlspecialchars($type); ?></h3>

      <form action="./save_payment.php" method="POST">
        <div class="form-group">
          <label for="amount">Total Amount</label>
          <input type="number" id="amount" name="amount" value="<?= htmlspecialchars($amount); ?>" readonly>
        </div>

        <div class="form-group">
          <label for="name">Name</label>
          <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']); ?>" readonly>
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" value="<?= htmlspecialchars($email); ?>" readonly>
        </div>

        <div class="form-group">
          <label for="method">Payment Method</label>
          <select id="method" name="method" required>
            <option value="">-- Select Method --</option>
            <option value="Bkash">Bkash</option>
            <option value="Nagad">Nagad</option>
            <option value="Rocket">Rocket</option>
          </select>
        </div>

        <div class="form-group">
          <label for="transaction">Transaction ID</label>
          <input type="text" id="transaction" name="transaction" placeholder="Enter Transaction ID" required>
        </div>

        <button type="submit" class="btn">Submit Payment</button>
      </form>

      <div class="logout">
        <a href="./applicationgenerator.php">Cancel Payment</a>
      </div>
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
