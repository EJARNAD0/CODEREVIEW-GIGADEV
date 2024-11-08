<?php
// Include database connection (adjust this to your DB connection)
$conn = new mysqli('localhost:3306', 'wanderej_plato', 'Demesa123', 'wanderej_plato');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the user_id from the request
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : 0; // Make sure to validate this

// Fetch notifications specific to this user
$sql = "SELECT notification_id, user_id, message, created_at 
        FROM notifications 
        WHERE user_id = ? 
        ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id); // 'i' indicates the data type (integer)
$stmt->execute();
$result = $stmt->get_result();

$notifications = array();

// Fetch and output the data
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

// Return the notifications as JSON
header('Content-Type: application/json');
echo json_encode($notifications);

// Close the database connection
$stmt->close();
$conn->close();
?>
