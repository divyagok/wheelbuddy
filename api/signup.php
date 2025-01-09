<?php

include('config.php');

// Define a function to ensure the directory exists
function ensureDirectoryExists($directoryPath) {
    if (!file_exists($directoryPath)) {
        if (!mkdir($directoryPath, 0755, true)) {
            throw new Exception("Failed to create directory: $directoryPath");
        }
    }
}

// Define a function to upload a file and return its filename
function uploadFile($file, $destinationDirectory) {
    try {
        ensureDirectoryExists($destinationDirectory);
        $filename = basename($file['name']);
        $filePath = $destinationDirectory . $filename;

        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            throw new Exception("Failed to move uploaded file.");
        }

        return $filename; // Return only the filename
    } catch (Exception $e) {
        die("File upload error: " . $e->getMessage());
    }
}

// Main logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $First_Name = $_POST['First_Name'];
        $Last_Name = $_POST['Last_name'];
        $Email = $_POST['Email'];
        $Phone_number = $_POST['Phone_number'];
        $Username = $_POST['Username'];
        $Password = $_POST['Password'];

        // Check if email or username already exists
        $checkQuery = "SELECT * FROM userdetails WHERE Email = ? OR User_Name = ?";
        $checkStmt = $conn->prepare($checkQuery);
        if (!$checkStmt) {
            throw new Exception("Database error: " . $conn->error);
        }
        $checkStmt->bind_param('ss', $Email, $Username);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            throw new Exception("Email or Username already exists. Please try a different one.");
        }
        $checkStmt->close();

        // Validate if profile photo is uploaded
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
            // Define the directory for uploads
            $uploadDirectory = __DIR__ . '/../user_uploads/';
            $profilePhotoFilename = uploadFile($_FILES['profile_photo'], $uploadDirectory);
        } else {
            throw new Exception("Profile photo is required.");
        }

        // Insert data into the database
        $sql = "INSERT INTO userdetails (First_Name, Last_Name, Email, Phone_number, Profile_Photo, User_Name, Password, usertype) VALUES (?, ?, ?, ?, ?, ?, ?, 1)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Database error: " . $conn->error);
        }
        $stmt->bind_param('sssssss', $First_Name, $Last_Name, $Email, $Phone_number, $profilePhotoFilename, $Username, $Password);

        if ($stmt->execute()) {
            echo "<script type='text/javascript'>alert('Success'); window.location.href='../login.html';</script>";
        } else {
            throw new Exception("Error executing query: " . $stmt->error);
            
        }

        $stmt->close();
    } catch (Exception $e) {
        
        echo "<script type='text/javascript'>alert('Error: ". addslashes($e->getMessage()) . " '); window.location.href='../login.html';</script>";

    } finally {
        $conn->close();
    }
}

?>
