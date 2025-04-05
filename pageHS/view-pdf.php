<?php
// view_file.php
$servername = "localhost";
$username = "root";
$password = "";
$database = "foreign_workers";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['form_id'])) {
    $form_id = $_GET['form_id'];

    // Prepare the SQL query to get the BLOB data
    $sql = "SELECT form_file FROM forms WHERE form_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $form_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($form_file);

    if ($stmt->fetch()) {
        // Set headers for PDF content
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="form.pdf"');

        // Output the file contents directly
        echo $form_file;
    } else {
        echo "File not found.";
    }

    $stmt->close();
} else {
    echo "No form ID provided.";
}

$conn->close();
?>
