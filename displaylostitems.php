<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap">
    <title>View Lost Items</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
        }

        .top-bar {
            background-color: #ff5858;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .top-bar h1 {
            color: white;
            margin: 0;
            font-size: 28px;
        }

        .nav-buttons {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
        }

        .nav-buttons a {
            text-decoration: none;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .nav-buttons a:hover {
            background-color: #cc0000;
        }

        .content {
            padding: 20px;
            margin-top: 50px;
            text-align: center; /* Center-align content */
        }

        img.findtherelogo {
            max-width: 100%;
            width: 300px;
            height: auto;
            display: block;
            margin: 0 auto; /* Center the image horizontally */
            margin-top: 50px;
            margin-bottom: 50px;
        }

        .content-box {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .content-box p {
            margin-bottom: 15px;
            line-height: 1.5;
        }

        footer {
            background-color: #808080;
            color: #fff;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        .search-bar {
            text-align: center; /* Center-align the search bar */
            margin-bottom: 20px; /* Add space between the search bar and paragraph */
        }

        .search-input {
            padding: 10px; /* Increase padding for a longer input field */
            border: none;
            border-radius: 50px;
            width: 70%; /* Make the input field longer */
            max-width: 400px; /* Limit the maximum width */
            background-color: lightgray;
        }

        .search-button {
            background-color: #cc0000;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px; /* Increase padding for a larger button */
            margin-left: 10px;
            cursor: pointer;
        }

        .content-box {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(450px, 1fr));
            grid-gap: 20px;
        }

        .lost-item {
            border: 1px solid #ccc;
            padding: 10px;
            margin: 10px; /* Add margin to create space around each grid item */
        }

        h2 {
            font-family: 'Montserrat', sans-serif;
        }

        .return-button {
            background-color: #cc0000;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            margin-top: 10px; /* Add some top margin for spacing */
            cursor: pointer;
        }

        .return-button:hover {
            background-color: #990000; /* Darker red on hover */
        }

        .popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }

        .popup-content {
            background: #fff;
            padding: 20px;
            width: 38%;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            justify-content: center;
            align-items: center;
        }

        .close {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 20px;
            cursor: pointer;
        }

        /* Add transition effect to the popup */
        .popup-content {
            transition: transform 0.3s;
        }

        /* Add style to make the popup visible */
        .popup.show {
            display: block;
        }
        .popup-content h2 {
        text-align: center;
        margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="top-bar">
    <ul class="nav-buttons">
        <li style="margin-right: 300px;"><a href="userhomepage.html">HOME</a></li>
        <li><a href="lostitemsubmission.html">I Lost an Item</a></li>
        <li><a href="founditemsubmission.html">I Found an Item</a></li>
        <li><a href="displaylostitems.php">View Lost Items</a></li>
        <li><a href="displayfounditems.php">View Found Items</a></li>
        <li><a href="displayreturneditems.php">View Returned Items</a></li>
    </ul>
</div>

<div class="content">
    <h2>LOST ITEMS</h2>
    <div class="search-bar">
        <form method="POST" action="">
            <input type="text" class="search-input" name="search" placeholder="Search item..."
                   value="<?php echo isset($_POST['search']) ? htmlspecialchars($_POST['search']) : ''; ?>">
            <button type="submit" class="search-button">Search</button>
        </form>
    </div>
    <div class="content-box">
        <?php
        // Database connection 
        $servername = "localhost";
        $username = "root";
        $password = "findthere123";
        $database = "findthere";

        $conn = mysqli_connect($servername, $username, $password, $database);

        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        if (isset($_POST['search'])) {
            $searchTerm = mysqli_real_escape_string($conn, $_POST['search']);
            $sql = "SELECT * FROM lost_items WHERE item LIKE '%$searchTerm%' OR details LIKE '%$searchTerm%' OR notes LIKE '%$searchTerm%'
                OR name LIKE '%$searchTerm%'";
        } else {
            $sql = "SELECT * FROM lost_items";
        }

        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<div class='lost-item'>";
                echo "<img src='uploads/{$row['image']}' alt='Lost Item Image' width='100' height='100' />";
                echo "<h3>{$row['item']}</h3>";
                echo "<p>{$row['details']}</p>";
                echo "<p>Lost by: {$row['name']}</p>";
                echo "<p>Contact: {$row['email']} | {$row['phone']}</p>";
                echo "<p>Notes: {$row['notes']}</p>";

                $dateTimeLost = date('F j, Y h:i:s A', strtotime($row['datelost']));
                echo "<p>Date Lost: " . date('F j, Y', strtotime($row['datelost'])) . "</p>";
                echo "<p>Time Lost: " . date('h:i A', strtotime($row['datelost'])) . "</p>";

                echo "<button class='return-button' data-item-image='uploads/{$row['image']}' data-item-name='{$row['item']}' data-item-id='{$row['id']}' onclick='returnItem(\"{$row['id']}\", \"{$row['item']}\", \"uploads/{$row['image']}\")'>Return Item</button>";
                echo "</div>";
            }
        } else {
            if (isset($_POST['search'])) {
                echo "No results found.";
            } else {
                echo "No lost items available.";
            }
        }

        mysqli_close($conn);
        ?>
    </div>
