<?php
// No whitespace or output before this line
// Start session
session_start();

// Include database connection file
require 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Database connection (already handled by db_connection.php, but included here for clarity)
global $conn;
if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Get booking_id from POST request and validate
$booking_id = filter_input(INPUT_POST, 'booking_id', FILTER_VALIDATE_INT);
$user_id = (int)$_SESSION['user_id'];

if (!$booking_id) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
    exit;
}

// Prepare and execute UPDATE query
$sql = "UPDATE bookings SET booking_status = 'Cancelled' WHERE booking_id = ? AND user_id = ? AND booking_status = 'Pending'";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database query preparation failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("ii", $booking_id, $user_id);
$success = $stmt->execute();

if ($success && $stmt->affected_rows > 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Booking not found or already cancelled']);
}

$stmt->close();
$conn->close();
exit;
?>
