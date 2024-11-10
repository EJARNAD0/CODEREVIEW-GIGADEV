<div id="third-submenu">
    <div class="submenu-actions">
        <a href="index.php?page=settings" id="list-users-link">
            <i class="fa fa-list"></i> List Users
        </a>
        
        <?php
        // Check if the user is not a Secretary or has other restrictions
        if ($user->get_user_access($user_id) != "Secretary" && $user->get_user_access($user_id) != "User") {
            ?>
            <a href="index.php?page=settings&action=create" id="new-user-link">
                <i class="fa fa-plus-circle"></i> Create Account
            </a>
            <?php
        }
        ?>
    </div>
    
    <div id="search-container">
        <div class="search-wrapper">
            <input type="text" id="search" name="search" onkeyup="showResults(this.value)" placeholder="Search...">
            <i class="fas fa-search search-icon"></i> <!-- Updated to Font Awesome icon -->
        </div>
    </div>
</div>

<!-- Search Result Container -->
<div id="search-result"></div>

<div id="subcontent">
    <?php
    $action = isset($_GET['action']) ? $_GET['action'] : '';

    switch ($action) {
        case 'create':
            require_once 'create-user.php'; // Create user page
            break;
        case 'profile':
            require_once 'view-profile.php'; // View profile page
            break;
        case 'result':
            require_once 'search.php'; // Search results page
            break;
        default:
            require_once 'main.php'; // Default page listing all users
            break;
    }
    ?>
</div>

<script>
    // Function to display search results based on user input
    function showResults(str) {
        const resultContainer = document.getElementById("search-result");
        if (str.length === 0) {
            resultContainer.innerHTML = "";
            return;
        }

        fetch('users-module/search.php?q=' + encodeURIComponent(str))
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
                console.error("Error during the search:", error);
                resultContainer.innerHTML = `<p>An error occurred: ${error.message}</p>`;
            });
    }

    // Hide search bar when "Create" or "Profile" actions are active
    function toggleSearchBarVisibility() {
        const searchContainer = document.getElementById('search-container');
        const urlParams = new URLSearchParams(window.location.search);
        const action = urlParams.get('action');

        if (action === 'create' || action === 'profile') {
            searchContainer.style.display = 'none'; // Hide search bar
        } else {
            searchContainer.style.display = 'block'; // Show search bar for other pages
        }
    }

    // Call the function on page load to set the search bar visibility
    window.onload = toggleSearchBarVisibility;
</script>
