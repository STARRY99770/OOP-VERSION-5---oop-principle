<?php
session_start();
$host = 'ehealth.mysql.database.azure.com';
$user = 'Ehealthsystem';
$password = 'ehealth@123';
$database = 'foreign_workers';

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['form_id'])) {
    $form_id = intval($_GET['form_id']);

    // Fetch the file path from the database
    $stmt = $conn->prepare("SELECT form_file FROM forms WHERE form_id = ?");
    $stmt->bind_param("i", $form_id);
    $stmt->execute();
    $stmt->bind_result($file_path);
    $stmt->fetch();
    $stmt->close();

    if ($file_path) {
        // Adjust the file path to point to the correct directory
        $file_path = __DIR__ . '/../pageFW/' . $file_path; // Navigate to the correct directory

        // Debugging: Output the file path
        echo "File path (relative): " . $file_path . "<br>";
        echo "File path (absolute): " . realpath($file_path) . "<br>";

        if (file_exists($file_path)) {
            // Serve the file as a PDF
            header("Content-Type: application/pdf");
            header("Content-Disposition: inline; filename=" . basename($file_path));
            readfile($file_path);
        } else {
            echo "Error: File not found for form_id = $form_id. Path: $file_path";
        }
    } else {
        echo "Error: No file path found for form_id = $form_id.";
    }
} else {
    echo "Error: No form_id provided.";
}

$conn->close();
?>
