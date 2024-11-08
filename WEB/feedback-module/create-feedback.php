<?php
// Include your database connection file (replace with your actual connection file)
include 'db_connection.php'; 

// Set content type to JSON
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get the POST data
    $user_id = isset($_POST['user_id']) ? trim($_POST['user_id']) : null;
    $feedback = isset($_POST['feedback']) ? trim($_POST['feedback']) : null;

    // Debugging: Log the received parameters to verify
    error_log("Received user_id: $user_id");
    error_log("Received feedback: $feedback");

    // Validate that user_id and feedback are not empty
    if (empty($user_id) || empty($feedback)) {
        echo json_encode([
            'success' => false,
            'message' => 'User ID and feedback are required.'
        ]);
        exit;
    }

    // Validate that user_id is a number
    if (!is_numeric($user_id)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid user ID.'
        ]);
        exit;
    }

    // Sanitize the inputs
    $user_id = mysqli_real_escape_string($conn, $user_id);
    $feedback = mysqli_real_escape_string($conn, $feedback);

    // Insert feedback into the database
    $sql = "INSERT INTO user_feedback (user_id, feedback) VALUES ('$user_id', '$feedback')";

    if (mysqli_query($conn, $sql)) {
        echo json_encode([
            'success' => true,
            'message' => 'Feedback submitted successfully!'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to submit feedback. Please try again.'
        ]);
    }

    // Close the database connection
    mysqli_close($conn);
} else {
    // Handle non-POST requests
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
}