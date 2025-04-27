<?php
session_start();

// Database connection variables
$servername = "localhost";
$username = "root";
$password = "";
$database = "foreign_workers";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle the login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Clean and trim input values
    $user_id = trim($_POST['userID']);
    $password_input = trim($_POST['password']);

    // Prepare SQL query to fetch user data based on user_id
    $sql = "SELECT * FROM registration WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        error_log("SQL Prepare failed: " . $conn->error);  // Log the error if prepare fails
        die("Prepare failed: " . $conn->error);
    }

    // Bind parameters and execute the query
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists and if the password is correct
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Check if the password matches (plain text comparison)
        if ($password_input === $user['password']) {  // Plain text password comparison
            $_SESSION['user_id'] = $user['user_id'];  // Store user_id in session
            
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
        error_log("User not found. Query executed: SELECT * FROM registration WHERE user_id = '$user_id'");
        echo "<script>
                alert('User not found. Please check your User ID.');
                window.location.href = '/views/login.php';
              </script>";
        exit();
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="icon" type="image/png" href="/images/srw.png" sizes="32x32">
    <link rel="stylesheet" href="/pageFW/foreign-worker-login.css">
</head>
<body>
    <header>
        <div class="logo-title centered-title">
            <a href="/index.php" class="back-button">Back</a>
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
