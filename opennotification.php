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

$unreadNotificationsQuery = "SELECT COUNT(*) AS unread_count FROM (
    SELECT id FROM claims WHERE read_status = 0
    UNION ALL
    SELECT id FROM return_forms WHERE read_status = 0
  ) AS unread_notifications";

$unreadNotificationsResult = mysqli_query($conn, $unreadNotificationsQuery);
$unreadNotificationsCount = 0;

if ($unreadNotificationsResult) {
$unreadNotificationsRow = mysqli_fetch_assoc($unreadNotificationsResult);
$unreadNotificationsCount = $unreadNotificationsRow['unread_count'];
} 

if (isset($_GET['id'])) {
    $notificationId = $_GET['id'];

    if (isset($_GET['id'])) {
        $notificationId = $_GET['id'];
    
        $notificationDetailQuery = "SELECT id, owner, course, owner_email, owner_phone, item_id, timestamp, claim_status, read_status, item_name, details, foundby, email, phone, date, image, notes, message, proof_image
    FROM (
        SELECT claims.id, claims.owner, claims.course, claims.owner_email, claims.owner_phone, claims.item_id, claims.timestamp, claims.claim_status, claims.read_status, found_items.item AS item_name, found_items.details,
            found_items.foundby, found_items.email, found_items.phone, found_items.datefound AS date, found_items.image, found_items.notes, null AS message, null AS proof_image
        FROM claims 
        LEFT JOIN found_items ON claims.item_id = found_items.id 
        WHERE claims.id = $notificationId
        UNION ALL
        SELECT return_forms.id, return_forms.name AS owner, return_forms.course, return_forms.email, return_forms.phone, return_forms.lostitem_id AS item_id, return_forms.timestamp, 
            return_forms.notification_message AS claim_status, return_forms.read_status, lost_items.item AS item_name, lost_items.details, lost_items.name AS foundby, lost_items.email, lost_items.phone, lost_items.datelost AS date, lost_items.image, lost_items.notes, return_forms.message, return_forms.image AS proof_image
        FROM return_forms 
        LEFT JOIN lost_items ON return_forms.lostitem_id = lost_items.id
        WHERE return_forms.id = $notificationId
    ) AS combined_data";

    
        $notificationDetailResult = mysqli_query($conn, $notificationDetailQuery);
    
        if (!$notificationDetailResult) {
            die("Error fetching notification details: " . mysqli_error($conn));
        }
    
        // Fetch the notification details
        $notificationDetail = mysqli_fetch_assoc($notificationDetailResult);
    }

    if (!$notificationDetailResult) {
        die("Error fetching notification details: " . mysqli_error($conn));
    }
}
    ?>
