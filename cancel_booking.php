<?php
// Ensure no whitespace or output before this line
ob_start(); // Start output buffering

// Start session
session_start();

// Include database connection file
require 'db_connection.php';

// Set content type to JSON and disable echoing errors to output
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0); // Disable on production
ini_set('log_errors', 1);
ini_set('error_log', '/var/www/html/errors.log');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    ob_end_clean(); // Clean output buffer
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Use global connection from db_connection.php
global $conn;
if ($conn->connect_error) {
    ob_end_clean(); // Clean output buffer
    error_log("Connection failed: " . $conn->connect_error, 3, '/var/www/html/errors.log');
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Get and validate booking_id from POST
$booking_id = filter_input(INPUT_POST, 'booking_id', FILTER_VALIDATE_INT);
$user_id = (int)$_SESSION['user_id'];

if (!$booking_id) {
    ob_end_clean(); // Clean output buffer
    echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
    exit;
}

// Prepare and execute UPDATE query
$sql = "UPDATE bookings SET booking_status = 'Cancelled' WHERE booking_id = ? AND user_id = ? AND booking_status = 'Pending'";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    ob_end_clean(); // Clean output buffer
    error_log("Query preparation failed: " . $conn->error, 3, '/var/www/html/errors.log');
    echo json_encode(['success' => false, 'message' => 'Database query preparation failed']);
    exit;
}

$stmt->bind_param("ii", $booking_id, $user_id);
$success = $stmt->execute();

if ($success && $stmt->affected_rows > 0) {
    ob_end_clean(); // Clean output buffer
    echo json_encode(['success' => true]);
} else {
    ob_end_clean(); // Clean output buffer
    echo json_encode(['success' => false, 'message' => 'Booking not found or already cancelled']);
}

$stmt->close();
$conn->close();
ob_end_flush(); // Flush output buffer
exit;
?>
