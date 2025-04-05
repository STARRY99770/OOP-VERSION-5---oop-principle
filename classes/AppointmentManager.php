<?php
require_once 'BaseManager.php';
require_once 'ManagerInterface.php';

class AppointmentManager extends BaseManager implements ManagerInterface {
    public function getAppointments($filter_status = '') {
        try {
            $sql = "SELECT 
                        r.medical_id, 
                        r.full_name AS name, 
                        r.email, 
                        a.appointment_date, 
                        a.appointment_time, 
                        a.status,
                        a.user_id,
                        a.appointment_id
                    FROM appointments a
                    JOIN registration r ON a.user_id = r.user_id";

            if (!empty($filter_status)) {
                $sql .= " WHERE a.status = ?";
                $stmt = $this->executeQuery($sql, ["s", $filter_status]);
            } else {
                $stmt = $this->executeQuery($sql);
            }

            return $stmt->get_result();
        } catch (Exception $e) {
            throw new Exception("Failed to fetch appointments: " . $e->getMessage());
        }
    }

    public function updateAppointmentStatus($appointment_id, $new_status) {
        try {
            if ($appointment_id && in_array($new_status, ['pending', 'approved', 'rejected'])) {
                $sql = "UPDATE appointments SET status = ? WHERE appointment_id = ?";
                $this->executeQuery($sql, ["si", $new_status, $appointment_id]);
                return true;
            }
            throw new Exception("Invalid appointment ID or status.");
        } catch (Exception $e) {
            throw new Exception("Failed to update appointment status: " . $e->getMessage());
        }
    }
}
?>