<?php
class FormManagerHS {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function updateFormStatus($form_id, $status) {
        $status_updated_date = date("Y-m-d");
        $connection = $this->db->getConnection();

        if ($status === "Approved") {
            $this->approveForm($connection, $form_id, $status, $status_updated_date);
        } else {
            $this->resetFormStatus($connection, $form_id, $status);
        }
    }

    private function approveForm($connection, $form_id, $status, $status_updated_date) {
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
    }

    private function resetFormStatus($connection, $form_id, $status) {
        $stmt = $connection->prepare("UPDATE forms SET permit_status = ?, permit_id = NULL, status_updated_date = NULL, valid_until = NULL WHERE form_id = ?");
        $stmt->bind_param("si", $status, $form_id);
        $stmt->execute();
        $stmt->close();
    }

    private function generatePermitId() {
        return "PRM-" . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public function getFilteredForms($filter) {
        $connection = $this->db->getConnection();
        $sql = "SELECT f.form_id, r.medical_id, r.full_name, r.email, f.health_status, f.comment, f.permit_status, f.permit_id, f.status_updated_date, f.valid_until
                FROM registration r
                JOIN forms f ON r.user_id = f.user_id";

        if (in_array($filter, ['Approved', 'Rejected', 'Pending'])) {
            $sql .= " WHERE f.permit_status = ?";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("s", $filter);
            $stmt->execute();
            return $stmt->get_result();
        }

        return $connection->query($sql);
    }
}