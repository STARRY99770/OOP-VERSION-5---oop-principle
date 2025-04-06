<?php
class DatabaseBase {
    protected $connection;

    public function __construct($host, $username, $password, $database) {
        try {
            $this->connection = new mysqli($host, $username, $password, $database);
            if ($this->connection->connect_error) {
                throw new Exception("Connection failed: " . $this->connection->connect_error);
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function getConnection() {
        return $this->connection;
    }

    public function closeConnection() {
        $this->connection->close();
    }
}

class Database extends DatabaseBase {
    public function __construct() {
        parent::__construct("localhost", "root", "", "foreign_workers");
    }
}