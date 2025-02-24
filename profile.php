<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION["id"])) {
    echo "Please log in first.";
    exit;
}

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wheelbuddy"; // Replace with your database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user data based on session user_id
$user_id = $_SESSION["id"];
$sql = "SELECT User_Name, Email, Phone_number FROM userdetails WHERE id = ?";

// Prepare the query
$stmt = $conn->prepare($sql);

// Error checking for prepare() failure
if ($stmt === false) {
    die('MySQL prepare error: ' . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if user exists
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit;
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile - Wheel Buddy</title>

  <!-- Tailwind CSS -->
  <link href="styles.css" rel="stylesheet"> <!-- Link to your built CSS -->
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
  
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  
  <style>
    /* Custom Styles */
    body {
      background-image: url('./blurred_image.png');
      background-size: cover;
      background-position: center;
      min-height: 100vh;
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

    /* Profile Container with Outer Border */
    .profile-container {
      max-width: 600px;
      margin: 50px auto;
      background: rgba(255, 255, 255, 0.95);
      padding: 25px;
      border-radius: 12px;
      border: 2px solid #032d5a;  /* 🔹 Added Outer Border */
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
      text-align: center;
      transition: all 0.3s ease-in-out;
    }

    .profile-container:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 18px rgba(0, 0, 0, 0.4);
    }

    .profile-container h2 {
      font-weight: bold;
      margin-bottom: 15px;
      color: #032d5a;
      text-transform: uppercase;
    }

    /* Inner Border for Profile Details */
    .profile-details {
      text-align: left;
      font-size: 1rem;
      color: #555;
      padding: 15px;
      border-radius: 10px;
      border: 2px solid #ddd; /* 🔹 Inner Border */
      background: #f8f9fa;
      transition: all 0.3s ease-in-out;
    }

    .profile-details:hover {
      border-color: #032d5a;
      background: #e9ecef;
    }

    .profile-details p {
      margin: 8px 0;
      font-size: 1rem;
    }

    .profile-details strong {
      color: #032d5a;
    }

    /* Action Buttons with Hover Effects */
    .action-buttons {
      display: flex;
      justify-content: center;
      gap: 15px;
      margin-top: 20px;
    }

    .action-buttons button {
      background-color: #032d5a;
      color: #fff;
      padding: 12px 18px;
      border-radius: 8px;
      border: 2px solid transparent;
      cursor: pointer;
      font-size: 1rem;
      transition: all 0.3s ease;
    }

    .action-buttons button:hover {
      background-color: #0262a1;
      border-color: #fff;
      transform: scale(1.05);
    }

  </style>
</head>

<body>
  <!-- Header -->
  <div class="header">
    <div class="d-flex align-items-center">
      <a href="home.html"><i class="fas fa-home text-white"></i></a>
    </div>
    <div class="d-flex align-items-center">
      <img alt="Wheel Buddy logo" src="./wb.png" width="100" />
      <div class="dropdown ms-3">
        <i class="fas fa-user-circle text-white dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"
          style="cursor: pointer; font-size: 24px"></i>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="settings.html">Settings</a></li>
          <li><hr class="dropdown-divider" /></li>
          <li><a class="dropdown-item" href="login.html">Logout</a></li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Profile Container -->
  <div class="profile-container">
    <h2>User Profile</h2>
    <div class="profile-details">
      <p><strong>Name:</strong> <?php echo htmlspecialchars($user['User_Name']); ?></p>
      <p><strong>Email:</strong> <?php echo htmlspecialchars($user['Email']); ?></p>
      <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['Phone_number']); ?></p>
    </div>

    <div class="action-buttons">
      <button onclick="editProfile()">Edit Profile</button>
      <button onclick="changePassword()">Change Password</button>
    </div>
  </div>
  <script>
    function editProfile() {
      alert("Redirecting to Edit Profile Page...");
      window.location.href = "updateprofile.php";
    }

    function changePassword() {
      alert("Redirecting to Change Password Page...");
      window.location.href = "change-password.html";
    }
  </script>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
