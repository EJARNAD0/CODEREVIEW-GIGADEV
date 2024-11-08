<?php
require_once '../classes/class.donations.php';

try {
    $q = isset($_GET['q']) ? trim($_GET['q']) : '';

    $donation_obj = new Donations();

    if (!empty($q)) {
        $data = $donation_obj->search_donations($q);

        if (!empty($data)) {
            $output = '<h3>Search Result(s)</h3>';
            $output .= '<table id="data-list">';
            $output .= '<tr>
                        <th>No.</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Amount</th>
                        <th>Purpose</th>
                        <th>Timestamp</th>
                        <th>Reference Number</th>
                      </tr>';

            $counter = 1;
            foreach ($data as $donation) {
                $output .= '<tr>';
                $output .= '<td>' . $counter++ . '</td>';
                $output .= '<td><a href="index.php?page=donation&action=view&id=' . htmlspecialchars($donation['donation_id']) . '">'
                         . htmlspecialchars($donation['user_firstname']) . '</a></td>';
                $output .= '<td>' . htmlspecialchars($donation['user_lastname']) . '</td>';
                $output .= '<td>' . htmlspecialchars($donation['amount']) . '</td>';
                $output .= '<td>' . htmlspecialchars($donation['purpose']) . '</td>';
                $output .= '<td>' . htmlspecialchars($donation['timestamp']) . '</td>';
                $output .= '<td>' . htmlspecialchars($donation['reference_number']) . '</td>';
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
