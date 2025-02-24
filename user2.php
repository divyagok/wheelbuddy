<?php
include('api/config.php');

// Get ride details from URL parameters with proper handling
$user_lat = isset($_GET['user_lat']) ? floatval($_GET['user_lat']) : 0;
$user_lng = isset($_GET['user_lng']) ? floatval($_GET['user_lng']) : 0;
$user_pickup = isset($_GET['user_pickup']) ? urldecode($_GET['user_pickup']) : '';
$user_drop = isset($_GET['user_drop']) ? urldecode($_GET['user_drop']) : '';

$driver_lat = isset($_GET['driver_lat']) ? floatval($_GET['driver_lat']) : 0;
$driver_lng = isset($_GET['driver_lng']) ? floatval($_GET['driver_lng']) : 0;
$driver_pickup = isset($_GET['driver_pickup']) ? urldecode($_GET['driver_pickup']) : '';
$driver_drop = isset($_GET['driver_drop']) ? urldecode($_GET['driver_drop']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Tracking - Wheel Buddy</title>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDquoADXRiPz-PHZ61ZgOsc0EUkAymp9rw&libraries=places&callback=initMap" async defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('./blurred_image.png');
            background-size: cover;
            background-position: center;
            margin: 0;
            padding: 0;
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

        .cancel-ride-box {
            background: #4035dc;
            border: none;
            padding: 15px;
            text-align: center;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-top: 15px;
        }

        .cancel-ride-box:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
        }

        .cancel-btn {
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

        .cancel-btn:hover {
            background: white;
            color:#4035dc;
            border-color: #4035dc;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="d-flex align-items-center">
            <a href="./index.php"><i class="fas fa-arrow-left text-white me-3"></i></a>
            <a href="./home.html"><i class="fas fa-home text-white"></i></a>
        </div>
        <div class="d-flex align-items-center">
            <img alt="Wheel Buddy logo" src="./wb.png" width="100"/>
            <div class="dropdown ms-3">
                <i class="fas fa-user-circle text-white dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer; font-size: 24px"></i>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                    <li><a class="dropdown-item" href="./settings.html">Settings</a></li>
                    <li><hr class="dropdown-divider" /></li>
                    <li><a class="dropdown-item" href="login.html">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="tracking-container"> 
        <div class="info-panel">
            <div class="ride-details-header">Ride Details</div>

            <div class="status-card">
                <h6>Pickup Location</h6>
                <p id="pickup-location"><?php echo htmlspecialchars($user_pickup); ?></p>
            </div>

            <div class="status-card">
                <h6>Drop Location</h6>
                <p id="drop-location"><?php echo htmlspecialchars($user_drop); ?></p>
            </div>

            <div class="eta-card">
                <p><strong>Distance:</strong> <span id="distance">Calculating...</span></p>
                <p><strong>ETA:</strong> <span id="eta">Calculating...</span></p>
            </div>

            <div class="cancel-ride-box">
                <button class="cancel-btn buy_now" data-id="">Pay Now</button>
            </div>
        </div>
        
        <div class="map-container">
            <div id="map"></div>
        </div>
    </div>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

    <script>
                $('body').on('click', '.buy_now', function(e) {

                    var list = $("input[name='fees[]']:checked").map(function() {
                        return this.value;
                    }).get();

                    var totalAmount = $(this).attr("data-amount");
                    var product_id = $(this).attr("data-id");

                    if (list == "") {

                        // $(".em").hide()

                        // $("#routesname").show()

                        alert('Select Fees.');
                        return false;
                    }

                    // var routeName = $('#route_name').val();

                    var options = {
                        "key": "",
                        "amount": (totalAmount * 100), // 2000 paise = INR 20
                        "name": "KPR School",
                        "image": "../Image/LOGO.png",
                        "description": "Payment",

                        "handler": function(response) {
                            $.ajax({
                                url: 'paymentbk.php',
                                type: 'post',
                                dataType: 'json',
                                data: {
                                    razorpay_payment_id: response.razorpay_payment_id,
                                    totalAmount: totalAmount,
                                    product_id: product_id,
                                },
                                success: function(msg) {
                                    window.location.href = 'payment-success.php';
                                }
                            });

                        },

                        "theme": {
                            "color": "#528FF0"
                        }
                    };

                    var rzp1 = new Razorpay(options);
                    rzp1.open();
                    e.preventDefault();
                });
            </script>            

    <script>
    let map, directionsService, directionsRenderer;
    let markers = [];

    // Fetching values from PHP
    let userLat = <?php echo json_encode($user_lat); ?>;
    let userLng = <?php echo json_encode($user_lng); ?>;
    let userPickup = <?php echo json_encode($user_pickup); ?>;
    let userDrop = <?php echo json_encode($user_drop); ?>;

    function initMap() {
        if (!userLat || !userLng) {
            console.error("Invalid user latitude/longitude values.");
            return;
        }

        map = new google.maps.Map(document.getElementById("map"), {
            center: { lat: userLat, lng: userLng },
            zoom: 14,
            styles: [
                {
                    featureType: "poi",
                    elementType: "labels",
                    stylers: [{ visibility: "off" }]
                }
            ]
        });

        directionsService = new google.maps.DirectionsService();
        directionsRenderer = new google.maps.DirectionsRenderer({
            map: map,
            suppressMarkers: true,
            polylineOptions: {
                strokeColor: "#032d5a",
                strokeWeight: 5,
                strokeOpacity: 0.8
            }
        });

        // Add user pickup marker
        addMarker({
            position: { lat: userLat, lng: userLng },
            title: "User Pickup",
            icon: {
                url: "car.png",
                scaledSize: new google.maps.Size(40, 40)
            },
            content: `<strong>Pickup Location</strong><br><small>${userPickup}</small>`
        });

        // Geocode the drop location and add marker
        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({ address: userDrop }, (results, status) => {
            if (status === "OK" && results[0]) {
                const dropPoint = results[0].geometry.location;
                
                // Add drop location marker
                addMarker({
                    position: dropPoint,
                    title: "Drop Location",
                    icon: {
                        url: "man.png",
                        scaledSize: new google.maps.Size(40, 40)
                    },
                    content: `<strong>Drop Location</strong><br><small>${userDrop}</small>`
                });

                // Calculate and display route
                calculateRoute({ lat: userLat, lng: userLng }, dropPoint);

                // Fit map to show both markers
                const bounds = new google.maps.LatLngBounds();
                markers.forEach(marker => bounds.extend(marker.getPosition()));
                map.fitBounds(bounds);
            } else {
                console.error("Geocode was not successful: " + status);
            }
        });
    }

    function addMarker(markerData) {
        const marker = new google.maps.Marker({
            position: markerData.position,
            map: map,
            title: markerData.title,
            icon: markerData.icon
        });

        const infoWindow = new google.maps.InfoWindow({
            content: markerData.content
        });

        marker.addListener("click", () => {
            infoWindow.open(map, marker);
        });

        markers.push(marker);
        return marker;
    }

    function calculateRoute(origin, destination) {
        directionsService.route(
            {
                origin: origin,
                destination: destination,
                travelMode: google.maps.TravelMode.DRIVING
            },
            (response, status) => {
                if (status === "OK") {
                    directionsRenderer.setDirections(response);
                    const route = response.routes[0];
                    const leg = route.legs[0];

                    // ✅ Updating the Distance and ETA on UI
                    document.getElementById("distance").innerText = leg.distance.text;
                    document.getElementById("eta").innerText = leg.duration.text;

                    console.log("Distance: " + leg.distance.text);
                    console.log("ETA: " + leg.duration.text);
                } else {
                    console.error("Directions request failed due to " + status);
                }
            }
        );
    }

    // Ensure the map initializes properly
    google.maps.event.addDomListener(window, 'load', initMap);
</script>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

            <script>
                $('body').on('click', '.buy_now', function(e) {

                    var list = $("input[name='fees[]']:checked").map(function() {
                        return this.value;
                    }).get();

                    var totalAmount = $(this).attr("data-amount");
                    var product_id = $(this).attr("data-id");

                    if (list == "") {

                        // $(".em").hide()

                        // $("#routesname").show()

                        alert('Select Fees.');
                        return false;
                    }

                    // var routeName = $('#route_name').val();

                    var options = {
                        "key": "",
                        "amount": (totalAmount * 100), // 2000 paise = INR 20
                        "name": "KPR School",
                        "image": "../Image/LOGO.png",
                        "description": "Payment",

                        "handler": function(response) {
                            $.ajax({
                                url: 'paymentbk.php',
                                type: 'post',
                                dataType: 'json',
                                data: {
                                    razorpay_payment_id: response.razorpay_payment_id,
                                    totalAmount: totalAmount,
                                    product_id: product_id,
                                },
                                success: function(msg) {
                                    window.location.href = 'payment-success.php';
                                }
                            });

                        },

                        "theme": {
                            "color": "#528FF0"
                        }
                    };

                    var rzp1 = new Razorpay(options);
                    rzp1.open();
                    e.preventDefault();
                });
            </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
