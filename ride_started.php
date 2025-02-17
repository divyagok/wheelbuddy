
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ride Started - Wheel Buddy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>

    <style>
        body {
            background-image: url('./blurred_image.png');
            background-size: cover;
            background-position: center;
            padding-top: 20px;
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
            background: rgba(255, 255, 255, 0.95);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 2px solid #032d5a;
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
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .eta-card {
            background: #e9ecef;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

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
</head>
<body>
    <div class="tracking-container">
        <div class="info-panel">
            <h4 class="mb-4">Ride Details</h4>
            
            <div class="status-card">
                <h6>Pickup: <?php echo htmlspecialchars($username); ?></h6>
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
                <button class="arrived-btn">🚗 Ride Started</button>
            </div>
        </div>

        <div class="map-container">
            <div id="map"></div>
        </div>
    </div>

    <script>
        function initMap() {
            let map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: 20.5937, lng: 78.9629 },
                zoom: 14
            });

            let userMarker = new google.maps.Marker({
                position: { lat: 20.5937, lng: 78.9629 },
                map: map,
                icon: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png',
                title: "User's Location"
            });

            let driverMarker = new google.maps.Marker({
                position: { lat: 20.5947, lng: 78.9645 },
                map: map,
                icon: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png',
                title: "Driver's Location"
            });
        }

        window.onload = initMap;
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDquoADXRiPz-PHZ61ZgOsc0EUkAymp9rw&callback=initMap" defer></script>
</body>
</html>
