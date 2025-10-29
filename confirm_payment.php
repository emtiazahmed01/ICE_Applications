<?php
// confirm_payment.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // PHPMailer autoload

$servername = "sql206.infinityfree.com";
$username = "if0_40132910";
$password = "2beornot2be2001";
$dbname = "if0_40132910_nstu_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'] ?? 0;
$action = $_GET['action'] ?? '';

if (!$id || !in_array($action, ['approve', 'reject'])) {
    die("Invalid request.");
}

$status = ($action === 'approve') ? 'Approved' : 'Rejected';

// First, fetch student info
$sql = "SELECT name, email, amount, method, transaction_id FROM payments WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Payment record not found.");
}

$payment = $result->fetch_assoc();
$student_name = $payment['name'];
$student_email = $payment['email'];
$amount = $payment['amount'];
$method = $payment['method'];
$transaction_id = $payment['transaction_id'];

$stmt->close();

// Update the payment status
$sql = "UPDATE payments SET status=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $id);

if ($stmt->execute()) {

    // ✅ Send email to student
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'officeicedept@gmail.com'; // Admin Gmail
        $mail->Password   = 'awhiltcnvtplzdhu';        // App password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('officeicedept@gmail.com', 'NSTU Payment System');
        $mail->addAddress($student_email, $student_name);

        $mail->isHTML(true);
        $mail->Subject = "Payment {$status} Notification";
        $mail->Body = "
            <h2>Payment Update</h2>
            <p>Dear <b>{$student_name}</b>,</p>
            <p>Your payment has been <b>{$status}</b> by the admin.</p>
            <p><b>Amount:</b> {$amount} Taka</p>
            <p><b>Payment Method:</b> {$method}</p>
            <p><b>Transaction ID:</b> {$transaction_id}</p>
            <hr>
            <p>Thank you for using NSTU Payment System.</p>
        ";

        $mail->send();
        $msg_color = ($status === 'Approved') ? '#16a34a' : '#dc2626';
        $msg = "Payment {$status} successfully! Student has been notified.";
    } catch (Exception $e) {
        $msg_color = ($status === 'Approved') ? '#16a34a' : '#dc2626';
        $msg = "Payment {$status}, but student notification failed: {$mail->ErrorInfo}";
    }

    echo "
    <div style='
      font-family: Arial, sans-serif;
      text-align: center;
      background: #fff;
      padding: 50px;
      border-radius: 10px;
      width: 400px;
      margin: 100px auto;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    '>
      <h2 style='color: {$msg_color};'>$msg</h2>
      <p>Payment ID: $id</p>
      <p>Student: $student_name</p>
      <p>Email: $student_email</p>
      <p>Amount: $amount</p>
      <p>Method: $method</p>
      <p>Transaction ID: $transaction_id</p>
    </div>
    ";
} else {
    echo "Error updating payment status: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
