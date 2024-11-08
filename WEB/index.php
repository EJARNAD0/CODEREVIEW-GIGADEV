<?php
include_once 'config/config.php';
include_once 'classes/class.user.php';
include_once 'classes/class.feedback.php';
include_once 'classes/class.request.php';
include_once 'classes/class.notifications.php';
include_once 'classes/class.donations.php';

session_start();

$user = new User();

// Check if the user is logged in
if (!$user->get_session()) {
    header("location: login.php");
    exit();
}

// Check user access level
$user_id = $_SESSION['user_id'];
$user_access = $user->get_user_access($user_id);

// Redirect "user" level accounts to login with an error message
if ($user_access === 'user') {
    $_SESSION['error_message'] = "Access denied for user level accounts.";
    header("location: login.php");
    exit();
}

// Determine the page from URL parameter (default to 'home' if not set)
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Plato Admin Dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="css/style.css?<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&display=swap" rel="stylesheet">

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body>
    <div id="container">
        <header>
            <h1>Plato Data Management System</h1>
        </header>
        <div class="sidebar-toggle" onclick="toggleSidebar()">
            <i class="fa fa-bars"></i>
        </div>

        <!-- Sidebar Menu -->
        <div id="sidebar">
            <img src="images/mambulac.jpg" alt="Logo" class="logo">
            <ul class="menu">
                <li><a class="<?php echo $page === 'home' ? 'active' : ''; ?>" href="index.php?page=home"><i class="fa-solid fa-house-chimney"></i> Home</a></li>
                <li><a class="<?php echo $page === 'feedback' ? 'active' : ''; ?>" href="index.php?page=feedback"><i class="fa-brands fa-product-hunt"></i> Feedback</a></li>
                <li><a class="<?php echo $page === 'donation' ? 'active' : ''; ?>" href="index.php?page=donation"><i class="fa-solid fa-hand-holding-dollar"></i> Logs</a></li>
                <li><a class="<?php echo $page === 'request' ? 'active' : ''; ?>" href="index.php?page=request"><i class="fa-sharp fa-solid fa-warehouse"></i> Approval of Request</a></li>
                <li><a class="<?php echo $page === 'maps' ? 'active' : ''; ?>" href="index.php?page=maps"><i class="fa-solid fa-map-marked"></i> Maps</a></li>
                <li><a class="<?php echo $page === 'notification' ? 'active' : ''; ?>" href="index.php?page=notification"><i class="fa-sharp fa-regular fa-envelope"></i> Notification</a></li>
                <li><a class="<?php echo $page === 'settings' ? 'active' : ''; ?>" href="index.php?page=settings"><i class="fa-solid fa-gear"></i> Manage Account</a></li>
                <li><a href="logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Log Out</a></li>
            </ul>
        </div>

        <main>
            <?php
            // Include the appropriate module based on the value of $page
            switch($page) {
                case 'settings':
                    include 'users-module/index.php';
                    break;
                case 'feedback':
                    include 'feedback-module/index.php';
                    break;
                case 'donation':
                    include 'donations-module/index.php';
                    break;
                case 'request':
                    include 'request-module/index.php';
                    break;
                case 'maps':
                    include 'maps-module/index.php';
                    break;
                case 'notification':
                    include 'notifications-module/index.php';
                    break;
                case 'home':
                default:
                    include 'home.php';
                    break;
            }
            ?>
        </main>
    </div>

    <script>
        document.addEventListener('contextmenu', (event) => event.preventDefault()); // Disable right-click

        document.addEventListener('keydown', function(e) {
            if (
                e.key === 'F12' || // Disable F12
                (e.ctrlKey && e.shiftKey && e.key === 'I') || // Disable Ctrl+Shift+I (Inspect)
                (e.ctrlKey && e.shiftKey && e.key === 'J') || // Disable Ctrl+Shift+J (Console)
                (e.ctrlKey && (e.key === 'U' || e.keyCode === 85)) // Disable Ctrl+U (View Source)
            ) {
                e.preventDefault(); // Prevent default behavior
                e.stopPropagation(); // Stop event from bubbling up
                return false; // Additional return to ensure prevention
            }
        });
    </script>
    <script src="js/script.js"></script>
</body>
</html>
