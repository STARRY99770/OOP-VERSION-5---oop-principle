<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

try {
    $db = new PDO('mysql:host=localhost;dbname=foreign_workers;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $user_id = $_SESSION['user_id'];

    $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = :user_id AND is_read = 0");
    $stmt->execute(['user_id' => $user_id]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>