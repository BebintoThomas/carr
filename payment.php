<?php
// No whitespace or output before this line
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '/var/www/html/errors.log');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: loginprogress.php");
    exit();
}

// Database connection
$servername = "my-mysql";
$username = "root";
$password = "root";
$dbname = "carren";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error, 3, '/var/www/html/errors.log');
    header("Location: payment_error.php?error=" . urlencode("Database connection failed."));
    exit();
}

// Collect and sanitize form data
$user_id = (int)$_SESSION['user_id'];
$car_id = isset($_POST['car_id']) && $_POST['car_id'] !== '' ? (int)$_POST['car_id'] : 0;
$booking_id = isset($_POST['booking_id']) && $_POST['booking_id'] !== '' ? (int)$_POST['booking_id'] : 0;
$payment_method = isset($_POST['payment_method']) ? $conn->real_escape_string($_POST['payment_method']) : '';
$amount_paid = isset($_POST['amount_paid']) ? floatval($_POST['amount_paid']) : 0;
$payment_status = "Completed";

// Optional fields
$upi_id = !empty($_POST['upi_id']) ? $conn->real_escape_string($_POST['upi_id']) : null;
$card_number = !empty($_POST['card_number']) ? $conn->real_escape_string($_POST['card_number']) : null;
$card_number_last4 = $card_number ? substr(preg_replace('/\D/', '', $card_number), -4) : null;
$card_expiry = !empty($_POST['card_expiry']) ? $conn->real_escape_string($_POST['card_expiry']) : null;

// Validate required fields
if ($user_id <= 0 || $car_id <= 0 || $booking_id <= 0 || !$payment_method || $amount_paid <= 0) {
    $missing_fields = [];
    if ($user_id <= 0) $missing_fields[] = "user_id";
    if ($car_id <= 0) $missing_fields[] = "car_id";
    if ($booking_id <= 0) $missing_fields[] = "booking_id";
    if (!$payment_method) $missing_fields[] = "payment_method";
    if ($amount_paid <= 0) $missing_fields[] = "amount_paid";
    header("Location: payment_error.php?error=" . urlencode("Missing or invalid fields: " . implode(", ", $missing_fields)));
    exit();
}

// Verify booking exists
$check_sql = "SELECT COUNT(*) FROM bookings WHERE booking_id = ? AND car_id = ? AND user_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("iii", $booking_id, $car_id, $user_id);
$check_stmt->execute();
$check_stmt->bind_result($count);
$check_stmt->fetch();
$check_stmt->close();

if ($count == 0) {
    header("Location: payment_error.php?error=" . urlencode("Invalid booking or car ID."));
    exit();
}

// Prepare SQL query
$sql = "INSERT INTO payments (
    user_id, 
    car_id, 
    booking_id, 
    payment_method, 
    upi_id, 
    card_number_last4, 
    card_expiry, 
    amount_paid, 
    payment_status
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("Prepare failed: " . $conn->error, 3, '/var/www/html/errors.log');
    header("Location: payment_error.php?error=" . urlencode("Database query preparation failed."));
    exit();
}

// Bind parameters
$stmt->bind_param(
    "iiissssds",
    $user_id,
    $car_id,
    $booking_id,
    $payment_method,
    $upi_id,
    $card_number_last4,
    $card_expiry,
    $amount_paid,
    $payment_status
);

// Execute and handle result
if ($stmt->execute()) {
    // Update booking status
    $update_sql = "UPDATE bookings SET booking_status = 'Confirmed' WHERE booking_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("i", $booking_id);
    $update_stmt->execute();
    $update_stmt->close();

    header("Location: paymentprogress.php?booking_id=$booking_id");
    exit();
} else {
    error_log("Payment failed: " . $stmt->error, 3, '/var/www/html/errors.log');
    header("Location: payment.php?booking_id=$booking_id&car_id=$car_id&total_amount=$amount_paid&error=" . urlencode($stmt->error));
    exit();
}

// Clean up
$stmt->close();
$conn->close();
?>
