<?php
class Request {
    private $conn;

    public function __construct() {
        // Include the config file with DB credentials
        include(__DIR__ . '/../config/config.php'); 

        // Use the credentials from config.php
        $this->conn = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_DATABASE, DB_USERNAME, DB_PASSWORD);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // Insert a new request
	public function new_request($user_id, $request_details, $latitude, $longitude) {
    	$sql = "INSERT INTO requests (user_id, request_details, latitude, longitude) VALUES (?, ?, ?, ?)";
    	$stmt = $this->conn->prepare($sql);
    
    	// Execute the statement with all parameters
    	return $stmt->execute([$user_id, $request_details, $latitude, $longitude]);
	}

    // Update request details
    public function update_request($request_id, $request_details) {
        $sql = "UPDATE requests SET request_details = ? WHERE request_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$request_details, $request_id]);
    }

    // Update request status
    public function update_request_status($request_id, $request_status) {
        $sql = "UPDATE requests SET request_status = ? WHERE request_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$request_status, $request_id]);
    }

    // Delete request
    public function delete_request($request_id) {
        $sql = "DELETE FROM requests WHERE request_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$request_id]);
    }

    // Retrieve requests for a specific user
    public function get_requests_by_user($user_id) {
        $sql = "SELECT r.request_id, r.request_details, r.request_status, r.created_at, tu.user_firstname, tu.user_lastname
                FROM requests r
                JOIN tbl_users tu ON r.user_id = tu.user_id
                WHERE r.user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Retrieve all requests
    public function get_all_requests() {
        $sql = "SELECT r.request_id, r.request_details, r.request_status, r.created_at, tu.user_firstname, tu.user_lastname
                FROM requests r
                JOIN tbl_users tu ON r.user_id = tu.user_id
                ORDER BY r.created_at DESC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
	
	public function get_all_requests_for_mapping() {
   	 try {
        $sql = "SELECT r.request_status, r.request_details, r.latitude, r.longitude, 
                       tu.user_firstname, tu.user_lastname
                FROM requests r
                JOIN tbl_users tu ON r.user_id = tu.user_id
                WHERE r.request_status IN ('pending', 'approved', 'rejected')";
        
        $stmt = $this->conn->query($sql);

        // Fetch all results as an associative array
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return an empty array if no data found
        return $results ? $results : [];
    } catch (PDOException $e) {
        // Handle and log the error (you can use a logger here)
        error_log('Database query error: ' . $e->getMessage());
        return [];
    }
}

	private function determineStatus($status, $created_at) {
        // Example logic to determine if a request is new based on its creation time
        $createdTime = strtotime($created_at);
        $currentTime = time();
        
        // Assuming a request is considered new for 1 hour (3600 seconds)
        if ($currentTime - $createdTime < 3600) {
            return 'new'; // Mark as new if it's within the last hour
        }

        return $status; // Return the existing status if not new
    }

    // Retrieve request details by request ID
    public function get_request_details($request_id) {
        $sql = "SELECT r.request_id, r.user_id, r.request_details, r.request_status, 
                       r.created_at, tu.user_firstname, tu.user_lastname
                FROM requests r
                JOIN tbl_users tu ON r.user_id = tu.user_id
                WHERE r.request_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$request_id]);
    
        // Fetch a single record and return it
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            return $result;
        } else {
            return null; // Return null if no record found
        }
    }
    

    // Inside class Request
    public function add_notification($user_id, $message) {
        $sql = "INSERT INTO notifications (user_id, message) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$user_id, $message]);
    }

    public function get_notifications_by_user($user_id) {
        $sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function count_requests_by_month($month, $year) {
        $sql = "SELECT COUNT(*) FROM requests WHERE MONTH(created_at) = ? AND YEAR(created_at) = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$month, $year]);
        return $stmt->fetchColumn();
    }
    
    public function count_feedbacks_by_month($month, $year) {
        $sql = "SELECT COUNT(*) FROM user_feedback WHERE MONTH(timestamp) = ? AND YEAR(timestamp) = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$month, $year]);
        return $stmt->fetchColumn();
    }
    
    public function count_donations_by_month($month, $year) {
        $sql = "SELECT COUNT(*) FROM donations WHERE MONTH(timestamp) = ? AND YEAR(timestamp) = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$month, $year]);
        return $stmt->fetchColumn();
    }
	
	public function get_user_fullname($user_id) {
        $query = "SELECT user_firstname, user_lastname FROM tbl_users WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user['user_firstname'] . ' ' . $user['user_lastname'];
        }
        return null; // Return null if user not found
    }

    public function search_requests($query) {
        $sql = "SELECT r.request_id, r.request_details, r.request_status, r.created_at, tu.user_firstname, tu.user_lastname
                FROM requests r
                JOIN tbl_users tu ON r.user_id = tu.user_id
                WHERE r.request_details LIKE :query OR tu.user_firstname LIKE :query OR tu.user_lastname LIKE :query
                ORDER BY r.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':query', '%' . $query . '%');
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
 }
?>