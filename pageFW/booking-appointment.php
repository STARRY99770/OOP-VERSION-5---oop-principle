<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /views/login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

$servername = "localhost";
$username = "root";
$password = "";
$database = "foreign_workers";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 删除预约
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM appointments WHERE appointment_id = ? AND user_id = ?");
    $stmt->bind_param("is", $delete_id, $user_id);
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('Appointment deleted successfully.'); window.location.href='booking-appointment.php';</script>";
    exit();
}

// 提交预约
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $appointmentDate = $_POST['appointmentDate'];
    $appointmentTime = $_POST['appointmentTime'];
    $appointmentPlace = $_POST['appointmentPlace'];

    // 1. 检查是否当天已有预约
    $stmt = $conn->prepare("SELECT COUNT(*) FROM appointments WHERE user_id = ? AND appointment_date = ?");
    $stmt->bind_param("ss", $user_id, $appointmentDate);
    $stmt->execute();
    $stmt->bind_result($existing_appointments);
    $stmt->fetch();
    $stmt->close();

    if ($existing_appointments > 0) {
        echo "<script>alert('You have already booked an appointment on this date.');</script>";
    } else {
        // 2. 检查时间是否冲突
        $stmt = $conn->prepare("SELECT COUNT(*) FROM appointments WHERE appointment_date = ? AND appointment_time = ? AND appointment_place = ?");
        $stmt->bind_param("sss", $appointmentDate, $appointmentTime, $appointmentPlace);
        $stmt->execute();
        $stmt->bind_result($time_conflict);
        $stmt->fetch();
        $stmt->close();

        if ($time_conflict > 0) {
            echo "<script>alert('This time slot has already been booked at the selected clinic. Please choose another.');</script>";
        } else {
            // 插入预约记录，状态为 Pending
            $status = 'Pending';
            $stmt = $conn->prepare("INSERT INTO appointments (appointment_date, appointment_time, appointment_place, user_id, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $appointmentDate, $appointmentTime, $appointmentPlace, $user_id, $status);
            if ($stmt->execute()) {
                echo "<script>alert('Appointment booked successfully!'); window.location.href='booking-appointment.php';</script>";
            } else {
                echo "<script>alert('Error booking appointment.');</script>";
            }
            $stmt->close();
        }
    }
}

// 获取当前用户的预约记录
$stmt = $conn->prepare("SELECT * FROM appointments WHERE user_id = ? ORDER BY appointment_date ASC");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>Booking Appointment</title>
    <link rel="stylesheet" href="/pageFW/booking-appointment.css">
</head>
<body>
    <header class="header">
        <div class="left-section">
            <div class="logo"><img src="/images/srw.png" alt="Logo"></div>
            <div class="title">Booking Appointment</div>
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
        </div>
    </header>

    <div class="container foreign-worker-container">
        <p>The medical check-up appointment is only available: <strong>Monday to Friday</strong>, <strong>8AM - 4PM</strong></p>

        <form id="bookingForm" method="POST" style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">
            <label for="appointmentDate">Select Date:</label>
            <input type="date" id="appointmentDate" name="appointmentDate" required>

            <label for="appointmentTime">Select Time:</label>
            <input type="time" id="appointmentTime" name="appointmentTime" required>

            <label for="appointmentPlace">Select Place:</label>
            <select id="appointmentPlace" name="appointmentPlace" required>
                <option value="" disabled selected>Select a place</option>
                <option value="clinic1">Clinic 1</option>
                <option value="clinic2">Clinic 2</option>
                <option value="clinic3">Clinic 3</option>
            </select>

            <button type="submit">Book Appointment</button>
        </form>

        <h2>Your Appointment Records</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Place</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $serial = 1;
                while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $serial++; ?></td>
                        <td><?= $row['appointment_date']; ?></td>
                        <td><?= date("g:i A", strtotime($row['appointment_time'])); ?></td>
                        <td><?= $row['appointment_place']; ?></td>
                        <td><?= htmlspecialchars($row['status']); ?></td>
                        <td>
                            <a href="?delete=<?= $row['appointment_id']; ?>" onclick="return confirm('Are you sure you want to delete this appointment?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div> <!-- End of container -->

    <div class="back-to-main">
        <button onclick="window.location.href='/pageFW/foreign-worker.php'">Back to Main Page</button>
    </div>

    <footer class="shared-footer">
        <p>© 2025 Sarawak E-health Management System. All rights reserved.</p>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const dateInput = document.getElementById("appointmentDate");
            const timeInput = document.getElementById("appointmentTime");

            const today = new Date().toISOString().split("T")[0];
            dateInput.setAttribute("min", today);

            dateInput.addEventListener("input", function () {
                const selectedDate = new Date(dateInput.value);
                const day = selectedDate.getDay();
                if (day === 0 || day === 6) {
                    dateInput.setCustomValidity("Please select a weekday (Monday to Friday).");
                } else {
                    dateInput.setCustomValidity("");
                }
                updateTimeRestrictions();
            });

            function updateTimeRestrictions() {
                const selectedDate = new Date(dateInput.value);
                const currentDate = new Date();
                let minTime = "08:00";
                let maxTime = "16:00";

                if (selectedDate.toDateString() === currentDate.toDateString()) {
                    const currentHour = currentDate.getHours();
                    const currentMinute = currentDate.getMinutes();

                    if (currentHour >= 16) {
                        timeInput.setCustomValidity("Booking time is closed for today.");
                        timeInput.value = "";
                    } else {
                        minTime = `${String(currentHour).padStart(2, "0")}:${String(currentMinute).padStart(2, "0")}`;
                        timeInput.setCustomValidity("");
                    }
                } else {
                    timeInput.setCustomValidity("");
                }

                timeInput.min = minTime;
                timeInput.max = maxTime;
            }

            dateInput.addEventListener("change", updateTimeRestrictions);
        });

        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }

        document.addEventListener('click', function (e) {
            const profile = document.querySelector('.profile-icon');
            const dropdown = document.getElementById('profileDropdown');
            if (!profile.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
    </script>
</body>
</html>
