<?php
include('api/config.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Available Cars - Wheel Buddy</title>
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDquoADXRiPz-PHZ61ZgOsc0EUkAymp9rw&libraries=places" defer></script>

  <style>
      body {
      background-image: url('./blurred_image.png');
      background-size: cover;
      background-position: center;
      color: #333;
      padding-top: 90px;
      display: flex;
      justify-content: space-between;
      gap: 20px;
    }

    .header {
      background-color: #032d5a;
      padding: 15px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: #f0f0f0;
      position: fixed;
      width: 100%;
      top: 0;
      z-index: 1000;
    }

    .header img {
      height: 50px;
    }

    .main-container {
      display: flex;
      gap: 20px;
      margin-top: 80px;
      width: 100%;
      height: calc(100vh - 90px);
      overflow: hidden;
    }

    /* Left side - Driver Details */
    .drivers-container {
      flex: 1;
      overflow-y: auto;
      max-height: calc(100vh - 90px);
      padding: 20px;
      background: rgba(255, 255, 255, 0.9);
      border-radius: 10px;
      box-shadow: 0 6px 10px rgba(0, 0, 0, 0.1);
    }

    .driver-card {
      background: rgba(255, 255, 255, 0.9);
      padding: 12px;
      border-radius: 8px;
      box-shadow: 0 6px 10px rgba(0, 0, 0, 0.1);
      text-align: center;
      font-size: 12px;
      margin-bottom: 15px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .driver-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 18px rgba(0, 0, 0, 0.2);
    }

    .driver-card img {
      width: 30px;
      height: 50px;
      border-radius: 8px;
      margin-bottom: 8px;
    }

    .driver-details {
      margin-top: 10px;
    }

    .driver-details h4 {
      font-size: 1rem;
      margin: 5px 0;
      font-weight: bold;
    }

    .driver-details p {
      margin: 3px 0;
      font-size: 0.85rem;
      color: #555;
    }

    .driver-card button {
      margin-top: 10px;
      background-color: #032d5a;
      color: #fff;
      padding: 8px;
      border-radius: 6px;
      border: none;
      cursor: pointer;
      font-size: 0.9rem;
      width: 30%; /* Reduced width */
      transition: background-color 0.3s ease;
    }

    .driver-card button:hover {
      background-color: #0262a1;
    }

    /* Right side - Map */
    .map-container {
      width: 60%;
      height: 100%;
      border-radius: 15px;
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
      border: 3px solid #032d5a;
    }

    #map {
      width: 100%;
      height: 100%;
      border-radius: 15px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .main-container {
        flex-direction: column;
        gap: 20px;
      }

      .map-container {
        width: 100%;
        height: 45vh;
      }

      .drivers-container {
        width: 100%;
      }
    }
  </style>
</head>
<body>
  <!-- Header -->
  <div class="header">
    <div class="d-flex align-items-center">
      <i class="fas fa-arrow-left text-white me-3"></i>
      <i class="fas fa-home text-white"></i>
    </div>
    <div class="d-flex align-items-center">
      <img alt="Wheel Buddy logo" src="./wb.png" width="100" />
      <div class="dropdown ms-3">
        <i class="fas fa-user-circle text-white dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"
          style="cursor: pointer; font-size: 24px"></i>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="#">Profile</a></li>
          <li><a class="dropdown-item" href="#">Settings</a></li>
          <li><hr class="dropdown-divider" /></li>
          <li><a class="dropdown-item" href="#">Logout</a></li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Main Container -->
  <div class="main-container">
    <!-- Left Side - Driver Details -->
    <div class="drivers-container">
    <?php
    $query = "SELECT * FROM driver_db WHERE driver_id !='no data' AND ride_status='Not Started'";

    // Execute Query
    $result = $conn->query($query);
    
    // Check if the query execution failed
    if (!$result) {
        die("Query Error: " . $conn->error);  // This will show the actual MySQL error
    }
    
    // Check if rows exist
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Fetch dynamic data
            $driver_name = htmlspecialchars($row['driver_name']);
             $car_model = htmlspecialchars($row['car_name']);
             $car_reg_no = htmlspecialchars($row['car_reg_no']);
             $seats_available = (int) $row['available_seats'];
             $price_per_seat = (int) $row['price_per_seat'];
             $latitude = (float) $row['pickup_latitude'];  // Ensure these values exist in your DB
             $longitude = (float) $row['pickup_longitude'];
    
            echo "<div class='driver-card'>
                <img src='./car1.jpg' alt='Car' />
                <div class='driver-details'>
                    <h4>{$car_model}</h4>
                    <p><strong>Driver:</strong> {$driver_name}</p>
                    <p><strong>Car Reg No:</strong> {$car_reg_no}</p>
                    <p><strong>Seats Available:</strong> {$seats_available}</p>
                    <p><strong>Price per Seat:</strong> ₹{$price_per_seat}</p>
                </div>
                <a href='user2.html'>
                    <button onclick=\"bookNow('$car_model', '$driver_name', '$car_reg_no', $seats_available, $price_per_seat, { lat: $latitude, lng: $longitude })\">Book Now</button>
                </a>
            </div>";
        }
    } else {
        echo 'No data found in the table.';
    }
    
    // Close connection
    $conn->close();
    ?>

      
    </div>

    <!-- Right Side - Map -->
    <div class="map-container">
      <div id="map"></div>
    </div>
  </div>
  <script>
    let map;

    function initMap() {
      const centerLocation = { lat: 20.5937, lng: 78.9629 };
      map = new google.maps.Map(document.getElementById("map"), {
        center: centerLocation,
        zoom: 12,
      });
    }

    function bookNow(carName, driverName, carRegNo, seatsAvailable, pricePerSeat, carLocation) {
  // Add marker for selected car
  new google.maps.Marker({
    position: carLocation,
    map: map,
    title: carName,
  });

  // Data to send to the backend
  const bookingDetails = {
    driver_name: driverName,
    car_name: carName,
    car_reg_no: carRegNo,
    available_seats: seatsAvailable,
    price_per_seat: pricePerSeat,
    liveLocation: carLocation,  // Directly pass the location object here
  };

  // Send data to backend via AJAX
  $.ajax({
    url: 'api/booknow.php', // Replace with your backend PHP file
    type: 'POST',
    data: JSON.stringify(bookingDetails), // Send data as JSON
    contentType: 'application/json', // Set the content type to JSON
    success: function(response) {
      console.log("Server Response: ", response);
      alert(`You have successfully booked a seat in ${carName}`);
    },
    error: function(xhr, status, error) {
      console.error("Error: ", status, error);
      alert("There was an error with your booking.");
    }
  });
}


    // Initialize map on page load
    window.onload = initMap;
  </script>
</body>
</html>
