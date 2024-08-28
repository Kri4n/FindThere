<?php
    $unreadNotificationsQuery = "SELECT COUNT(*) AS unread_count FROM (
        SELECT id FROM claims WHERE read_status = 0
        UNION ALL
        SELECT id FROM return_forms WHERE read_status = 0
    ) AS unread_notifications";

    $conn = mysqli_connect("localhost", "root", "findthere123", "findthere");

    $unreadNotificationsResult = mysqli_query($conn, $unreadNotificationsQuery);

    if ($unreadNotificationsResult) {
        $unreadNotificationsRow = mysqli_fetch_assoc($unreadNotificationsResult);
        $unreadNotificationsCount = $unreadNotificationsRow['unread_count'];
        echo $unreadNotificationsCount;
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    mysqli_close($conn);
?>
