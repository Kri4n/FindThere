<?php
    $servername = "localhost";
    $username = "root";
    $password = "findthere123";
    $database = "findthere";

    $conn = mysqli_connect($servername, $username, $password, $database);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    
    $combinedQuery = "SELECT id, owner, item_id, timestamp, claim_status, read_status, notification_message, item_name
              FROM (
                  SELECT claims.id, claims.owner, claims.item_id, claims.timestamp, claims.claim_status, claims.read_status, null AS notification_message, found_items.item AS item_name
                  FROM claims 
                  LEFT JOIN found_items ON claims.item_id = found_items.id 
                  UNION ALL
                  SELECT return_forms.id, return_forms.name AS owner, return_forms.lostitem_id AS item_id, return_forms.timestamp, return_forms.notification_message AS claim_status, return_forms.read_status, return_forms.notification_message, lost_items.item AS item_name
                  FROM return_forms 
                  LEFT JOIN lost_items ON return_forms.lostitem_id = lost_items.id
              ) AS combined_data
              ORDER BY timestamp DESC
              LIMIT 3";


    $combinedResult = mysqli_query($conn, $combinedQuery);

    $tableQuery = "SELECT claims.*, found_items.item AS found_item_name FROM claims 
    LEFT JOIN found_items ON claims.item_id = found_items.id";
    $tableResult = mysqli_query($conn, $tableQuery);
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        .top-bar {
            background-color: red;
            padding: 10px 20px;
            color: white;
            display: flex;
            align-items: center; /* Center align text and image vertically */
            justify-content: center; 
        }

        .left-bar {
        background-color: yellow;
        width: 150px;
        height: 100%;
        position: fixed;
        top: 0;
        left: 0;
        padding: 20px;
        box-shadow: 5px 0px 10px rgba(0, 0, 0, 0.2); /* Add a shadow to the right side */
        }

        .left-bar a {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            text-decoration: none;
            color: black;
            font-weight: bold;
        }

        .left-bar a:hover {
            color: rgb(221, 22, 22); /* Change link color to gray on hover */
        }

        .left-bar a svg {
            width: 20px;
            height: 20px;
            margin-right: 10px;
            fill: black;
        }

        .content {
            margin-left: 130px; /* Adjust the margin to accommodate the left bar width */
            padding: 20px;
            display: flex;
            flex-wrap: wrap; /* Enable wrapping for the item containers */
            margin: 20px auto; /* Center the content and add space around it */
            max-width: 800px; /* Adjust the maximum width as needed */
        }

        .logout {
            margin-right: 5px;
            width: 25px;
            height: 25px;
        }

        .findtherelogo {
            width: 150px; /* Adjust the width of the image as needed */
            height: 80px;
            margin-right: 10px; /* Add some spacing between the image and text */
        }
        .dashboard {
            margin-right: 5px;
            width: 25px;
            height: 25px;
        }
        .reports {
            margin-right: 5px;
            width: 25px;
            height: 25px;
        }
        .settings {
            margin-right: 5px;
            width: 25px;
            height: 25px;
        }

        .notification-bell {
            cursor: pointer;
            margin-right: 5px;
            width: 25px;
            height: 25px;
        }

        .notification-box {
        position: fixed;
        top: 50px; /* Adjust the vertical position as needed */
        right: 0; /* Position it at the right side */
        width: 300px;
        background-color: white;
        border: 1px solid #ccc;
        border-radius: 5px;
        display: none; /* Initially hidden */
        padding: 10px;
        z-index: 1; /* Ensure it appears above other content */
        margin-right: 1025px; /* Adjust horizontal position */
        margin-top: 70px;
        }

        .notification-box::before {
        content: '';
        position: absolute;
        top: 37px;
        left: -20px; /* Adjust to move the arrow */
        border-width: 10px; /* Increase the border width to make the arrow bigger */
        border-style: solid;
        border-color: transparent transparent transparent #FF0000;
        transform: rotate(180deg); /* Rotate the arrow to point left */
        }

        .notification-title {
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .notification-message {
            margin-bottom: 10px;
        }

        .close-notification {
            cursor: pointer;
            background-image: url('close.png'); /* Add background image */
            background-size: contain; /* Adjust the size of the background image */
            background-repeat: no-repeat; /* Prevent the background image from repeating */
            width: 25px; /* Set the width to match the image width */
            height: 25px; /* Set the height to match the image height */
            margin-left: 10px; /* Adjust the margin */
            margin-bottom: 10px;
            float: right; /* Align the close button to the right */
        }

        .close-notification:hover {
            text-decoration: underline;
        }

        .show-all-notifications {
        display: block;
        text-align: center;
        margin-top: 10px;
        padding: 5px 10px;
        background-color: red;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        }

        .show-all-notifications:hover {
        background-color: darkred;
        }

        .notification-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .notification-item {
            padding: 10px;
            border-bottom: 1px solid #ccc;
            transition: background-color 0.3s;
        }

        .notification-item:hover {
            background-color: #D3D3D3;
        }

        .notification-header {
        background-color: red;
        text-align: center;
        padding: 6px; 
        color: white; 
        font-size: 18px; 
        font-weight: bold; 
        }

        table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        th, td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ccc; /* Add a bottom border to each cell */
        }

        th {
        background-color: #f2f2f2;
        }

        tbody tr:hover {
        background-color: #D3D3D3; /* Change the background color on hover */
        }
        
        a {
        text-decoration: none; /* Remove underlines */
        color: inherit; /* Use the default text color */
        }
        .badge {
        background-color: red;
        color: white;
        font-size: 12px;
        padding: 3px 6px;
        border-radius: 50%;
        
        }
    </style>
