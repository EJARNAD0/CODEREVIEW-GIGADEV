<?php
include '../classes/class.request.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';

switch($action) {
    case 'new':
        create_new_request();
        break;
    case 'update':
        update_request();
        break;
    case 'delete':
        delete_request();
        break;
    case 'status':
        update_request_status();
        break;
}

function create_new_request() {
    if (isset($_POST['user_id']) && !empty($_POST['user_id']) &&
        isset($_POST['request_details']) && !empty($_POST['request_details']) &&
        isset($_POST['latitude']) && !empty($_POST['latitude']) &&
        isset($_POST['longitude']) && !empty($_POST['longitude'])) {
        
        $request = new Request();
        $user_id = $_POST['user_id'];
        $request_details = $_POST['request_details'];
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];

        // Debugging: Log the received data
        error_log("Received User ID: $user_id, Request Details: $request_details, Latitude: $latitude, Longitude: $longitude");

        // Pass the latitude and longitude to the new_request method
        $result = $request->new_request($user_id, $request_details, $latitude, $longitude);
        if ($result) {
            header('location: ../index.php?page=requests&id=' . $user_id);
            exit();
        } else {
            echo json_encode(['success' => false, 'message' => 'Database insertion failed']);
        }
    } else {
        // Log missing fields
        error_log('Missing required fields. User ID: ' . ($_POST['user_id'] ?? 'not set') . 
                  ', Request Details: ' . ($_POST['request_details'] ?? 'not set') . 
                  ', Latitude: ' . ($_POST['latitude'] ?? 'not set') . 
                  ', Longitude: ' . ($_POST['longitude'] ?? 'not set'));
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    }
}

function update_request() {
    if (isset($_POST['requestid'], $_POST['request_details'])) {
        $request = new Request();
        $request_id = $_POST['requestid'];
        $request_details = $_POST['request_details'];
        
        $result = $request->update_request($request_id, $request_details);
        if ($result) {
            header('location: ../index.php?page=requests&id=' . $request_id);
            exit();
        }
    }
}

function update_request_status() {
    if (isset($_POST['requestid'], $_POST['request_status'])) {
        $request = new Request();
        $request_id = $_POST['requestid'];
        $request_status = $_POST['request_status'];

        // Update the request status
        $result = $request->update_request_status($request_id, $request_status);

        if ($result) {
            // Fetch the request details to get the user ID
            $request_details = $request->get_request_details($request_id);

            // Check if request details were fetched successfully
            if ($request_details && isset($request_details['user_id'])) {
                $user_id = $request_details['user_id'];

                // Fetch the user's full name
                $user_fullname = $request->get_user_fullname($user_id);

                // Prepare a notification message
                if ($request_status === 'approved') {
                    $message = "Hello $user_fullname, your request has been approved. You can go now to the barangay hall.";
                } else {
                    $message = "Hello $user_fullname, your request has been rejected. Please correct any issues and resubmit.";
                }

                // Add a notification for the user
                $request->add_notification($user_id, $message);

                // Redirect to the request listing page
                header('Location: ../index.php?page=request');
                exit();
            } else {
                // Log the issue if the request details are missing
                error_log("Error: Request details not found for request ID: $request_id");
                echo "Error: Request details not found for request ID: $request_id";
                exit();
            }
        } else {
            echo "Error: Failed to update request status.";
            exit();
        }
    } else {
        echo "Error: Missing request ID or status.";
        exit();
    }
}

function delete_request() {
    if (isset($_POST['requestid']) && is_numeric($_POST['requestid'])) {
        $request = new Request();
        $request_id = $_POST['requestid'];
        $user_id = $_POST['userid'];
        
        $result = $request->delete_request($request_id);
        if ($result) {
            header('location: ../index.php?page=requests&id=' . $user_id);
            exit();
        }
    }
}
?>