</div>

<footer>
    ALTT © 2023
</footer>
<div id="returnPopup" class="popup">
    <div class="popup-content">
        <span class="close" onclick="closePopup()">&times;</span>
        <h2>Return Item Form</h2>
        <img src="" alt="" id="itemImage" style="max-width: 280px; max-height: 280px; margin: 0 auto; display: block;">

        <form id="returnForm" action="return_item.php" method="post" enctype="multipart/form-data" required >
    <input type="hidden" id="itemIdInput" name="lostitem_id" value="">
    <br>
    <input type="text" id="returnerInput" name="returner" placeholder="Name" required style="margin: 0 300px; display: block; padding: 5px; border: 1px solid #ccc; border-radius: 10px;">
  
    <br>
    <input type="text" id="returnAddressInput" name="return_address" placeholder="Address" required style="margin: 0 300px; display: block; padding: 5px; border: 1px solid #ccc; border-radius: 10px;">
 
    <br>
    <input type="text" id="returnCourseInput" name="return_course" placeholder="Course (Optional)" style="margin: 0 300px; display: block; padding: 5px; border: 1px solid #ccc; border-radius: 10px;">
   
    <br>
   

    <input type="text" id="returnPhoneInput" name="return_phone" placeholder="Phone No." required style="margin: 0 300px; display: block; padding: 5px; border: 1px solid #ccc; border-radius: 10px;">

    <br>
    <input type="text" id="returnEmailInput" name="return_email" placeholder="Email" required style="margin: 0 300px; display: block; padding: 5px; border: 1px solid #ccc; border-radius: 10px;">
    
   
    <textarea id="returnMessageInput" name="return_message" rows="4" placeholder="Message" style="margin-left: 25px; margin-top: -200px; width: 250px;  height: 190px; border: 1px solid #ccc; border-radius: 1px; display: block;"></textarea>
    <input type="file" id="proofImageInput" name="proof_image" accept="uploads/*" required style="margin-left: 60px; margin-top: 15px;">
    <br>
    <button type='submit' name='submitReturn' style='margin-right: 35px; margin-top: 70px; float: right; background-color: #cc0000; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;'>Submit</button>
</form>

    </div>
</div>
<script>
    // Get the popup
    var popup = document.getElementById('returnPopup');

    // Get the <span> element that closes the popup
    var closeBtn = document.querySelector('.popup-content .close');

    // When the user clicks on <span> (x), close the popup
    closeBtn.onclick = function () {
        popup.classList.remove('show');
    }

    // Close popup function
    function closePopup() {
        popup.classList.remove('show');
    }

    var claimButtons = document.querySelectorAll('.return-button');

    claimButtons.forEach(function (button) {
    button.onclick = function (event) {
        event.preventDefault();
        var itemName = button.getAttribute('data-item-name');
        var itemImage = button.getAttribute('data-item-image');
        var itemId = button.getAttribute('data-item-id'); 
        popupContent(itemName, itemImage, itemId);
        popup.classList.add('show');
    }
});
    // Function to update the popup content
    function popupContent(itemName, itemImage, itemId) {
        var titleElement = document.querySelector('.popup-content h2');
        titleElement.textContent = 'Return Item: ' + itemName;
        titleElement.style.fontWeight = 'bold';
        titleElement.style.fontSize = '23px';
        titleElement.style.color = 'gray'; 

        var itemIdInt = parseInt(itemId);

        // Set the item ID in the hidden input field
        var itemIdInput = document.getElementById('itemIdInput');
        itemIdInput.value = isNaN(itemIdInt) ? '' : itemIdInt;
    
        var imageElement = document.querySelector('.popup-content img');
        imageElement.src = itemImage;
    
        

        // Set the item ID in the hidden input field
        var itemIdInput = document.getElementById('itemIdInput');
        itemIdInput.value = itemId;
    }
</script>
</body>
</html>
