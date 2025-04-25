<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /views/login.php");
    exit();
}

// Secure PDO connection with SSL (Windows)
try {
    $options = [
        PDO::MYSQL_ATTR_SSL_CA => 'C:/xampp/htdocs/certs/DigiCertGlobalRootCA.crt.pem', // Windows-style path
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ];

    $db = new PDO(
        'mysql:host=ehealth.mysql.database.azure.com;dbname=foreign_workers;charset=utf8',
        'Ehealthsystem',
        'ehealth@123',
        $options
    );
} catch (PDOException $e) {
    die("Database connection failed (SSL): " . $e->getMessage());
}

$user_id = $_SESSION['user_id'];

// Count unread notifications
$unreadCount = 0;
try {
    $stmt = $db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND is_read = 0");
    $stmt->execute(['user_id' => $user_id]);
    $unreadCount = $stmt->fetchColumn();
} catch (PDOException $e) {
    die("Error counting unread notifications: " . $e->getMessage());
}

// Fetch notifications
$notifications = [];
try {
    $stmt = $db->prepare("SELECT id, message, is_read FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC");
    $stmt->execute(['user_id' => $user_id]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching notifications: " . $e->getMessage());
}

// Mark as read
try {
    $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = :user_id AND is_read = 0");
    $stmt->execute(['user_id' => $user_id]);
} catch (PDOException $e) {
    die("Error updating notifications: " . $e->getMessage());
}

// Insert welcome notification if not already there
$message = "Welcome to the Foreign Workers Services page!";
try {
    $stmt = $db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND message = :message");
    $stmt->execute([
        'user_id' => $user_id,
        'message' => $message
    ]);
    if ($stmt->fetchColumn() == 0) {
        $stmt = $db->prepare("INSERT INTO notifications (user_id, message) VALUES (:user_id, :message)");
        $stmt->execute([
            'user_id' => $user_id,
            'message' => $message
        ]);
    }
} catch (PDOException $e) {
    die("Error inserting notification: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Foreign Workers Services</title>
  <link rel="stylesheet" href="/pageFW/foreign-worker.css" />
  <link rel="icon" type="image/png" href="/images/srw.png" sizes="32x32">
</head>
<body>
<header class="header">
    <div class="left-section">
        <div class="logo">
            <img src="/images/srw.png" alt="Logo" />
        </div>
        <div class="title">Foreign Worker Page</div>
    </div>
    <div class="right-section">
        <div class="profile-wrapper">
            <div class="profile-icon" onclick="toggleProfileDropdown()" title="User Profile">
                <img src="/images/profile-icon.png" alt="Profile" />
            </div>
            <div class="profile-dropdown" id="profileDropdown">
                <p>👤 Username: <span id="username"><?php echo htmlspecialchars($user_id); ?></span></p>
            </div>
        </div>
        <div class="notification-wrapper">
            <div class="notification-icon" onclick="toggleNotificationDropdown()" title="Notifications">
                <img src="/images/notification-icon.png" alt="Notifications" />
                <?php if ($unreadCount > 0): ?>
                    <span class="notification-count"><?php echo $unreadCount; ?></span>
                <?php endif; ?>
            </div>
            <div class="notification-dropdown" id="notificationDropdown">
                <?php if (empty($notifications)): ?>
                    <p>No notifications</p>
                <?php else: ?>
                    <ul>
                        <?php foreach ($notifications as $notification): ?>
                            <li class="<?php echo $notification['is_read'] ? 'read' : 'unread'; ?>">
                                <?php echo htmlspecialchars($notification['message']); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
        <div class="logout-button">
            <button onclick="logout()">Log Out</button>
        </div>
    </div>
</header>

<div class="container foreign-worker-container">
  <h1>FOREIGN WORKERS</h1>
  <p>(Please choose your needs)</p>
  <div class="options foreign-worker-options">
    <div class="option foreign-worker-option">
      <img src="/images/form.png" alt="Submit Health Form" />
      <button onclick="location.href='/pageFW/submit-health-form.php'">Submit Health Form</button>
    </div>
    <div class="option foreign-worker-option">
      <img src="/images/schedule.png" alt="Booking for Appointment" />
      <button onclick="location.href='/pageFW/booking-appointment.php'">Booking for Appointment</button>
    </div>
    <div class="option foreign-worker-option">
      <img src="/images/print.png" alt="Print Approval Status" />
      <button onclick="location.href='/pageFW/print-approval-status.php'">Check Records and Print Approval Status</button>
    </div>
  </div>
</div>

<script>
const unreadCount = <?php echo json_encode($unreadCount); ?>;

function logout() {
    alert("You have logged out successfully!");
    window.location.href = "/home.php";
}

function toggleProfileDropdown() {
  const dropdown = document.getElementById('profileDropdown');
  dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}

function toggleNotificationDropdown() {
    const dropdown = document.getElementById('notificationDropdown');
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    markNotificationsAsRead();
}

document.addEventListener('click', function (e) {
    const notificationIcon = document.querySelector('.notification-icon');
    const notificationDropdown = document.getElementById('notificationDropdown');
    if (!notificationIcon.contains(e.target) && !notificationDropdown.contains(e.target)) {
        notificationDropdown.style.display = 'none';
    }

    const profile = document.querySelector('.profile-icon');
    const profileDropdown = document.getElementById('profileDropdown');
    if (!profile.contains(e.target) && !profileDropdown.contains(e.target)) {
        profileDropdown.style.display = 'none';
    }
});

function markNotificationsAsRead() {
    const notificationCount = document.querySelector('.notification-count');
    if (notificationCount) {
        notificationCount.remove();
    }

    fetch('/mark-notifications-read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ user_id: <?php echo json_encode($user_id); ?> })
    }).then(response => response.json())
      .then(data => {
          if (!data.success) {
              console.error('Failed to mark notifications as read:', data.message);
          }
      })
      .catch(error => {
          console.error('Error marking notifications as read:', error);
      });
}
</script>

<footer class="footer">
    © 2025 Sarawak E-health Management System. All rights reserved.
</footer>
</body>
</html>
