<?php
$servername = "localhost"; 
$username = "root"; 
$password = "root"; 
$dbname = "carren"; 
$port = "3307"; // Ensure it's a string

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
