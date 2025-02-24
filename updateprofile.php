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
$sql = "SELECT User_Name, First_Name, Last_Name, Email, Phone_number, Profile_Photo FROM userdetails WHERE id = ?";

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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Update Profile - Wheel Buddy</title>

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

    /* Profile Update Box with Outer Border */
    .form-container {
      background: rgba(255, 255, 255, 0.95);
      padding: 30px;
      border-radius: 12px;
      border: 2px solid #032d5a;  /* 🔹 Outer Border */
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
      max-width: 800px;
      width: 100%;
      margin: auto;
      margin-top: 20px;
      transition: all 0.3s ease-in-out;
    }

    .form-container:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 18px rgba(0, 0, 0, 0.4);
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
      font-weight: bold;
      color: #032d5a;
      text-transform: uppercase;
    }

    /* Inner Borders for Form Fields */
    .form-group {
      padding: 12px;
      border-radius: 10px;
      border: 2px solid #ddd;
      background: #f8f9fa;
      transition: all 0.3s ease-in-out;
      margin-bottom: 15px;
    }

    .form-group:hover {
      border-color: #032d5a;
      background: #e9ecef;
    }

    .form-control {
      border: none;
      box-shadow: none;
      font-size: 1rem;
    }

    /* Profile Image Preview */
    .profile-image-container {
      display: flex;
      justify-content: center;
      align-items: center;
      margin-bottom: 20px;
    }

    .profile-image-container img {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #032d5a;
      transition: transform 0.3s ease-in-out;
    }

    .profile-image-container img:hover {
      transform: scale(1.1);
    }

    /* Button Enhancements */
    .btn-primary {
      background-color: #032d5a;
      border: 2px solid transparent;
      padding: 12px;
      font-size: 1rem;
      border-radius: 8px;
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
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
      <a href="./settings.html"> <i class="fas fa-arrow-left text-white me-3"></i></a>
      <a href="./newhome.html"> <i class="fas fa-home text-white"></i></a>
    </div>
    <div class="d-flex align-items-center">
      <img alt="Wheel Buddy logo" src="./wb.png" width="100">
      <div class="dropdown ms-3">
        <i class="fas fa-user-circle text-white dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"
          style="cursor: pointer; font-size: 24px"></i>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="#">Profile</a></li>
          <li><a class="dropdown-item" href="./settings.html">Settings</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="#">Logout</a></li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Body -->
  <div class="container py-5">
    <div class="form-container">
      <h2>Update Profile</h2>
      <!-- Profile Image Preview -->
      <div class="profile-image-container">
        <img id="profileImagePreview" src="uploads/<?php echo htmlspecialchars($user['Profile_Photo']); ?>" alt="Profile Picture">
      </div>

      <form id="updateProfileForm" enctype="multipart/form-data">
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input type="text" class="form-control" name="username" id="username" placeholder="Enter your username" value="<?php echo htmlspecialchars($user['User_Name']); ?>" required>
        </div>

        <div class="mb-3">
          <label for="firstName" class="form-label">First Name</label>
          <input type="text" class="form-control" name="firstName" id="firstName" placeholder="Enter your first name" value="<?php echo htmlspecialchars($user['First_Name']); ?>" required>
        </div>
        <div class="mb-3">
          <label for="lastName" class="form-label">Last Name</label>
          <input type="text" class="form-control" name="lastName" id="lastName" placeholder="Enter your last name" value="<?php echo htmlspecialchars($user['Last_Name']); ?>" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" name="email" id="email" placeholder="Enter your email" value="<?php echo htmlspecialchars($user['Email']); ?>" required>
        </div>
        
        <div class="mb-3">
          <label for="profileImage" class="form-label">Profile Picture</label>
          <input type="file" class="form-control" name="profilePhoto" id="profilePhoto">
        </div>
        <div class="mb-3">
          <label for="phoneNumber" class="form-label">Phone Number</label>
          <input type="tel" class="form-control" name="phoneNumber" id="phoneNumber" placeholder="Enter your phone number" value="<?php echo htmlspecialchars($user['Phone_number']); ?>" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" name="password" id="password" placeholder="Enter new password" required>
        </div>
        <div class="mb-3">
          <label for="confirmPassword" class="form-label">Confirm Password</label>
          <input type="password" class="form-control" name="confirmPassword" id="confirmPassword" placeholder="Confirm new password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100" id="saveChangesBtn">Save Changes</button>
      </form>
    </div>
  </div>

  <!-- AJAX Script -->
  <script>
    $(document).ready(function () {
      // Attach submit event to form
      $("#updateProfileForm").submit(function (event) {
        event.preventDefault(); // Prevent default form submission

        let formData = new FormData(this);

        $.ajax({
          url: "api/updatepro.php", // Backend PHP File
          type: "POST",
          data: formData,
          contentType: false,
          processData: false,
          beforeSend: function() {
            // Disable the button to prevent multiple submissions
            $("#saveChangesBtn").prop("disabled", true);
            $("#saveChangesBtn").text("Saving...");
          },
          success: function (response) {
            response = JSON.parse(response);  // Parse JSON response
            alert(response.message);
            if (response.status) {
              window.location.reload(); // Reload page after update
            } else {
              // Enable button and restore original text if error occurs
              $("#saveChangesBtn").prop("disabled", false);
              $("#saveChangesBtn").text("Save Changes");
            }
          },
          error: function (xhr, status, error) {
            console.error("Error:", error);
            alert("Error updating profile. Please try again.");
            // Enable button and restore original text on error
            $("#saveChangesBtn").prop("disabled", false);
            $("#saveChangesBtn").text("Save Changes");
          }
        });
      });

      // Show Profile Image Preview
      $("#profilePhoto").change(function () {
        let reader = new FileReader();
        reader.onload = function (e) {
          $("#profileImagePreview").attr("src", e.target.result);
        };
        reader.readAsDataURL(this.files[0]);
      });
    });
  </script>

  <!-- Bootstrap Bundle JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
