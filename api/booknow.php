<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Database connection
include "config.php";

try {                                      
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // Validate the data
    if (!isset($data['liveLocation'], $data['driver_name'], $data['car_name'], $data['car_reg_no'], $data['available_seats'], $data['price_per_seat'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input data']);
        exit;
    }

    // Check if liveLocation contains lat and lng
    if (!isset($data['liveLocation']['lat']) || !isset($data['liveLocation']['lng'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Live location data is missing or invalid']);
        exit;
    }

    // Save the values into session variables (if needed)
    $_SESSION['userliveLat'] = $data['liveLocation']['lat'];
    $_SESSION['userliveLng'] = $data['liveLocation']['lng'];
    $_SESSION['driver_name'] = $data['driver_name'];
    $_SESSION['car_name'] = $data['car_name'];
    $_SESSION['car_reg_no'] = $data['car_reg_no'];
    $_SESSION['available_seats'] = $data['available_seats'];
    $_SESSION['price_per_seat'] = $data['price_per_seat'];

    // Use a default user_id (if no session data exists)
    $user_id = isset($_SESSION["id"]) ? $_SESSION["id"] : 2;

    // Prepare SQL query to insert the driver details into the database
    $stmt = $conn->prepare("INSERT INTO driver_db (user_id, driver_name, car_name, car_reg_no, available_seats, price_per_seat) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $user_id, $_SESSION['driver_name'], $_SESSION['car_name'], $_SESSION['car_reg_no'], $_SESSION['available_seats'], $_SESSION['price_per_seat']);

    // Execute the query
    if (!$stmt->execute()) {
        throw new Exception('Failed to save driver details in the database.');
    }

    // Return success response
    $response = ['status' => true, 'message' => 'Driver details saved successfully!'];
    echo json_encode($response);

} catch (Exception $e) {
    // Return error response
    $response = ['status' => false, 'message' => $e->getMessage()];
    echo json_encode($response);
}

// Close the connection
$conn->close();
?>
