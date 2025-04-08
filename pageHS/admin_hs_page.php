<?php
session_start();
require_once __DIR__ . '/../classes/DatabaseConnection.php';
require_once __DIR__ . '/../classes/UserManager.php';

try {
    // Initialize database connection
    $db = new DatabaseConnection("localhost", "root", "", "foreign_workers");
    $conn = $db->getConnection();

    // Get the current user
    $userManager = new UserManager($conn);
    $current_user = $userManager->getCurrentUser($_SESSION);

    // Close database connection
    $db->closeConnection();
} catch (mysqli_sql_exception $e) {
    // Handle database-specific errors
    die("A database error occurred: " . $e->getMessage());
} catch (Exception $e) {
    // Handle general exceptions
    die("An error occurred: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Health Staff Page</title>
    <link rel="stylesheet" href="/pageHS/admin-style.css">
</head>
<body id="admin-page">
    <header style="position: relative; display: flex; align-items: center; justify-content: space-between; padding: 10px;">
        <h1>Admin Health Staff</h1>
        <div style="display: flex; align-items: center; gap: 10px; margin-left: auto; position: relative;">
            <!-- 用户图标 -->
            <div class="user-icon-container">
                <img src="/images/user-icon.png" alt="User Icon" class="user-icon">
                <div class="tooltip">
                    <p><i class="fas fa-user"></i> Username: <?php echo $current_user; ?></p>
                </div>
            </div>
            <!-- 登出按钮 -->
            <a href="/views/admin.php" class="logout-btn">Log Out</a>
        </div>
    </header>
    <section class="container">
        <div class="card">
            <h2>Check Foreign Workers Appointment Time</h2>
            <p>Review The Appointment Schedules For Foreign Workers.</p>
            <a href="/pageHS/appointments.php" class="btn">View Appointments</a>
        </div>

        <div class="card">
            <h2>Update Foreign Workers Medical Information</h2>
            <p>Ensure All Health Records Are Up-To-Date For Reporting.</p>
            <a href="/pageHS/update_records.php" class="btn">Update Records</a>
        </div>
    </section>

    <footer>
        <p>© 2025 Sarawak E-health Management System. All rights reserved.</p>
    </footer>       
</body>
</html>

