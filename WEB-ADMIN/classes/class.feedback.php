<?php
class Feedback {

    private $conn;

    public function __construct() {
        // Include the config file with DB credentials
        include(__DIR__ . '/../config/config.php'); 

        // Use the credentials from config.php
        try {
            $this->conn = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_DATABASE, DB_USERNAME, DB_PASSWORD);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    // Insert new feedback
    public function new_feedback($user_id, $feedback){
        $sql = "INSERT INTO user_feedback (user_id, feedback) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$user_id, $feedback]);
    }

    // Update feedback
    public function update_feedback($feedback_id, $feedback){
        $sql = "UPDATE user_feedback SET feedback = ? WHERE feedback_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$feedback, $feedback_id]);
    }

    // Archive feedback instead of deleting
    public function archive_feedback($feedback_id){
        $sql = "UPDATE user_feedback SET archived = 1 WHERE feedback_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$feedback_id]);
    }

    // Retrieve feedback for a user, excluding archived feedback
    public function get_feedback_by_user($user_id){
        $sql = "SELECT uf.feedback_id, uf.feedback, uf.timestamp, tu.user_firstname, tu.user_lastname
                FROM user_feedback uf
                JOIN tbl_users tu ON uf.user_id = tu.user_id
                WHERE uf.user_id = ? AND uf.archived = 0";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Retrieve all feedback, excluding archived feedback
    public function get_all_feedback(){
        $sql = "SELECT uf.feedback_id, uf.feedback, uf.timestamp, tu.user_firstname, tu.user_lastname
                FROM user_feedback uf
                JOIN tbl_users tu ON uf.user_id = tu.user_id
                WHERE uf.archived = 0
                ORDER BY uf.timestamp DESC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Retrieve all archived feedback
    public function get_archived_feedback(){
        $sql = "SELECT uf.feedback_id, uf.feedback, uf.timestamp, tu.user_firstname, tu.user_lastname
                FROM user_feedback uf
                JOIN tbl_users tu ON uf.user_id = tu.user_id
                WHERE uf.archived = 1
                ORDER BY uf.timestamp DESC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function restore_feedback($feedback_id) {
        $sql = "UPDATE user_feedback SET archived = 0 WHERE feedback_id = :feedback_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':feedback_id', $feedback_id);
        return $stmt->execute();
    }
    
      public function count_feedback() {
        $sql = "SELECT COUNT(*) FROM user_feedback"; // Adjusted to the correct table name
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function search_feedback($keyword) {
        $stmt = $this->conn->prepare('
            SELECT uf.feedback_id, uf.feedback, uf.timestamp, tu.user_firstname, tu.user_lastname
            FROM user_feedback uf
            JOIN tbl_users tu ON uf.user_id = tu.user_id
            WHERE (uf.feedback LIKE ? OR tu.user_firstname LIKE ? OR tu.user_lastname LIKE ?)
            AND uf.archived = 0
    ');
        $searchKeyword = "%$keyword%";
        $stmt->bindValue(1, $searchKeyword, PDO::PARAM_STR);
        $stmt->bindValue(2, $searchKeyword, PDO::PARAM_STR);
        $stmt->bindValue(3, $searchKeyword, PDO::PARAM_STR);
        $stmt->execute();
    
         return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }  
}
