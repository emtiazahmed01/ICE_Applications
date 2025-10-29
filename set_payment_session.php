<?php
session_start();

// Only allow logged-in users
if (!isset($_SESSION['email'])) {
    exit("Unauthorized");
}

// Get type from query
$type = $_GET['type'] ?? '';

// Define amounts for each type
$amounts = [
    "মার্কশীট" => 50.00,
    "প্রত্যয়ন পত্র" => 150.00,
    "প্রশংসাপত্র" => 100.00
];

// Store session flags
$_SESSION['allow_payment'] = true;
$_SESSION['payment_type'] = $type;
$_SESSION['payment_amount'] = $amounts[$type] ?? 0;
?>
