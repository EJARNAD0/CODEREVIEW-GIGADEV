<?php
require_once '../classes/class.user.php';

try {
    // Check if the query string is received
    $q = isset($_GET['q']) ? trim($_GET['q']) : '';

    // Proceed with normal flow if query is not empty
    if (!empty($q)) {
        $users = new User();
        $data = $users->list_users_search($q);

        if (!empty($data)) {
            $hint = '<h3>Search Result(s)</h3>';
            $hint .= '<table id="data-list">';
            $hint .= '<tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Access Level</th>
                        <th>Username</th>
                      </tr>';

            $count = 1;
            foreach ($data as $user) {
                $hint .= '<tr>';
                $hint .= '<td>' . $count . '</td>';
                $hint .= '<td><a href="index.php?page=settings&action=profile&id=' . htmlspecialchars($user['user_id']) . '">'
                         . htmlspecialchars($user['user_firstname']) . ' ' . htmlspecialchars($user['user_lastname']) . '</a></td>';
                $hint .= '<td>' . htmlspecialchars($user['user_access']) . '</td>';
                $hint .= '<td>' . htmlspecialchars($user['username']) . '</td>';
                $hint .= '</tr>';
                $count++;
            }
            $hint .= '</table>';
        } else {
            $hint = '<p>No results found for "' . htmlspecialchars($q) . '".</p>';
        }
    } else {
        $hint = '<p>Please enter a search query.</p>';
    }

    // Output the result
    echo $hint;

} catch (Exception $e) {
    // Display error message in case of any exceptions
    echo '<p>Error: ' . $e->getMessage() . '</p>';
}
?>
