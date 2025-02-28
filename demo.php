<?php
include('api/config.php');

// Fetch details of the driver with ID from session
$driver_id = $_SESSION["id"];

$result = $conn->query("SELECT * FROM driver_db WHERE driver_id = $driver_id AND ride_status='Not Started' ORDER BY driver_id DESC LIMIT 1");

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc(); 

    $car_reg_no = $row["car_reg_no"]; 

} else {
    echo "No driver found.";
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ride Requests - Wheel Buddy</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Bootstrap CSS -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <!-- Font Awesome -->
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
      rel="stylesheet"
    />
    <style>
      body {
        background-image: url('./blurred_image.png');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        color: #333;
        padding-top: 90px;
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
        justify-content: center;
        align-items: flex-start;
        gap: 20px;
        padding: 20px;
        height: calc(100vh - 90px);
      }

      .input-box,
      .map-container {
        background: rgba(255, 255, 255, 0.9);
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        flex: 1;
        max-height: 100%;
        overflow-y: auto;
      }

      .input-box h2 {
        font-weight: bold;
        font-size: 1.75rem;
        margin-bottom: 25px;
      }

      .request-card {
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
      }

      .request-card button {
        background-color: #032d5a;
        color: #fff;
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 1rem;
      }

      .request-card button:hover {
        background-color: #0262a1;
      }

      .map-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
        position: relative;
      }

      #map {
        width: 100%;
        height: 100%;
        border-radius: 10px;
      }

      @media (max-width: 768px) {
        .main-container {
          flex-direction: column;
        }
      }
    </style>
  </head>
  <body> 


    <!-- Header -->
    <div class="header">
      <div class="d-flex align-items-center">
       <a href="provider_avilability.html"> <i class="fas fa-arrow-left text-white me-3"></i></a>
        <a href="home.html"><i class="fas fa-home text-white"></i></a>
      </div>
      <div class="d-flex align-items-center">
        <img alt="Wheel Buddy logo" src="./wb.png" width="100" />
        <div class="dropdown ms-3">
          <i
            class="fas fa-user-circle text-white dropdown-toggle"
            data-bs-toggle="dropdown"
            aria-expanded="false"
            style="cursor: pointer; font-size: 24px"
          ></i>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
            <li><a class="dropdown-item" href="settings.html">Settings</a></li>
            <li><hr class="dropdown-divider" /></li>
            <li><a class="dropdown-item" href="login.html">Logout</a></li>
          </ul>
        </div>
      </div>


    </div>



    <!-- Main Content -->
    <div class="main-container">
      <!-- Ride Requests -->
      <div class="input-box">
        <h2 class="text-dark">Ride Requests</h2>
        <?php
 
            $query = "SELECT 
                        driver_db.pickup_location, 
                        driver_db.drop_location,
                        driver_db.available_seats, 
                        driver_db.pickup_latitude, 
                        driver_db.pickup_longitude,
                        driver_db.ride_id,
                        userdetails.User_Name, 
                        userdetails.Phone_number,
                        driver_db.user_id
                      FROM driver_db 
                      INNER JOIN userdetails ON driver_db.user_id = userdetails.id 
                      WHERE driver_db.user_id != 'no data' 
                      AND driver_db.car_reg_no = '$car_reg_no' 
                      AND driver_db.ride_status='Not Started'";

            $result = $conn->query($query);

            // Check if the query execution failed
            if (!$result) {
                die("Query Error: " . $conn->error);
            }

            // Check if rows exist
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Fetch dynamic data
                  
                    $seats_available = (int) $row['available_seats'];
                    $pickup = $row['pickup_location'];
                    $drop = $row['drop_location'];
                    $userlatitude =  $row['pickup_latitude'];
                    $userlongitude = $row['pickup_longitude'];
                    $userid = $row['user_id'];
                    $username = htmlspecialchars($row['User_Name']);
                    $phone_number = htmlspecialchars($row['Phone_number']);
                    $rideid = $row['ride_id'];

                    $ride_status = "Accepted";

                    echo "<div class='request-card'>
                      <div>
                        <p><strong>Name:</strong> {$username}</p>
                        <p><strong>Pickup:</strong> {$pickup}</p>
                        <p><strong>Drop-off:</strong> {$drop}</p>
                        <p><strong>Contact:</strong> {$phone_number}</p>
                        <p><strong>Seats:</strong> {$seats_available}</p>
                      </div>
      
                      <button onclick='showLiveTracking(\"{$phone_number}\", \"{$drop}\", \"{$pickup}\", \"{$username}\", {$userlatitude}, {$userlongitude}, {$userid}, {$rideid})'>Accept</button>

                      </div>";
                }
            } else {
                echo 'No data found in the table.';
            }

            // Close connection
            $conn->close();
        ?>
      </div>


      <!-- Map -->
      <div class="map-container">
        <div id="map"></div>
      </div>
    </div>

    <!-- Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDquoADXRiPz-PHZ61ZgOsc0EUkAymp9rw&libraries=places" defer></script>
    <script>
      let map;
      let pickupMarkers = [];
      let geocoder;

    
      function initMap() {
        map = new google.maps.Map(document.getElementById("map"), {
            center: {lat: 20.5937, lng: 78.9629}, 
            zoom: 5,
        });

        

        // Mark all user pickup locations
        pickupLocations.forEach(location => {
            addMarker(location.lat, location.lng, location.pickup, location.username);
        });

        if (pickupLocations.length > 0) {
            let firstPickup = pickupLocations[0];
            map.setCenter({ lat: firstPickup.lat, lng: firstPickup.lng });
            map.setZoom(12);
        }
    }
   
    function addMarker(lat, lng, pickup, username) {
        if (!lat || !lng) return;

        const marker = new google.maps.Marker({
            position: { lat, lng },
            map: map,
            title: `Pickup: ${pickup} (User: ${username})`,
            icon: "http://maps.google.com/mapfiles/ms/icons/green-dot.png",
        });
        pickupMarkers.push(marker);
    }


      function geocodeAndMark(address, name) {
        geocoder.geocode({ address: address }, (results, status) => {
          if (status === "OK") {
            const location = results[0].geometry.location;

            new google.maps.Marker({
              position: location,
              map: map,
              title: `${name}'s Pickup Location: ${address}`,
              icon: "http://maps.google.com/mapfiles/ms/icons/red-dot.png",
            });

        
            
          } else {
            console.error(`Geocode failed for ${address}: ${status}`);
          }
        });
      }

      function acceptRequest(pickup, drop) {
        alert(`Accepted ride request from ${pickup} to ${drop}`);
      }


      // Replace the bookNow function with this:
      function showLiveTracking(phone, drop, pickup, username, lat, lng,userid, rideid) {
        window.location.href = `welcome.php?phone=${encodeURIComponent(phone)}&pickup=${encodeURIComponent(pickup)}&drop=${encodeURIComponent(drop)}&username=${encodeURIComponent(username)}&lat=${lat}&lng=${lng}&user_id=${userid}&rideid=${rideid}`;
      }
      
    window.onload = initMap;
    </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
 
