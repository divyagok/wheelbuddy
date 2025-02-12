<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type and allow CORS for testing
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

include('config.php'); // Include database connection file


try {
    // Query to fetch user data
    $sql = "SELECT First_Name, Last_Name, Email, Phone_number, Profile_Photo, User_Name, Password, usertype FROM userdetails";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch data and send it back as JSON
        $users = array();
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        echo json_encode(["status" => true, "data" => $users]);
    } else {
        echo json_encode(["status" => false, "message" => "No users found."]);
    }

} catch (Exception $e) {
    echo json_encode(["status" => false, "message" => "Database error: " . $e->getMessage()]);
}

$conn->close();
?>
