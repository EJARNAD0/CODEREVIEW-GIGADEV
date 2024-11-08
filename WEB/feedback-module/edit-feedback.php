<?php
$feedback = new Feedback();

// Check if the feedback ID is set and fetch the feedback item
$feedback_id = isset($_GET['id']) ? $_GET['id'] : null;
$feedbackItem = null;

if ($feedback_id) {
    // Fetch the specific feedback item to display
    $feedbackList = $feedback->get_all_feedback();
    foreach ($feedbackList as $item) {
        if ($item['feedback_id'] == $feedback_id) {
            $feedbackItem = $item;
            break;
        }
    }
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle feedback archiving
    if (isset($_POST['action']) && $_POST['action'] === 'archive') {
        // Check if feedback_id is set before using it
        if (isset($_POST['feedback_id'])) {
            $feedback_id = $_POST['feedback_id'];

            if ($feedback->archive_feedback($feedback_id)) {
                echo "Feedback archived successfully.";
                header('location: index.php?page=feedback'); // Redirect to feedback listing
                exit(); // Ensure the script stops after the redirect
            } else {
                echo "Error archiving feedback.";
            }
        } else {
            echo "Feedback ID is not set.";
        }
    }
}
?>

<div class="feedback-container">
    <h1>View Feedback</h1>
    <?php if (!empty($feedbackItem)): ?>
        <form method="POST">
            <input type="hidden" name="feedback_id" value="<?php echo htmlspecialchars($feedbackItem['feedback_id']); ?>">

            <label for="feedback">Feedback:</label>
            <p><?php echo nl2br(htmlspecialchars($feedbackItem['feedback'])); ?></p>

            <button type="submit" name="action" value="archive">Archive Feedback</button>
        </form>
    <?php else: ?>
        <p class="no-feedback">No feedback found.</p>
    <?php endif; ?>
</div>

