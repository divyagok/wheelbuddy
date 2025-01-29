<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Database connection
include "config.php";

try {                                      

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);


    // Validate the data
    if (!isset($data['liveLocation'], $data['pickupLocation'], $data['dropLocation'], $data['availableSeats'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input data']);
    
    }

    $_SESSION['userliveLat'] = $data['liveLocation']['lat'];
    $_SESSION['userliveLng'] = $data['liveLocation']['lng'];
    $_SESSION['userpickupLocation'] = $data['pickupLocation'];
    $_SESSION['userdropLocation'] = $data['dropLocation'];
    $_SESSION['useravailableSeats'] = $data['availableSeats'];

    if (isset($_SESSION["id"])) {
        $user_id = $_SESSION["id"]; 
    }else{
        $user_id = 2;
    }
    
    // // Prepare SQL query to insert the file info into the database
    // $stmt = $conn->prepare("INSERT INTO driver_db (user_id, available_seats, pickup_location, drop_location, pickup_latitude, pickup_longitude) VALUES (?, ?, ?, ?, ?, ?)");
    // $stmt->bind_param("ssssss", $user_id, $availableSeats, $pickupLocation, $dropLocation, $liveLat, $liveLng);

    // // Execute the query
    // if (!$stmt->execute()) {
    //     throw new Exception('Failed to save file info in the database.');
    // }

    // Return success response
    $response = ['status' => true, 'message' => 'Details saved successfully!.'];
    echo json_encode($response);

} catch (Exception $e) {
    // Return error response
    $response = ['status' => false, 'message' => $e->getMessage()];
    echo json_encode($response);
}

// Close the connection
$conn->close();
?>
