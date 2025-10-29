<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if(isset($_POST['email'])){
    $email = $_POST['email'];

    // Database connection
$conn = new mysqli("sql206.infinityfree.com", "if0_40132910", "2beornot2be2001", "if0_40132910_nstu_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

    // Check if email exists
    $stmt = $conn->prepare("SELECT * FROM usersignup WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        // Generate token
        $token = bin2hex(random_bytes(50));
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // Save token and expiry in DB
        $stmt2 = $conn->prepare("UPDATE usersignup SET reset_token=?, reset_expiry=? WHERE email=?");
        $stmt2->bind_param("sss", $token, $expiry, $email);
        $stmt2->execute();

        // Send email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'officeicedept@gmail.com';
            $mail->Password   = 'awhiltcnvtplzdhu'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('officeicedept@gmail.com', 'NSTU System');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = "Password Reset Request";
            $mail->Body    = "
                <p>Hello,</p>
                <p>Click the link below to reset your password (valid for 1 hour):</p>
                <p><a href='https://documents-ice.page.gd/reset_password.php?token=$token'>Reset Password</a></p>
            ";

            $mail->send();
            $msg = "✅ Password reset link sent to your email.";
        } catch (Exception $e) {
            $msg = "Mailer Error: " . $mail->ErrorInfo;
        }
    } else {
        $msg = "❌ Email not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<link rel="icon" href="./images/favicon.ico">
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Forgot Password - ICE Applications</title>
<link rel="stylesheet" href="./header.css" />
<link rel="stylesheet" href="./footer.css" />
<style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f4;
      margin: 0;
      padding: 0;
    }

    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: #fff;
      padding: 15px 30px;
      border-bottom: 3px solid #001f3f;
    }

    .logo {
      display: flex;
      align-items: center;
    }

    .logo img {
      height: 60px;
      margin-right: 10px;
    }

    .logo h1 {
      font-size: 18px;
      color: #001f3f;
    }

    .contact {
      text-align: right;
      font-size: 14px;
    }

    .auth-buttons button {
      margin-left: 10px;
      padding: 5px 10px;
      cursor: pointer;
      border: none;
      border-radius: 4px;
    }

    .auth-buttons .login {
      background-color: #9120dd;
      color: white;
    }

    .auth-buttons .signup {
      background-color: #49149f;
      color: white;
    }

    .login-box {
      width: 400px;
      margin: 50px auto;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .login-box h2 {
      text-align: center;
      margin-bottom: 30px;
    }

    .login-box input[type="email"],
    .login-box input[type="password"] {
      width: 100%;
      padding: 10px;
      margin: 10px 0 20px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    .login-box button {
      width: 100%;
      padding: 10px;
      background-color: #001f3f;
      color: white;
      border: none;
      border-radius: 5px;
      font-size: 16px;
    }

    .login-box .footer {
      text-align: center;
      margin-top: 15px;
    }

    .login-box .footer a {
      color: #2196f3;
      text-decoration: none;
    }

    .forgot {
      text-align: right;
      margin-bottom: 15px;
    }

    .forgot a {
      color: #2196f3;
      font-size: 14px;
      text-decoration: none;
    }
  </style>
</head>
<body>

<header>
    <div class="logo">
      <a href="./index.html"><img src="./images/logo.png" alt="NSTU Logo" /></a>
      <h1>NOAKHALI SCIENCE AND TECHNOLOGY UNIVERSITY</h1>
    </div>
    <div class="contact">
      <div>📞 02334496522 | 88027791052</div>
      <div>✉️ registrar@office.nstu.edu.bd</div>
      <div class="auth-buttons">
  <button class="login" onclick="window.location.href='./login.html'">Login</button>
  <button class="signup" onclick="window.location.href='./signup.html'">Signup</button>
</div>

    </div>
  </header>
<div class="login-box">
<h2>Forgot Password</h2>
<?php if(isset($msg)) echo "<p>$msg</p>"; ?>
<form action="" method="POST">
  <label for="email">Enter your registered email:</label>
  <input type="email" id="email" name="email" required>
  <button type="submit">Send Reset Link</button>
</form>
</div>

  <!-- Start of Footer Section -->
<footer class="footer">
  <div class="container">
    <div class="footer-top">
      <div class="footer-col">
        <h4>SOCIAL LINK</h4>
        <ul class="social-links">
          <li><a href="#"><i class="fa fa-facebook"></i> FACEBOOK</a></li>
          <li><a href="#"><i class="fa fa-twitter"></i> TWITTER</a></li>
          <li><a href="#"><i class="fa fa-instagram"></i> INSTAGRAM</a></li>
          <li><a href="#"><i class="fa fa-linkedin"></i> LINKEDIN</a></li>
          <li><a href="#"><i class="fa fa-google-plus"></i> GOOGLE +</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>USEFUL LINKS</h4>
        <ul>
          <li><a href="#">UGC</a></li>
          <li><a href="#">Ministry of Education</a></li>
          <li><a href="#">Office of Chancellor</a></li>
          <li><a href="#">Office of Prime Minister</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>CENTER</h4>
        <ul>
          <li><a href="#">Research Cell</a></li>
          <li><a href="#">Cyber Center</a></li>
          <li><a href="#">IQAC</a></li>
          <li><a href="#">ICT Cell</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>FACILITIES</h4>
        <ul>
          <li><a href="#">Hall of Residence</a></li>
          <li><a href="#">Medical Center</a></li>
          <li><a href="#">Central Library</a></li>
          <li><a href="#">Auditorium</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>HELP & CONTACT</h4>
        <ul>
          <li><i class="fa fa-map-marker"></i> Postal Code: 3814</li>
          <li><i class="fa fa-phone"></i> Phone: +88-0321-72720</li>
          <li><i class="fa fa-envelope"></i> Email: info@nstu.edu.bd</li>
          <li><i class="fa fa-globe"></i><a href="https://nstudiary.nstu.edu.bd/" target="_blank">NSTU Diary Web Version</a></li>
        </ul>
      </div>
    </div>

    <div class="footer-bottom">
      <p>© 2025 NSTU. All Rights Reserved.</p>
      <p>Developed By: <a href="#" target="_blank">Mithila Jahan Choity</a></p>
    </div>
  </div>
</footer>
</body>
</html>
