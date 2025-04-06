<?php
class FormFilter {
    public static function getFilter() {
        try {
            if (!isset($_GET['filter'])) {
                throw new Exception("Filter parameter is missing.");
            }
            return $_GET['filter'];
        } catch (Exception $e) {
            return ""; // Default to empty filter if an exception occurs
        }
    }
}