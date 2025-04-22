<?php
// Database credentials
$servername = "my-mysql"; // This is the name of the MySQL container
$username = "root"; 
$password = "root"; 
$dbname = "carren";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected successfully to MySQL on Docker network!";
?>
