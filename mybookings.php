<?php
// Start session (if not already started)
session_start();

// Include database connection (for fetching bookings, adjust as needed)
require 'db_connection.php';

// Example: Fetch user's bookings (adjust query as per your database)
global $conn;
$user_id = $_SESSION['user_id'] ?? null;
$bookings = [];
if ($user_id && $conn) {
    $sql = "SELECT b.booking_id, c.car_name, b.pickup_date, b.pickup_time, b.return_date, b.return_time, b.total_amount, b.booking_status 
            FROM bookings b 
            JOIN cars c ON b.car_id = c.car_id 
            WHERE b.user_id = ? AND b.booking_status IN ('Pending', 'Confirmed')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - RentalX</title>
    <link rel="stylesheet" href="css/admin_style.css"> <!-- Adjust path as needed -->
    <style>
        .booking-card {
            background: #1a1a1a;
            color: #fff;
            padding: 20px;
            margin: 10px 0;
            border-radius: 10px;
            position: relative;
        }
        .booking-card h3 {
            margin: 0 0 10px;
            color: #ffd700;
        }
        .booking-card p {
            margin: 5px 0;
        }
        .booking-card .status {
            background: #ff4500;
            padding: 5px 10px;
            border-radius: 5px;
            display: inline-block;
        }
        .booking-card .cancel-btn {
            background: #ff0000;
            color: #fff;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        .booking-card .cancel-btn:hover {
            background: #cc0000;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            width: 300px;
        }
        .modal-content button {
            margin: 10px 5px;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .modal-content .cancel-confirm {
            background: #ff0000;
            color: #fff;
        }
        .modal-content .cancel-confirm:hover {
            background: #cc0000;
        }
        .modal-content .keep {
            background: #ccc;
            color: #000;
        }
        .modal-content .keep:hover {
            background: #bbb;
        }
    </style>
</head>
<body>
    <h2>My Bookings - RentalX</h2>
    <?php if (empty($bookings)): ?>
        <p>No bookings found.</p>
    <?php else: ?>
        <?php foreach ($bookings as $booking): ?>
            <div class="booking-card">
                <h3><?php echo htmlspecialchars($booking['car_name']); ?></h3>
                <p>Booking ID: <?php echo htmlspecialchars($booking['booking_id']); ?></p>
                <p>Pickup: <?php echo date('Y-m-d H:i', strtotime($booking['pickup_date'] . ' ' . $booking['pickup_time'])); ?></p>
                <p>Return: <?php echo date('Y-m-d H:i', strtotime($booking['return_date'] . ' ' . $booking['return_time'])); ?></p>
                <p>Total Amount: ₹<?php echo number_format($booking['total_amount'], 2); ?></p>
                <p>Status: <span class="status"><?php echo htmlspecialchars($booking['booking_status']); ?></span></p>
                <?php if ($booking['booking_status'] === 'Pending'): ?>
                    <button class="cancel-btn" onclick="showCancelModal(<?php echo $booking['booking_id']; ?>)">Cancel Booking</button>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif;

    // Modal for cancellation confirmation
    echo '<div id="cancelModal" class="modal">
            <div class="modal-content">
                <h3>Cancel Booking</h3>
                <p>Are you sure you want to cancel this booking? This action cannot be undone.</p>
                <button class="cancel-confirm" id="confirmCancel">Yes, Cancel</button>
                <button class="keep" id="keepBooking">No, Keep</button>
            </div>
          </div>';
    ?>

    <script>
        let currentBookingId = null;

        function showCancelModal(bookingId) {
            currentBookingId = bookingId;
            document.getElementById('cancelModal').style.display = 'flex';
        }

        document.getElementById('confirmCancel').addEventListener('click', function() {
            if (currentBookingId) {
                fetch('cancel.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'booking_id=' + encodeURIComponent(currentBookingId)
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('cancelModal').style.display = 'none';
                    if (data.success) {
                        alert('Booking cancelled successfully');
                        location.reload(); // Reload page to reflect changes
                    } else {
                        alert(data.message || 'Cancellation failed');
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        });

        document.getElementById('keepBooking').addEventListener('click', function() {
            document.getElementById('cancelModal').style.display = 'none';
        });
    </script>
</body>
</html>
