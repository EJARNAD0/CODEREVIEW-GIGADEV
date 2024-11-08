<div id="third-submenu">
    <div class="submenu-actions">
        <a href="index.php?page=settings" id="list-users-link">
            <i class="fa fa-list"></i> List Users
        </a>
        
        <?php if (!in_array($user->get_user_access($user_id), ["Secretary", "User"]) && $id != $user_id): ?>
            <a href="index.php?page=settings&action=create" id="new-user-link">
                <i class="fa fa-plus-circle"></i> Create Account
            </a>
        <?php endif; ?>
    </div> 

    <div id="search-container">
        <div class="search-wrapper">
            <input type="text" id="search" name="search" onkeyup="showResults(this.value)" placeholder="Search...">
            <i class="fas fa-search search-icon"></i> 
        </div>
    </div>
</div> 

<div id="search-result"></div>

<div id="subcontent">
    <?php
    $action = $_GET['action'] ?? ''; 

    $actionMap = [
        'create' => 'create-user.php',
        'profile' => 'view-profile.php',
        'result' => 'search.php'
    ];

    $fileToInclude = $actionMap[$action] ?? 'main.php';
    require_once $fileToInclude; 
    ?>
</div>

<script>
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
    
    function toggleSearchBarVisibility() {
        const searchContainer = document.getElementById('search-container');
        const action = new URLSearchParams(window.location.search).get('action');

        // Hide the search bar for specific actions
        searchContainer.style.display = (action === 'create' || action === 'profile') ? 'none' : 'block';
    }

    window.onload = toggleSearchBarVisibility;
</script>

