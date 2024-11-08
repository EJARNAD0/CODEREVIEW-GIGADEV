<?php
// Include database connection (adjust this to your DB connection)
$conn = new mysqli('localhost:3306', 'wanderej_plato', 'Demesa123', 'wanderej_plato');

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Enable error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Fetch all users with user_status = 1
    $sql = "SELECT user_id, user_firstname, user_lastname FROM tbl_users WHERE user_status = 1";
    $result = $conn->query($sql);

    if ($result === false) {
        throw new Exception("Query error: " . $conn->error);
    }
} catch (Exception $e) {
    die("Error fetching users: " . $e->getMessage());
}
?>
<h3>Send Notification</h3>
    <div id="form-block-center">
        <form action="processes/process.notifications.php" method="POST">
            
            <input type="hidden" name="action" value="send">
            
            <label for="recipient">Send To:</label>
            <select class="input" id="recipient" name="recipient" required>
                <option value="all">All Users</option>
                <option value="specific">Specific User</option>
            </select>

            <div id="username-block" style="display:none;">
                <label for="user-search">Search and Select User:</label>
                <input type="text" id="user-search" placeholder="Search users..." class="input" onkeyup="filterUsers()">

                <div id="user-list">
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "
                            <label class='user-item'>
                                <input type='checkbox' name='users[]' value='" . $row['user_id'] . "'>
                                <span>" . htmlspecialchars($row['user_firstname'] . " " . $row['user_lastname']) . "</span>
                            </label>";
                        }
                    } else {
                        echo "<p>No users available</p>";
                    }
                    ?>
                </div>
            </div>

            <label for="message">Message:</label>
            <textarea class="input" id="message" name="message" rows="4" placeholder="Enter your message" required></textarea>

            <input type="submit" value="Send Notification">
        </form>
    </div>
</main>

<style>
    /* Styles for the user list and items */
    #user-list {
        border: 1px solid #ccc;
        border-radius: 5px;
        max-height: 200px;
        overflow-y: auto;
        padding: 10px;
        margin-top: 10px;
    }

    .user-item {
        display: flex;
        align-items: center;
        padding: 8px;
        border-bottom: 1px solid #f0f0f0;
    }

    .user-item input {
        margin-right: 12px;
        transform: scale(1.2); /* Enlarges the checkbox slightly for better UI */
    }

    .user-item span {
        flex-grow: 1;
        font-size: 16px;
        color: #333;
    }

    #user-search {
        width: 100%;
        padding: 8px;
        margin-bottom: 5px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    #user-list p {
        text-align: center;
        color: #666;
        margin: 10px 0;
    }
</style>

<script>
    const recipientSelect = document.getElementById('recipient');
    const usernameBlock = document.getElementById('username-block');

    recipientSelect.addEventListener('change', function() {
        if (this.value === 'specific') {
            usernameBlock.style.display = 'block';
        } else {
            usernameBlock.style.display = 'none';
        }
    });

    function filterUsers() {
        const searchInput = document.getElementById('user-search').value.toLowerCase();
        const userItems = document.querySelectorAll('#user-list .user-item');

        userItems.forEach(item => {
            const userName = item.textContent.toLowerCase();
            item.style.display = userName.includes(searchInput) ? 'flex' : 'none';
        });
    }
</script>

<?php
$conn->close();
?>
