<?php
$servername = "localhost";
$username = "root";
$password = "findthere123";
$database = "findthere";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$unreadNotificationsQuery = "SELECT COUNT(*) AS unread_count FROM (
    SELECT id FROM claims WHERE read_status = 0
    UNION ALL
    SELECT id FROM return_forms WHERE read_status = 0
) AS unread_notifications";

$unreadNotificationsResult = mysqli_query($conn, $unreadNotificationsQuery);

$response = array();

if ($unreadNotificationsResult) {
    $unreadNotificationsRow = mysqli_fetch_assoc($unreadNotificationsResult);
    $response['unread_count'] = $unreadNotificationsRow['unread_count'];
} else {
    $response['error'] = "Error: " . mysqli_error($conn);
}

header('Content-Type: application/json');
echo json_encode($response);

mysqli_close($conn);
?>
