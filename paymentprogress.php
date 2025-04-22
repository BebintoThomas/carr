
<?php
    session_start();

    // Enable error reporting for debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        die("User not logged in. Please log in first.");
    }

    // Database connection
    $servername = "my-mysql";
    $username = "root";
    $password = "root";
    $dbname = "carren";
    

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Debugging: Log all POST data
    echo "<pre>POST Data: ";
    print_r($_POST);
    echo "</pre>";
    echo "Session User ID: " . $_SESSION['user_id'] . "<br>";

    // Collect and sanitize form data
    $user_id = intval($_SESSION['user_id']);#it will convert into integer
    $car_id = isset($_POST['car_id']) && $_POST['car_id'] !== '' ? intval($_POST['car_id']) : 0;#check wheather id is set or not and if it is set then convert into integer
    $car_id = $conn->real_escape_string($car_id);#it will convert into string
    $booking_id = isset($_POST['booking_id']) && $_POST['booking_id'] !== '' ? intval($_POST['booking_id']) : 0;
    $payment_method = isset($_POST['payment_method']) ? $conn->real_escape_string($_POST['payment_method']) : '';
    $amount_paid = isset($_POST['amount_paid']) ? floatval($_POST['amount_paid']) : 0;
    $payment_status = "Completed";

    // Optional fields
    $upi_id = !empty($_POST['upi_id']) ? $conn->real_escape_string($_POST['upi_id']) : NULL;
    $card_number = !empty($_POST['card_number']) ? $conn->real_escape_string($_POST['card_number']) : NULL;
    $card_number_last4 = $card_number ? substr(preg_replace('/\D/', '', $card_number), -4) : NULL;
    $card_expiry = !empty($_POST['card_expiry']) ? $conn->real_escape_string($_POST['card_expiry']) : NULL;

    // Validate required fields
    if ($user_id <= 0 || $car_id <= 0 || $booking_id <= 0 || !$payment_method || $amount_paid <= 0) {
        $missing_fields = [];
        if ($user_id <= 0) $missing_fields[] = "user_id";
        if ($car_id <= 0) $missing_fields[] = "car_id";
        if ($booking_id <= 0) $missing_fields[] = "booking_id";
        if (!$payment_method) $missing_fields[] = "payment_method";
        if ($amount_paid <= 0) $missing_fields[] = "amount_paid";
        header("Location: payment_error.php?error=" . urlencode("Missing or invalid fields: " . implode(", ", $missing_fields)));
        exit;
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
        exit;
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
        die("Prepare failed: " . $conn->error);
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

        echo "<script>
            alert('Payment successful!');
            window.location.href = 'receipt.php?booking_id=$booking_id';
        </script>";
    } else {
        echo "<script>
            alert('Payment failed: " . addslashes($stmt->error) . "');
            window.location.href = 'payment.php?booking_id=$booking_id&car_id=$car_id&total_amount=$amount_paid';
        </script>";
    }

    // Clean up
    $stmt->close();
    $conn->close();
    ?>
