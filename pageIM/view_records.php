<?php
require_once '../classes/Database.php';
require_once '../classes/UserManager.php';
require_once '../classes/RecordManager.php';

$db = new Database("localhost", "root", "", "foreign_workers");
$conn = $db->getConnection();
$userManager = new UserManager($conn);
$recordManager = new RecordManager($db);

session_start();
$current_user = $userManager->getCurrentUser($_SESSION);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["medical_id"])) {
    $data = array_map('htmlspecialchars', $_POST);
    if ($recordManager->updateRecord($data)) {
        echo "<script>alert('Information updated successfully!');</script>";
    } else {
        echo "<script>alert('Update failed.');</script>";
    }
}

$records = $recordManager->getAllRecords();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Check Foreign Workers' Information</title>
  <link rel="stylesheet" href="/pageIM/view_records-style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<header>
  <h2>Check Foreign Workers' Information</h2>
  <div class="user-icon-container">
    <img src="/images/user-icon.png" alt="User Icon" class="user-icon">
    <div class="tooltip">
      <p><i class="fas fa-user"></i> Username: <?php echo $current_user; ?></p>
    </div>
  </div>
</header>

<div class="table-container">
  <div class="search-filter">
    <input type="text" id="searchName" class="search-bar" placeholder="Search by name...">
    <button class="btn-primary" onclick="filterTable()">Search</button>
  </div>

  <table id="infoTable">
    <thead>
      <tr>
        <th>#</th>
        <th>Medical ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if ($records->num_rows > 0) {
        $count = 1;
        while ($row = $records->fetch_assoc()) {
          echo "<tr>";
          echo "<td>" . $count++ . "</td>";
          echo "<td>" . htmlspecialchars($row["medical_id"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["full_name"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
          echo "<td><button class='view-btn' onclick='viewDetails(" . json_encode($row) . ")'>View Details</button></td>";
          echo "</tr>";
        }
      } else {
        echo "<tr><td colspan='5'>No records found</td></tr>";
      }
      ?>
    </tbody>
  </table>
</div>

<!-- Modal -->
<div id="detailsModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <h3>Edit Foreign Worker Information</h3>
    <form id="workerForm" class="form-grid" method="POST">
      <label>Medical ID</label>
      <input type="text" name="medical_id" readonly>

      <label>Full Name</label>
      <input type="text" name="full_name" readonly>

      <label>Date of Birth</label>
      <input type="date" name="dob" readonly>

      <label>Gender</label>
      <select name="gender" disabled>
        <option value="">-- Select --</option>
        <option>Male</option>
        <option>Female</option>
      </select>

      <label>Nationality</label>
      <input type="text" name="nationality" readonly>

      <label>Passport Number</label>
      <input type="text" name="passport_number" readonly>

      <label>Phone Number</label>
      <input type="text" name="phone_number" required>

      <label>Company Name</label>
      <input type="text" name="company_name" required>

      <label>Company Address</label>
      <textarea name="company_address" required></textarea>

      <label>Employer Name</label>
      <input type="text" name="employer_name" required>

      <label>Employer Phone</label>
      <input type="text" name="employer_phone" required>

      <label>Office Phone</label>
      <input type="text" name="office_phone" required>

      <label>Email</label>
      <input type="email" name="email" required>

      <label>User ID</label>
      <input type="text" name="user_id" readonly>

      <label>Password</label>
      <input type="password" name="password" readonly>

      <button type="submit">Update Information</button>
    </form>
  </div>
</div>

<div class="back-to-main">
        <a href="/pageIM/immigration.php" class="btn-back">Back to Main Page</a>
    </div>

<footer>
  <p>© 2025 Sarawak E-health Management System. All rights reserved.</p>
</footer>

<script>
  function filterTable() {
    const nameFilter = document.getElementById('searchName').value.toLowerCase();
    const table = document.getElementById('infoTable');
    const tr = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) {
      const tdName = tr[i].getElementsByTagName('td')[2];
      if (tdName) {
        const nameValue = tdName.textContent.toLowerCase();
        tr[i].style.display = nameValue.includes(nameFilter) || !nameFilter ? '' : 'none';
      }
    }
  }

  function viewDetails(data) {
    const form = document.getElementById('workerForm');
    for (const key in data) {
      if (form.elements[key]) {
        form.elements[key].value = data[key];
      }
    }
    document.getElementById('detailsModal').style.display = 'block';
  }

  function closeModal() {
    document.getElementById('detailsModal').style.display = 'none';
  }
</script>

</body>
</html>

<?php $db->closeConnection(); ?>
