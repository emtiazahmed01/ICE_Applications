<?php
session_start();
header('Content-Type: application/json');

$servername = "";
$username = "";
$password = "";
$dbname = "";

// Connect to database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Assuming user is logged in and email is stored in session
if(!isset($_SESSION['email'])){
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$email = $_SESSION['email'];

$sql = "SELECT * FROM usersignup WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
    $user = $result->fetch_assoc();
    // Remove password from output
    unset($user['password']);
    echo json_encode($user);
} else {
    echo json_encode(["error" => "User not found"]);
}

$stmt->close();
$conn->close();
?>
