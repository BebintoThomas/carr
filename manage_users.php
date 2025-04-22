<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Block/unblock user
if (isset($_GET['block'])) {
    $id = $_GET['block'];
    $stmt = $conn->prepare("UPDATE registration SET is_blocked = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}
if (isset($_GET['unblock'])) {
    $id = $_GET['unblock'];
    $stmt = $conn->prepare("UPDATE registration SET is_blocked = 0 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Fetch users
$users = $conn->query("SELECT *, IFNULL(is_blocked, 0) AS is_blocked FROM registration");
if (!$users) {
    $error = "Error fetching users: Table 'registration' may not exist.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - RentalX</title>
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
                <li><a href="manage_users.php" class="active">Manage Users</a></li>
                <li><a href="manage_payments.php">Manage Payments</a></li>
                <li><a href="admin_logout.php">Logout</a></li>
            </ul>
        </nav>
        <main class="main-content">
            <h1>Manage Users</h1>
            <?php if (isset($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($users && $users->num_rows > 0): ?>
                        <?php while ($user = $users->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo $user['is_blocked'] ? 'Blocked' : 'Active'; ?></td>
                                <td>
                                    <?php if ($user['is_blocked']): ?>
                                        <a href="?unblock=<?php echo $user['id']; ?>" onclick="return confirm('Unblock this user?');">Unblock</a>
                                    <?php else: ?>
                                        <a href="?block=<?php echo $user['id']; ?>" onclick="return confirm('Block this user?');">Block</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>