</head>
<body>
    <div class="top-bar">
        <h2>NOTIFICATIONS</h2>
    </div>

    <div class="left-bar">
        <img src="findtherelogo.png" alt="findtherelogo" class="findtherelogo">
         <p>Hello, Admin</p>
         <a href="#" id="notification-bell" style="color: red;">
            <img src="notificationbell.png" alt="Notification Bell" class="notification-bell"> Notifications
            <span id="unread-count" class="badge"></span>
    </a>
        <a href="dashboard.php">
            <img src = "dashboardlogo.png" alt="Dashboard" class="dashboard"> Dashboard
        </a>
        <a href="lostitemreports.php">
            <img src = "reports.png" alt="Reports" class="reports"> Reports
        </a>
        <a href="#" id="logout-button">
            <img src="logout.png" alt="Logout" class="logout"> Logout
        </a>
    </div>
        <div class="notification-box" id="notification-box">
        <div class="notification-header">
        Notifications
        <div class="close-notification" id="close-notification"></div>
    </div>
    <ul class="notification-list" id="notification-list">
    <?php
    while ($row = mysqli_fetch_assoc($combinedResult)) {
    echo "<li class='notification-item " . ($row['read_status'] ? 'read' : 'unread') . "'>";
    echo "<a href='opennotification.php?id={$row['id']}'>";
    echo "<div style='color: black'; class='notification-title'>{$row['owner']}</div>";

    $itemMessage = isset($row['item_name']) ? $row['item_name'] : 'Item not found';
    echo "<div style='color: black'; class='notification-message'>{$row['claim_status']}&nbsp{$itemMessage}</div>";
    echo "<div style='color: black'; class='notification-message timestamp'>{$row['timestamp']}</div>";
    echo "</a>";
    echo "</li>";
    }
    ?>
        </ul>
            <a href="notifications.php" class="show-all-notifications">Show All Notifications</a>
        </div>
        <script>
    document.addEventListener("DOMContentLoaded", function () {
        const notificationBell = document.getElementById('notification-bell');
        const notificationBox = document.getElementById('notification-box');
        const closeNotification = document.getElementById('close-notification');
        const unreadCountElement = document.getElementById('unread-count');

        function showNotificationBox() {
            notificationBox.style.display = 'block';
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'mark_notifications_as_read.php', true);
            xhr.send();
        }

        function hideNotificationBox() {
            notificationBox.style.display = 'none';
        }

        function handleDocumentClick(event) {
            // Check if the clicked element is not inside the notification box
            if (!notificationBox.contains(event.target) && event.target !== notificationBell) {
                hideNotificationBox();
            }
        }

        function updateUnreadCount() {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_unread_count.php', true);

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const newUnreadCount = parseInt(xhr.responseText);
                    if (!isNaN(newUnreadCount > 0)) {
                        unreadCountElement.textContent = newUnreadCount;
                        badgeElement.style.display = 'inline-block';
                    } else {
                    badgeElement.style.display = 'none'; 
                    }
                }
            };

            xhr.send();
        }

        if (notificationBell) {
            notificationBell.addEventListener('click', showNotificationBox);
        }

        closeNotification.addEventListener('click', hideNotificationBox);
        document.body.addEventListener('click', handleDocumentClick);

        // Update unread count every 1 minute (adjust the interval as needed)
        setInterval(updateUnreadCount, 1000);
    });
