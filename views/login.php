<?php
session_start();

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'foreign_workers';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = trim($_POST['userID']);
    $password_input = trim($_POST['password']);

    $sql = "SELECT * FROM registration WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if ($password_input === $user['password']) {
            $_SESSION['user_id'] = $user['user_id'];
            echo "<script>
                    alert('Login successful!');
                    window.location.href = '/pageFW/foreign-worker.php';
                  </script>";
            exit();
        } else {
            echo "<script>
                    alert('Incorrect password.');
                    window.location.href = '/views/login.php';
                  </script>";
            exit();
        }
    } else {
        // Enhanced debug for user not found
        error_log("User not found in database. Query executed: SELECT * FROM registration WHERE user_id = '$user_id'");
        echo "<script>
                alert('User not found. Please check your User ID.');
                window.location.href = '/views/login.php';
              </script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="/pageFW/foreign-worker-login.css">
</head>
<body>
    <header>
        <div class="logo-title centered-title">
            <button onclick="history.back()" class="back-button">Back</button>
            <img src="/images/srw.png" alt="Logo" class="logo">
            <h1 class="title">Sarawak E-health Management System</h1>
        </div>
    </header>

    <main class="login-main">
        <div class="login-container">
            <h2>Sign in E-health Management System (Foreign Workers)</h2>
            <form id="loginForm" method="POST" action="">
                <div class="input-group">
                    <label for="userID">User ID</label>
                    <input type="text" id="userID" name="userID" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <a href="/password-reset/forgot-password.php" class="forgot-password">Forgot Password?</a>
                <button type="submit" class="sign-in-btn">Sign In</button>
            </form>

            <p class="sign-up-text">No account? <a href="/views/signup.php">Sign up here</a></p>
        </div>
    </main>
</body>
</html>