<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Add car
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_car'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_FILES['image']['name'];
    $target = "images/" . basename($image);
    move_uploaded_file($_FILES['image']['tmp_name'], $target);

    $stmt = $conn->prepare("INSERT INTO cars (name, price, image) VALUES (?, ?, ?)");
    $stmt->bind_param("sds", $name, $price, $image);
    $stmt->execute();
    $stmt->close();
}

// Delete car
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM cars WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Fetch cars
$cars = $conn->query("SELECT * FROM cars");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Cars - RentalX</title>
    <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>
    <div class="dashboard-container">
        <nav class="sidebar">
            <h2>RentalX Admin</h2>
            <ul>
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="manage_cars.php" class="active">Manage Cars</a></li>
                <li><a href="manage_bookings.php">Manage Bookings</a></li>
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="manage_payments.php">Manage Payments</a></li>
                <li><a href="admin_logout.php">Logout</a></li>
            </ul>
        </nav>
        <main class="main-content">
            <h1>Manage Cars</h1>
            <form method="POST" enctype="multipart/form-data" class="add-form">
                <input type="text" name="name" placeholder="Car Name" required>
                <input type="number" name="price" placeholder="Price per Day" step="0.01" required>
                <input type="file" name="image" accept="image/*" required>
                <button type="submit" name="add_car">Add Car</button>
            </form>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($car = $cars->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $car['id']; ?></td>
                            <td><?php echo htmlspecialchars($car['name']); ?></td>
                            <td><?php echo $car['price']; ?></td>
                            <td><img src="<?php echo $car['image']; ?>" alt="Car" width="100"></td>
                            <td>
                                <a href="?delete=<?php echo $car['id']; ?>" onclick="return confirm('Delete this car?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>