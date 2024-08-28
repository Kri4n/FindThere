<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "findthere123";
$dbname = "findthere";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



// Update the counter
$sql = "UPDATE visitor_counter SET count = count + 1";
$conn->query($sql);

// Get the current count
$sql = "SELECT count FROM visitor_counter";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $count = $row["count"];
} else {
    $count = 0;
}

$conn->close();
?>
