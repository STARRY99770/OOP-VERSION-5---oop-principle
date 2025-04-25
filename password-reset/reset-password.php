<?php
session_start();
$servername = "sql313.infinityfree.com";
$username = "if0_38830936";
$password = "IxMNMzyIdNj"; 
$database = "if0_38830936_ehealth";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newPassword = $_POST["newPassword"];
    $confirmPassword = $_POST["confirmPassword"];
    $email = $_SESSION['verified_email'] ?? '';  // Use the verified email from the session

    if (!empty($email) && !empty($newPassword) && !empty($confirmPassword)) {
        if ($newPassword === $confirmPassword) {
            // No hashing, just use the plain password
            $stmt = $conn->prepare("UPDATE registration SET password = ?, reset_code = NULL WHERE email = ?");
            $stmt->bind_param("ss", $newPassword, $email);
            if ($stmt->execute()) {
                // Set a success message and trigger JavaScript for the alert and redirect
                echo "<script>
                        alert('Password reset successfully!');
                        window.location.href = '/views/login.php';
                      </script>";
                session_destroy(); // Clear session
                exit();
            } else {
                $message = "<p style='color: red;'>Error resetting password.</p>";
            }
            $stmt->close();
        } else {
            $message = "<p style='color: red;'>Passwords do not match.</p>";
        }
    } else {
        $message = "<p style='color: red;'>Please fill in all fields.</p>";
    }
}
?>

<!-- HTML Form for resetting password -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="/pageFW/foreign-worker-login.css">
    <link rel="icon" type="image/png" href="/images/srw.png" sizes="32x32">
</head>
<body>
    <header>
        <div class="logo-title centered-title">
            <img src="/images/srw.png" alt="Logo" class="logo">
            <h1>Sarawak E-health Management System</h1>
        </div>
    </header>

    <main class="login-main">
        <div class="login-container">
            <h2>Reset Password</h2>
            <form method="POST" action="">
                <div class="input-group">
                    <label for="newPassword">New Password</label>
                    <input type="password" id="newPassword" name="newPassword" required>
                </div>
                <div class="input-group">
                    <label for="confirmPassword">Confirm New Password</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" required>
                </div>
                <button type="submit" class="sign-in-btn">Submit</button>
            </form>
            <p class="sign-up-text">Back to <a href="/FWlogin/foreign-worker-signin.php">Sign in</a></p>
        </div>
    </main>

    <?php if (!empty($message)) echo $message; ?>
</body>
</html>
