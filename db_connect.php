<?php
// Database connection settings
$servername = "localhost";
$username = "root";            // Change if different
$password = "";                // Change if set
$dbname = "greencircle_db";    // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection success
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset for proper encoding
$conn->set_charset("utf8mb4");
?>
