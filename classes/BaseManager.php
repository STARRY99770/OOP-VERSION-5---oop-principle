<?php
class BaseManager {
    protected $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // 通用的查询执行方法，带有异常处理
    protected function executeQuery($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Failed to prepare statement: " . $this->conn->error);
            }
            if (!empty($params)) {
                $stmt->bind_param(...$params);
            }
            $stmt->execute();
            return $stmt;
        } catch (Exception $e) {
            throw new Exception("Database query failed: " . $e->getMessage());
        }
    }
}
?>