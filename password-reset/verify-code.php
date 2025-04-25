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
    $enteredCode = trim($_POST["verificationCode"]);
    $email = $_SESSION['email'] ?? '';

    if (!empty($email) && !empty($enteredCode)) {
        $stmt = $conn->prepare("SELECT id FROM registration WHERE email = ? AND reset_code = ?");
        $stmt->bind_param("ss", $email, $enteredCode);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $_SESSION['verified_email'] = $email; // Store for password reset
            // Add JavaScript alert for successful verification
            echo "<script type='text/javascript'>alert('Verification successful! Redirecting to password reset...'); window.location.href = 'reset-password.php';</script>";
            exit();
        } else {
            $message = "<p style='color: red;'>Invalid verification code.</p>";
        }
        $stmt->close();
    } else {
        $message = "<p style='color: red;'>Please enter the verification code.</p>";
    }
}
?>

<!-- HTML Form for verification -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter verification code</title>
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
        <h2>Enter Verification Code</h2>
        <form method="POST" action="">
            <div class="input-group">
                <label for="verificationCode">Enter Verification Code</label>
                <input type="text" id="verificationCode" name="verificationCode" required>
            </div>
            <button type="submit" class="sign-in-btn">Verify</button>
        </form>
    </div>
</main>

<?php if (!empty($message)) echo $message; ?>
</body>
</html>
