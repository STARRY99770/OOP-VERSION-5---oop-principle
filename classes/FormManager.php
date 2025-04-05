<?php
require_once 'BaseManager.php';

class FormManager extends BaseManager {
    // 获取表单数据
    public function getForms() {
        try {
            $sql = "SELECT r.user_id, r.medical_id, r.full_name, r.email, f.form_id, f.form_file, f.health_status, f.comment
                    FROM registration r
                    JOIN forms f ON r.user_id = f.user_id";
            $stmt = $this->executeQuery($sql);
            return $stmt->get_result();
        } catch (Exception $e) {
            throw new Exception("Failed to fetch forms: " . $e->getMessage());
        }
    }

    // 更新表单状态
    public function updateForm($form_id, $new_status, $new_comment) {
        try {
            $sql = "UPDATE forms SET health_status = ?, comment = ? WHERE form_id = ?";
            $this->executeQuery($sql, ["ssi", $new_status, $new_comment, $form_id]);
            return true;
        } catch (Exception $e) {
            throw new Exception("Failed to update form: " . $e->getMessage());
        }
    }
}
?>