<?php
$feedback_obj = new Feedback();  
$all_feedback = $feedback_obj->get_all_feedback(); 

$action = isset($_GET['action']) ? $_GET['action'] : '';
?>

<h1><?php echo ($action === 'archive') ? 'Archived User Feedback' : 'All User Feedback'; ?></h1>

<?php if ($action !== 'edit' && $action !== 'archive'): ?>
    <div class="submenu-actions">
        <a href="index.php?page=feedback&action=archive" class="button">View Archived Feedback</a>
    </div>
    <div id="search-container">
        <div class="search-wrapper">
             <input type="text" id="search" name="search" onkeyup="showFeedbackResults(this.value)" placeholder="Search...">
             <i class="fas fa-search search-icon"></i>
       </div>
</div>

    <div id="search-result"></div> 
    <table>
        <thead>
            <tr>
                <th>No.</th> <!-- Changed column title -->
                <th>User</th>
                <th>Timestamp</th>
                <th>Feedback</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($all_feedback) && is_array($all_feedback)): ?>
                <?php $counter = 1; // Initialize counter ?>
                <?php foreach ($all_feedback as $feedback): ?>
                <tr>
                    <td><?php echo $counter++; ?></td> <!-- Display the counter and increment it -->
                    <td>
                        <a href="index.php?page=feedback&action=edit&id=<?php echo $feedback['feedback_id']; ?>">
                            <?php echo htmlspecialchars($feedback['user_firstname'] . ' ' . $feedback['user_lastname']); ?>
                        </a>
                    </td>
                    <td><?php echo htmlspecialchars($feedback['timestamp']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($feedback['feedback'])); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4">No feedback available.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
<?php endif; ?>

<div id="subcontent">
    <?php
    switch ($action) {
        case 'edit':
            $feedback_id = isset($_GET['id']) ? $_GET['id'] : null;
            if ($feedback_id) {
                require_once 'edit-feedback.php';
            } else {
                echo "Invalid feedback ID.";
            }
            break;
        case 'archive':
            require_once 'view-archive.php';
            break;
        default:
            break;
    }
    ?>
</div>

<script>
    function showFeedbackResults(str) {
        const resultContainer = document.getElementById("search-result");
        if (str.length === 0) {
            resultContainer.innerHTML = "";
            return;
        }

        fetch('feedback-module/search-feedback.php?q=' + encodeURIComponent(str))
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error: ${response.status}`);
                }
                return response.text();
            })
            .then(data => {
                resultContainer.innerHTML = data;
            })
            .catch(error => {
                console.error("Error during feedback search:", error);
                resultContainer.innerHTML = `<p>An error occurred: ${error.message}</p>`;
            });
    }
</script>
