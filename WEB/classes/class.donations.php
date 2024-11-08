<?php
class Donations {
    private $conn;

    public function __construct() {
        // Include the config file with DB credentials
        include(__DIR__ . '/../config/config.php'); 

        // Use the credentials from config.php
        $this->conn = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_DATABASE, DB_USERNAME, DB_PASSWORD);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // Insert a new donation with a unique reference number
    public function new_donation($user_id, $user_firstname, $user_lastname, $amount, $purpose, $status = 'pending') {
        // Generate a unique reference number
        $reference_number = $this->generate_reference_number();

        $sql = "INSERT INTO donations (user_id, user_firstname, user_lastname, amount, purpose, status, reference_number) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$user_id, $user_firstname, $user_lastname, $amount, $purpose, $status, $reference_number]);
    }


    private function generate_reference_number() {
        return uniqid('DON-', true); 
    }

    // Update donation details, including status
    public function update_donation($donation_id, $amount, $purpose, $status) {
        $sql = "UPDATE donations SET amount = ?, purpose = ?, status = ? WHERE donation_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$amount, $purpose, $status, $donation_id]);
    }

    // Delete a donation
    public function delete_donation($donation_id) {
        $sql = "DELETE FROM donations WHERE donation_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$donation_id]);
    }

    // Retrieve donations for a specific user
    public function get_donations_by_user($user_id) {
        $sql = "SELECT donation_id, user_firstname, user_lastname, amount, purpose, status, timestamp, reference_number 
                FROM donations 
                WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Retrieve all donations
    public function get_all_donations() {
        $sql = "SELECT donation_id, user_firstname, user_lastname, amount, purpose, status, timestamp, reference_number 
                FROM donations 
                ORDER BY timestamp DESC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Retrieve donation details by donation ID
    public function get_donation_details($donation_id) {
        $sql = "SELECT donation_id, user_id, user_firstname, user_lastname, amount, purpose, status, timestamp, reference_number 
                FROM donations 
                WHERE donation_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$donation_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC); // Fetch a single record
    }

    // Add a notification for a user
    public function add_notification($user_id, $message) {
        $sql = "INSERT INTO notifications (user_id, message) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$user_id, $message]);
    }

    // Retrieve all notifications for a specific user
    public function get_notifications_by_user($user_id) {
        $sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Retrieve user's full name by user_id
    public function get_user_fullname($user_id) {
        $sql = "SELECT user_firstname, user_lastname FROM tbl_users WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ? $user['user_firstname'] . ' ' . $user['user_lastname'] : null;
    }

    // Search donations by keyword
   public function search_donations($keyword) {
    $sql = "
        SELECT donation_id, user_firstname, user_lastname, amount, purpose, status, timestamp, reference_number 
        FROM donations 
        WHERE (user_firstname LIKE ? OR user_lastname LIKE ? OR purpose LIKE ? OR reference_number LIKE ?)
        ORDER BY timestamp DESC
    ";
    $stmt = $this->conn->prepare($sql);
    $searchKeyword = "%$keyword%";
    $stmt->execute([$searchKeyword, $searchKeyword, $searchKeyword, $searchKeyword]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
?>
