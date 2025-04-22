<?php
session_start();
require 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: loginprogress.php"); // Redirect to login page
    exit();
}

// Check if booking_id or payment_id is provided
if (!isset($_GET['booking_id']) && !isset($_GET['payment_id'])) {
    header("Location: rental.php"); // Redirect to homepage if no ID
    exit();
}

// Fetch receipt details
$booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : null;
$payment_id = isset($_GET['payment_id']) ? (int)$_GET['payment_id'] : null;
$user_id = $_SESSION['user_id'];
$error = null;
$receipt = null;

try {
    // Query based on booking_id or payment_id
    $query = "SELECT 
                p.payment_id AS payment_id, p.amount_paid AS amount, p.payment_method, p.payment_status, p.payment_date,
                b.booking_id AS booking_id, b.pickup_date, b.pickup_time, b.return_date, b.return_time, b.total_amount,
                r.user_id AS user_id, r.username, r.email,
                c.car_name AS car_name, c.price_per_hour, c.car_image
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
        $error = "No receipt found or unauthorized access.";
    }
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt - RentalX</title>
    <link rel="stylesheet" href="css/admin_style.css">
    <style>
        .receipt-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .receipt-section {
            margin-bottom: 20px;
        }
        .receipt-section h3 {
            margin-bottom: 10px;
            color: #333;
        }
        .receipt-section p {
            margin: 5px 0;
            color: #555;
        }
        .car-image {
            max-width: 200px;
            border-radius: 5px;
            margin-top: 10px;
        }
        .home-button {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 10px;
            text-align: center;
            background: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 16px;
        }
        .home-button:hover {
            background: #218838;
        }
        .error {
            color: #dc3545;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <h2>RentalX Payment Receipt</h2>
            <p>Thank you for your payment!</p>
        </div>

        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php elseif ($receipt): ?>
            <div class="receipt-section">
                <h3>Customer Details</h3>
                <p><strong>User ID:</strong> <?php echo $receipt['user_id']; ?></p>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($receipt['username']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($receipt['email']); ?></p>
            </div>

            <div class="receipt-section">
                <h3>Booking Details</h3>
                <p><strong>Booking ID:</strong> <?php echo $receipt['booking_id']; ?></p>
                <p><strong>Pickup Date:</strong> <?php echo date('d-M-Y', strtotime($receipt['pickup_date'])); ?></p>
                <p><strong>Pickup Time:</strong> <?php echo date('H:i', strtotime($receipt['pickup_time'])); ?></p>
                <p><strong>Return Date:</strong> <?php echo date('d-M-Y', strtotime($receipt['return_date'])); ?></p>
                <p><strong>Return Time:</strong> <?php echo date('H:i', strtotime($receipt['return_time'])); ?></p>
                <p><strong>Total Amount:</strong> ₹<?php echo number_format($receipt['total_amount'], 2); ?></p>
            </div>

          

            <div class="receipt-section">
                <h3>Payment Details</h3>
                <p><strong>Payment ID:</strong> <?php echo $receipt['payment_id']; ?></p>
                <p><strong>Amount:</strong> ₹<?php echo number_format($receipt['amount'], 2); ?></p>
                <p><strong>Payment Method:</strong> <?php echo ucfirst(str_replace('_', ' ', $receipt['payment_method'])); ?></p>
                <p><strong>Status:</strong> <?php echo ucfirst($receipt['payment_status']); ?></p>
                <p><strong>Date:</strong> <?php echo date('d-M-Y H:i', strtotime($receipt['payment_date'])); ?></p>
            </div>
        <?php endif; ?>

        <a href="dashbord.php" class="home-button">Back to Home</a>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
    <script>
        // GSAP animations
        gsap.from(".receipt-container", {
            opacity: 0,
            y: 50,
            duration: 1,
            ease: "power3.out"
        });
        gsap.from(".receipt-section", {
            opacity: 0,
            x: -20,
            duration: 0.8,
            stagger: 0.2,
            delay: 0.5
        });
        gsap.from(".home-button", {
            opacity: 0,
            scale: 0.8,
            duration: 0.5,
            delay: 1.5
        });

        // Button hover animation
        const homeButton = document.querySelector(".home-button");
        homeButton.addEventListener("mouseenter", () => {
            gsap.to(homeButton, { scale: 1.05, duration: 0.3 });
        });
        homeButton.addEventListener("mouseleave", () => {
            gsap.to(homeButton, { scale: 1, duration: 0.3 });
        });
    </script>
</body>
</html>