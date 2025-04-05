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

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_id = trim($_POST["admin_id"]);
    $password = trim($_POST["password"]);
    $role = trim($_POST["role"]);

    if (empty($admin_id) || empty($password) || empty($role)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM login_h_i_staff WHERE admin_id = ? AND role = ?");
        $stmt->bind_param("ss", $admin_id, $role);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if ($password === $user["password"]) {
                // Set session variables
                $_SESSION["admin_id"] = $admin_id;
                $_SESSION["role"] = $role;

                // OPTIONAL: If you have email column and need it later
                if (isset($user["email"])) {
                    $_SESSION["email"] = $user["email"];
                }

                if ($role === "admin_health") {
                  echo "<script>
                      alert('Login successful');
                      window.location.href = '/pageHS/admin_hs_page.php';
                  </script>";
                  exit();
              } else {
                  echo "<script>
                      alert('Login successful');
                      window.location.href = '/pageIM/immigration.php';
                  </script>";
                  exit();
              }
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "User not found or role mismatch.";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login Page</title>
  <link rel="stylesheet" href="/views/AdminStyle.css">
</head>
<body>
<header class="page-header">
  <img src="/images/srw.png" alt="Logo" class="logo"> <!-- Add logo -->
  <h1>Login Page for Admin Health Staff / Immigration Staff</h1>
</header>
  <div class="login-container">
    <h2>Sign in to E-health Management System</h2>
    <form method="POST" action="">  
      <label for="admin_id">User ID:</label>
      <input type="text" id="admin_id" name="admin_id" placeholder="Enter User ID" required>

      <label for="password">Password:</label>
      <input type="password" id="password" name="password" placeholder="Enter Password" required>

      <div class="radio-buttons">
        <label><input type="radio" name="role" value="admin_health" required> Admin Health Staff</label>
        <label><input type="radio" name="role" value="immigration" required> Immigration Staff</label>
      </div>

      <button type="submit">Log In</button>
      <button onclick="history.back()" class="back-button">Back to main page</button>

      <?php if (!empty($error)): ?>
        <p style="color: red; margin-top: 10px;"><?php echo htmlspecialchars($error); ?></p>
      <?php endif; ?>
    </form>
  </div>
</body>
</html>
