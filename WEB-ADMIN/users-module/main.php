<?php
$current_user_id = isset($_SESSION['username']) ? $user->get_user_id($_SESSION['username']) : null;

// Fetch all users
$userList = $user->list_users();
$current_user_access = $current_user_id ? $user->get_user_access($current_user_id) : null;
?>

<h1>User List</h1>
<table border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Access Level</th>
            <th>Username</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($userList !== false && count($userList) > 0): ?>
            <?php foreach ($userList as $index => $value): ?>
                <?php
                $user_firstname = htmlspecialchars($value['user_firstname'] ?? 'N/A');
                $user_lastname = htmlspecialchars($value['user_lastname'] ?? 'N/A');
                $user_access = htmlspecialchars($value['user_access'] ?? 'N/A');
                $username = htmlspecialchars($value['username'] ?? 'N/A');
                $user_id = $value['user_id'];
                ?>

                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td>
                        <?php if ($current_user_access != "Secretary" && $current_user_access != "User"): ?>
                           <a href="index.php?page=settings&action=profile&id=<?php echo $user_id; ?>">
                        <?php echo $user_firstname . ' ' . $user_lastname; ?>
                            </a>
                        <?php else: ?>
                             <?php echo $user_firstname . ' ' . $user_lastname; ?>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $user_access; ?></td>
                    <td><?php echo $username; ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">No Record Found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

