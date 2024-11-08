<?php
// db_connection.php

// Database configuration
$servername = "localhost:3306"; // Your server name, usually localhost
$username = "wanderej_plato"; // Your database username
$password = "Demesa123"; // Your database password
$dbname = "wanderej_plato"; // Replace with your database name

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>