<?php
// Set the default timezone to Asia/Manila
date_default_timezone_set("Asia/Manila");

// Start a session if one isn't already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define constants for database connection
if (!defined('DB_SERVER')) {
    define('DB_SERVER', 'localhost:3306'); // Database server address
}

if (!defined('DB_USERNAME')) {
    define('DB_USERNAME', 'wanderej_plato'); // Database username
}

if (!defined('DB_PASSWORD')) {
    define('DB_PASSWORD', 'Demesa123'); // Database password (leave empty if none)
}

if (!defined('DB_DATABASE')) {
    define('DB_DATABASE', 'wanderej_plato'); // Database name
}

// Create a connection to the database
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

// Check if the connection was successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error()); // Show error message if connection fails
}


?>
