<?php
require_once '../classes/class.feedback.php';
try {
    $q = isset($_GET['q']) ? trim($_GET['q']) : '';

    $feedback_obj = new Feedback();

    if (!empty($q)) {
        $data = $feedback_obj->search_feedback($q);

        if (!empty($data)) {
            $output = '<h3>Search Result(s)</h3>';
            $output .= '<table id="data-list">';
            $output .= '<tr>
                        <th>No.</th> <!-- Changed column header -->
                        <th>User</th>
                        <th>Timestamp</th>
                        <th>Feedback</th>
                      </tr>';

            $counter = 1; // Initialize counter
            foreach ($data as $feedback) {
                $output .= '<tr>';
                $output .= '<td>' . $counter++ . '</td>'; // Display and increment counter
                $output .= '<td><a href="index.php?page=feedback&action=edit&id=' . htmlspecialchars($feedback['feedback_id']) . '">'
                         . htmlspecialchars($feedback['user_firstname'] . ' ' . $feedback['user_lastname']) . '</a></td>';
                $output .= '<td>' . htmlspecialchars($feedback['timestamp']) . '</td>';
                $output .= '<td>' . nl2br(htmlspecialchars($feedback['feedback'])) . '</td>';
                $output .= '</tr>';
            }
            $output .= '</table>';
        } else {
            $output = '<p>No results found for "' . htmlspecialchars($q) . '".</p>';
        }
    } else {
        $output = '<p>Please enter a search query.</p>';
    }

    echo $output;

} catch (Exception $e) {
    echo '<p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
}
?>
