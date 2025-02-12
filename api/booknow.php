<?php

include "config.php";

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

try {
    // Get the POST data
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // Debug log
    error_log("Received booking data: " . print_r($data, true));

    // Validate required data
    if (!isset($data['car_name'], $data['driver_name'], $data['car_reg_no'], 
              $data['available_seats'], $data['liveLocation'])) {
        throw new Exception('Missing required booking data');
    }

    // Get user ID from session
    $user_id = isset($_SESSION["id"]) ? $_SESSION["id"] : 0;

    // Get location details from session
    $pickup_location = isset($_SESSION['pickup_location']['address']) ? $_SESSION['pickup_location']['address'] : '';
    $pickup_lat = isset($_SESSION['pickup_location']['lat']) ? $_SESSION['pickup_location']['lat'] : 0;
    $pickup_lng = isset($_SESSION['pickup_location']['lng']) ? $_SESSION['pickup_location']['lng'] : 0;
    $drop_location = isset($_SESSION['drop_location']['address']) ? $_SESSION['drop_location']['address'] : '';

    // Basic SQL query with essential fields
    $sql = "INSERT INTO driver_db (
        user_id,
        driver_name,
        car_name,
        car_reg_no,
        available_seats,
        pickup_location,
        drop_location,
        pickup_latitude,
        pickup_longitude,
        ride_status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Not Started')";

    error_log("SQL Query: " . $sql); // Debug log

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param(
        "isssissdd",
        $user_id,
        $data['driver_name'],
        $data['car_name'],
        $data['car_reg_no'],
        $data['available_seats'],
        $pickup_location,
        $drop_location,
        $pickup_lat,
        $pickup_lng
    );

    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    // Return success response
    echo json_encode([
        'status' => 'success',
        'message' => 'Booking saved successfully!',
        'booking_details' => [
            'pickup' => $pickup_location,
            'drop' => $drop_location,
            'car' => $data['car_name'],
            'driver' => $data['driver_name']
        ]
    ]);

} catch (Exception $e) {
    error_log("Error in booknow.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

// Close the connection
if (isset($stmt)) {
    $stmt->close();
}
$conn->close();