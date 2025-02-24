<?php

include "config.php";

$userid = $_SESSION['admission_number'];

// Capture data from POST request
$data = [ 
    'payment_id' => $_POST['razorpay_payment_id'],
    'amount' => $_POST['totalAmount'],
    'product_id' => $_POST['product_id']
];


// Define the SQL query to insert data into the orders table
$sql = "INSERT INTO fees_table (admission_no, transaction_id, total, paid_fees) VALUES (?, ?, ?, ?)";

// Prepare the statement
if ($stmt = $conn->prepare($sql)) {
    // Bind the parameters and execute the query
    $stmt->bind_param("ssss", $userid, $data['payment_id'], $data['amount'], $data['product_id']);

    if ($stmt->execute()) {
        $arr = array('msg' => 'Payment successfully credited', 'status' => true);
        echo json_encode($arr);
    } else {
        $arr = array('msg' => 'Error: Unable to insert data into the database', 'status' => false);
        echo json_encode($arr);
    }

    // Close the statement
    $stmt->close();
} else {
    // If statement preparation fails, output the error
    $arr = array('msg' => 'Error: ' . $conn->error, 'status' => false);
    echo json_encode($arr);
}

// Close the database connection
$conn->close();
?>