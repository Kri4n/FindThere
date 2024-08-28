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

    $counterQuery = "SELECT count FROM visitor_counter";
    $counterResult = mysqli_query($conn, $counterQuery);

    if ($counterResult) {
    $counterRow = mysqli_fetch_assoc($counterResult);
    $visitorCounter = $counterRow['count'];
    } else {
    $visitorCounter = "Error: " . mysqli_error($conn);
    }
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            align-items: center; 
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
        box-shadow: 5px 0px 10px rgba(0, 0, 0, 0.2);
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
            flex-wrap: wrap; 
        }

        .item a:hover {
        text-decoration: underline;
        }

        .item {
            margin: 10px;
            
            width: 195px;
            border: 1px solid #ccc;
            padding: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            flex-grow: 1; /* Allow items to grow and occupy available space */
        }

        .item:nth-child(1) {
            margin-left: 70px;
            background-color: #eae5e5; /* Set different background colors for each item */
        }

        .item:nth-child(2) {
            background-color: #eae5e5;
        }

        .item:nth-child(3) {
            background-color: #eae5e5;
        }

        .item:nth-child(4) {
            background-color: #eae5e5;
        }

        .item img {
            max-width: 100px;
            max-height: 100px;
            margin-bottom: 10px;
        }

        .item-number {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
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
        .recent-lost-items {
        border: 1px solid lightgray;
        width: 24%;
        min-width: 200px; /* Set a minimum width */
        height: 550px; /* Set a specific height */
        min-height: 150px; /* Set a minimum height */
        margin-top: 20px;
        margin-left: 70px; /* Adjusted margin-left */
        margin-bottom: 20px; /* Added margin-bottom for spacing */
        padding: 10px;
        background-color: #f4f4f4; /* Gray background color */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Added a subtle box shadow */
        border-radius: 8px; /* Added border-radius for rounded corners */
        }
        .recent-found-items {
        border: 1px solid lightgray;
        width: 24%;
        min-width: 200px; /* Set a minimum width */
        height: 550px; /* Set a specific height */
        min-height: 150px; /* Set a minimum height */
        margin-top: 20px;
        margin-left: 58px; /* Adjusted margin-left */
        margin-bottom: 20px; /* Added margin-bottom for spacing */
        padding: 10px;
        background-color: #f4f4f4; /* Gray background color */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Added a subtle box shadow */
        border-radius: 8px; /* Added border-radius for rounded corners */
        }

        .recent-items h2 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #333;
        }

        .recent-item {
            display: flex;
            margin-bottom: 10px;
        }

        .recent-item img {
            max-width: 40px;
            max-height: 40px;
            margin-right: 10px;
        }

        .recent-item-details {
            flex-grow: 1;
            margin-left: 10px;
        }

        /* Style for individual recent items */
        .recent-item-box {
            margin-left: 100px;
            padding: 10px;
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .recent-items > div {
            border-top: 1px solid #ccc; /* Border at the top */
            padding: 10px 0; /* Add some vertical spacing between items */
            margin: 5px 0; /* Add some margin between items */
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
        margin-right: 1010px; /* Adjust horizontal position */
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
            color: black;
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
        text-align: center; /* Center-align the text */
        padding: 6px; /* Increase padding to make the header taller */
        color: white; /* Set the text color to white for better visibility */
        font-size: 18px; /* Increase font size */
        font-weight: bold; /* Make the text bold */
        }

        a {
        text-decoration: none; 
        color: #444444; /* Use the default text color */
        }

        .total-records-container {
        margin-top: -443px;
        margin-left: 875px;
        text-align: center;
        font-size: 18px;
        width: 463px;
        height: 140px;
        background-color: #f4f4f4;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .total-records {
        font-size: 30px;
        font-weight: bold;
        color: #333;
        }
        .totalreports-image {
            margin-top: 30px;
            margin-right: 350px;
            max-width: 70px;
            max-height: 70px;
            margin-bottom: 10px;
        }

        .visitor-container {
        margin-top: 20px;
        text-align: center;
        font-size: 18px;
        width: 463px;
        height: 140px;
        margin-left: 55px;
        background-color: #f4f4f4;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .visitor-count {
        font-size: 30px;
        font-weight: bold;
        color: #333;
        }

        .visitor-image {
            margin-top: 30px;
            margin-right: 350px;
            max-width: 70px;
            max-height: 70px;
            margin-bottom: 10px;
        }

        .piechart-container {
        margin-top: -295px;
        text-align: center;
        font-size: 18px;
        width: 463px;
        height: 340px;
        margin-left: 875px;
        background-color: #f4f4f4;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
        <h2>DASHBOARD</h2>
    </div>

    <div class="left-bar">
        <img src="findtherelogo.png" alt="findtherelogo" class="findtherelogo">
    </a>
         <p>Hello, Admin</p>
        <a href="#" id="notification-bell">
            <img src="notificationbell.png" alt="Notification Bell" class="notification-bell"> Notifications 
            <span id="unread-count" class="badge"></span>
    </a>
        <a href="dashboard.php">
            <img src = "dashboardlogo.png" alt="Dashboard" class="dashboard"> <span style="color: red;">Dashboard</span>
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
        <div class="item">
            <a href="lostitemreports.php" >
            <img src="lostitemlogo.png" alt="Lost Item">
            <h2>Lost Item</h2>
            <?php
                // Query to fetch the count of rows from the lost_items table
                $query = "SELECT COUNT(*) FROM lost_items";
                $result = mysqli_query($conn, $query);
                if ($result) {
                    $row = mysqli_fetch_array($result);
                    $lost_items_count = $row[0];
                    echo "<span class='item-number'>{$lost_items_count}</span>";
                } else {
                    echo "<span class='item-number'>Error: " . mysqli_error($conn) . "</span>";
                }
            ?>
            </a>
        </div>
        <div class="item">
            <a href="founditemreports.php" >
            <img src="founditemlogo.png" style="width: 100px; height: 99px;" alt="Found Item">
            <h2>Found Item</h2>
            <?php
                $query = "SELECT COUNT(*) FROM found_items";
                $result = mysqli_query($conn, $query);
                if ($result) {
                    $row = mysqli_fetch_array($result);
                    $found_items_count = $row[0];
                    echo "<span class='item-number'>{$found_items_count}</span>";
                } else {
                    echo "<span class='item-number'>Error: " . mysqli_error($conn) . "</span>";
                }
            ?>
            </a>
        </div>
        <div class="item">
            <a href="returneditemreports.php" >
            <img src="returneditemlogo.png" alt="Returned Item">
            <h2>Returned Item</h2>
            <?php
                $query = "SELECT COUNT(*) FROM returned_items";
                $result = mysqli_query($conn, $query);
                if ($result) {
                    $row = mysqli_fetch_array($result);
                    $returned_items_count = $row[0];
                    echo "<span class='item-number'>{$returned_items_count}</span>";
                } else {
                    echo "<span class='item-number'>Error: " . mysqli_error($conn) . "</span>";
                }
            ?>
            </a>
        </div>
        <div class="item">
        <a href="disposeditemreports.php" >
            <img src="disposeditemlogo.png" alt="Disposed Item">
            <h2>Disposed Item</h2>
            <?php
                $query = "SELECT COUNT(*) FROM disposed_items";
                $result = mysqli_query($conn, $query);
                if ($result) {
                    $row = mysqli_fetch_array($result);
                    $disposed_items_count = $row[0];
                    echo "<span class='item-number'>{$disposed_items_count}</span>";
                } else {
                    echo "<span class='item-number'>Error: " . mysqli_error($conn) . "</span>";
                }
            ?>
            </a>
        </div> 
        <div class="recent-lost-items">
            <h2>Recent Lost Items</h2>
    <?php 
    //Query to retrieve the 3 most recent lost items
          $query = "SELECT * FROM lost_items ORDER BY datelost DESC LIMIT 3";
          $result = mysqli_query($conn, $query);

          if (mysqli_num_rows($result) > 0) {
          while ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='recent-item'>";
            echo "<div class='recent-item-details'>";
            echo "<img src='uploads/{$row['image']}' alt='Lost Item Image' width='100' height='100' />";
            echo "<h3>{$row['item']}</h3>";
            echo "<p class='item-date' style='font-size: 14px; color: gray;'>Date Lost: " . date('Y-m-d H:i:s', strtotime($row['datelost'])) . "</p>";
            echo "</div>";
            echo "</div>";
            }
        } else {
        echo "<p>No recent lost items.</p>";
        }
    ?>
        </div>
        <div class="recent-found-items">
            <h2>Recent Found Items</h2>
            <?php 
    //Query to retrieve the 3 most recent found items
          $query = "SELECT * FROM found_items ORDER BY datefound DESC LIMIT 3";
          $result = mysqli_query($conn, $query);

          if (mysqli_num_rows($result) > 0) {
          while ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='recent-item'>";
            echo "<div class='recent-item-details'>";
            echo "<img src='uploads/{$row['image']}' alt='Found Item Image' width='100' height='100' />";
            echo "<h3>{$row['item']}</h3>";
            echo "<p class='item-date' style='font-size: 14px; color: gray;'>Date Found: " . date('Y-m-d H:i:s', strtotime($row['datefound'])) . "</p>";
            echo "</div>";
            echo "</div>";
            }
        } else {
        echo "<p>No recent found items.</p>";
        }

        mysqli_close($conn);
    ?>
        </div>
        <div class="visitor-container">
            <img src="visitors.png" alt="Visitor Image" class="visitor-image">
            <h3 style="margin-top: -88px;">Total Visitors</h3>
            <p class="visitor-count"><?php echo $visitorCounter; ?></p>