</script>

    <div class="content">
    <table>
        <thead>
            <tr>
                <th>From</th>
                <th>Subject</th>
                <th>Date and Time</th>
            </tr>
        </thead>
        <tbody>
        <?php
    $combinedQuery = "SELECT id, owner, item_id, timestamp, claim_status, notification_message, item_name
                      FROM (
                          SELECT claims.id, claims.owner, claims.item_id, claims.timestamp, claims.claim_status, null AS notification_message, found_items.item AS item_name
                          FROM claims 
                          LEFT JOIN found_items ON claims.item_id = found_items.id 
                          UNION ALL
                          SELECT return_forms.id, return_forms.name AS owner, return_forms.lostitem_id AS item_id, return_forms.timestamp, return_forms.notification_message AS claim_status, return_forms.notification_message, null AS item_name
                          FROM return_forms 
                      ) AS combined_data
                      ORDER BY timestamp";

    $result = mysqli_query($conn, $combinedQuery);

    // Fetch all notifications and store them in an array
    $allNotifications = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $allNotifications[] = $row;
    }

    for ($i = count($allNotifications) - 1; $i >= 0; $i--) {
        echo "<tr id='notification-row-$i' class='notification-row' data-notification-id='{$allNotifications[$i]['id']}'>";
        echo "<td>{$allNotifications[$i]['owner']}</td>";
        echo "<td>{$allNotifications[$i]['claim_status']}&nbsp{$allNotifications[$i]['item_name']}</td>";
        $timestamp = strtotime($allNotifications[$i]['timestamp']);
        $formattedDate = date('F j, Y, g:i a', $timestamp);

        echo "<td>{$formattedDate}</td>";
        echo "</tr>";
    }
?>

            </tbody>
            </table>
            </div>
            <script>
                
                const logoutButton = document.getElementById('logout-button');
        
                function showLogoutConfirmation() {
                    const confirmLogout = window.confirm("Are you sure you want to logout?");
                    if (confirmLogout) {
              
                        window.location.href = 'index.html';
                    }
                }
        
                logoutButton.addEventListener('click', showLogoutConfirmation);

            notificationRows.forEach((row, index) => {
                row.addEventListener('click', () => showNotificationPopup(index));
            });
    </script>
        </div>
<script>
    // Function to redirect to opennotification.php with the given notification ID
    function redirectToOpenNotification(id) {
        window.location.href = 'opennotification.php?id=' + id;
    }

    document.addEventListener("DOMContentLoaded", function () {
        // Get all notification rows
        const notificationRows = document.querySelectorAll('.notification-row');

        // Add a click event listener to each <tr> element
        notificationRows.forEach((row) => {
            row.addEventListener('click', function () {
                // Get the notification ID from the row's ID attribute
                const notificationId = this.getAttribute('data-notification-id');

                // Redirect to opennotification.php with the notification ID
                redirectToOpenNotification(notificationId);
            });
        });
    });
</script>
<script>
    function timeAgo(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);

        let interval = Math.floor(seconds / 31536000);
        if (interval >= 1) {
            return interval + " year" + (interval === 1 ? "" : "s") + " ago";
        }

        interval = Math.floor(seconds / 2592000);
        if (interval >= 1) {
            return interval + " month" + (interval === 1 ? "" : "s") + " ago";
        }

        interval = Math.floor(seconds / 86400);
        if (interval >= 1) {
            return interval + " day" + (interval === 1 ? "" : "s") + " ago";
        }

        interval = Math.floor(seconds / 3600);
        if (interval >= 1) {
            return interval + " hour" + (interval === 1 ? "" : "s") + " ago";
        }

        interval = Math.floor(seconds / 60);
        if (interval >= 1) {
            return interval + " minute" + (interval === 1 ? "" : "s") + " ago";
        }

        return Math.floor(seconds) + " second" + (seconds === 1 ? "" : "s") + " ago";
    }

    // Call the function for each notification timestamp
    const timestampElements = document.querySelectorAll('.notification-message.timestamp');
    timestampElements.forEach(element => {
        const timestamp = element.textContent;
        element.textContent = timeAgo(timestamp);
    });
</script>
</body>
</html>