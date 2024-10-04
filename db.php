<?php
$servername = "localhost";
$username = "root";
$password = ""; // Update with your database password
$database = "sukumawa"; // Update with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
sssss