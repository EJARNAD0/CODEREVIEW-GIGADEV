<?php
include_once 'classes/class.request.php';

$request = new Request();
header('Access-Control-Allow-Origin: *'); // Allows requests from any domain
header('Access-Control-Allow-Methods: GET, POST'); // Allow only necessary methods
header('Access-Control-Allow-Headers: Content-Type');

header('Content-Type: application/json'); // Set content type for JSON response

try {
    // Fetch requests for mapping
    $data = $request->get_all_requests_for_mapping();

    // Check if data is empty
    if (empty($data)) {
        echo json_encode(['message' => 'No requests found.']);
        http_response_code(204); // No content
    } else {
        echo json_encode($data); // Send data as JSON
    }
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => $e->getMessage()]);
}
?>
