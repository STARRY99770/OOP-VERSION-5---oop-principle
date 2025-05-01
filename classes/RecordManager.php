<?php

abstract class RecordManagerBase {
    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    abstract public function getAllRecords();
}

class RecordManager extends RecordManagerBase {
    public function getAllRecords() {
        $connection = $this->db->getConnection();
        $sql = "SELECT * FROM registration";

        try {
            return $connection->query($sql);
        } catch (Exception $e) {
            throw new Exception("Error fetching records: " . $e->getMessage());
        }
    }

    public function updateRecord($data) {
        $connection = $this->db->getConnection();
        $sql = "UPDATE registration SET 
            phone_number = ?, 
            company_name = ?, 
            company_address = ?, 
            employer_name = ?, 
            employer_phone = ?, 
            office_phone = ?, 
            email = ?
            WHERE medical_id = ?";

        try {
            $stmt = $connection->prepare($sql);
            $stmt->bind_param(
                "ssssssss",
                $data['phone_number'],
                $data['company_name'],
                $data['company_address'],
                $data['employer_name'],
                $data['employer_phone'],
                $data['office_phone'],
                $data['email'],
                $data['medical_id']
            );

            $result = $stmt->execute();
            $stmt->close();
            return $result;
        } catch (Exception $e) {
            throw new Exception("Error updating record: " . $e->getMessage());
        }
    }
}