<!DOCTYPE html>
<html lang="en">
<head><script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Open Notification</title>
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

        a {
        text-decoration: none; 
        color: inherit; /
        }

        .notification-details {
        background-color: #D3D3D3;
        padding-top: 10px;
        padding-bottom: 20px;
        padding-left: 130px;
        padding-right: 130px;
        border-radius: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2); 
        max-width: 90%; 
        margin-left: auto; 
        margin-right: auto;
        }

        .notification-details h3 {
        font-size: 18px;
        margin-bottom: 15px;
        }

        .notification-details p {
         margin-bottom: 8px;
        }

        .notification-details strong {
        margin-right: 5px;
        }

        .approval-section {
            margin-top: 50px;
            text-align: center;
        }

        .approve-text {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .approve-buttons {
            margin-top: 10px;
        }

        .approve-button {
    padding: 8px 20px;
    margin: 0 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: filter 0.3s; /* Add a transition for a smooth effect */
}

.approve-yes {
    background-color: green;
    color: white;
}

.approve-no {
    background-color: red;
    color: white;
}


.approve-yes:hover {
    background-color: darkgreen; /* Change background color on hover */
}

.approve-no:hover {
    background-color: darkred; /* Change background color on hover */
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
        <h2>NOTIFICATION DETAILS</h2>
    </div>

    <div class="left-bar">
        <img src="findtherelogo.png" alt="findtherelogo" class="findtherelogo">
         <p>Hello, Admin</p>
        <a href="#" id="notification-bell">
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
    <?php if (isset($notificationDetail)): ?>
            <div class="notification-details">
            <h2>Notification Details</h2>
<p><strong>From:</strong> <?php echo $notificationDetail['owner'];  ?></p>
<p><strong>Course:</strong> <?php echo $notificationDetail['course'];  ?></p>
<p><strong>Email:</strong> <?php echo $notificationDetail['owner_email']; ?></p>
<p><strong>Phone:</strong> <?php echo $notificationDetail['owner_phone']; ?></p>
<p><strong>Date and Time:</strong> <?php echo date('F j, Y - h:i A', strtotime($notificationDetail['timestamp'])); ?></p>
<p><strong>Subject:</strong> <?php echo $notificationDetail['claim_status']; ?></p>


<?php if (!empty($notificationDetail['image'])): ?>
    <?php
    $imagePath = "uploads/{$notificationDetail['image']}";
    echo file_exists($imagePath) ? "<img src='{$imagePath}' alt='Item Image' style='margin-bottom: 10px; margin-top: -50%; margin-left: 400px; width: 380px; height: 250px;'>" : "<p>Image file does not exist.</p>";
    ?>
<?php else: ?>
    
<?php endif; ?>
   
<hr style="margin-left: -110px; margin-top: 10px; margin-bottom: 20px;  width: 130%;">
<p style="margin-left: 400px;"><strong>Item Name:</strong> <?php echo $notificationDetail['item_name']; ?></p>
<p style="margin-left: 400px;"><strong>Details:</strong> <?php echo $notificationDetail['details']; ?></p>
<p style="margin-left: 400px;"><strong>Notes:</strong> <?php echo $notificationDetail['notes']; ?></p>
<p style="margin-left: 400px;"><strong>Posted By:</strong> <?php echo $notificationDetail['foundby']; ?></p>
<p style="margin-left: 400px;"><strong>Email:</strong> <?php echo $notificationDetail['email']; ?></p>
<p style="margin-left: 400px;"><strong>Phone:</strong> <?php echo $notificationDetail['phone']; ?></p>
<p style="margin-left: 400px;"><strong>Date Lost / Found:</strong> <?php echo date('F j, Y - h:i A', strtotime($notificationDetail['date'])); ?></p>

<?php if (!empty($notificationDetail['message'])): ?>
    <p style="margin-top: -265px;"><strong>Message:</strong> <?php echo wordwrap($notificationDetail['message'], 35, "<br />\n", true); ?></p>
<?php endif; ?>


    <?php if (!empty($notificationDetail['proof_image'])): ?>
    <?php
    $imagePath = "uploads/{$notificationDetail['proof_image']}";
    echo file_exists($imagePath) ? "<p style='margin-top: 100px;'><strong>Image Sent:</strong></p> <img src='{$imagePath}' alt='Item Image' style='margin-bottom: 10px;  margin-left: -40px; width: 380px; height: 250px;'>" : "<p>Image file does not exist.</p>";
    ?>
<?php else: ?>
    
<?php endif; ?>
        <div class="approval-section">
                <p class="approve-text">Approve Request?</p>
                <div class="approve-buttons">
                <button class="approve-button approve-yes" onclick="approveRequest(<?php echo $notificationId; ?>)">Yes</button>
                <button class="approve-button approve-no" onclick="rejectRequest(<?php echo $notificationId; ?>)">No</button>
        </div>
    </div>
<?php else: ?>
    <p>No notification details found.</p>
<?php endif; ?>
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
        </div>
        <script>
            console.log('notificationBell:', notificationBell);
            console.log('notificationBox:', notificationBox);
            console.log('closeNotification:', closeNotification);
</script>
<script>
    function approveRequest(notificationId) {
      
        $.ajax({
            type: 'POST',
            url: 'save_to_returned_items.php',
            data: { notificationId: notificationId },
            success: function (response) {
                alert(response); // Display a success message
                location.reload(); // Refresh the page
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }
</script>
<script>
    function rejectRequest(notificationId) {
        // Ask for confirmation
        if (confirm("Are you sure you want to reject this request?")) {
          
            $.ajax({
                type: 'POST',
                url: 'delete_from_claims.php',
                data: { notificationId: notificationId },
                success: function (response) {
                    alert(response); // Display a success message
                    location.reload(); // Refresh the page
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }
    }
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

    const timestampElements = document.querySelectorAll('.notification-message.timestamp');
    timestampElements.forEach(element => {
        const timestamp = element.textContent;
        element.textContent = timeAgo(timestamp);
    });
</script>
</body>
</html>
