<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Include PHPMailer

$servername = "localhost";
$username = "root";
$password = "";
$database = "foreign_workers";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);

    if (!empty($email)) {
        $stmt = $conn->prepare("SELECT id FROM registration WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Generate a verification code
            $verificationCode = rand(100000, 999999);

            // Store the verification code in the database
            $stmt = $conn->prepare("UPDATE registration SET reset_code = ? WHERE email = ?");
            $stmt->bind_param("ss", $verificationCode, $email);
            $stmt->execute();

            // Send email with PHPMailer
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Use your SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = 'ehealthmanagementSarawak@gmail.com'; // Your email
                $mail->Password = 'dplj vjcl ibpt onzq'; // Your email password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('ehealthmanagementSarawak@gmail.com', 'Sarawak E-health System');
                $mail->addAddress($email);

                // HTML email content
                $mail->isHTML(true);
                $mail->Subject = "Password Reset Code";
                $mail->Body = "
                    <html>
                    <head>
                        <title>Password Reset Code</title>
                    </head>
                    <body>
                        <p>Hello,</p>
                        <p>We received a request to reset the password for your account associated with this email address. If you did not request a password reset, please ignore this email.</p>
                        <p>Your password reset code is: <b>$verificationCode</b></p>
                        <p>Enter this code on the password reset page to proceed.</p>
                        <p>If you have any questions or did not request this reset, please contact us at <a href='ehealthmanagementsarawak@gmail.com'>ehealthmanagementsarawak@gmail.com</a>.</p>
                        <p>Thank you,</p>
                        <p><strong>Sarawak E-health Management System Team</strong></p>
                    </body>
                    </html>";

                // Plain text email content
                $mail->AltBody = "Hello,\n\nWe received a request to reset the password for your account associated with this email address. If you did not request a password reset, please ignore this email.\n\nYour password reset code is: $verificationCode\n\nEnter this code on the password reset page to proceed.\n\nIf you have any questions or did not request this reset, please contact us at support@ehealthmanagementsarawak.com.\n\nThank you,\nSarawak E-health Management System Team";

                $mail->send();
                $_SESSION['email'] = $email; // Store email for next steps
                header("Location: verify-code.php");
                exit();
            } catch (Exception $e) {
                $message = "<p style='color: red;'>Email could not be sent. Mailer Error: {$mail->ErrorInfo}</p>";
            }
        } else {
            $message = "<p style='color: red;'>Email not found in the system.</p>";
        }
        $stmt->close();
    } else {
        $message = "<p style='color: red;'>Please enter your email.</p>";
    }
}
?>

<!-- Add the same HTML form as your original one -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="/pageFW/foreign-worker-login.css">
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
            <h2>Forgot Password</h2>
            <?php if (!empty($message)) echo $message; ?> <!-- Display feedback message -->
            <form method="POST" action="">
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <button type="submit" class="sign-in-btn">Reset Password</button>
            </form>
            <p class="sign-up-text">Remember your password? <a href="/views/lo">Sign in here</a></p>
        </div>
    </main>
</body>
</html>
