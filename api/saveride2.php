<?php
include "config.php";

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

try {                                      

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // Debug log
    error_log("Received data: " . print_r($data, true));

    // Validate the data
    if (!$data) {
        throw new Exception('Invalid JSON data received');
    }

    if (!isset($data['liveLocation'], $data['pickupLocation'], $data['dropLocation'], 
              $data['availableSeats'], $data['distance'])) {
        throw new Exception('Missing required data fields');
    }

    // Store location data in sessions
    $_SESSION['live_location'] = [
        'lat' => $data['liveLocation']['lat'],
        'lng' => $data['liveLocation']['lng']
    ];

    $_SESSION['pickup_location'] = [
        'address' => $data['pickupLocation']['address'],
        'lat' => $data['pickupLocation']['lat'],
        'lng' => $data['pickupLocation']['lng']
    ];

    $_SESSION['drop_location'] = [
        'address' => $data['dropLocation']['address'],
        'lat' => $data['dropLocation']['lat'],
        'lng' => $data['dropLocation']['lng']
    ];

    // Store distance data
    $_SESSION['ride_distance'] = [
        'text' => $data['distance']['text'],
        'value' => $data['distance']['value']  // distance in meters
    ];

    $_SESSION['available_seats'] = $data['availableSeats'];

    // Debug log
    error_log("Session data saved: " . print_r($_SESSION, true));

    if (!isset($_SESSION["id"])) {
        throw new Exception('User not logged in');
    }
    
    $user_id = $_SESSION["id"]; 

    // Return success response
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'Details saved successfully!'
    ]);

} catch (Exception $e) {
    error_log("Error in saveride2.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

// Close the connection
$conn->close();
?>
