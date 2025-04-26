<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /views/login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// DB connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "foreign_workers";
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle file upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["pdfUpload"])) {
    $uploadDir = "uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $filename = basename($_FILES["pdfUpload"]["name"]);
    $fileType = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $filePath = $uploadDir . uniqid("form_", true) . "." . $fileType;

    if (move_uploaded_file($_FILES["pdfUpload"]["tmp_name"], $filePath)) {
        $stmt = $conn->prepare("INSERT INTO forms (user_id, form_file, upload_date) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $user_id, $filePath);

        if ($stmt->execute()) {
            echo "<script>
                    alert('PDF submitted successfully!');
                    window.location.href='/pageFW/submit-health-form.php';
                  </script>";
        } else {
            echo "<script>alert('Error saving to database.');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('File upload failed.');</script>";
    }
}

// Handle file deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $delete_id = $_POST['delete_id'];

    $query = "SELECT form_file FROM forms WHERE form_id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $delete_id, $user_id);
    $stmt->execute();
    $stmt->bind_result($filePath);
    if ($stmt->fetch()) {
        $stmt->close();
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $delQuery = "DELETE FROM forms WHERE form_id = ? AND user_id = ?";
        $stmt = $conn->prepare($delQuery);
        $stmt->bind_param("is", $delete_id, $user_id);
        if ($stmt->execute()) {
            echo "<script>alert('File deleted successfully!'); window.location.href='/pageFW/submit-health-form.php';</script>";
        } else {
            echo "<script>alert('Error deleting file.');</script>";
        }
        $stmt->close();
    } else {
        $stmt->close();
        echo "<script>alert('File not found or permission denied.');</script>";
    }
}

// Format date helper
function formatUploadDate($datetime) {
    return date("d M Y, h:i A", strtotime($datetime));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit Health Form</title>
    <link rel="stylesheet" href="/pageFW/submit-health-form.css">
    <link rel="icon" type="image/png" href="/images/srw.png" sizes="32x32">
</head>
<body>
<header class="header">
    <div class="left-section">
        <div class="logo"><img src="/images/srw.png" alt="Logo"></div>
        <div class="title">Submit Health Form</div>
    </div>
    <div class="right-section">
        <div class="profile-wrapper">
            <div class="profile-icon" onclick="toggleProfileDropdown()" title="User Profile">
                <img src="/images/profile-icon.png" alt="Profile" />
            </div>
            <div class="profile-dropdown" id="profileDropdown">
                <p>👤 User ID: <span id="username"><?php echo htmlspecialchars($user_id); ?></span></p>
            </div>
        </div>
    </div>
</header>

<div class="container">
    <!-- Upload Form -->
    <div class="upload-section">
        <form id="uploadForm" action="/pageFW/submit-health-form.php" method="POST" enctype="multipart/form-data" style="display: flex; align-items: center; gap: 10px;">
            <label for="pdfUpload">Upload PDF:</label>
            <input type="file" id="pdfUpload" name="pdfUpload" accept="application/pdf" required onchange="previewPDF()">
            <button type="submit">Submit PDF</button>
        </form>
    </div>

    <!-- PDF List -->
    <h2>Submitted Health Forms</h2>
    <table border="1">
        <thead>
            <tr>
                <th>No</th>
                <th>Filename</th>
                <th>Upload Date</th>
                <th>View</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "SELECT * FROM forms WHERE user_id = ? ORDER BY upload_date DESC";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $no = 1;
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $no++ . "</td>";
                echo "<td>" . basename($row['form_file']) . "</td>";
                echo "<td>" . formatUploadDate($row['upload_date']) . "</td>";
                echo "<td><button type='button' onclick='viewPDF(\"" . $row['form_file'] . "\")'>View</button></td>";
                echo "<td>
                        <form method='POST' style='display:inline;'>
                            <input type='hidden' name='delete_id' value='" . $row['form_id'] . "'>
                            <button type='submit' name='delete' onclick='return confirm(\"Are you sure?\")'>Delete</button>
                        </form>
                      </td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- PDF Viewer -->
    <div id="pdfViewer" style="display: none;">
        <h2>PDF Preview</h2>
        <iframe id="pdfFrame" width="100%" height="600px"></iframe>
    </div>
</div> <!-- End of container -->

<div class="back-to-main">
    <button onclick="window.location.href='/pageFW/foreign-worker.php'">Back to Main Page</button>
</div>

<footer class="footer">
    <p>© 2025 Sarawak E-health Management System. All rights reserved.</p>
</footer>

<script>
    function previewPDF() {
        const file = document.getElementById('pdfUpload').files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('pdfFrame').src = e.target.result;
                document.getElementById('pdfViewer').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }

    function viewPDF(filePath) {
        document.getElementById('pdfFrame').src = filePath;
        document.getElementById('pdfViewer').style.display = 'block';
    }

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
