<?php
// db_connection.php

// Database configuration
$servername = ""; // Your server name, usually localhost
$username = ""; // Your database username
$password = ""; // Your database password
$dbname = ""; // Replace with your database name

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
