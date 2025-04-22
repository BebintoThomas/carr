
<?php
session_start();
include('db_connection.php'); // Include your database connection file

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "carren";
$port = 3307;

$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$booking_id = filter_input(INPUT_POST, 'booking_id', FILTER_VALIDATE_INT);
$user_id = $_SESSION['user_id'];

if (!$booking_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
    exit;
}

// Verify booking belongs to the user and is cancellable
$sql = "UPDATE bookings SET booking_status = 'Cancelled' WHERE booking_id = ? AND user_id = ? AND booking_status = 'Pending'";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database query error']);
    exit;
}
$stmt->bind_param("ii", $booking_id, $user_id);
$success = $stmt->execute();

if ($success && $stmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Booking not found or already cancelled']);
}

$stmt->close();
$conn->close();
?>
