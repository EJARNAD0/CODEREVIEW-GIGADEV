<?php
$request_obj = new Request();  
$all_requests = $request_obj->get_all_requests(); 

// Determine action based on query parameter
$action = isset($_GET['action']) ? $_GET['action'] : '';
?>

<h1><?php echo ($action === 'view') ? 'View User Request' : 'All User Requests'; ?></h1>

<?php if ($action !== 'view'): ?>
<div id="search-container">
    <div class="search-wrapper">
        <input type="text" id="search" name="search" onkeyup="showRequestResults(this.value)" placeholder="Search...">
        <i class="fas fa-search search-icon"></i>
    </div>
</div>
<div id="search-result"></div> 
<table>
    <thead>
        <tr>
            <th>#</th> 
            <th>Name</th>
            <th>Request Details</th>
            <th>Status</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($all_requests) && is_array($all_requests)): ?>
            <?php $counter = 1; // Initialize counter ?>
            <?php foreach ($all_requests as $req): ?>
            <tr>
                <td><?php echo $counter++; // Increment counter for each row ?></td> <!-- Display the current counter value -->
                <td>
                    <a href="index.php?page=request&action=view&id=<?php echo $req['request_id']; ?>">
                        <?php echo htmlspecialchars($req['user_firstname'] . ' ' . $req['user_lastname']); ?>
                    </a>
                </td>
                <td><?php echo htmlspecialchars($req['request_details']); ?></td>
                <td><?php echo ucfirst($req['request_status']); ?></td>
                <td><?php echo htmlspecialchars($req['created_at']); ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">No requests found.</td> <!-- Adjust colspan to include the new column -->
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<?php endif; ?>

<div id="subcontent">
    <?php
    // View request details if in 'view' mode
    if ($action === 'view' && isset($_GET['id'])) {
        $request_id = $_GET['id'];
        require_once 'view-request.php';
    }
    ?>
</div>

<script>
    function showRequestResults(str) {
        const resultContainer = document.getElementById("search-result");
        if (str.length === 0) {
            resultContainer.innerHTML = "";
            return;
        }

        fetch('request-module/search-request.php?q=' + encodeURIComponent(str))
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
                console.error("Error during request search:", error);
                resultContainer.innerHTML = `<p>An error occurred: ${error.message}</p>`;
            });
    }
</script>
