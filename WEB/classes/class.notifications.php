<?php
class Notification {
    private $conn;

    public function __construct() {
        // Include the config file with DB credentials
        include(__DIR__ . '/../config/config.php'); 

        // Initialize the database connection using credentials from config.php
        try {
            $this->conn = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_DATABASE, DB_USERNAME, DB_PASSWORD);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    // Function to send a notification by user ID
    public function send_notification_by_username($user_id, $message) {
        $sql = "INSERT INTO notifications (user_id, message) VALUES (:user_id, :message)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['user_id' => $user_id, 'message' => $message]);
    }

    // Function to get notifications by username
    public function get_notifications_by_username($username, $only_unread = false) {
        $sql = "SELECT * FROM notifications WHERE user_id = (SELECT user_id FROM tbl_users WHERE username = :username)";
        if ($only_unread) {
            $sql .= " AND is_read = 0";
        }
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['username' => $username]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Function to mark a notification as read
    public function mark_as_read($notification_id) {
        $sql = "UPDATE notifications SET is_read = 1 WHERE notification_id = :notification_id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['notification_id' => $notification_id]);
    }

    // Function to mark all notifications as read by username
    public function mark_all_as_read_by_username($username) {
        $sql = "UPDATE notifications SET is_read = 1 WHERE user_id = (SELECT user_id FROM tbl_users WHERE username = :username)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['username' => $username]);
    }

    // Function to delete a notification
    public function delete_notification($notification_id) {
        $sql = "DELETE FROM notifications WHERE notification_id = :notification_id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['notification_id' => $notification_id]);
    }

    // Function to delete all notifications for a user
    public function delete_all_notifications_by_username($username) {
        $sql = "DELETE FROM notifications WHERE user_id = (SELECT user_id FROM tbl_users WHERE username = :username)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['username' => $username]);
    }

     // Function to retrieve all active user IDs
     public function get_all_active_user_ids() {
        $sql = "SELECT user_id FROM tbl_users WHERE user_status = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }
}
