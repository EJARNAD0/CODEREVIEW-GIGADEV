<?php
require_once '../classes/class.request.php';

try {
    $q = isset($_GET['q']) ? trim($_GET['q']) : '';
    error_log("Search query: " . $q); // Debugging output

    $request_obj = new Request();

    if (!empty($q)) {
        $data = $request_obj->search_requests($q);
        error_log("Search results: " . print_r($data, true)); // Debugging output

        if (!empty($data)) {
            $output = '<h3>Search Result(s)</h3>';
            $output .= '<table id="data-list">';
            $output .= '<tr>
                        <th>No.</th>
                        <th>Name</th>
                        <th>Request Details</th>
                        <th>Status</th>
                        <th>Created At</th>
                      </tr>';

            $counter = 1;
            foreach ($data as $request) {
                $output .= '<tr>';
                $output .= '<td>' . $counter++ . '</td>';
                $output .= '<td><a href="index.php?page=request&action=view&id=' . htmlspecialchars($request['request_id']) . '">'
                         . htmlspecialchars($request['user_firstname']) . ' ' . htmlspecialchars($request['user_lastname']) . '</a></td>';
                $output .= '<td>' . htmlspecialchars($request['request_details']) . '</td>';
                $output .= '<td>' . ucfirst(htmlspecialchars($request['request_status'])) . '</td>';
                $output .= '<td>' . htmlspecialchars($request['created_at']) . '</td>';
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
