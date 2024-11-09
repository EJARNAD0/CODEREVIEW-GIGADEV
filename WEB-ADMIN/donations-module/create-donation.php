<?php
// Include necessary files and initiate database connection
include_once 'config.php';  // Adjust this path if needed
include_once 'class.donations.php';

header('Content-Type: application/json');

// Check if POST request includes all required fields
if (isset($_POST['user_id'], $_POST['user_firstname'], $_POST['user_lastname'], $_POST['amount'], $_POST['reference_number'], $_POST['purpose'])) {
    $user_id = $_POST['user_id'];
    $user_firstname = $_POST['user_firstname'];
    $user_lastname = $_POST['user_lastname'];
    $amount = $_POST['amount'];
    $reference_number = $_POST['reference_number'];
    $purpose = $_POST['purpose'];

    // Validate data types and format as needed
    if (!is_numeric($amount) || !is_numeric($user_id) || empty($reference_number) || empty($purpose)) {
        echo json_encode(["success" => false, "message" => "Invalid input data."]);
        exit;
    }

    // Initialize the Donations class
    $donation = new Donations();

    // Call the function to create a new donation entry
    $result = $donation->new_donation($user_id, $user_firstname, $user_lastname, $amount, $purpose, $reference_number);

    if ($result['success']) {
        echo json_encode(["success" => true, "message" => "Donation recorded successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to create donation: " . $result['message']]);
    }
} else {
    // Missing required parameters
    echo json_encode(["success" => false, "message" => "Required parameters are missing."]);
}
?>