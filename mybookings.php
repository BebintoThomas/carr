<?php
session_start();
include('includes/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch bookings for the logged-in user
$sql = "SELECT b.booking_id, c.car_name, c.car_image, b.pickup_datetime, b.dropoff_datetime, b.total_price, b.status
        FROM bookings b
        JOIN cars c ON b.car_id = c.car_id
        WHERE b.user_id = ? 
        ORDER BY b.booking_id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css"> <!-- Your custom CSS if any -->
    <style>
        .car-img {
            width: 100px;
            height: auto;
        }
        .booking-card {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            margin-bottom: 20px;
            padding: 20px;
        }
        .cancel-btn {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">My Bookings</h2>

    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="booking-card row align-items-center">
            <div class="col-md-2">
                <img src="<?php echo htmlspecialchars($row['car_image']); ?>" alt="Car Image" class="car-img">
            </div>
            <div class="col-md-7">
                <h5><?php echo htmlspecialchars($row['car_name']); ?></h5>
                <p><strong>Pickup:</strong> <?php echo $row['pickup_datetime']; ?></p>
                <p><strong>Drop-off:</strong> <?php echo $row['dropoff_datetime']; ?></p>
                <p><strong>Total Price:</strong> ₹<?php echo number_format($row['total_price'], 2); ?></p>
                <p><strong>Status:</strong> 
                    <?php if ($row['status'] == 'Cancelled'): ?>
                        <span class="text-danger">Cancelled</span>
                    <?php else: ?>
                        <span class="text-success">Active</span>
                    <?php endif; ?>
                </p>
            </div>
            <div class="col-md-3 text-end">
                <?php if ($row['status'] != 'Cancelled'): ?>
                    <button class="btn cancel-btn" onclick="confirmCancel(<?php echo $row['booking_id']; ?>)">Cancel</button>
                <?php else: ?>
                    <button class="btn btn-secondary" disabled>Cancelled</button>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile; ?>

</div>

<!-- Cancel Confirmation Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="cancelModalLabel">Confirm Cancellation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to cancel this booking?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
        <button type="button" class="btn btn-danger" id="confirmCancelBtn">Yes, Cancel</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let selectedBookingId = null;

    function confirmCancel(bookingId) {
        selectedBookingId = bookingId;
        const cancelModal = new bootstrap.Modal(document.getElementById('cancelModal'));
        cancelModal.show();
    }

    document.getElementById('confirmCancelBtn').addEventListener('click', function () {
        if (selectedBookingId) {
            fetch('cancel_booking.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'booking_id=' + selectedBookingId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Failed to cancel booking.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred.');
            });
        }
    });
</script>
</body>
</html>
