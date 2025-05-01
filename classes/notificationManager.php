<?php
class NotificationManager {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function addNotification($user_id, $message) {
        $stmt = $this->conn->prepare("INSERT INTO notifications (user_id, message, is_read) VALUES (?, ?, 0)");
        $stmt->bind_param("ss", $user_id, $message);
        $stmt->execute();
    }

    public function getUnreadNotifications($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function markNotificationsAsRead($user_id) {
        $stmt = $this->conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
    }
}
?>