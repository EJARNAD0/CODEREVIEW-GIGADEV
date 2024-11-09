<?php
// Include the database connection
include 'db_connection.php'; // Adjust this path if necessary

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the data
    $firstname = $_POST['user_firstname']; // Ensure these match your form input names
    $lastname = $_POST['user_lastname'];
    $username = $_POST['username'];
    $password = $_POST['user_password'];
    $access = $_POST['user_access'];
    $status = $_POST['user_status'];
    $address = $_POST['user_address'];
    $city = $_POST['user_city'];

    // Hash the password before storing
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // SQL Insert query
    $sql = "INSERT INTO tbl_users (user_firstname, user_lastname, username, user_password, user_access, user_status, user_address, user_city)
            VALUES ('$firstname', '$lastname', '$username', '$hashedPassword', '$access', '$status', '$address', '$city')";

    // Set the response content type to JSON
    header('Content-Type: application/json');

    if (mysqli_query($conn, $sql)) {
        echo json_encode(['message' => 'User registered successfully!']);
    } else {
        echo json_encode(['message' => 'Error: ' . mysqli_error($conn)]);
    }
    mysqli_close($conn);
    exit; // Ensure no further output
}
?>
<h3>Registration</h3>
<p>Complete the form below and press the save button!</p>
<div id="form-block">
    <div id="error-message" style="color: red; display: none;"></div>
    <form id="registration-form" method="POST" action="processes/process.user.php?action=new">
        <div id="form-container">
            <div id="form-block-half">
                <label for="fname">First Name</label>
                <input type="text" id="fname" class="input" name="firstname" placeholder="Your name.." required>

                <label for="lname">Last Name</label>
                <input type="text" id="lname" class="input" name="lastname" placeholder="Your last name.." required>

                <label for="access">Access Level</label>
                <select id="access" name="access" required>
                    <option value="" disabled selected>Select access level</option>
                    <option value="Secretary">Secretary</option>
                    <option value="Admin">Admin</option>
                    <option value="Super admin">Super Admin</option>
                </select>
            </div>
            <div id="form-block-half">
                <label for="username">Username</label>
                <input type="text" id="username" class="input" name="username" placeholder="Your username.." required>

                <!-- Password Guidelines Section -->
                <div id="password-guidelines" style="color: gray; font-size: 0.9em; margin-top: 10px;">
                    <strong>Password Requirements:</strong>
                    <ul style="margin-top: 5px; padding-left: 20px;">
                        <li>At least 8 characters in length</li>
                        <li>At least one uppercase letter (A-Z)</li>
                        <li>At least one lowercase letter (a-z)</li>
                        <li>At least one number (0-9)</li>
                        <li>At least one special character (e.g., @, $, !, %, *, ?)</li>
                    </ul>
                </div>

                <label for="password">Password</label>
                <input type="password" id="password" class="input" name="password" placeholder="Enter password.." minlength="8" required>

                <label for="confirmpassword">Confirm Password</label>
                <input type="password" id="confirmpassword" class="input" name="confirmpassword" placeholder="Confirm password.." minlength="8" required>

                <!-- Show Password Checkbox -->
                <div>
                    <input type="checkbox" id="show-password" onclick="togglePasswordVisibility()"> Show Password
                </div>

                <div id="password-strength" style="color: green; display: none;">Password is strong</div>
                <div id="password-strength-weak" style="color: red; display: none;">Please meet the required password criteria.</div>
            </div>
        </div>
        <div id="form-container">
            <div id="form-block-half">
                <label for="address">Address</label>
                <input type="text" id="address" class="input" name="address" placeholder="Your address.." required>
            </div>
            <div id="form-block-half">
                <label for="city">City</label>
                <input type="text" id="city" class="input" name="city" placeholder="Your city.." required>
            </div>
        </div>
        <div id="button-block">
            <input type="submit" value="Save">
        </div>
    </form>
</div>

<script>
function validateForm(event) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmpassword').value;
    const errorMessage = document.getElementById('error-message');
    const passwordStrengthWeak = document.getElementById('password-strength-weak');
    const passwordStrength = document.getElementById('password-strength');

    // Regex for strong password
    const strongPasswordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

    // Check if passwords match
    if (password !== confirmPassword) {
        errorMessage.textContent = "Passwords do not match!";
        errorMessage.style.display = "block";
        passwordStrengthWeak.style.display = "none";
        passwordStrength.style.display = "none";
        event.preventDefault();
        return false;
    }

    // Check if password meets the required criteria
    if (!strongPasswordRegex.test(password)) {
        errorMessage.textContent = "Please enter a password that meets the required criteria.";
        errorMessage.style.display = "block";
        passwordStrengthWeak.style.display = "block";
        passwordStrength.style.display = "none";
        event.preventDefault();
        return false;
    }

    // Hide error message and weak strength indicator if password is strong
    errorMessage.style.display = "none";
    passwordStrengthWeak.style.display = "none";
    passwordStrength.style.display = "block";
    return true;
}

// Add form submission listener
document.getElementById('registration-form').addEventListener('submit', validateForm);

// Real-time password strength validation
document.getElementById('password').addEventListener('input', function () {
    const password = document.getElementById('password').value;
    const passwordStrengthWeak = document.getElementById('password-strength-weak');
    const passwordStrength = document.getElementById('password-strength');
    const strongPasswordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

    if (strongPasswordRegex.test(password)) {
        passwordStrength.style.display = "block";
        passwordStrengthWeak.style.display = "none";
    } else {
        passwordStrength.style.display = "none";
        passwordStrengthWeak.style.display = "block";
    }
});

// Toggle password visibility
function togglePasswordVisibility() {
    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('confirmpassword');
    const type = passwordField.type === 'password' ? 'text' : 'password';
    passwordField.type = type;
    confirmPasswordField.type = type;
}
</script>
