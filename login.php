<?php
session_start();

// Database connection
$conn = new mysqli("", "", "", "");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM usersignup WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['email'] = $user['email']; // Store session
            $_SESSION['name']  = $user['name'];  // Optional: store name
            header("Location: ./services.php");
            exit;
        } else {
            // Invalid password
            $error = urlencode("❌ Invalid password!");
            header("Location: ./login.html?error=$error");
            exit;
        }
    } else {
        // User not found
        $error = urlencode("❌ User not found!");
        header("Location: ./login.html?error=$error");
        exit;
    }

    $stmt->close();
}

$conn->close();
