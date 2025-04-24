<?php
require_once '../classes/DatabaseConnection.php';
require_once '../classes/UserManager.php';

session_start();

$db = new DatabaseConnection("ehealth.mysql.database.azure.com", "Ehealthsystem", "ehealth@123", "foreign_workers");
$conn = $db->getConnection();
$userManager = new UserManager($conn);

$current_user = $userManager->getCurrentUser($_SESSION);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Immigration Staff - E-Health Management System</title>
    <link rel="stylesheet" href="/pageIM/Immigration-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" type="image/png" href="/images/srw.png" sizes="32x32">
</head>
<body id="immigration-page">
    <header>
        <h1>Immigration Staff</h1>
        <div class="user-icon-container">
            <img src="/images/user-icon.png" alt="User Icon" class="user-icon">
            <div class="tooltip">
                <p><i class="fas fa-user"></i> Username: <?php echo $current_user; ?></p>
            </div>
        </div>
        <button class="logout-btn" onclick="location.href='/views/admin.php'">Log Out</button>
    </header>

    <main>
        <section class="card">
            <h2>Approve / Reject Work in Sarawak</h2>
            <p>Approve to issue a work pass, or reject and prompt foreign workers to book an appointment.</p>
            <button onclick="location.href='/pageIM/approve-page.php'">Manage Approvals</button>
        </section>

        <section class="card">
            <h2>Check Foreign Workers' Information</h2>
            <p>View detailed information and health status records of foreign workers.</p>
            <button onclick="location.href='/pageIM/view_records.php'">View Records</button>
        </section>
    </main>

    <footer>
        <p>© 2025 Sarawak E-health Management System. All rights reserved.</p>
    </footer>
</body>
</html>