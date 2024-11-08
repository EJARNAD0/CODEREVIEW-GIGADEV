<?php
class User {

    private $conn;

    public function __construct() {
        // Include the config file with DB credentials
        include(__DIR__ . '/../config/config.php'); 

        // Use the credentials from config.php
        $this->conn = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_DATABASE, DB_USERNAME, DB_PASSWORD);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // Insert new user
    public function new_user($username, $password, $lastname, $firstname, $access, $address, $city) {
        // Check if username already exists
        $checkUsername = $this->conn->prepare("SELECT COUNT(*) FROM tbl_users WHERE username = :username");
        $checkUsername->execute([':username' => $username]);
        $usernameExists = $checkUsername->fetchColumn();

        if ($usernameExists > 0) {
            throw new Exception("The username '$username' is already taken. Please choose a different username.");
        }

        // Hash the password using bcrypt
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Set timezone and format the date
        $NOW = new DateTime('now', new DateTimeZone('Asia/Manila'));
        $NOW = $NOW->format('Y-m-d H:i:s');

        $stmt = $this->conn->prepare("INSERT INTO tbl_users 
            (user_lastname, user_firstname, username, user_password, user_address, user_city, user_date_added, user_date_updated, user_status, user_access) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        try {
            $this->conn->beginTransaction();
            $stmt->execute([$lastname, $firstname, $username, $hashedPassword, $address, $city, $NOW, $NOW, '1', $access]);
            $this->conn->commit();
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }

        return true;
    }

    // Update user details
    public function update_user($lastname, $firstname, $access, $id, $address, $city) {
        $NOW = new DateTime('now', new DateTimeZone('Asia/Manila'));
        $NOW = $NOW->format('Y-m-d H:i:s');

        $sql = "UPDATE tbl_users SET user_firstname = :user_firstname, user_lastname = :user_lastname, user_access = :user_access, 
                user_address = :user_address, user_city = :user_city, user_date_updated = :user_date_updated WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':user_firstname' => $firstname,
            ':user_lastname' => $lastname,
            ':user_access' => $access,
            ':user_address' => $address,
            ':user_city' => $city,
            ':user_date_updated' => $NOW,
            ':user_id' => $id
        ]);

        return true;
    }

   // Search users by keyword (case-insensitive)
public function list_users_search($keyword) {
    $stmt = $this->conn->prepare('
        SELECT * FROM tbl_users 
        WHERE LOWER(username) LIKE LOWER(?) 
        OR LOWER(user_firstname) LIKE LOWER(?) 
        OR LOWER(user_lastname) LIKE LOWER(?)
        OR LOWER(user_access) LIKE LOWER(?)
    ');
    
    $searchKeyword = "%$keyword%";
    $stmt->bindValue(1, $searchKeyword, PDO::PARAM_STR);
    $stmt->bindValue(2, $searchKeyword, PDO::PARAM_STR);
    $stmt->bindValue(3, $searchKeyword, PDO::PARAM_STR);
    $stmt->bindValue(4, $searchKeyword, PDO::PARAM_STR);
    
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}
    // Change user status
    public function change_user_status($id, $status) {
        $NOW = new DateTime('now', new DateTimeZone('Asia/Manila'));
        $NOW = $NOW->format('Y-m-d H:i:s');

        $sql = "UPDATE tbl_users SET user_status = :user_status, user_date_updated = :user_date_updated WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':user_status' => $status,
            ':user_date_updated' => $NOW,
            ':user_id' => $id
        ]);

        return true;
    }

    // Change username
    public function change_username($id, $username) {
        $NOW = new DateTime('now', new DateTimeZone('Asia/Manila'));
        $NOW = $NOW->format('Y-m-d H:i:s');

        $sql = "UPDATE tbl_users SET username = :username, user_date_updated = :user_date_updated WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':username' => $username,
            ':user_date_updated' => $NOW,
            ':user_id' => $id
        ]);

        return true;
    }

    // Change user password
    public function change_password($id, $password) {
        $NOW = new DateTime('now', new DateTimeZone('Asia/Manila'));
        $NOW = $NOW->format('Y-m-d H:i:s');

        // Hash the password using bcrypt
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $sql = "UPDATE tbl_users SET user_password = :user_password, user_date_updated = :user_date_updated WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':user_password' => $hashedPassword,
            ':user_date_updated' => $NOW,
            ':user_id' => $id
        ]);

        return true;
    }

    // List all users
    public function list_users() {
        $sql = "SELECT user_id, user_firstname, user_lastname, username, user_address, user_city, user_access, user_status FROM tbl_users";
        $stmt = $this->conn->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: false;
    }

    // Delete user
    public function delete_user($id) {
        $sql = "DELETE FROM tbl_users WHERE user_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Get user ID by username
    public function get_user_id($username) {
        $sql = "SELECT user_id FROM tbl_users WHERE username = :username";    
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':username' => $username]);
        return $stmt->fetchColumn();
    }

    // Get username by ID
    public function get_username($id) {
        $sql = "SELECT username FROM tbl_users WHERE user_id = :id";  
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetchColumn();
    }

    // Get user firstname by ID
 public function get_user_firstname($id) {
    $sql = "SELECT user_firstname FROM tbl_users WHERE user_id = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([':id' => $id]);
    $result = $stmt->fetchColumn();
    if ($result === false) {
        echo "User with ID $id not found.";
    }
    return $result;
}


    // Get user lastname by ID
    public function get_user_lastname($id) {
        $sql = "SELECT user_lastname FROM tbl_users WHERE user_id = :id"; 
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetchColumn();
    }

    // Get user access by ID
    public function get_user_access($id) {
        $sql = "SELECT user_access FROM tbl_users WHERE user_id = :id";   
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetchColumn();
    }

    // Get user status by ID
    public function get_user_status($id) {
        $sql = "SELECT user_status FROM tbl_users WHERE user_id = :id";   
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetchColumn();
    }

    // Check if the user is logged in
    public function get_session() {
        return isset($_SESSION['login']) && $_SESSION['login'] === true;
    }

    // Check login credentials
public function check_login($username, $password) {
    // Retrieve the stored password hash and user status from the database
    $sql = "SELECT user_password, user_status FROM tbl_users WHERE username = :username";
    $q = $this->conn->prepare($sql);
    $q->execute(['username' => $username]);
    $user = $q->fetch(PDO::FETCH_ASSOC);

    // Check if user exists and verify the password
    if ($user && password_verify($password, $user['user_password'])) {
        // Check if the user is active (user_status = 1)
        if ($user['user_status'] != 1) {
            // If the account is disabled, prevent login
            return false; // Account is disabled
        }

        // Set session variables if login is successful
        $_SESSION['login'] = true;
        $_SESSION['username'] = $username;
        return true; // Login successful
    }

    return false; // Login failed (incorrect password or user doesn't exist)
}
    // Get full user data by ID
    public function get_user_by_id($user_id) {
        $sql = "SELECT * FROM tbl_users WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
   
    }

     public function count_users() {
        $sql = "SELECT COUNT(*) FROM tbl_users";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}