<?php
include('config.php');

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST["username"];
    $password = $_POST["password"];
    

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM userdetails WHERE User_Name = ? AND Password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a user with the given credentials exists
    if ($result->num_rows == 1) {
        // User is authenticated, set session variable to indicate login
        $_SESSION["logged_in"] = true;
        $userInfo = $result->fetch_assoc();
        $_SESSION["id"] = $userInfo["id"];
        $usertype = $userInfo["usertype"];
        $_SESSION["username"] = $userInfo["username"];
        
        // Redirect to a protected page based on the user role
        if ($usertype == "1") {
            header("Location: ../og.html");
            exit();
        } elseif ($usertype == "2") {
            header("Location: ../admindashboard.html");
            exit();
        } else {
            echo "Unknown role: $usertype";
        }
    } else {
        // Invalid credentials, show an error message
        echo "<script type='text/javascript'>alert('Invalid Username or Password');window.location.href='../login.html';</script>";
    }

    // Close the database connection
    $conn->close();
}
?>
