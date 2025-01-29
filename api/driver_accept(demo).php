<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Database connection
include "config.php";

try {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // Validate input data
    if (!isset($data['driverId'], $data['liveLocation'], $data['pickupLocation'], $data['dropLocation'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input data']);
        exit;
    }

    $driverId = $data['driverId'];
    $liveLat = $data['liveLocation']['lat'];
    $liveLng = $data['liveLocation']['lng'];
    $pickupLat = $data['pickupLocation']['lat'];
    $pickupLng = $data['pickupLocation']['lng'];
    $dropLat = $data['dropLocation']['lat'];
    $dropLng = $data['dropLocation']['lng'];

    // Insert ride request details into the database
    $stmt = $conn->prepare("
        INSERT INTO ride_requests (
            driver_id, live_lat, live_lng, pickup_latitude, pickup_longitude, drop_latitude, drop_longitude
        ) VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("sdddddd", $driverId, $liveLat, $liveLng, $pickupLat, $pickupLng, $dropLat, $dropLng);

    if (!$stmt->execute()) {
        throw new Exception('Failed to save ride request in the database.');
    }

    // Return success response
    echo json_encode(['status' => true, 'message' => 'Ride request saved successfully.']);
} catch (Exception $e) {
    echo json_encode(['status' => false, 'message' => $e->getMessage()]);
}

// Close the connection
$conn->close();
?>
