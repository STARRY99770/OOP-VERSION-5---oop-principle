<?php
session_start();

//// ------------------------------
// CLASS: Database Connection (Encapsulation + Object Creation)
//// ------------------------------
class Database {
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $dbname = "foreign_workers";
    public $conn;

    public function __construct() { // ✅ Object creation
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);
        if ($this->conn->connect_error) {
            // ✅ Exception Handling
            throw new Exception("Database connection failed: " . $this->conn->connect_error);
        }
    }
}

//// ------------------------------
// CLASS: Appointment Object (Object Creation)
//// ------------------------------
class Appointment { // ✅ Object to hold appointment data
    public $id, $date, $time, $place, $status;

    public function __construct($id, $date, $time, $place, $status) {
        $this->id = $id;
        $this->date = $date;
        $this->time = $time;
        $this->place = $place;
        $this->status = $status;
    }
}

//// ------------------------------
// CLASS: Appointment Manager (Encapsulation + Object Handling)
//// ------------------------------
class AppointmentManager { // ✅ Class for logic encapsulation
    private $conn;
    private $user_id;

    public function __construct($user_id) {
        $db = new Database(); // ✅ Object Composition
        $this->conn = $db->conn;
        $this->user_id = $user_id;
    }

    public function bookAppointment($date, $time, $place) {
        // ✅ Encapsulated logic with Exception Handling
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM appointments WHERE user_id = ? AND appointment_date = ?");
        $stmt->bind_param("ss", $this->user_id, $date);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            throw new Exception("You already booked an appointment on this date.");
        }

        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM appointments WHERE appointment_date = ? AND appointment_time = ? AND appointment_place = ?");
        $stmt->bind_param("sss", $date, $time, $place);
        $stmt->execute();
        $stmt->bind_result($conflict);
        $stmt->fetch();
        $stmt->close();

        if ($conflict > 0) {
            throw new Exception("Time slot already booked at selected place.");
        }

        $status = 'Pending';
        $stmt = $this->conn->prepare("INSERT INTO appointments (appointment_date, appointment_time, appointment_place, user_id, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $date, $time, $place, $this->user_id, $status);
        if (!$stmt->execute()) {
            throw new Exception("Failed to book appointment.");
        }
        $stmt->close();
    }

    public function deleteAppointment($id) {
        $stmt = $this->conn->prepare("DELETE FROM appointments WHERE appointment_id = ? AND user_id = ?");
        $stmt->bind_param("is", $id, $this->user_id);
        if (!$stmt->execute()) {
            throw new Exception("Failed to delete appointment.");
        }
        $stmt->close();
    }

    public function getUserAppointments() {
        $stmt = $this->conn->prepare("SELECT * FROM appointments WHERE user_id = ? ORDER BY appointment_date ASC");
        $stmt->bind_param("s", $this->user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $appointments = [];

        while ($row = $result->fetch_assoc()) {
            $appointments[] = new Appointment( // ✅ Object Creation
                $row['appointment_id'],
                $row['appointment_date'],
                $row['appointment_time'],
                $row['appointment_place'],
                $row['status']
            );
        }

        return $appointments;
    }
}

//// ------------------------------
// PROCESSING LOGIC
//// ------------------------------
$appointments = [];
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $manager = new AppointmentManager($user_id); // ✅ Object Instantiation

    try {
        if (isset($_POST['submit'])) {
            $manager->bookAppointment($_POST['date'], $_POST['time'], $_POST['place']);
            echo "<script>alert('Appointment booked successfully.');</script>";
        }

        if (isset($_GET['delete'])) {
            $manager->deleteAppointment($_GET['delete']);
            echo "<script>alert('Appointment deleted successfully.');</script>";
        }

        $appointments = $manager->getUserAppointments();
    } catch (Exception $e) {
        echo "<script>alert('" . $e->getMessage() . "');</script>"; // ✅ Exception Handling Output
    }
} else {
    echo "<script>alert('User not logged in.');</script>";
}
?>

<!-- ✅ HTML Section Starts Here (Unchanged) -->
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
