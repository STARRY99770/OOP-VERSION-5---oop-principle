<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$database = "foreign_workers";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 获取当前登录用户的用户名
$current_user = 'Guest';
if (isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id'];
    $stmt = $conn->prepare("SELECT admin_id FROM login_h_i_staff WHERE admin_id = ?");
    $stmt->bind_param("s", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $current_user = htmlspecialchars($row['admin_id']);
    }
    $stmt->close();
}

$message_script = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $new_status = $_POST['status'];
    $appointment_id = $_POST['appointment_id'];

    if ($appointment_id && in_array($new_status, ['pending', 'approved', 'rejected'])) {
        $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE appointment_id = ?");
        $stmt->bind_param("si", $new_status, $appointment_id);
        $stmt->execute();
        $stmt->close();

        $message_script = "<script>alert('Status updated successfully.');</script>";
    } else {
        $message_script = "<script>alert('Invalid input.');</script>";
    }
}

// Filter by status
$filter_status = $_POST['filter_status'] ?? '';

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
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $filter_status);
} else {
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appointment Schedules</title>
    <link rel="stylesheet" href="/pageHS/appointment-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
            <?php if ($result && $result->num_rows > 0): 
                $i = 1;
                while ($row = $result->fetch_assoc()): ?>
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
