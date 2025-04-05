<?php
interface ManagerInterface {
    public function getAppointments($filter_status);
    public function updateAppointmentStatus($appointment_id, $new_status);
}
?>