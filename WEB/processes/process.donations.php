<?php
include '../classes/class.donations.php'; // Include the Donations class

$action = $_GET['action'] ?? ''; // Use null coalescing operator for cleaner code
$id = $_GET['id'] ?? '';

switch ($action) {
    case 'new':
        create_new_donation();
        break;
    case 'update':
        update_donation(); // Handle update action
        break;
    case 'delete':
        delete_donation();
        break;
    default:
        // Provide more informative feedback for debugging
        echo "Invalid action: " . htmlspecialchars($action);
        exit();
}

// Create a new donation
function create_new_donation() {
    if (isset($_POST['userid'], $_POST['user_firstname'], $_POST['user_lastname'], $_POST['amount'], $_POST['purpose'])) {
        $donation = new Donations();
        $user_id = $_POST['userid'];
        $user_firstname = $_POST['user_firstname'];
        $user_lastname = $_POST['user_lastname'];
        $amount = $_POST['amount'];
        $purpose = $_POST['purpose'];

        // Insert new donation
        $result = $donation->new_donation($user_id, $user_firstname, $user_lastname, $amount, $purpose);

        if ($result) {
            header('Location: ../index.php?page=donations&id=' . $user_id);
            exit();
        } else {
            echo "Failed to create donation.";
            exit();
        }
    } else {
        echo "Required parameters are missing.";
        exit();
    }
}
// Update donation status and notify the user
function update_donation() {
    if (isset($_POST['donation_id'], $_POST['donation_status'])) {
        $donation = new Donations();
        $donation_id = $_POST['donation_id'];
        $donation_status = $_POST['donation_status'];

        // Log the donation ID and status for debugging
        error_log("Updating donation status: ID = $donation_id, Status = $donation_status");

        // Retrieve donation details
        $donation_details = $donation->get_donation_details($donation_id);

        if ($donation_details) {
            // Update the donation status
            $result = $donation->update_donation($donation_id, $donation_details['amount'], $donation_details['purpose'], $donation_status);

            if ($result) {
                // Fetch user's full name
                $user_id = $donation_details['user_id'];
                $user_fullname = $donation->get_user_fullname($user_id);

                // Customize the message based on the donation status
                if ($donation_status === 'received') {
                    $message = "Thank you for your donation, $user_fullname! We appreciate your support!";
                } else {
                    $message = "Hello $user_fullname, your donation is still pending.";
                }

                // Use the add_notification() method from the Donations class
                $donation->add_notification($user_id, $message);

                // Redirect to the donations listing page
                header('Location: ../index.php?page=donation'); // Updated this line
                exit();
            } else {
                error_log("Error: Failed to update donation status for donation ID: $donation_id");
                echo "Error: Failed to update donation status.";
                exit();
            }
        } else {
            error_log("Error: Donation details not found for donation ID: $donation_id");
            echo "Error: Donation details not found.";
            exit();
        }
    } else {
        echo "Error: Missing donation ID or status.";
        exit();
    }
}
// Delete a donation
function delete_donation() {
    if (isset($_POST['donation_id']) && is_numeric($_POST['donation_id'])) {
        $donation = new Donations();
        $donation_id = $_POST['donation_id'];

        // Optionally get user ID from the request or another source
        $user_id = $_POST['userid'] ?? null;

        if ($donation->delete_donation($donation_id)) {
            header('Location: ../index.php?page=donations&id=' . ($user_id ?? ''));
            exit();
        } else {
            echo "Failed to delete donation.";
            exit();
        }
    } else {
        echo "Donation ID is invalid.";
        exit();
    }
}
?>
