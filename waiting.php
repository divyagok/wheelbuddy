<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Waiting for Driver - Wheel Buddy</title>
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

  <style>
    body {
      background-image: url('./blurred_image.png');
      background-size: cover;
      background-position: center;
      min-height: 100vh;
      color: #333;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }

    .header {
      background-color: #032d5a;
      padding: 15px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: #f0f0f0;
      position: fixed;
      width: 100%;
      top: 0;
      z-index: 1000;
    }

    .waiting-box {
      background: rgba(255, 255, 255, 0.95);
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
      text-align: center;
      max-width: 400px;
      width: 100%;
    }

    .loader {
      border: 5px solid #f3f3f3;
      border-top: 5px solid #032d5a;
      border-radius: 50%;
      width: 50px;
      height: 50px;
      animation: spin 1s linear infinite;
      margin: 15px auto;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    .status-message {
      font-size: 1.2rem;
      color: #032d5a;
      margin-top: 15px;
      font-weight: bold;
    }

    .cancel-btn {
      background-color: red;
      color: white;
      padding: 10px 15px;
      border-radius: 8px;
      font-size: 1rem;
      width: 100%;
      margin-top: 15px;
      border: none;
      cursor: pointer;
    }
  </style>
</head>

<?php
include('api/config.php');

$user_ride_id = $_SESSION['user_ride_id'] ;
 
            $query = "SELECT 
                        driver_db.pickup_location, 
                        driver_db.drop_location,
                        driver_db.available_seats, 
                        driver_db.pickup_latitude, 
                        driver_db.pickup_longitude,
                        driver_db.ride_id,
                        driver_db.ride_status,
                        userdetails.User_Name, 
                        userdetails.Phone_number,
                        driver_db.user_id
                      FROM driver_db 
                      INNER JOIN userdetails ON driver_db.user_id = userdetails.id 
                      WHERE driver_db.ride_id = $user_ride_id 
                     ";

            $result = $conn->query($query);

            // Check if the query execution failed
            if (!$result) {
                die("Query Error: " . $conn->error);
            }

            // Check if rows exist
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Fetch dynamic data
                  

                    $ridestatus = $row['ride_status'];

                    

                    echo $ridestatus;
                }
            } else {
                echo 'No data found in the table.';
            }

            // Close connection
            $conn->close();
        ?>
<body>
  <!-- Header -->
                    <div class="waiting-box">
                      <h2>Waiting for Driver</h2>
                      <div class="loader"></div>
                      <p class="status-message">Your request has been sent to nearby drivers.<br>Waiting for acceptance...</p>
                      <button class="cancel-btn" onclick="cancelRequest()">Cancel Request</button>
                    </div>
  <div class="header">
    <div class="d-flex align-items-center">
      <i class="fas fa-arrow-left text-white me-3"></i>
      <i class="fas fa-home text-white"></i>
    </div>
    <div class="d-flex align-items-center">
      <img alt="Wheel Buddy logo" src="./wb.png" width="100">
    </div>
  </div>

  <!-- Waiting Box -->
  



  <script>
    function cancelRequest() {
      alert("Ride request cancelled!");
      window.location.href = "home.html"; // Redirect to home or previous page
    }

    function checkDriverStatus() {
      // Simulating API call to check if a driver accepted the ride
      setTimeout(() => {
        let driverAccepted = Math.random() < 0.3; // Simulate a 30% chance of acceptance
        if (driverAccepted) {
          alert("Driver has accepted your request!");
          window.location.href = "user2.html"; // Redirect to ride in progress page
        } else {
          checkDriverStatus(); // Keep checking until a driver accepts
        }
      }, 5000);
    }

    window.onload = checkDriverStatus;
  </script>
</body>
</html>
