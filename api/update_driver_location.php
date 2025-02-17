<?php
include('config.php');

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['user_id']) && isset($data['latitude']) && isset($data['longitude'])) {
    $user_id = $data['user_id'];
    $latitude = $data['latitude'];
    $longitude = $data['longitude'];

    // Update driver location in database
    $stmt = $conn->prepare("UPDATE driver_db SET driver_latitude = ?, driver_longitude = ? WHERE user_id = ?");
    $stmt->bind_param("dds", $latitude, $longitude, $user_id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update location']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
}
?>
