<?php
// No whitespace or output before this line
session_start();
require 'db_connect.php';

// Enable error reporting for development (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '/var/www/html/errors.log');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: loginprogress.php");
    exit();
}

// Check if booking_id or payment_id is provided
if (!isset($_GET['booking_id']) && !isset($_GET['payment_id'])) {
    header("Location: rental.php");
    exit();
}

// Fetch receipt details
$booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : null;
$payment_id = isset($_GET['payment_id']) ? (int)$_GET['payment_id'] : null;
$user_id = (int)$_SESSION['user_id'];
$error = null;
$receipt = null;

try {
    // Query to fetch receipt details
    $query = "SELECT 
                p.payment_id, p.amount_paid AS amount, p.payment_method, p.payment_status, p.payment_date,
                b.booking_id, b.pickup_date, b.pickup_time, b.return_date, b.return_time, b.total_amount,
                r.user_id, r.username, r.email,
                c.car_name, c.price_per_hour, c.car_image
              FROM payments p
              JOIN bookings b ON p.booking_id = b.booking_id
              JOIN registration r ON p.user_id = r.user_id
              JOIN cars c ON b.car_id = c.car_id
              WHERE p.user_id = ? AND ";
    
    if ($booking_id) {
        $query .= "b.booking_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $user_id, $booking_id);
    } else {
        $query .= "p.payment_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $user_id, $payment_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $receipt = $result->fetch_assoc();
    } else {
        $error = $booking_id || $payment_id ? "Receipt not found." : "Unauthorized access.";
    }
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    $error = "Database error
