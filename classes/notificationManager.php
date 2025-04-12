<?php
class NotificationManager {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function addNotification($user_id, $message) {
        $query = "INSERT INTO notifications (user_id, message, created_at) VALUES (?, ?, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $user_id, $message); // 修改为 "ss"

        if (!$stmt->execute()) {
            throw new Exception("Failed to add notification: " . $stmt->error);
        }
    }
}
?>