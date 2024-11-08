<?php
require_once '../classes/class.notifications.php';

$notification = new Notification();

if (isset($_POST['action'])) {
    $action = $_POST['action'];
} elseif (isset($_GET['action'])) {
    $action = $_GET['action'];
} else {
    echo json_encode(['error' => 'No action specified']);
    exit;
}

try {
    switch ($action) {
        // Send notification
        case 'send':
            // Check if recipient type is set
            if (!isset($_POST['recipient'])) {
                echo json_encode(['error' => 'Recipient type not provided']);
                exit;
            }

            $recipient = $_POST['recipient'];
            $message = $_POST['message'];

            // If sending to all users
            if ($recipient == 'all') {
                // Retrieve all active users via a method in Notification class
                $user_ids = $notification->get_all_active_user_ids();

                // Send notification to all users
                foreach ($user_ids as $user_id) {
                    $notification->send_notification_by_username($user_id, $message);
                }

                echo json_encode(['success' => 'Notification sent to all users']);
            }
            // If sending to specific users
            elseif ($recipient == 'specific' && isset($_POST['users'])) {
                $user_ids = $_POST['users'];

                foreach ($user_ids as $user_id) {
                    $notification->send_notification_by_username($user_id, $message);
                }

                echo json_encode(['success' => 'Notification sent to selected users']);
            } else {
                echo json_encode(['error' => 'No users selected']);
            }

            // Redirect to the notification page after sending
            header('location: ../index.php?page=notification');
            exit;

        // Get notifications
        case 'get':
            if (!isset($_POST['username']) || empty($_POST['username'])) {
                echo json_encode(['error' => 'Username not provided']);
                exit;
            }
            $username = $_POST['username'];
            $only_unread = isset($_POST['only_unread']) && $_POST['only_unread'] == 'true' ? true : false;
            $notifications = $notification->get_notifications_by_username($username, $only_unread);
            echo json_encode(['notifications' => $notifications]);
            break;

        // Mark notification as read
        case 'mark_as_read':
            if (!isset($_POST['notification_id'])) {
                echo json_encode(['error' => 'Notification ID not provided']);
                exit;
            }
            $notification_id = $_POST['notification_id'];
            $notification->mark_as_read($notification_id);
            echo json_encode(['success' => 'Notification marked as read']);
            break;

        // Mark all notifications as read
        case 'mark_all_as_read':
            if (!isset($_POST['username']) || empty($_POST['username'])) {
                echo json_encode(['error' => 'Username not provided']);
                exit;
            }
            $username = $_POST['username'];
            $notification->mark_all_as_read_by_username($username);
            echo json_encode(['success' => 'All notifications marked as read']);
            break;

        // Delete a notification
        case 'delete':
            if (!isset($_POST['notification_id'])) {
                echo json_encode(['error' => 'Notification ID not provided']);
                exit;
            }
            $notification_id = $_POST['notification_id'];
            $notification->delete_notification($notification_id);
            echo json_encode(['success' => 'Notification deleted']);
            break;

        // Delete all notifications for a user
        case 'delete_all':
            if (!isset($_POST['username']) || empty($_POST['username'])) {
                echo json_encode(['error' => 'Username not provided']);
                exit;
            }
            $username = $_POST['username'];
            $notification->delete_all_notifications_by_username($username);
            echo json_encode(['success' => 'All notifications deleted']);
            break;

        default:
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
