<?php
session_start();

include_once 'config/config.php';
include_once 'classes/class.user.php';

$user = new User();
$response = array(); // Initialize an array for the API response

// Check if the user is already logged in
if ($user->get_session()) {
    header("location: index.php");
    exit(); // Make sure to exit after redirection
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Check login credentials
    $login = $user->check_login($username, $password);

    // Handle API requests 
    if (isset($_POST['is_api']) && $_POST['is_api'] == 'true') {
    header('Content-Type: application/json'); 

    if ($login) {
        $user_id = (int)$user->get_user_id($username); 
        $userDetails = $user->get_user_by_id($user_id);

        $response['success'] = true;
        $response['message'] = 'Login successful.';
        $response['user_id'] = (int)$userDetails['user_id']; 
        $response['user_firstname'] = $userDetails['user_firstname'];
        $response['user_lastname'] = $userDetails['user_lastname'];
        $response['user_address'] = $userDetails['user_address'];
        $response['user_city'] = $userDetails['user_city'];
    } else {
        // Login failed
        $response['success'] = false;
        $response['message'] = 'Incorrect username or password.';
    }

    echo json_encode($response); // Return JSON response for API
    exit(); // Stop further processing for API request
}


    // Handle regular web login form submission
    if ($login) {
        // Successful login: set session and redirect to homepage
        $_SESSION['user'] = $username;
        $user_id = $user->get_user_id($username);
        $_SESSION['user_id'] = $user_id; // Store user ID in session
        header("location: index.php"); // Redirect to homepage
        exit();
    } else {
        // Failed login: set an error message for the HTML form
        $error_message = "Incorrect username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plato</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div id="login-block">
        <!-- Top Icon Header -->
        <div class="login-header">
            <div class="user-icon">
                <img src="images/mambulac.jpg" alt="User Icon">
            </div>
        </div>

        <form method="POST" action="">
            <div class="login-box">
                <p>Login Here</p>
                <!-- Error message display -->
                <?php if (isset($error_message)) { ?>
                    <div class="error-notif">
                        <span><?php echo $error_message; ?></span>
                    </div>
                <?php } ?>
                
                <div class="user-box">
                    <input type="text" name="username" required autocomplete="off">
                    <label>Username</label>
                </div>

                <div class="user-box">
                    <input type="password" id="password" name="password" required>
                    <label>Password</label>
                    <!-- Eye icon for show/hide password -->
                    <span class="toggle-password" id="togglePassword" onclick="togglePasswordVisibility()">
                        👁️ <!-- Initially set to open eye -->
                    </span>
                </div>

                <input type="submit" name="submit" value="Login">
            </div>
        </form>
    </div>
<script>
    document.addEventListener('contextmenu', (event) => event.preventDefault()); // Disable right-click
    document.addEventListener('keydown', function(e) {
        // Check if the key combination is one that you want to disable
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
    // Toggle password visibility
    function togglePasswordVisibility() {
        const passwordField = document.getElementById('password');
        const togglePassword = document.getElementById('togglePassword');
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            togglePassword.textContent = '🙈'; // Closed eye
        } else {
            passwordField.type = 'password';
            togglePassword.textContent = '👁️'; // Open eye
        }
    }
</script>
</body>
</html>
