<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

$servername = "";
$username = "";
$password = "";
$dbname = "";

// Connect to database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Collect POST data
    $name = $_POST['name'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $blood = $_POST['blood'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $faculty = $_POST['faculty'] ?? '';
    $department = $_POST['department'] ?? '';
    $level = $_POST['level'] ?? '';
    $year = $_POST['year'] ?? '';
    $term = $_POST['term'] ?? '';
    $studentid = $_POST['studentid'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Simple validation
    if (!$name || !$email || !$password) {
        die("Please fill all required fields!");
    }

    // Hash password
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    // Insert into UserSignup table
    $sql = "INSERT INTO usersignup (name,dob,blood,gender,faculty,department,level,year,term,studentid,email,password)
            VALUES ('$name','$dob','$blood','$gender','$faculty','$department','$level','$year','$term','$studentid','$email','$password_hashed')";

    if ($conn->query($sql) === TRUE) {
        echo "✅ Signup successful! Redirecting to login page...";
        header("refresh:2;url=./login.html");
        exit;
    } else {
        echo "❌ Registration Error: " . $conn->error;
        header("refresh:1;url=./signup.html");
        exit;
    }
}

$conn->close();
?>
