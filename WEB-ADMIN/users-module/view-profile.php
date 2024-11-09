<?php
include_once 'classes/class.user.php';

// Check if user ID is passed from URL or session
$id = isset($_GET['id']) ? $_GET['id'] : (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null);

$user = new User(); // Assuming User class is correctly instantiated here

// Check if ID is valid and user exists
if ($id && $user->get_username($id)) {
?>
<h3>System User Profile</h3>
<div class="btn-box">
    <a class="btn-jsactive" onclick="document.getElementById('id03').style.display='block'">
        <i class="fas fa-user-edit"></i> Change Username
    </a> |
    <a class="btn-jsactive" onclick="document.getElementById('id02').style.display='block'">
        <i class="fas fa-lock"></i> Change Password
    </a> |

    <?php if ($user->get_user_status($id) == "1") { ?>
        <a class="btn-jsactive" onclick="document.getElementById('id01').style.display='block'">
            <i class="fas fa-user-slash"></i> Disable Account
        </a>
    <?php } else { ?>
        <a class="btn-jsactive" onclick="document.getElementById('id01').style.display='block'">
            <i class="fas fa-user-check"></i> Activate Account
        </a>
    <?php } ?>
</div>
<div id="form-block">
    <form method="POST" action="processes/process.user.php?action=update">
        <?php if ($user->get_user_access($id) != "Secretary") { ?>
        <div id="form-block-half">
            <label for="fname">First Name</label>
            <input type="text" id="fname" class="input" name="firstname" value="<?php echo htmlspecialchars($user->get_user_firstname($id));?>" placeholder="Your name..">

            <label for="lname">Last Name</label>
            <input type="text" id="lname" class="input" name="lastname" value="<?php echo htmlspecialchars($user->get_user_lastname($id));?>" placeholder="Your last name..">

            <label for="access">Access Level</label>
            <select id="access" name="access">
                <option value="Secretary" <?php if ($user->get_user_access($id) == "Secretary") { echo "selected"; } ?>>Secretary</option>
                <option value="Admin" <?php if ($user->get_user_access($id) == "Admin") { echo "selected"; } ?>>Admin</option>
                <option value="Super Admin" <?php if ($user->get_user_access($id) == "Super Admin") { echo "selected"; } ?>>Super Admin</option>
            </select>
        </div>
        <?php } ?>
        <div id="form-block-half">
            <label for="status">Account Status</label>
            <select id="status" name="status" disabled>
                <option <?php if ($user->get_user_status($id) == "0") { echo "selected";};?>>Deactivated</option>
                <option <?php if ($user->get_user_status($id) == "1") { echo "selected";};?>>Active</option>
            </select>
            <label for="username">Username</label>
            <input type="text" id="username" class="input" name="username" disabled value="<?php echo htmlspecialchars($user->get_username($id));?>" placeholder="Your username..">
            <input type="hidden" id="userid" name="userid" value="<?php echo htmlspecialchars($id);?>"/>
        </div>

        <div id="button-container">
            <div id="button-block">
                <input type="submit" value="Update">
            </div>
        </div>
    </form>
</div>

<!-- Account Status Modal -->
<div id="id01" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2>Change User Status</h2>
    </div>
    <div class="modal-body">
      <p>Are you sure you want to change the user status?</p>
      <div class="modal-footer">
        <?php if($user->get_user_status($id) == "1") { ?>
          <button class="confirmbtn" onclick="disableSubmit()">Confirm</button>
        <?php } else { ?>
          <button class="confirmbtn" onclick="enableSubmit()">Confirm</button>
        <?php } ?>
        <button class="cancelbtn" onclick="document.getElementById('id01').style.display='none'">Cancel</button>
      </div>
    </div>
  </div>
</div>
<!-- Password Change Modal with Security Enhancements -->
<!-- Password Change Modal with Enhanced Validation -->
<div id="id02" class="modal">
  <div id="form-update" class="modal-content">
    <div class="container">
      <h2>Update User Password</h2>
      <p>Provide required details...</p>
      <form method="POST" id="passwordForm" action="processes/process.user.php?action=updatepassword" onsubmit="return validatePassword()">
        <input type="hidden" id="userid" name="userid" value="<?php echo htmlspecialchars($id); ?>"/>
        
        <label for="crpassword">Current Password</label>
        <input type="password" id="crpassword" class="input" name="crpassword" placeholder="Current Password" required>
        
        <label for="npassword">New Password</label>
        <input type="password" id="npassword" class="input" name="npassword" placeholder="New Password" required oninput="checkPasswordStrength()">
        
        <div id="password-strength" style="display: none;">Password Strength: <span id="password-strength-text"></span></div>
        
        <label for="copassword">Confirm Password</label>
        <input type="password" id="copassword" class="input" name="copassword" placeholder="Confirm Password" required oninput="checkPasswordsMatch()">
        
        <!-- Show Password Toggle -->
        <input type="checkbox" onclick="togglePasswordVisibility()"> Show Password
        
        <!-- Password Requirements -->
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
      </form>
      
      <div class="clearfix">
        <button class="submitbtn" id="confirmPasswordBtn" disabled onclick="passwordSubmit()">Confirm</button>
        <button class="cancelbtn" onclick="document.getElementById('id02').style.display='none'">Cancel</button>
      </div>
    </div>
  </div>
</div>

<!-- Username Change Modal -->
<div id="id03" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2>Update Username</h2>
    </div>
    <div class="modal-body">
      <p>Provide required details...</p>
      <form method="POST" id="usernameForm" action="processes/process.user.php?action=updateusername">
        <input type="hidden" id="userid" name="userid" value="<?php echo htmlspecialchars($id);?>"/>
        <label for="username">Current Username</label>
        <input type="text" id="username" class="input" name="username" placeholder="Current Username"> 
        <label for="crpassword">Current Password</label>
        <input type="password" id="crpassword" class="input" name="crpassword" placeholder="Current Password"> 
        <label for="newusername">New Username</label>
        <input type="text" id="newusername" class="input" name="newusername" placeholder="New Username">           
      </form> 
      <div class="modal-footer">
        <button class="submitbtn" onclick="usernameSubmit()">Confirm</button>
        <button class="cancelbtn" onclick="document.getElementById('id03').style.display='none'">Cancel</button>
      </div>
    </div>
  </div>
</div>

<!-- Custom Alert Modal -->
<div id="customAlert" class="modal">
  <div class="modal-content">
    
    <div class="modal-body">
      <p id="alertMessage">Password does not meet the required strength. Please ensure it meets all requirements.</p>
    </div>
    <div class="modal-footer">
      <button class="confirmbtn" onclick="closeAlert()">OK</button>
    </div>
  </div>
</div>
<script>
  // Password Strength Check
  function checkPasswordStrength() {
    const password = document.getElementById('npassword').value;
    const strengthText = document.getElementById('password-strength-text');
    const strengthDiv = document.getElementById('password-strength');
    const strongPasswordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

    if (password.length === 0) {
      strengthDiv.style.display = "none";
    } else if (strongPasswordRegex.test(password)) {
      strengthText.textContent = "Strong";
      strengthText.style.color = "green";
      strengthDiv.style.display = "block";
    } else {
      strengthText.textContent = "Weak";
      strengthText.style.color = "red";
      strengthDiv.style.display = "block";
    }
  }

  // Password Matching Check
  function checkPasswordsMatch() {
    const newPassword = document.getElementById('npassword').value;
    const confirmPassword = document.getElementById('copassword').value;
    const submitButton = document.getElementById("confirmPasswordBtn");

    if (confirmPassword === "") {
      submitButton.disabled = true;
    } else if (newPassword === confirmPassword) {
      submitButton.disabled = false;
    } else {
      submitButton.disabled = true;
    }
  }

  // Toggle Password Visibility
  function togglePasswordVisibility() {
    const fields = ['crpassword', 'npassword', 'copassword'];
    fields.forEach(fieldId => {
      const field = document.getElementById(fieldId);
      field.type = field.type === 'password' ? 'text' : 'password';
    });
  }

  // Close modal if user clicks outside
  window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
      event.target.style.display = "none";
    }
  };

  // Redirect Functions
  function enableSubmit() {
    window.location.href = "processes/process.user.php?action=status&id=<?php echo htmlspecialchars($id);?>&status=1";
  }

  function disableSubmit() {
    window.location.href = "processes/process.user.php?action=status&id=<?php echo htmlspecialchars($id);?>&status=0";
  }

  // Show custom alert modal
  function showAlert(message) {
    const alertModal = document.getElementById('customAlert');
    document.getElementById('alertMessage').innerText = message;
    alertModal.style.display = 'block';
  }

  // Close custom alert modal
  function closeAlert() {
    document.getElementById('customAlert').style.display = 'none';
  }

  // Password Submit Function with Custom Alert
  function passwordSubmit() {
    const newPassword = document.getElementById('npassword').value;
    const confirmPassword = document.getElementById('copassword').value;
    const strongPasswordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

    if (!strongPasswordRegex.test(newPassword)) {
      showAlert("Password does not meet the required strength. Please ensure it meets all requirements.");
      return false;
    }
    if (newPassword !== confirmPassword) {
      showAlert("Passwords do not match!");
      return false;
    }
    document.getElementById("passwordForm").submit();
  }

  // Username Submit Function
  function usernameSubmit() {
    document.getElementById("usernameForm").submit();
  }
</script>
<?php } else { echo "User profile not found."; } ?>
