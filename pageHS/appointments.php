<?php
session_start();
require_once __DIR__ . '/../classes/DatabaseConnection.php';
require_once __DIR__ . '/../classes/UserManager.php';
require_once __DIR__ . '/../classes/BaseManager.php';
require_once __DIR__ . '/../classes/ManagerInterface.php';
require_once __DIR__ . '/../classes/AppointmentManager.php';
require_once __DIR__ . '/../classes/notificationManager.php'; // 引入 NotificationManager

$message_script = '';

try {
    // 初始化数据库连接
    $db = new DatabaseConnection("sql313.infinityfree.com", "if0_38830936", "IxMNMzyIdNj", "if0_38830936_ehealth");
    $conn = $db->getConnection();

    // 获取当前用户
    $userManager = new UserManager($conn);
    $current_user = $userManager->getCurrentUser($_SESSION);

    // 初始化管理器
    $appointmentManager = new AppointmentManager($conn);
    $notificationManager = new NotificationManager($conn); // 初始化 NotificationManager

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['update_status'])) {
            $new_status = $_POST['status'];
            $appointment_id = $_POST['appointment_id'];
            $user_id = $_POST['user_id']; // 获取用户ID

            // 更新预约状态
            if ($appointmentManager->updateAppointmentStatus($appointment_id, $new_status)) {
                // 使用 NotificationManager 插入通知
                $notification_message = "Your appointment has been updated to: " . ucfirst($new_status);
                $notificationManager->addNotification($user_id, $notification_message);

                echo "<script>alert('Status updated successfully and notification sent.');</script>";
            } else {
                echo "<script>alert('Failed to update status.');</script>";
            }
        }
    }

    // 获取预约列表
    $filter_status = $_POST['filter_status'] ?? '';
    $appointments = $appointmentManager->getAppointments($filter_status);

} catch (Exception $e) {
    // 捕获异常并显示错误消息
    $message_script = "<script>alert('Error: " . $e->getMessage() . "');</script>";
} finally {
    // 确保数据库连接被关闭
    if (isset($db)) {
        $db->closeConnection();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appointment Schedules</title>
    <link rel="stylesheet" href="/pageHS/appointment-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" type="image/png" href="/images/srw.png" sizes="32x32">
</head>
<body>
<?= $message_script ?>

<header class="header">
    <h1>Appointment Schedules</h1>
    <div class="user-icon-container">
        <img src="/images/user-icon.png" alt="User Icon" class="user-icon">
        <div class="tooltip">
            <p><i class="fas fa-user"></i> Username: <?php echo $current_user; ?></p>
        </div>
    </div>
</header>

<div class="container">
    <!-- Status Filter Form -->
    <form method="post" class="search-filter">
        <select name="filter_status" class="status-filter">
            <option value="">-- Filter by Status --</option>
            <option value="pending" <?= $filter_status == 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="approved" <?= $filter_status == 'approved' ? 'selected' : '' ?>>Approved</option>
            <option value="rejected" <?= $filter_status == 'rejected' ? 'selected' : '' ?>>Rejected</option>
        </select>
        <button type="submit" class="btn-primary">Filter</button>
    </form>

    <!-- Appointment Table -->
    <table class="appointment-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Medical ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Date & Time</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($appointments && $appointments->num_rows > 0): 
                $i = 1;
                while ($row = $appointments->fetch_assoc()): ?>
                <tr>
                    <form method="post">
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($row['medical_id']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['appointment_date'] . ' ' . $row['appointment_time']) ?></td>
                        <td>
                            <select name="status">
                                <option value="pending" <?= $row['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="approved" <?= $row['status'] == 'approved' ? 'selected' : '' ?>>Approved</option>
                                <option value="rejected" <?= $row['status'] == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                            </select>
                        </td>
                        <td class="action-buttons">
                            <input type="hidden" name="appointment_id" value="<?= $row['appointment_id'] ?>">
                            <input type="hidden" name="user_id" value="<?= $row['user_id'] ?>">
                            <button type="submit" name="update_status" class="update-btn">
                                <i class="fa fa-check"></i> Update
                            </button>
                        </td>
                    </form>
                </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="7" style="text-align:center;">No appointments found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="back-to-main">
    <a href="/pageHS/admin_hs_page.php" class="btn-back">Back to Main Page</a>
</div>

<footer class="footer">
    © 2025 Sarawak E-health Management System. All rights reserved.
</footer>
</body>
</html>
