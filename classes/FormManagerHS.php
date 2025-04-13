<?php
abstract class FormManagerBase {
    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    abstract public function updateFormStatus($form_id, $status);
}

class FormManagerHS extends FormManagerBase {
    public function updateFormStatus($form_id, $status) {
        $status_updated_date = date("Y-m-d");
        $connection = $this->db->getConnection();

        try {
            if ($status === "Approved") {
                $this->approveForm($connection, $form_id, $status, $status_updated_date);
            } else {
                $this->resetFormStatus($connection, $form_id, $status);
            }
        } catch (Exception $e) {
            throw new Exception("Error updating form status: " . $e->getMessage());
        }
    }

    private function approveForm($connection, $form_id, $status, $status_updated_date) {
        try {
            $checkStmt = $connection->prepare("SELECT permit_id FROM forms WHERE form_id = ?");
            $checkStmt->bind_param("i", $form_id);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            $row = $checkResult->fetch_assoc();
            $checkStmt->close();

            $valid_until = date("Y-m-d", strtotime("+1 year"));

            if (empty($row['permit_id'])) {
                $permit_id = $this->generatePermitId();
                $stmt = $connection->prepare("UPDATE forms SET permit_status = ?, permit_id = ?, status_updated_date = ?, valid_until = ? WHERE form_id = ?");
                $stmt->bind_param("ssssi", $status, $permit_id, $status_updated_date, $valid_until, $form_id);
            } else {
                $stmt = $connection->prepare("UPDATE forms SET permit_status = ?, status_updated_date = ?, valid_until = ? WHERE form_id = ?");
                $stmt->bind_param("sssi", $status, $status_updated_date, $valid_until, $form_id);
            }

            $stmt->execute();
            $stmt->close();
        } catch (Exception $e) {
            throw new Exception("Error approving form: " . $e->getMessage());
        }
    }

    private function resetFormStatus($connection, $form_id, $status) {
        try {
            $stmt = $connection->prepare("UPDATE forms SET permit_status = ?, permit_id = NULL, status_updated_date = NULL, valid_until = NULL WHERE form_id = ?");
            $stmt->bind_param("si", $status, $form_id);
            $stmt->execute();
            $stmt->close();
        } catch (Exception $e) {
            throw new Exception("Error resetting form status: " . $e->getMessage());
        }
    }

    private function generatePermitId() {
        return "PRM-" . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public function getFilteredForms($filter) {
        $connection = $this->db->getConnection();
        $sql = "SELECT f.form_id, r.medical_id, r.full_name, r.email, f.health_status, f.comment, f.permit_status, f.permit_id, f.status_updated_date, f.valid_until
                FROM registration r
                JOIN forms f ON r.user_id = f.user_id";

        try {
            if (in_array($filter, ['Approved', 'Rejected', 'Pending'])) {
                $sql .= " WHERE f.permit_status = ?";
                $stmt = $connection->prepare($sql);
                $stmt->bind_param("s", $filter);
                $stmt->execute();
                return $stmt->get_result();
            }

            return $connection->query($sql);
        } catch (Exception $e) {
            throw new Exception("Error fetching filtered forms: " . $e->getMessage());
        }
    }

    public function sendNotification($userId, $message) {
        $stmt = $this->db->prepare("INSERT INTO notifications (user_id, message, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("is", $userId, $message);
        $stmt->execute();
        $stmt->close();
    }

    public function getFormDetails($form_id) {
        $query = "SELECT * FROM forms WHERE form_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $form_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc(); // 返回表单详情
        } else {
            throw new Exception("Form not found with ID: $form_id");
        }
    }

    public function getUserIdByFormId($form_id) {
        $query = "SELECT user_id FROM forms WHERE form_id = ?";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bind_param("i", $form_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return $row['user_id'];
        } else {
            throw new Exception("User ID not found for form ID: {$form_id}");
        }
    }
}