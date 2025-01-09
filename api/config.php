<?php
session_start();

$servername = "localhost";
$username   = "root";
$password   = "";
$db         = "wheelbuddy"; // Write you DB name here

// Create connection
$conn =  mysqli_connect($servername, $username, $password, $db);


// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}


?>