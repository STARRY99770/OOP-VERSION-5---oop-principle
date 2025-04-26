<?php
session_start();
require_once __DIR__ . '/../classes/DatabaseConnection.php';
require_once __DIR__ . '/../classes/UserManager.php';
require_once __DIR__ . '/../classes/FormManagerHS.php';
require_once __DIR__ . '/../classes/FormFilter.php';
require_once __DIR__ . '/../classes/notificationManager.php'; // 引入 NotificationManager

$message_script = '';

try {
    // 初始化数据库连接
    $db = new DatabaseConnection("localhost", "root", "", "foreign_workers");
    $conn = $db->getConnection();

    // 初始化管理器
    $userManager = new UserManager($conn);
    $formManager = new FormManagerHS($db);
    $notificationManager = new NotificationManager($conn); // 初始化 NotificationManager

    $current_user = $userManager->getCurrentUser($_SESSION);
    $filter = FormFilter::getFilter();

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['form_id']) && isset($_POST['status'])) {
        $form_id = $_POST['form_id'];
        $status = $_POST['status'];

        // 更新表单状态
        $formManager->updateFormStatus($form_id, $status);

        // 获取用户 ID 和状态消息
        $form_details = $formManager->getFormDetails($form_id); // 假设此方法返回表单的详细信息
        $user_id = $form_details['user_id'];
        $notification_message = "Your work pass has been " . strtolower($status) . ".";

        // 使用 NotificationManager 发送通知
        $notificationManager->addNotification($user_id, $notification_message);

        echo "<script>alert('Form ID {$form_id} updated successfully and notification sent.'); window.location.href='approve-page.php';</script>";
        exit();
    }

    $result = $formManager->getFilteredForms($filter);

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Page - E-Health Management System</title>
    <link rel="stylesheet" href="/pageIM/approve-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" type="image/png" href="/images/srw.png" sizes="32x32">
</head>
<body>
<?= $message_script ?>

    <header>
        <h1>Approve Work Pass</h1>
        <div class="user-icon-container">
            <img src="/images/user-icon.png" alt="User Icon" class="user-icon">
            <div class="tooltip">
                <p><i class="fas fa-user"></i> Username: <?php echo $current_user; ?></p>
            </div>
        </div>
    </header>

    <main>
        <div class="table-container">
            <div class="search-filter">
                <form method="GET" action="approve-page.php">
                    <select name="filter" class="search-bar">
                        <option value="">Filter by Status</option>
                        <option value="Approved" <?= $filter == 'Approved' ? 'selected' : '' ?>>Approved</option>
                        <option value="Rejected" <?= $filter == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                        <option value="Pending" <?= $filter == 'Pending' ? 'selected' : '' ?>>Pending</option>
                    </select>
                    <button class="btn-primary" type="submit">Apply</button>
                </form>
            </div>

            <table id="worker-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Medical ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Health Status</th>
                        <th>Comment</th>
                        <th>Permit Status</th>
                        <th>Permit ID</th>
                        <th>Status Updated Date</th>
                        <th>Valid Until</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        $count = 1;
                        while ($row = $result->fetch_assoc()) {
                            $form_id = $row['form_id'];
                            $permit_finalized = !empty($row['permit_id']) && $row['permit_status'] === 'Approved';

                            echo "<tr>
                                <td>{$count}</td>
                                <td>" . htmlspecialchars($row['medical_id']) . "</td>
                                <td>" . htmlspecialchars($row['full_name']) . "</td>
                                <td>" . htmlspecialchars($row['email']) . "</td>
                                <td>" . htmlspecialchars($row['health_status']) . "</td>
                                <td>" . nl2br(htmlspecialchars($row['comment'])) . "</td>
                                <td>" . htmlspecialchars($row['permit_status']) . "</td>
                                <td>" . ($permit_finalized
                                        ? "<span style='color: gray; font-weight: bold;'>" . htmlspecialchars($row['permit_id']) . "</span>"
                                        : '-') . "</td>
                                <td>" . ($row['status_updated_date'] ?? '-') . "</td>
                                <td>" . ($row['valid_until'] ?? '-') . "</td>
                                <td>
                                    <form method='POST' style='display:inline;'>
                                        <input type='hidden' name='form_id' value='{$form_id}'>
                                        <input type='hidden' name='status' value='Approved'>
                                        <button class='approve-btn' type='submit'>Approve</button>
                                    </form>
                                    <form method='POST' style='display:inline;'>
                                        <input type='hidden' name='form_id' value='{$form_id}'>
                                        <input type='hidden' name='status' value='Rejected'>
                                        <button class='reject-btn' type='submit'>Reject</button>
                                    </form>
                                    <form method='POST' style='display:inline;'>
                                        <input type='hidden' name='form_id' value='{$form_id}'>
                                        <input type='hidden' name='status' value='Pending'>
                                        <button class='pending-btn' type='submit'>Pending</button>
                                    </form>
                                </td>
                            </tr>";
                            $count++;
                        }
                    } else {
                        echo "<tr><td colspan='11'>No data found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>

    <div class="back-to-main">
        <a href="/pageIM/immigration.php" class="btn-back">Back to Main Page</a>
    </div>

    <footer>
        <p>© 2025 Sarawak E-health Management System. All rights reserved.</p>
    </footer>
</body>
</html>
