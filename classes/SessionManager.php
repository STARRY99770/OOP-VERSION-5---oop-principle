<?php
class SessionManager {
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function getCurrentUser() {
        return isset($_SESSION['admin_id']) ? htmlspecialchars($_SESSION['admin_id']) : 'Guest';
    }
}