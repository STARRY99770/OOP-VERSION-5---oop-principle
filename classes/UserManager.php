<?php
class UserManager {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getCurrentUser($session) {
        if (!isset($session['admin_id'])) {
            throw new Exception("User session is not set.");
        }

        $admin_id = $session['admin_id'];
        $stmt = $this->conn->prepare("SELECT admin_id FROM login_h_i_staff WHERE admin_id = ?");
        $stmt->bind_param("s", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $stmt->close();
            return htmlspecialchars($row['admin_id']);
        } else {
            $stmt->close();
            throw new Exception("No user found with the provided admin ID.");
        }
    }
}
?>