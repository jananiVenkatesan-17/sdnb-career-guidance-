<?php

// Database credentials
$host = "localhost";
$user = "root";
$password = "";
$database = "placify";

// Create connection
$conn = mysqli_connect($host, $user, $password, $database);

// Check connection
if (!$conn) {
    die("❌ Database connection failed: " . mysqli_connect_error());
}

echo "✅ Database connected successfully to database!";

?>
