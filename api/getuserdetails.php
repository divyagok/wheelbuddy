<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION["id"])) {
    echo "Please log in first.";
    exit;
}

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wheelbuddy"; // Replace with your database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user data based on session user_id
$user_id = $_SESSION["id"];
$sql = "SELECT User_Name, Email, Phone_number FROM userdetails WHERE id = ?";

// Prepare the query
$stmt = $conn->prepare($sql);

// Error checking for prepare() failure
if ($stmt === false) {
    die('MySQL prepare error: ' . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if user exists
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit;
}

// Close the database connection
$conn->close();
?>