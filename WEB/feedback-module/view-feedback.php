<?php
// Include your database connection or any necessary files here
require_once 'db_connection.php'; // Assuming this file handles database connection
date_default_timezone_set('Asia/Manila');

class Feedback {
    private $db;

    public function __construct() {
        $this->db = new mysqli('localhost:3306', 'wanderej_plato', 'Demesa123', 'wanderej_plato'); // Replace with your actual DB credentials
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }

    // Fetch all feedback with JOIN to get user details
    public function get_all_feedback() {
        $sql = "SELECT 
                    user_feedback.feedback_id, 
                    user_feedback.feedback, 
                    user_feedback.timestamp, 
                    tbl_users.user_firstname, 
                    tbl_users.user_lastname
                FROM user_feedback
                JOIN tbl_users ON user_feedback.user_id = tbl_users.user_id
                ORDER BY user_feedback.timestamp DESC"; // Order by latest feedback

        $result = $this->db->query($sql);

        $feedback_list = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $feedback_list[] = $row;
            }
        }
        return $feedback_list;
    }
}

// Instantiate the Feedback class
$feedback_obj = new Feedback();  

// Fetch all feedback including user details
$all_feedback = $feedback_obj->get_all_feedback();

// Set headers for JSON response
header('Content-Type: application/json');

// Output feedback as JSON
echo json_encode($all_feedback);
?>