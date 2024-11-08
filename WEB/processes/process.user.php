<?php
include '../classes/class.user.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

switch($action){
    case 'new':
        create_new_user();
        break;
    case 'update':
        update_user();
        break;
    case 'status':
        change_user_status();
        break;
    case 'updatepassword':
        change_user_password();
        break;
    case 'updateusername':
        change_username();
        break;
    case 'profile':
        view_user_profile($id);
        break;
}

function create_new_user(){
    $user = new User();
    $username = $_POST['username'];
    $lastname = ucwords($_POST['lastname']);
    $firstname = ucwords($_POST['firstname']); // Ensure the firstname is captured properly
    $access = $_POST['access'];
    $password = $_POST['password'];
    $confirmpassword = $_POST['confirmpassword'];
    $address = ucwords($_POST['address']);
    $city = ucwords($_POST['city']);

    // Check if passwords match before proceeding
    if ($password !== $confirmpassword) {
        header('location: ../index.php?page=settings&error=password_mismatch');
        exit();
    }

    // Proceed with user creation
    $result = $user->new_user($username, $password, $lastname, $firstname, $access, $address, $city);
    if ($result) {
        // Redirect to the user list after successful creation
        header('location: ../index.php?page=settings');
    } else {
        // Redirect to the users page with a general error message
        header('location: ../index.php?page=settings&error=creation_failed');
    }
}

function update_user(){
    $user = new User();
    $user_id = $_POST['userid'];
    $lastname = ucwords($_POST['lastname']);
    $firstname = ucwords($_POST['firstname']);
    $access = ucwords($_POST['access']);
    $address = ucwords($_POST['address']);
    $city = ucwords($_POST['city']);

   
    $result = $user->update_user($lastname, $firstname, $access, $user_id, $address, $city);
    if ($result) {
        // Redirect to the user list after successful update
        header('location: ../index.php?page=settings');
    }
}

function change_user_status(){
    $user = new User();
    $id = isset($_GET['id']) ? $_GET['id'] : '';
    $status = isset($_GET['status']) ? $_GET['status'] : '';
    $result = $user->change_user_status($id, $status);
    if ($result) {
        // Redirect to the user list after status change
        header('location: ../index.php?page=settings');
    }
}

function change_user_password(){
    $user = new User();
    $id = $_POST['userid'];
    $current_password = $_POST['crpassword'];
    $new_password = $_POST['npassword'];
    $confirm_password = $_POST['copassword'];
    $result = $user->change_password($id, $new_password);
    if ($result) {
        // Redirect to the user list after password change
        header('location: ../index.php?page=settings');
    }
}

function change_username(){
    $user = new User();
    $id = $_POST['userid'];
    $current_username = $_POST['username'];
    $new_username = $_POST['newusername'];
    $current_password = $_POST['crpassword'];
    $result = $user->change_username($id, $new_username);
    if ($result) {
        // Redirect to the user list after username change
        header('location: ../index.php?page=settings');
    }
}

function delete_user(){
    if (isset($_POST['userid']) && is_numeric($_POST['userid'])) {
        $user = new User();
        $userid = $_POST['userid'];
        $result = $user->delete_user($userid);
        if ($result) {
            // Redirect to the user list after deletion
            header('location: ../index.php?page=settings');
        }
    }
function view_user_profile($id) {
    $user = new User();
    if (!$id) {
        echo "Error: User ID is not specified.";
        exit;
    }

    $userDetails = $user->get_user_by_id($id);
    if (!$userDetails) {
        echo "Error: User with ID $id not found.";
        exit;
    }

    // Assuming there's a `view-profile.php` to display user details
    include 'users-module/view-profile.php';
}

}
