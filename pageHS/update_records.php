<?php
session_start();
require_once __DIR__ . '/../classes/DatabaseConnection.php';
require_once __DIR__ . '/../classes/UserManager.php';
require_once __DIR__ . '/../classes/FormManager.php';
require_once __DIR__ . '/../classes/NotificationManager.php'; // 确保引入 NotificationManager 类

$message_script = '';

try {
    // 初始化数据库连接
    $db = new DatabaseConnection("localhost", "root", "", "foreign_workers");
    $conn = $db->getConnection();

    // 获取当前用户
    $userManager = new UserManager($conn);
    $current_user = $userManager->getCurrentUser($_SESSION);

    // 处理表单逻辑
    $formManager = new FormManager($conn);
    $notificationManager = new NotificationManager($conn); // 初始化通知管理器

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
        $form_id = $_POST['form_id'];
        $new_status = $_POST['health_status'];
        $new_comment = $_POST['comment'];
        $worker_id = $_POST['user_id']; // 假设表单数据中包含 user_id

        if ($formManager->updateForm($form_id, $new_status, $new_comment)) {
            // 设置通知消息，添加换行符
            $notification_message = "Your medical form has been ($new_status).\n\nComment: $new_comment.";

            // 添加通知
            $notificationManager->addNotification($worker_id, $notification_message);

            // 确保换行符和特殊字符在 JavaScript 中正确显示
            $escaped_message = addslashes($notification_message); // 转义特殊字符
            $message_script = "<script>alert('" . str_replace("\n", "\\n", $escaped_message) . "');</script>";
        }
    }

    // 获取表单数据
    $forms = $formManager->getForms();
    if (!$forms) {
        throw new Exception("Failed to fetch forms data.");
    }

} catch (Exception $e) {
    $message_script = "<script>alert('Error: " . $e->getMessage() . "');</script>";
} finally {
    if (isset($db)) {
        $db->closeConnection();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Update Medical Information</title>
  <link rel="stylesheet" href="/pageHS/update-style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js"></script>
</head>
<body id="update-page">
  <?= $message_script ?>
  <script>
    alert('Your medical form has been updated. Status: Passed.\nComment: Hi.');
  </script>
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
        <?php if ($forms->num_rows > 0): $count = 1; ?>
          <?php while ($row = $forms->fetch_assoc()): ?>
            <tr>
              <form method="post">
                <td><?= $count++ ?></td>
                <td><?= htmlspecialchars($row['medical_id']) ?></td>
                <td><?= htmlspecialchars($row['full_name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td>
                  <?php if (!empty($row['form_file'])): ?>
                    <a href="view-pdf.php?form_id=<?= htmlspecialchars($row['form_id']) ?>" class="btn-view">View</a>
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
                  <textarea name="comment" placeholder="Enter comments..."><?= htmlspecialchars($row['comment']) ?></textarea>
                </td>
                <td>
                  <input type="hidden" name="form_id" value="<?= $row['form_id'] ?>">
                  <input type="hidden" name="user_id" value="<?= htmlspecialchars($row['user_id']) ?>">
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

  <div id="pdf-modal" class="modal">
    <div class="modal-content">
      <div class="modal-header">PDF Viewer</div>
      <iframe id="pdf-viewer-modal"></iframe>
      <div class="modal-footer">
        <button class="close-btn">Close</button>
      </div>
    </div>
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

    // Close the modal when the close button is clicked
    document.querySelector('.close-btn').addEventListener('click', function() {
      const modal = document.getElementById('pdf-modal');
      modal.style.display = 'none';
    });

    // Close the modal when clicking outside the modal content
    window.addEventListener('click', function(event) {
      const modal = document.getElementById('pdf-modal');
      if (event.target === modal) {
        modal.style.display = 'none';
      }
    });

    document.querySelectorAll('.view-pdf-btn').forEach(function(button) {
      button.addEventListener('click', function() {
        const formId = this.getAttribute('data-form-id');
        const pdfUrl = `view-pdf.php?form_id=${formId}`;

        const container = document.getElementById('pdf-container');
        container.innerHTML = ''; // Clear previous content

        const loadingTask = pdfjsLib.getDocument(pdfUrl);
        loadingTask.promise.then(function(pdf) {
          pdf.getPage(1).then(function(page) {
            const viewport = page.getViewport({ scale: 1.5 });
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            container.appendChild(canvas);

            const renderContext = {
              canvasContext: context,
              viewport: viewport
            };
            page.render(renderContext);
          });
        }).catch(function(error) {
          console.error('Error loading PDF:', error);
        });
      });
    });

    document.querySelectorAll('.btn-view').forEach(function(button) {
      button.addEventListener('click', function(event) {
        event.preventDefault(); // Prevent default link behavior
        const formId = this.getAttribute('href').split('form_id=')[1]; // Extract form_id from the URL
        const iframe = document.getElementById('pdf-viewer');
        iframe.src = `view-pdf.php?form_id=${formId}`; // Set the iframe source to the PDF URL
      });
    });

    // 为每个 "View" 按钮添加点击事件
    document.querySelectorAll('.btn-view').forEach(function(button) {
      button.addEventListener('click', function(event) {
        event.preventDefault(); // 阻止默认行为（跳转）

        const formId = this.getAttribute('href').split('form_id=')[1]; // 获取 form_id
        const iframe = document.getElementById('pdf-viewer-modal');
        iframe.src = `view-pdf.php?form_id=${formId}`; // 设置 iframe 的 src

        // 显示模态框
        const modal = document.getElementById('pdf-modal');
        modal.style.display = 'block';
      });
    });

    // 关闭模态框
    document.querySelector('.close-btn').addEventListener('click', function() {
      const modal = document.getElementById('pdf-modal');
      modal.style.display = 'none';
      const iframe = document.getElementById('pdf-viewer-modal');
      iframe.src = ''; // 清空 iframe 的 src
    });

    // 点击模态框外部关闭模态框
    window.addEventListener('click', function(event) {
      const modal = document.getElementById('pdf-modal');
      if (event.target === modal) {
        modal.style.display = 'none';
        const iframe = document.getElementById('pdf-viewer-modal');
        iframe.src = ''; // 清空 iframe 的 src
      }
    });
  </script>
</body>
</html>