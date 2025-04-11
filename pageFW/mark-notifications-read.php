<?php
header('Content-Type: application/json');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $user_id = $input['user_id'] ?? null;

    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
        exit;
    }

    try {
        $db = new PDO('mysql:host=localhost;dbname=foreign_workers;charset=utf8', 'root', '');
        $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = :user_id AND is_read = 0");
        $stmt->execute(['user_id' => $user_id]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>