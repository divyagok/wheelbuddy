<?php
include('api/config.php');

// Get ride details from URL parameters
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';
$phone = isset($_GET['phone']) ? $_GET['phone'] : '';
$pickup = isset($_GET['pickup']) ? $_GET['pickup'] : '';
$drop = isset($_GET['drop']) ? $_GET['drop'] : '';
$username = isset($_GET['username']) ? $_GET['username'] : '';
$userLat = isset($_GET['lat']) ? $_GET['lat'] : 0;
$userLng = isset($_GET['lng']) ? $_GET['lng'] : 0;



$ride_id = $_GET['rideid'];

// Update ride status
if ($user_id) {
    $stmt = $conn->prepare("UPDATE driver_db SET ride_status = 'In Progress' WHERE ride_id = ?");
    $stmt->bind_param("s", $ride_id);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Tracking - Wheel Buddy</title>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDquoADXRiPz-PHZ61ZgOsc0EUkAymp9rw&libraries=places,geometry" defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('./blurred_image.png');
            background-size: cover;
            background-position: center;
            /* padding-top: 20px; */
        }
        
        .tracking-container {
            display: flex;
            gap: 20px;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .info-panel {
            flex: 1;
            background: rgba(240, 243, 245, 0.95);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .map-container {
            flex: 2;
            height: 80vh;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        #map {
            width: 100%;
            height: 100%;
        }
        .status-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .eta-card {
            background: #e9ecef;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .btn-danger{
            background:red;
            color:#fff;
            border:none;
            /* height:200px; */
            width: 150px;
        }
        .header {
        background-color: #032d5a;
        padding: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #f0f0f0;
      }

      .header img {
        height: 50px;
      }
    </style>
</head>
<body>
    <!-- header -->

    <div class="header">
      <div class="d-flex align-items-center">
        <a href="./welcome.html" ><i class="fas fa-arrow-left text-white me-3"></i></a>
        <a href="./newhome.html" ><i class="fas fa-home text-white"></i></a>
      </div>
      <div class="d-flex align-items-center">
        <img
          alt="Wheel Buddy logo"
          src="./wb.png"
          width="100"
        />
        <div class="dropdown ms-3">
          <i
            class="fas fa-user-circle text-white dropdown-toggle"
            data-bs-toggle="dropdown"
            aria-expanded="false"
            style="cursor: pointer; font-size: 24px"
          ></i>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="#">Profile</a></li>
            <li><a class="dropdown-item" href="./settings.html">Settings</a></li>
            <li><hr class="dropdown-divider" /></li>
            <li><a class="dropdown-item" href="#">Logout</a></li>
          </ul>
        </div>
      </div>
    </div>    <div class="tracking-container">
    <!-- ✅ Ride Details Box with Border & Header -->
    <div class="info-panel">
        <div class="ride-details-header">Ride Details</div> <!-- ✅ Added Header -->

        <div class="status-card">
            <h6>Pickup: <?php echo htmlspecialchars($username);  ?> </h6>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($pickup); ?></p>
            <p><strong>Contact:</strong> <?php echo htmlspecialchars($phone); ?></p>
        </div>

        <div class="eta-card">
            <p><strong>Distance:</strong> <span id="distance">Calculating...</span></p>
            <p><strong>ETA:</strong> <span id="eta">Calculating...</span></p>
        </div>
            <div class="status-card">
                <h6>Drop Location</h6>
                <p><?php echo htmlspecialchars($drop); ?></p>
            </div>
            <div class="status-card arrived-box">
    <a href="ride_started.php"><button class=arrived-btn>🚗 Reached the user</button></a>
</div>

<style>
    .arrived-box {
        background: linear-gradient(135deg, #28a745 30%, #218838 100%);
        border: none;
        padding: 15px;
        text-align: center;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .arrived-box:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
    }

    .arrived-btn {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        font-size: 1.2rem;
        font-weight: bold;
        padding: 12px 25px;
        border: 2px solid white;
        border-radius: 10px;
        width: 100%;
        cursor: pointer;
        transition: background 0.3s ease, color 0.3s ease;
    }

    .arrived-btn:hover {
        background: white;
        color: #218838;
        border-color: #218838;
    }
</style>

        </div>
        
        <div class="map-container">
            <div id="map"></div>
        </div>
    </div>

    <script>
    let map;
    let driverMarker;
    let userMarker;
    let directionsService;
    let directionsRenderer;
    const userPosition = { lat: <?php echo $userLat; ?>, lng: <?php echo $userLng; ?> };

    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            center: userPosition,
            zoom: 15
        });

        directionsService = new google.maps.DirectionsService();
        directionsRenderer = new google.maps.DirectionsRenderer({
            map: map,
            suppressMarkers: true
        });

        // Add user marker
        userMarker = new google.maps.Marker({
            position: userPosition,
            map: map,
            icon: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png',
            title: 'User Location'
        });

        // Initialize driver marker with current position
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const driverPosition = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    
                    driverMarker = new google.maps.Marker({
                        position: driverPosition,
                        map: map,
                        icon: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png',
                        title: 'Driver Location'
                    });

                    // Start tracking
                    startTracking();
                    // Calculate initial route
                    calculateRoute();
                },
                () => {
                    alert("Error: Could not get your location.");
                }
            );
        }
    }

    function startTracking() {
        // Watch driver's position
        if (navigator.geolocation) {
            navigator.geolocation.watchPosition(
                (position) => {
                    const newPosition = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    
                    // Update driver marker position
                    driverMarker.setPosition(newPosition);
                    
                    // Recalculate route and update ETA
                    calculateRoute();

                    // Save driver's location to database
                    updateDriverLocation(newPosition.lat, newPosition.lng);
                },
                (error) => {
                    console.error("Error watching position:", error);
                },
                {
                    enableHighAccuracy: true,
                    maximumAge: 0,
                    timeout: 5000
                }
            );
        }
    }

    function calculateRoute() {
        if (!driverMarker) return;

        const request = {
            origin: driverMarker.getPosition(),
            destination: userMarker.getPosition(),
            travelMode: google.maps.TravelMode.DRIVING
        };

        directionsService.route(request, (result, status) => {
            if (status === 'OK') {
                directionsRenderer.setDirections(result);
                
                // Update distance and ETA
                const route = result.routes[0];
                if (route.legs.length > 0) {
                    const leg = route.legs[0];
                    document.getElementById('distance').textContent = leg.distance.text;
                    document.getElementById('eta').textContent = leg.duration.text;
                }
            }
        });
    }

    function updateDriverLocation(lat, lng) {
        fetch('api/update_driver_location.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                user_id: '<?php echo $user_id; ?>',
                latitude: lat,
                longitude: lng
            })
        })
        .catch(error => console.error('Error updating driver location:', error));
    }

    // Initialize map when the page loads
    window.onload = initMap;
    </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
