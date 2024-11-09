<?php
$donation_obj = new Donations();
$all_donations = $donation_obj->get_all_donations(); 
$action = isset($_GET['action']) ? $_GET['action'] : '';
?>

<h1><?php echo ($action === 'view') ? 'View User Donation' : 'All User Donations'; ?></h1>

<?php if ($action !== 'edit' && $action !== 'view'): ?>
<div id="search-container">
    <div class="search-wrapper">
        <input type="text" id="search" name="search" onkeyup="showDonationResults(this.value)" placeholder="Search...">
        <i class="fas fa-search search-icon"></i>
    </div>
</div>
<div id="search-result"></div> 

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Amount</th>
            <th>Purpose</th>
            <th>Timestamp</th>
            <th>Reference Number</th>
        </tr>
    </thead>
    <tbody id="donations-table-body">
        <?php if (!empty($all_donations) && is_array($all_donations)): ?>
            <?php foreach ($all_donations as $index => $donation): ?>
    <tr>
        <td><?php echo $index + 1; ?></td>
        <td>
            <a href="index.php?page=donation&action=view&id=<?php echo $donation['donation_id']; ?>">
                <?php echo htmlspecialchars($donation['user_firstname']); ?>
            </a>
        </td>
        <td><?php echo htmlspecialchars($donation['user_lastname']); ?></td>
        <td><?php echo htmlspecialchars($donation['amount']); ?></td>
        <td><?php echo htmlspecialchars($donation['purpose']); ?></td>
        <td><?php echo htmlspecialchars($donation['timestamp']); ?></td>
        <td><?php echo htmlspecialchars($donation['reference_number']); ?></td>
    </tr>
<?php endforeach; ?>

        <?php else: ?>
            <tr><td colspan="7">No donations found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<?php endif; ?>
<div id="subcontent">
    <?php
    // This part should handle the 'view' action as well.
    switch ($action) {
        case 'view':
            require_once 'update_donation.php'; 
            break;
        case 'edit':
            // You may want to add code here for editing donations.
            break;
        default:
            // You can handle other actions or just leave it empty.
            break;
    }
    ?>
</div>
<script>
    function showDonationResults(str) {
        const resultContainer = document.getElementById("search-result");
        if (str.length === 0) {
            resultContainer.innerHTML = "";
            return;
        }

        fetch('donations-module/search-donations.php?q=' + encodeURIComponent(str))
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
                console.error("Error during donation search:", error);
                resultContainer.innerHTML = `<p>An error occurred: ${error.message}</p>`;
            });
    }
</script>

