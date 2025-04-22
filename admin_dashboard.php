<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Initialize stats with default values
$bookings = $cars = $registration = $payments = 0;
$error = null;

// Fetch stats with error handling
try {
    $result = $conn->query("SELECT COUNT(*) FROM bookings");
    if ($result) {
        $bookings = $result->fetch_row()[0];
    } else {
        $error = "Error fetching bookings: Table 'bookings' may not exist.";
    }

    $result = $conn->query("SELECT COUNT(*) FROM cars");
    if ($result) {
        $cars = $result->fetch_row()[0];
    } else {
        $error = $error ? $error . "<br>Error fetching cars: Table 'cars' may not exist." : "Error fetching cars: Table 'cars' may not exist.";
    }

    $result = $conn->query("SELECT COUNT(*) FROM registration");
    if ($result) {
        $registration = $result->fetch_row()[0];
    } else {
        $error = $error ? $error . "<br>Error fetching users: Table 'registration' may not exist." : "Error fetching users: Table 'registration' may not exist.";
    }

    $result = $conn->query("SELECT COUNT(*) FROM payments WHERE status = 'completed'");
    if ($result) {
        $payments = $result->fetch_row()[0];
    } else {
        $error = $error ? $error . "<br>Error fetching payments: Table 'payments' may not exist." : "Error fetching payments: Table 'payments' may not exist.";
    }
} catch (mysqli_sql_exception $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - RentalX</title>
    <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>
    <div class="dashboard-container">
        <nav class="sidebar">
            <h2>RentalX Admin</h2>
            <ul>
                <li><a href="admin_dashboard.php" class="active">Dashboard</a></li>
                <li><a href="manage_cars.php">Manage Cars</a></li>
                <li><a href="manage_bookings.php">Manage Bookings</a></li>
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="manage_payments.php">Manage Payments</a></li>
                <li><a href="admin_logout.php">Logout</a></li>
            </ul>
        </nav>
        <main class="main-content">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['admin']); ?>!</h1>
            <?php if ($error): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Bookings</h3>
                    <p><?php echo $bookings; ?></p>
                </div>
                <div class="stat-card"a>
                    <h3>Available Cars</h3>
                    <p><?php echo $cars; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Registered Users</h3>
                    <p><?php echo $registration; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Completed Payments</h3>
                    <p><?php echo $payments; ?></p>
                </div>
            </div>
        </main>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
    <script src="js/admin_dashboard.js"></script>
</body>
</html>