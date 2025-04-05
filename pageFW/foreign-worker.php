<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /views/login.php");
    exit();
}
$user_id = $_SESSION['user_id']; // Get the logged-in user's ID
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Foreign Workers Services</title>
  <link rel="stylesheet" href="/pageFW/foreign-worker.css" />
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
        <div class="logout-button">
            <button onclick="logout()">Log Out</button>
        </div>
        <div class="profile-wrapper">
            <div class="profile-icon" onclick="toggleProfileDropdown()" title="User Profile">
                <img src="/images/profile-icon.png" alt="Profile" />
            </div>
            <div class="profile-dropdown" id="profileDropdown">
                <p>👤 Username: <span id="username"><?php echo htmlspecialchars($user_id); ?></span></p>
            </div>
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
    function logout() {
        alert("You have logged out successfully!");
        window.location.href = "/home.php";
    }

function toggleProfileDropdown() {
  const dropdown = document.getElementById('profileDropdown');
  dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}

// Optional: Click outside to close dropdown
document.addEventListener('click', function (e) {
  const profile = document.querySelector('.profile-icon');
  const dropdown = document.getElementById('profileDropdown');
  if (!profile.contains(e.target) && !dropdown.contains(e.target)) {
    dropdown.style.display = 'none';
  }
});
</script>
<footer class="footer">
    © 2025 Sarawak E-health Management System. All rights reserved.
</footer>
</body>
</html>
