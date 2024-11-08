<?php
// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Test Output
echo "API is working.<br>"; // Test to see if the script is running

// Enable CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight (OPTIONS) requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include the config.php file (ensure it exists with the correct DB credentials)
// Adjust the path to the correct location of config.php
require_once '../config/config.php';  // Go up one directory to access the config folder

// Connect to the MySQL database using config.php values
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

// Check if the connection was successfulf
if ($conn->connect_error) {
    die(json_encode(["success" => false, "error" => "Connection failed: " . $conn->connect_error]));
} else {
    echo "Database connected successfully.<br>"; // Test to see if the database connection is successful
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the JSON data from the request
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Check if 'name' is set in the JSON payload
    if (isset($data['name'])) {
        $name = $data['name']; // Get the 'name' value
        
        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO users (name) VALUES (?)");
        $stmt->bind_param("s", $name); // Bind the 'name' parameter
        
        // Execute the statement and check if successful
        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => $stmt->error]);
        }
        
        // Close the prepared statement
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "error" => "Invalid input. 'name' field is required."]);
    }
}

// Close the database connection
$conn->close();
?>