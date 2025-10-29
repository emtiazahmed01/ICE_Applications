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

$email = $_SESSION['email'];

// Fetch user info
$user_sql = "SELECT name FROM usersignup WHERE email=?";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param("s", $email);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();

// Fetch payment data
$sql = "SELECT * FROM payments WHERE email=? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="./images/favicon.ico">
  <title>Payment Status - NSTU Academic Services</title>
  <link rel="stylesheet" href="./dashboard.css">
  <style>
    .payment-container {
      max-width: 900px;
      margin: 0 auto;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.1);
      overflow-x: auto;
    }

    .payment-container h2 {
      text-align: center;
      color: #1a237e;
      font-weight: 700;
      margin-bottom: 25px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      text-align: center;
    }

    th, td {
      padding: 12px 15px;
      border-bottom: 1px solid #ddd;
      font-size: 15px;
    }

    th {
      background: #1a237e;
      color: #fff;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    tr:hover {
      background-color: #f5f5f5;
    }

    .status {
      padding: 5px 10px;
      border-radius: 20px;
      font-weight: 600;
      text-transform: capitalize;
    }

    .status.pending {
      background: #fff3cd;
      color: #856404;
    }

    .status.completed {
      background: #d4edda;
      color: #155724;
    }

    .status.failed {
      background: #f8d7da;
      color: #721c24;
    }

    .no-record {
      text-align: center;
      color: #555;
      font-weight: 600;
      margin-top: 20px;
    }
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
      <li><a href="./services.php" >🏠 Dashboard</a></li>
      <li><a href="./applicationgenerator.php">📝 Application Generator</a></li>
      <li><a href="./payment_update.php"  class="active">💳 Payment Status</a></li>
      <li><a href="./edit_profile.php">⚙️ Edit Profile</a></li>
      <li><a href="./logout.php" class="logout-btn">🚫 Logout</a></li>
    </ul>
  </aside>

  <!-- ===== MAIN CONTENT ===== -->
  <main class="dashboard-main">
    <div class="payment-container">
      <h2>Your Payment History</h2>

      <?php if ($result->num_rows > 0): ?>
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Transaction ID</th>
            <th>Method</th>
            <th>Amount (৳)</th>
            <th>Payment Date</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $count = 1;
          while ($row = $result->fetch_assoc()): 
            $statusClass = strtolower($row['status']);
          ?>
            <tr>
              <td><?php echo $count++; ?></td>
              <td><?php echo htmlspecialchars($row['transaction_id']); ?></td>
              <td><?php echo htmlspecialchars($row['method']); ?></td>
              <td><?php echo htmlspecialchars(number_format($row['amount'], 2)); ?></td>
              <td><?php echo date("d M, Y", strtotime($row['created_at'])); ?></td>
              <td><span class="status <?php echo $statusClass; ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
      <?php else: ?>
        <p class="no-record">No payment records found.</p>
      <?php endif; ?>
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
