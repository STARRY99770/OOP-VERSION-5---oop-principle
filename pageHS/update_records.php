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

// Handle update form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $form_id = $_POST['form_id'];
    $new_status = $_POST['health_status'];
    $new_comment = $_POST['comment'];

    $sql = "UPDATE forms SET health_status = ?, comment = ? WHERE form_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $new_status, $new_comment, $form_id);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Form updated successfully.');</script>";
}

// Fetch workers and their forms (with form_id)
$sql = "SELECT r.user_id, r.medical_id, r.full_name, r.email, f.form_id, f.form_file, f.health_status, f.comment
        FROM registration r
        JOIN forms f ON r.user_id = f.user_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Update Medical Information</title>
  <link rel="stylesheet" href="/pageHS/update-style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
</head>
<body id="update-page">
  <header>
    <h1>Update Foreign Worker's Medical Information</h1>
    <div class="user-icon-container">
        <img src="/images/user-icon.png" alt="User Icon" class="user-icon">
        <div class="tooltip">
            <p><i class="fas fa-user"></i> Username: <?php echo $current_user; ?></p>
        </div>
    </div>
  </header>

  <div class="container">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Medical ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>PDF File</th>
          <th>Health Status</th>
          <th>Comments</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0): $count = 1; ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <form method="post">
                <td><?= $count++ ?></td>
                <td><?= htmlspecialchars($row['medical_id']) ?></td>
                <td><?= htmlspecialchars($row['full_name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td>
                  <?php 
                  // Check if the form_file (BLOB) is not null
                  if (!empty($row['form_file'])):
                    $form_id = $row['form_id'];
                  ?>
                    <a href="view_file.php?form_id=<?= htmlspecialchars($form_id) ?>" target="_blank">View</a>
                  <?php else: ?>
                    No file
                  <?php endif; ?>
                </td>
                <td>
                  <select name="health_status">
                    <option value="Passed" <?= $row['health_status'] === 'Passed' ? 'selected' : '' ?>>Passed</option>
                    <option value="Pending" <?= $row['health_status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Failed" <?= $row['health_status'] === 'Failed' ? 'selected' : '' ?>>Failed</option>
                  </select>
                </td>
                <td>
                  <textarea name="comment" placeholder="Enter comments..."> <?= htmlspecialchars($row['comment']) ?> </textarea>
                </td>
                <td>
                  <input type="hidden" name="form_id" value="<?= $row['form_id'] ?>">
                  <button type="submit" name="update" class="btn-submit">Update</button>
                </td>
              </form>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="8">No data found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  
  <div class="back-to-main">
    <a href="/pageHS/admin_hs_page.php" class="btn-back">Back to Main Page</a>
  </div>
  
  <footer>
    <p>© 2025 Sarawak E-health Management System. All rights reserved.</p>
  </footer>

  <script>
    document.querySelectorAll('select').forEach(function(selectElement) {
      selectElement.addEventListener('change', function () {
        const value = this.value;
        this.style.backgroundColor = '';
        this.style.color = '';

        if (value === "Passed") {
          this.style.backgroundColor = '#d4edda';
          this.style.color = '#155724';
        } else if (value === "Pending") {
          this.style.backgroundColor = '#fff3cd';
          this.style.color = '#856404';
        } else if (value === "Failed") {
          this.style.backgroundColor = '#f8d7da';
          this.style.color = '#721c24';
        }
      });
      selectElement.dispatchEvent(new Event('change'));
    });
  </script>
</body>
</html>

<?php $conn->close(); ?>
