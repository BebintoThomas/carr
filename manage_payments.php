<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Update payment status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $payment_id = $_POST['payment_id'];
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE payments SET status = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("si", $status, $payment_id);
        if ($stmt->execute()) {
            $success = "Payment status updated successfully.";
        } else {
            $error = "Failed to update payment status.";
        }
        $stmt->close();
    } else {
        $error = "Database error: Unable to prepare statement.";
    }
}

// Fetch payments
$query = "SELECT p.*, r.username, b.id AS booking_id 
          FROM payments p 
          JOIN registration r ON p.user_id = r.id 
          JOIN bookings b ON p.booking_id = b.id";
$payments = $conn->query($query);
if (!$payments) {
    $error = "Error fetching payments: Check if tables 'payments', 'registration', or 'bookings' exist.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Payments - RentalX</title>
    <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>
    <div class="dashboard-container">
        <nav class="sidebar">
            <h2>RentalX Admin</h2>
            <ul>
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="manage_cars.php">Manage Cars</a></li>
                <li><a href="manage_bookings.php">Manage Bookings</a></li>
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="manage_payments.php" class="active">Manage Payments</a></li>
                <li><a href="admin_logout.php">Logout</a></li>
            </ul>
        </nav>
        <main class="main-content">
            <h1>Manage Payments</h1>
            <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Booking ID</th>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($payments && $payments->num_rows > 0): ?>
                        <?php while ($payment = $payments->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $payment['id']; ?></td>
                                <td><?php echo $payment['booking_id']; ?></td>
                                <td><?php echo htmlspecialchars($payment['username']); ?></td>
                                <td>₹<?php echo number_format($payment['amount'], 2); ?></td>
                                <td><?php echo ucfirst(str_replace('_', ' ', $payment['payment_method'])); ?></td>
                                <td><?php echo ucfirst($payment['status']); ?></td>
                                <td><?php echo date('d-M-Y H:i', strtotime($payment['created_at'])); ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="payment_id" value="<?php echo $payment['id']; ?>">
                                        <select name="status">
                                            <option value="pending" <?php if ($payment['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                                            <option value="completed" <?php if ($payment['status'] == 'completed') echo 'selected'; ?>>Completed</option>
                                            <option value="failed" <?php if ($payment['status'] == 'failed') echo 'selected'; ?>>Failed</option>
                                        </select>
                                        <button type="submit" name="update_status">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">No payments found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
    <script src="js/admin_dashboard.js"></script>
</body>
</html>