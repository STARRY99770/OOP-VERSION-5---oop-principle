<?php
class NewsView {
    private $conn;
    private $table_name = "news_views";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create the news_views table if it doesn't exist
    public function createTable() {
        $query = "CREATE TABLE IF NOT EXISTS " . $this->table_name . " (
            id INT AUTO_INCREMENT PRIMARY KEY,
            news_id INT NOT NULL,
            view_count INT DEFAULT 0,
            last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_news (news_id)
        )";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute();
    }

    // Increment view count for a news item
    public function incrementView($news_id) {
        // First try to insert, if the news_id doesn't exist
        $query = "INSERT INTO " . $this->table_name . " (news_id, view_count) 
                 VALUES (:news_id, 1)
                 ON DUPLICATE KEY UPDATE view_count = view_count + 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":news_id", $news_id);
        return $stmt->execute();
    }

    // Get view count for a news item
    public function getViewCount($news_id) {
        $query = "SELECT view_count FROM " . $this->table_name . " WHERE news_id = :news_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":news_id", $news_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['view_count'] : 0;
    }
}
?> 