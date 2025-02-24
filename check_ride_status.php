<?php
include('api/config.php');

$user_ride_id = $_SESSION['user_ride_id'];

$response = array();
 
$query = "SELECT * FROM driver_db WHERE ride_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_ride_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $response['status'] = $row['ride_status'];
    $response['user_lat'] = $row['pickup_latitude'];
    $response['user_lng'] = $row['pickup_longitude'];
    $response['user_pickup_location'] = $row['pickup_location'];
    $response['user_drop_location'] = $row['drop_location'];
    $driver_ride_id = $row['accepted_driver'];
    
    $query = "SELECT * FROM driver_db WHERE ride_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $driver_ride_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $response['driver_lat'] = $row['pickup_latitude'];
        $response['driver_lng'] = $row['pickup_longitude'];
        $response['driver_pickup_location'] = $row['pickup_location'];
        $response['driver_drop_location'] = $row['drop_location'];
    }
} else {
    $response['error'] = "No data found";
}

header('Content-Type: application/json');
echo json_encode($response);

$conn->close();
?>
