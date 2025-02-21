<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "pet_database";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Optional: Uncomment to confirm connection in debugging
// echo "✅ Connected successfully!";
?>
