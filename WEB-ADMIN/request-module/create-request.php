<?php
header('Content-Type: application/json');

// Include your database connection here
require 'db_connection.php';

// Disable error reporting in production (uncomment this line once you're done debugging)
// error_reporting(0);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the request body data
    $user_id = isset($_POST['user_id']) ? trim($_POST['user_id']) : '';
    $request_details = isset($_POST['request_details']) ? trim($_POST['request_details']) : ''; // Corrected parameter name
    $latitude = isset($_POST['latitude']) ? trim($_POST['latitude']) : ''; // Get latitude
    $longitude = isset($_POST['longitude']) ? trim($_POST['longitude']) : ''; // Get longitude

    // Debugging output to verify received parameters
    error_log("Received user_id: $user_id");
    error_log("Received request_details: $request_details");
    error_log("Received latitude: $latitude");
    error_log("Received longitude: $longitude");

    // Ensure all fields are provided
    if (empty($user_id) || empty($request_details) || empty($latitude) || empty($longitude)) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }

    // Validate user_id is a number
    if (!is_numeric($user_id)) {
        echo json_encode(['success' => false, 'message' => 'Invalid user_id']);
        exit;
    }

    // Validate latitude and longitude are numeric
    if (!is_numeric($latitude) || !is_numeric($longitude)) {
        echo json_encode(['success' => false, 'message' => 'Invalid latitude or longitude']);
        exit;
    }

    // Insert the request into the database
    $query = "INSERT INTO requests (user_id, request_details, latitude, longitude) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare statement: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("isss", $user_id, $request_details, $latitude, $longitude); // Bind parameters

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Request successfully submitted']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to submit request: ' . $conn->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}