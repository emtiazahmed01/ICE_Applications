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

// Fetch student info
$sql = "SELECT * FROM usersignup WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"];
    $year = $_POST["year"];
    $term = $_POST["term"];
    $level = $_POST["level"];
    $blood = $_POST["blood"];

    $update_sql = "UPDATE usersignup 
                   SET name = ?, year = ?, term = ?, level = ?, blood = ? 
                   WHERE email = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sissss", $name, $year, $term, $level, $blood, $email);

    if ($update_stmt->execute()) {
        $success = "✅ Profile updated successfully!";
        // Refresh data after update
        $stmt = $conn->prepare("SELECT * FROM usersignup WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $student = $stmt->get_result()->fetch_assoc();
    } else {
        $error = "❌ Error updating profile. Please try again.";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="icon" href="./images/favicon.ico">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Profile - NSTU Academic Services</title>
  <link rel="stylesheet" href="./dashboard.css">
  <style>
    .edit-profile-container {
      max-width: 600px;
      margin: 40px auto;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    }

    .edit-profile-container h2 {
      text-align: center;
      color: #1a237e;
      font-weight: 700;
      margin-bottom: 25px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin-bottom: 6px;
      font-weight: 600;
      color: #333;
    }

    input, select {
      width: 100%;
      padding: 10px 12px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 15px;
      outline: none;
      transition: all 0.3s ease;
    }

    input:focus, select:focus {
      border-color: #1a237e;
      box-shadow: 0 0 5px rgba(26, 35, 126, 0.2);
    }

    .submit-btn {
      width: 100%;
      padding: 12px;
      background-color: #1a237e;
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: 0.3s;
    }

    .submit-btn:hover {
      background-color: #3949ab;
      transform: translateY(-2px);
    }

    .success-msg {
      color: green;
      text-align: center;
      font-weight: 600;
      margin-bottom: 15px;
    }

    .error-msg {
      color: red;
      text-align: center;
      font-weight: 600;
      margin-bottom: 15px;
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
      <span><?php echo htmlspecialchars($student['name']); ?></span>
    </div>
  </header>

  <!-- ===== SIDEBAR ===== -->
  <aside class="sidebar">
    <ul>
      <li><a href="./services.php" >🏠 Dashboard</a></li>
      <li><a href="./applicationgenerator.php">📝 Application Generator</a></li>
      <li><a href="./payment_update.php">💳 Payment Status</a></li>
      <li><a href="./edit_profile.php"  class="active">⚙️ Edit Profile</a></li>
      <li><a href="./logout.php" class="logout-btn">🚫 Logout</a></li>
    </ul>
  </aside>

  <!-- ===== MAIN CONTENT ===== -->
  <main class="dashboard-main">
    <div class="edit-profile-container">
      <h2>Edit Your Profile</h2>

      <?php if (isset($success)) echo "<p class='success-msg'>$success</p>"; ?>
      <?php if (isset($error)) echo "<p class='error-msg'>$error</p>"; ?>

      <form method="POST">
        <div class="form-group">
          <label for="name">Full Name</label>
          <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" required>
        </div>

        <div class="form-group">
          <label for="year">Year</label>
          <input type="number" id="year" name="year" value="<?php echo htmlspecialchars($student['year']); ?>" required>
        </div>

        <div class="form-group">
          <label for="term">Term</label>
          <select name="term" id="term" required>
            <option value="Term-1" <?php if($student['term']=="Term-1") echo "selected"; ?>>Term-1</option>
            <option value="Term-2" <?php if($student['term']=="Term-2") echo "selected"; ?>>Term-2</option>
          </select>
        </div>

        <div class="form-group">
          <label for="level">Level</label>
          <select name="level" id="level" required>
            <option value="Level-1" <?php if($student['level']=="Level-1") echo "selected"; ?>>Level-1</option>
            <option value="Level-2" <?php if($student['level']=="Level-2") echo "selected"; ?>>Level-2</option>
            <option value="Level-3" <?php if($student['level']=="Level-3") echo "selected"; ?>>Level-3</option>
            <option value="Level-4" <?php if($student['level']=="Level-4") echo "selected"; ?>>Level-4</option>
          </select>
        </div>

        <div class="form-group">
          <label for="blood">Blood Group</label>
          <select name="blood" id="blood">
            <option value="">Select</option>
            <?php 
              $bloodGroups = ["A+", "A-", "B+", "B-", "O+", "O-", "AB+", "AB-"];
              foreach ($bloodGroups as $bg) {
                $selected = ($student["blood"] == $bg) ? "selected" : "";
                echo "<option value='$bg' $selected>$bg</option>";
              }
            ?>
          </select>
        </div>

        <button type="submit" class="submit-btn">Update Profile</button>
      </form>
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
