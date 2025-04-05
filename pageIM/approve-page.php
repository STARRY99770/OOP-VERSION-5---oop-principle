<?php
session_start();
$current_user = 'Guest';
if (isset($_SESSION['admin_id'])) {
    $current_user = htmlspecialchars($_SESSION['admin_id']);
}

$host = "localhost";
$username = "root";
$password = "";
$database = "foreign_workers";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update permit_status and generate permit_id logic
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['form_id']) && isset($_POST['status'])) {
    $form_id = $_POST['form_id'];
    $status = $_POST['status'];
    $status_updated_date = date("Y-m-d");

    if ($status === "Approved") {
        // Check if permit_id already exists
        $checkStmt = $conn->prepare("SELECT permit_id FROM forms WHERE form_id = ?");
        $checkStmt->bind_param("i", $form_id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        $row = $checkResult->fetch_assoc();
        $checkStmt->close();

        $valid_until = date("Y-m-d", strtotime("+1 year"));

        if (empty($row['permit_id'])) {
            // Generate new Permit ID
            $permit_id = "PRM-" . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

            // Update permit_id, valid_until, status, date
            $stmt = $conn->prepare("UPDATE forms SET permit_status = ?, permit_id = ?, status_updated_date = ?, valid_until = ? WHERE form_id = ?");
            $stmt->bind_param("ssssi", $status, $permit_id, $status_updated_date, $valid_until, $form_id);
        } else {
            // Permit already exists – just update date, status, and valid_until
            $stmt = $conn->prepare("UPDATE forms SET permit_status = ?, status_updated_date = ?, valid_until = ? WHERE form_id = ?");
            $stmt->bind_param("sssi", $status, $status_updated_date, $valid_until, $form_id);
        }
    } else {
        // Rejected or Pending – clear permit fields
        $stmt = $conn->prepare("UPDATE forms SET permit_status = ?, permit_id = NULL, status_updated_date = NULL, valid_until = NULL WHERE form_id = ?");
        $stmt->bind_param("si", $status, $form_id);
    }

    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Form ID {$form_id} updated successfully'); window.location.href='approve-page.php';</script>";
    exit();
}

// Filtering logic
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';
$sql = "SELECT f.form_id, r.medical_id, r.full_name, r.email, f.health_status, f.comment, f.permit_status, f.permit_id, f.status_updated_date, f.valid_until
        FROM registration r
        JOIN forms f ON r.user_id = f.user_id";

if (in_array($filter, ['Approved', 'Rejected', 'Pending'])) {
    $sql .= " WHERE f.permit_status = '$filter'";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Page - E-Health Management System</title>
    <link rel="stylesheet" href="/pageIM/approve-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
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
                                <td>";

                            if (!$permit_finalized) {
                                echo "
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
                                    </form>";
                            } else {
                                echo "<span style='color: gray;'>Finalized</span>";
                            }

                            echo "</td>
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

<?php $conn->close(); ?>
