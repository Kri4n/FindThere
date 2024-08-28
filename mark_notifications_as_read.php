<?php
$servername = "localhost";
$username = "root";
$password = "findthere123";
$database = "findthere";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Update read_status to 1 for unread notifications
$updateReadStatusQuery = "UPDATE claims SET read_status = 1 WHERE read_status = 0";
mysqli_query($conn, $updateReadStatusQuery);
$updateReadStatusReturnFormsQuery = "UPDATE return_forms SET read_status = 1 WHERE read_status = 0";
mysqli_query($conn, $updateReadStatusReturnFormsQuery);

mysqli_close($conn);
?>
