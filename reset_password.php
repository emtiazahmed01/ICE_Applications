<?php
session_start();
// Database connection
$conn = new mysqli("sql206.infinityfree.com", "if0_40132910", "2beornot2be2001", "if0_40132910_nstu_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$token = $_GET['token'] ?? '';
if(!$token) die("Invalid request.");

if(isset($_POST['password'], $_POST['confirmpassword'])){
    $password = $_POST['password'];
    $confirmpassword = $_POST['confirmpassword'];

    if($password !== $confirmpassword){
        $error = "Passwords do not match!";
    } else {
        // Verify token
        $stmt = $conn->prepare("SELECT * FROM usersignup WHERE reset_token=? AND reset_expiry >= NOW()");
        $stmt->bind_param("s",$token);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0){
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt2 = $conn->prepare("UPDATE usersignup SET password=?, reset_token=NULL, reset_expiry=NULL WHERE reset_token=?");
            $stmt2->bind_param("ss", $hashed, $token);
            $stmt2->execute();
            $success = "✅ Password successfully reset! You can now <a href='./login.html'>login</a>.";
        } else {
            $error = "Invalid or expired token!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<link rel="icon" href="./images/favicon.ico">
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Reset Password - ICE Applications</title>
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
  <button class="login" onclick="window.location.href='./login.php'">Login</button>
  <button class="signup" onclick="window.location.href='./signup.php'">Signup</button>
</div>

    </div>
  </header>
<div class="login-box">
<h2>Reset Password</h2>
<?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
<?php if(isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
<?php if(!isset($success)) : ?>
<form action="" method="POST">
  <label for="password">New Password:</label>
  <input type="password" id="password" name="password" required>
  <label for="confirmpassword">Confirm New Password:</label>
  <input type="password" id="confirmpassword" name="confirmpassword" required>
  <button type="submit">Reset Password</button>
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
      <p>Developed By: <a href="#">Mithila Jahan Choity<br>& Emtiaz</a></p>
    </div>
  </div>
</footer>
<?php endif; ?>
</body>
</html>
