<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION["id"])) {
    echo json_encode(['status' => false, 'message' => 'Please log in first.']);
    exit;
}

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wheelbuddy"; // Replace with your actual database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    echo json_encode(['status' => false, 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

// Fetch user ID from session
$user_id = $_SESSION["id"];

// Validate and sanitize input fields
$user_name = isset($_POST['username']) ? trim($_POST['username']) : null;
$first_name = isset($_POST['firstName']) ? trim($_POST['firstName']) : null;
$last_name = isset($_POST['lastName']) ? trim($_POST['lastName']) : null;
$email = isset($_POST['email']) ? trim($_POST['email']) : null;
$phone_number = isset($_POST['phoneNumber']) ? trim($_POST['phoneNumber']) : null;
$password = isset($_POST['password']) ? trim($_POST['password']) : null;
$confirm_password = isset($_POST['confirmPassword']) ? trim($_POST['confirmPassword']) : null;

// Ensure required fields are not empty
if (!$user_name || !$first_name || !$last_name || !$email || !$phone_number) {
    echo json_encode(['status' => false, 'message' => 'All fields are required except password.']);
    exit;
}

// Check if passwords match (only if password is being updated)
if (!empty($password) && $password !== $confirm_password) {
    echo json_encode(['status' => false, 'message' => 'Passwords do not match.']);
    exit;
}

// Check if the user exists
$checkUserQuery = "SELECT * FROM userdetails WHERE id = ?";
$stmt = $conn->prepare($checkUserQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo json_encode(['status' => false, 'message' => 'User not found.']);
    exit;
}
$userData = $result->fetch_assoc(); // Fetch user data for checking existing values

// Handle profile photo upload (if a new one is uploaded)
$profile_photo_path = $userData['Profile_Photo']; // Keep old photo by default
if (isset($_FILES['profilePhoto']) && $_FILES['profilePhoto']['error'] === UPLOAD_ERR_OK) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["profilePhoto"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if file is an image
    $check = getimagesize($_FILES["profilePhoto"]["tmp_name"]);
    if ($check === false) {
        echo json_encode(['status' => false, 'message' => 'File is not an image.']);
        exit;
    }

    // Move uploaded file to target directory
    if (move_uploaded_file($_FILES["profilePhoto"]["tmp_name"], $target_file)) {
        $profile_photo_path = basename($_FILES["profilePhoto"]["name"]);
    } else {
        echo json_encode(['status' => false, 'message' => 'Error uploading file.']);
        exit;
    }
}

// Prepare SQL query for updating user details
$sql = "UPDATE userdetails SET 
        User_Name = ?, 
        First_Name = ?, 
        Last_Name = ?, 
        Email = ?, 
        Phone_number = ?, 
        Profile_Photo = ?";

$params = [$user_name, $first_name, $last_name, $email, $phone_number, $profile_photo_path];
$types = "ssssss";

// Only update password if a new one is provided
if (!empty($password)) {
    $sql .= ", Password = ?";
    $params[] = $password;  // ⚠ Storing in plaintext (Not Recommended)
    $types .= "s";
}

$sql .= " WHERE id = ?";
$params[] = $user_id;
$types .= "i";

// Prepare and execute the statement
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    echo json_encode(['status' => true, 'message' => 'Profile updated successfully.']);
} else {
    echo json_encode(['status' => false, 'message' => 'Error updating profile. Please try again.']);
}

// Close the database connection
$stmt->close();
$conn->close();
?>
