<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Error: User is not logged in. Please log in first.");
}

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "carren";
$port = 3307;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$user_id = $_SESSION['user_id']; // Ensure user_id is set in session
$car_id = $_POST['car_id'];
$total_amount = $_POST['total_amount'];
$pickup_datetime = $_POST['pickup_datetime'];
$dropoff_datetime = $_POST['dropoff_datetime'];

// Convert to MySQL date and time format
$pickup_date = date('Y-m-d', strtotime($pickup_datetime));
$pickup_time = date('H:i:s', strtotime($pickup_datetime));
$dropoff_date = date('Y-m-d', strtotime($dropoff_datetime));
$dropoff_time = date('H:i:s', strtotime($dropoff_datetime));

$sql = "INSERT INTO bookings (user_id, car_id, pickup_date, pickup_time, return_date, return_time, total_amount, booking_status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$booking_status = "Pending";

$stmt->bind_param("iissssds", $user_id, $car_id, $pickup_date, $pickup_time, $dropoff_date, $dropoff_time, $total_amount, $booking_status);

if ($stmt->execute()) {
    $booking_id = $conn->insert_id; // Get inserted booking ID
    echo "<script>
        
        window.location.href='payment.php?total_amount=$total_amount&booking_id=$booking_id&car_id=$car_id';
    </script>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
