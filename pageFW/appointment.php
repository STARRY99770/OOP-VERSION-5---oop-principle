<?php
// Include database connection
require_once 'db_connection.php';

// Example: Approve/Reject logic
if (isset($_POST['action']) && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $action = $_POST['action']; // 'approve' or 'reject'

    // Update appointment status in the database
    $status = ($action === 'approve') ? 'Approved' : 'Rejected';
    $stmt = $db->prepare("UPDATE appointments SET status = ? WHERE user_id = ?");
    $stmt->execute([$status, $user_id]);

    // Insert notification into the notifications table
    $message = ($action === 'approve') ? "Your appointment has been approved." : "Your appointment has been rejected.";
    $stmt = $db->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    $stmt->execute([$user_id, $message]);

    echo "Appointment $status and notification sent.";
}
?>