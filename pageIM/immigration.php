<?php
session_start();
$current_user = 'Guest';
if (isset($_SESSION['admin_id'])) {
    $current_user = htmlspecialchars($_SESSION['admin_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Immigration Staff - E-Health Management System</title>
    <link rel="stylesheet" href="/pageIM/Immigration-style.css">
</head>
<body id="immigration-page"></body>
<body>
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