</div>
        <div class="total-records-container">
        <img src="totalreports.png" alt="total reports image" class="totalreports-image">
        <h3 style="margin-top: -88px;">Total Records</h3>
        <?php
        $totalRecords = $lost_items_count + $found_items_count + $returned_items_count + $disposed_items_count;
        echo "<span class='total-records'>{$totalRecords}</span>";
    ?>
    </div>
    <div class="piechart-container">
    <canvas id="myPieChart"></canvas>
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
            </script>
            <!-- Add this script section after including Chart.js -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Fetch the data for the pie chart
        const lostItemsCount = <?php echo $lost_items_count; ?>;
        const foundItemsCount = <?php echo $found_items_count; ?>;
        const returnedItemsCount = <?php echo $returned_items_count; ?>;
        const disposedItemsCount = <?php echo $disposed_items_count; ?>;

        const totalRecords = lostItemsCount + foundItemsCount + returnedItemsCount + disposedItemsCount;
        const lostItemsPercentage = ((lostItemsCount / totalRecords) * 100).toFixed(2);
        const foundItemsPercentage = ((foundItemsCount / totalRecords) * 100).toFixed(2);
        const returnedItemsPercentage = ((returnedItemsCount / totalRecords) * 100).toFixed(2);
        const disposedItemsPercentage = ((disposedItemsCount / totalRecords) * 100).toFixed(2);

        // Get the canvas element
        const ctx = document.getElementById('myPieChart').getContext('2d');

        // Create the pie chart
        const canvasContainer = document.getElementById('piechart-container');
        ctx.canvas.style.marginLeft = '90px';
        ctx.canvas.style.marginTop = '18px';
        const myPieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: [
                    `Lost Items (${lostItemsPercentage}%)`,
                    `Found Items (${foundItemsPercentage}%)`,
                    `Returned Items (${returnedItemsPercentage}%)`,
                    `Disposed Items (${disposedItemsPercentage}%)`,
                ],
                datasets: [{
                    data: [lostItemsCount, foundItemsCount, returnedItemsCount, disposedItemsCount],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: false,
                maintainAspectRatio: false,
                legend: {
                    position: 'right'
                },
            }
        });
    });
</script>
<script>
    // Function to convert timestamp to "time ago" format
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
        </div>
</body>
</html>
