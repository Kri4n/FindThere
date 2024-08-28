<?php
$servername = "localhost";
$username = "root";
$password = "findthere123";
$database = "findthere";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['notificationId'])) {
    $notificationId = $_POST['notificationId'];

    // Perform the deletion for "claims" table
    $deleteClaimsQuery = "DELETE FROM claims WHERE id = $notificationId";
    $deleteClaimsResult = mysqli_query($conn, $deleteClaimsQuery);

    if (!$deleteClaimsResult) {
        echo "Error deleting claims data: " . mysqli_error($conn);
        exit;
    }

    // Perform the deletion for "return_forms" table
    $deleteReturnFormsQuery = "DELETE FROM return_forms WHERE id = $notificationId";
    $deleteReturnFormsResult = mysqli_query($conn, $deleteReturnFormsQuery);

    if (!$deleteReturnFormsResult) {
        echo "Error deleting return_forms data: " . mysqli_error($conn);
    } else {
        echo "Deleted successfully";
    }
} else {
    echo "Invalid request";
}

mysqli_close($conn);
?>
