<?php
include '../classes/class.feedback.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';

switch($action) {
    case 'new':
        create_new_feedback();
        break;
    case 'update':
        update_feedback();
        break;
    case 'archive':
        archive_feedback();
        break;
    case 'restore':
        restore_feedback();
        break;
}

function create_new_feedback() {
    $feedback = new Feedback();
    $user_id = $_POST['userid'];
    $user_feedback = $_POST['feedback'];
    
    // Insert the feedback into the database
    $result = $feedback->new_feedback($user_id, $user_feedback);
    if ($result) {
        // Redirect to the user's feedback page after success
        header('location: ../index.php?page=feedback&id=' . $user_id);
    }
}

function update_feedback() {
    $feedback = new Feedback();
    $feedback_id = $_POST['feedbackid'];
    $user_feedback = $_POST['feedback'];
    
    // Update the feedback in the database
    $result = $feedback->update_feedback($feedback_id, $user_feedback);
    if ($result) {
        // Redirect back to the feedback listing page after success
        header('location: ../index.php?page=feedback');
        exit(); // Ensure the script stops after the redirect
    }
}

function archive_feedback() {
    if (isset($_POST['feedbackid']) && is_numeric($_POST['feedbackid'])) {
        $feedback = new Feedback();
        $feedback_id = $_POST['feedbackid'];
        
        // Archive the feedback in the database
        $result = $feedback->archive_feedback($feedback_id);
        if ($result) {
            // Redirect back to the feedback listing page after deletion
            header('location: ../index.php?page=feedback');
            exit(); // Ensure the script stops after the redirect
        }
    }
}

function restore_feedback() {
    if (isset($_POST['feedbackid']) && is_numeric($_POST['feedbackid'])) {
        $feedback = new Feedback();
        $feedback_id = $_POST['feedbackid'];
        
        // Restore the feedback from the archive
        $result = $feedback->restore_feedback($feedback_id);
        if ($result) {
            // Redirect back to the archived feedback page after restoration
            header('location: ../index.php?page=feedback');
            exit(); // Ensure the script stops after the redirect
        }
    }
}
?>
