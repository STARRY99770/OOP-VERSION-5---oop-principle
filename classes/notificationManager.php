<?php
class NotificationManager {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    // 插入通知
    public function addNotification($user_id, $message) {
        $stmt = $this->conn->prepare("INSERT INTO notifications (user_id, message, is_read) VALUES (?, ?, 0)");
        $stmt->bind_param("ss", $user_id, $message);
        $stmt->execute();
    }

    // 获取用户的未读通知
    public function getUnreadNotifications($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    // 标记通知为已读
    public function markNotificationsAsRead($user_id) {
        $stmt = $this->conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
    }
}
?>