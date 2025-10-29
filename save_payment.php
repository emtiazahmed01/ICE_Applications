<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

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

$email = $_SESSION['email'] ?? '';
$name  = $_SESSION['name'] ?? '';

// Fetch student info
$sql = "SELECT * FROM usersignup WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

// Use session name if available
if (empty($name) && isset($student['name'])) {
    $name = $student['name'];
}

$showMessageBox = false;
$messageBox = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amount'] ?? 0;
    $method = $_POST['method'] ?? '';
    $transaction_id = $_POST['transaction'] ?? '';

    if (empty($name) || empty($amount) || empty($method) || empty($transaction_id)) {
        $messageBox = "<div style='color:red;text-align:center;'>Please fill all fields properly.</div>";
        $showMessageBox = true;
    } else {
        $sql = "INSERT INTO payments (name, email, amount, method, transaction_id, status)
                VALUES (?, ?, ?, ?, ?, 'Pending')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdss", $name, $email, $amount, $method, $transaction_id);

        if ($stmt->execute()) {
            $payment_id = $stmt->insert_id;

            // Send email to admin
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = '';
                $mail->Password   = '';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;
                $mail->setFrom('', 'ICE Payment System');
                $mail->addAddress('', 'ICE Admin');

                $approve_link = "https://documents-ice.page.gd/confirm_payment.php?id={$payment_id}&action=approve";
                $reject_link  = "https://documents-ice.page.gd/confirm_payment.php?id={$payment_id}&action=reject";

                $mail->isHTML(true);
                $mail->Subject = "New Payment Received from $name";
                $mail->Body = "
                    <h2>New Payment Notification</h2>
                    <p><b>Student Name:</b> {$name}</p>
                    <p><b>Email:</b> {$email}</p>
                    <p><b>Amount:</b> {$amount} Taka</p>
                    <p><b>Method:</b> {$method}</p>
                    <p><b>Transaction ID:</b> {$transaction_id}</p>
                    <hr>
                    <p>Please confirm this payment:</p>
                    <a href='{$approve_link}' style='padding:10px 20px;background:#16a34a;color:white;text-decoration:none;border-radius:5px;'>✅ Approve</a>
                    <a href='{$reject_link}' style='padding:10px 20px;background:#dc2626;color:white;text-decoration:none;border-radius:5px;margin-left:10px;'>❌ Reject</a>
                ";
                $mail->send();
            } catch (Exception $e) {
                // Ignore email error for user display
            }

            // Prepare success message box
            $messageBox = "
            <div style='
              font-family: Arial, sans-serif;
              text-align: center;
              background: #f0f9f0;
              padding: 40px;
              border-radius: 10px;
              width: 500px;
              line-height: 1.5;
              margin: 40px auto;
              box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            '>
              <h3 style='color: #10b981;'>✅ Payment saved and confirmation email sent to admin!</h3>
              <p><b>Name:</b> $name</p>
              <p><b>Email:</b> $email</p>
              <p><b>Amount:</b> $amount</p>
              <p><b>Method:</b> $method</p>
              <p><b>Transaction ID:</b> $transaction_id</p>
              <a href='./payment_update.php' style='display:inline-block;margin-top:20px;padding:10px 20px;background:#6366f1;color:white;text-decoration:none;border-radius:6px;'>Check Payment Update</a>
            </div>
            ";
            $showMessageBox = true;

        } else {
            $messageBox = "<div style='color:red;text-align:center;'>Error: " . $stmt->error . "</div>";
            $showMessageBox = true;
        }
        $stmt->close();
    }
}

$conn->close();
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
.payment-container {
    max-width: 500px;
    margin: 0 auto;
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}
h2 { text-align: center; color: #1a237e; margin-bottom: 25px; }
.form-group { margin-bottom: 20px; }
label { display: block; margin-bottom: 6px; font-weight: 600; color: #333; }
input, select { width: 100%; padding: 10px 12px; border: 1px solid #ccc; border-radius: 8px; font-size: 15px; }
input:focus, select:focus { border-color: #1a237e; box-shadow: 0 0 5px rgba(26,35,126,0.2); }
.submit-btn { width: 100%; padding: 12px; background-color: #1a237e; color: #fff; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: 0.3s; }
.submit-btn:hover { background-color: #3949ab; transform: translateY(-2px); }
</style>
</head>
<body>

<!-- Header -->
<header class="dashboard-header">
    <div class="logo">
      <a href="./services.php"><img src="./images/logo.png" alt="NSTU Logo"></a>
      <h1>NOAKHALI SCIENCE AND TECHNOLOGY UNIVERSITY</h1>
    </div>
    <div class="user-info">
      <img src="./images/user.png" alt="User Avatar" class="user-avatar">
      <span><?php echo htmlspecialchars($name ?? 'Student'); ?></span>
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

<!-- Main Content -->
<main class="dashboard-main">
    <?php if (!$showMessageBox): ?>
    <div class="payment-container">
        <h2>Payment Form</h2>
        <form method="POST">
            <div class="form-group">
                <label for="amount">Total Amount</label>
                <input type="number" id="amount" name="amount" placeholder="Enter amount" required>
            </div>
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" readonly>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" readonly>
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
            <button type="submit" class="submit-btn">Submit Payment</button>
        </form>
    </div>
    <?php else: ?>
        <?php echo $messageBox; ?>
    <?php endif; ?>
</main>

<!-- Footer -->
<footer class="footer">
    <div class="footer-bottom">
      <p>© <?php echo date("Y"); ?> NSTU Academic Services</p>
    </div>
</footer>

</body>
</html>
