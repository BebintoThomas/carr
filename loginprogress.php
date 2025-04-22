<?php
session_start();
include("db_connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"] ?? '');
    $password = trim($_POST["password"] ?? '');

    if (empty($email) || empty($password)) {
        die("<script>alert('All fields are required!'); window.location.href='login.php';</script>");
    }

    // Retrieve user ID and password from the database
    $stmt = $conn->prepare("SELECT user_id, password FROM registration WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $stored_password);
        $stmt->fetch();

        if ($password === $stored_password) { 
            // Set session user_id
            $_SESSION['user_id'] = $user_id;

            echo "<script>alert('Login successful!'); window.location.href='dashbord.php';</script>";
            exit;
        } else {
            echo "<script>alert('Invalid email or password!'); window.location.href='login.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('User not found!'); window.location.href='login.php';</script>";
        exit;
    }

    $stmt->close();
}

$conn->close();
?>
