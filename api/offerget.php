<?php
// backend.php
session_start();
// Database credentials
$servername = "localhost"; // Your database server
$username = "root";        // Your MySQL username
$password = "";            // Your MySQL password (empty by default for XAMPP)
$dbname = "wheelbuddy"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_SESSION["id"]; // Assuming the user's ID is stored in session

// Handle file upload
if (isset($_FILES['licenseUpload'])) {
    // Check if the file was uploaded without errors
    if ($_FILES['licenseUpload']['error'] == 0) {
        // Get file details
        $fileTmpPath = $_FILES['licenseUpload']['tmp_name'];
        $fileName = $_FILES['licenseUpload']['name'];  // Get only the file name
        $fileSize = $_FILES['licenseUpload']['size'];
        $fileType = $_FILES['licenseUpload']['type'];

        // Define file upload directory
        $uploadDir = '../user_uploads/';

        // Ensure the file's name is safe by sanitizing it
        $safeFileName = basename($fileName);  // Basename to remove any directory paths
        
        // Check if file already exists in the upload directory
        if (file_exists($uploadDir . $safeFileName)) {
            echo "Error: File already exists.";
        } else {
            // Move the uploaded file to the server directory
            if (move_uploaded_file($fileTmpPath, $uploadDir . $safeFileName)) {
                // Sanitize the filename to avoid SQL injection
                $licenseImage = $conn->real_escape_string($safeFileName); // Use only the filename

                // Insert the filename (not path) into the database
                $sql = "UPDATE userdetails SET driving_license = '$licenseImage' WHERE id = $id";

                if ($conn->query($sql) === TRUE) {
                    echo "File uploaded and data saved successfully!";
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            } else {
                echo "Error uploading the file.";
            }
        }
    } else {
        echo "Error in file upload. Please try again.";
    }
} else {
    echo "No file uploaded.";
}

// Close the database connection
$conn->close();
?>
