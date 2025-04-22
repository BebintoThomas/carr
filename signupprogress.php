<?php
// Ensure no whitespace or output before this line
ob_start(); // Start output buffering to capture any accidental output

// Enable error reporting for development (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Disable direct error display
ini_set('log_errors', 1);
ini_set('error_log', '/var/www/html/errors.log');

// Database connection
$servername = "my-mysql";
$username = "root";
$password = "root"; // Adjust if your MySQL password is different
$dbname = "carren";
// Default MySQL port, adjust if using 3307

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error, 3, '/var/www/html/errors.log');
    ob_end_clean(); // Clean output buffer
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Handle POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debugging (optional, remove in production)
    // error_log("POST Data: " . print_r($_POST, true), 3, '/var/www/html/errors.log');

    $username = trim($_POST["username"] ?? '');
    $phoneno = trim($_POST["phoneno"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $password = trim($_POST["password"] ?? '');
    $confirm_password = trim($_POST["confirm_password"] ?? '');

    // Validation
    if (empty($username) || empty($phoneno) || empty($email) || empty($password) || empty($confirm_password)) {
        ob_end_clean(); // Clean output buffer
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'All fields are required!']);
        exit;
    }

    if (!preg_match("/^[0-9]{10,15}$/", $phoneno)) {
        ob_end_clean(); // Clean output buffer
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid phone number! Must be 10-15 digits.']);
        exit;
    }

    if ($password !== $confirm_password) {
        ob_end_clean(); // Clean output buffer
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Passwords do not match!']);
        exit;
    }

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO registration (username, phoneno, email, password) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        error_log("SQL Error: " . $conn->error, 3, '/var/www/html/errors.log');
        ob_end_clean(); // Clean output buffer
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Database query preparation failed']);
        exit;
    }

    $stmt->bind_param("ssss", $username, $phoneno, $email, $hashed_password);

    if ($stmt->execute()) {
        ob_end_clean(); // Clean output buffer
        header("Location: signup.php?success=true");
        exit;
    } else {
        error_log("SQL Execution Error: " . $stmt->error, 3, '/var/www/html/errors.log');
        ob_end_clean(); // Clean output buffer
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Registration failed']);
        exit;
    }

    $stmt->close();
}

$conn->close();
ob_end_flush(); // Flush output buffer
exit;
?>
