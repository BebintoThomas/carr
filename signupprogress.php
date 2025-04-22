<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "root"; // Adjust if your MySQL password is different
$dbname = "carren";
$port = 3307; // Adjust if your port is different (default is 3306)

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    $username = trim($_POST["username"] ?? '');
    $phoneno = trim($_POST["phoneno"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $password = trim($_POST["password"] ?? '');
    $confirm_password = trim($_POST["confirm_password"] ?? '');

    // Validation
    if (empty($username) || empty($phoneno) || empty($email) || empty($password) || empty($confirm_password)) {
        die("<script>alert('All fields are required!'); window.location.href='signup.php';</script>");
    }

    if (!preg_match("/^[0-9]{10,15}$/", $phoneno)) {
        die("<script>alert('Invalid phone number! Must be 10-15 digits.'); window.location.href='signup.php';</script>");
    }

    if ($password !== $confirm_password) {
        die("<script>alert('Passwords do not match!'); window.location.href='signup.php';</script>");
    }

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare and bind (excluding confirm_password)
    $stmt = $conn->prepare("INSERT INTO registration (username, phoneno, email, password) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }

    $stmt->bind_param("ssss", $username, $phoneno, $email, $hashed_password);

    if ($stmt->execute()) {
        // Redirect with success parameter
        header("Location: signup.php?success=true");
        exit;
    } else {
        die("SQL Execution Error: " . $stmt->error);
    }

    $stmt->close();
}

$conn->close();
?>