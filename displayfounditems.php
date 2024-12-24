<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap">
    <title>View Found Items</title>
    <link rel="stylesheet" href="./index.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>

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
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    display: flex;
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
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg top-bar">
    <div class="container-fluid nav-buttons">
        <a class="navbar-brand" href="userhomepage.html">HOME</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav m-auto">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="lostitemsubmission.html">I Lost an item</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="founditemsubmission.html">I Found an Item</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="displaylostitems.php">View Lost Items</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="displayfounditems.php">View Found Items</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="displayreturneditems.php">View Returned Items</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

    <div class="content">
        <h2>FOUND ITEMS</h2>
        <div class="search-bar">
        <form method="POST" action="">
        <input type="text" class="search-input" name="search" placeholder="Search item..." 
        value="<?php echo isset($_POST['search']) ? htmlspecialchars($_POST['search']) : ''; ?>">
        <button type="submit" class="search-button">Search</button>
    </form>
        </div>
        <div class="content-box">
            <?php
            // Database connection (replace with your database credentials)
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
                $sql = "SELECT * FROM found_items WHERE item LIKE '%$searchTerm%' OR details LIKE '%$searchTerm%' OR notes LIKE '%$searchTerm%'  
                OR foundby LIKE '%$searchTerm%'";
            } else {
                $sql = "SELECT * FROM found_items";
            }
            
            $result = mysqli_query($conn, $sql);
            
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<div class='item-container'>";
                    echo "<img src='uploads/{$row['image']}' alt='Found Item Image' width='100' height='100' />";
                    echo "<h3>{$row['item']}</h3>";
                    echo "<p>{$row['details']}</p>";
                    echo "<p>Found by: {$row['foundby']}</p>";
                    echo "<p>Contact: {$row['email']} | {$row['phone']}</p>";
                    echo "<p>Notes: {$row['notes']}</p>";
                    
                    $dateTimeFound = date('F j, Y h:i:s A', strtotime($row['datefound']));
                    echo "<p>Date Found: " . date('F j, Y', strtotime($row['datefound'])) . "</p>";
                    echo "<p>Time Found: " . date('h:i A', strtotime($row['datefound'])) . "</p>";

                    echo "<form method='post' action=''>";
                    echo "<input type='hidden' name='item_id' value='{$row['id']}' />";
                    echo "<button class='claim-button' type='button' data-item-id='{$row['id']}' data-item-name='{$row['item']}' 
                    data-item-image='uploads/{$row['image']}' name='claim' style='background-color: #cc0000; 
                    color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;'>Request to Claim</button>";
                    echo "</form>";
                    echo "</div>";
                }
            } else {
                if (isset($_POST['search'])) {
                    echo "No results found.";
                } else {
                    echo "No found items available.";
                }
            }

            // Close the database connection
            mysqli_close($conn);
            ?>
            
        </div>
    </div>
    <footer class="p-5">
        ALTT © 2023
    </footer>
    <div id="claimPopup" class="popup">
    <div class="popup-content">
        <span class="close" onclick="closePopup()">&times;</span>
        <h2>Request to Claim Form</h2>
        <img src="" alt="" id="itemImage" style="max-width: 280px; max-height: 280px; margin-right: 25px;">
        <form id="claimForm" action="save_claim.php" method="post" required style="margin-left: 10px;">
            <input type="hidden" id="itemIdInput" name="item_id" value="">
            <br>
            
            <input type="text" id="ownerInput" name="owner" placeholder="Name" required style="padding: 5px; border: 1px solid #ccc; border-radius: 10px;">
            <br>
            <br>
          
            <input type="text" id="addressInput" name="address" placeholder="Adress" required style="padding: 5px; border: 1px solid #ccc; border-radius: 10px;">
            <br>
            <br>
           
            <input type="text" id="courseInput" name="course" placeholder="Course" required style="padding: 5px; border: 1px solid #ccc; border-radius: 10px;">
            <br>
            <br>
           
            <input type="text" id="owner_phone" name="owner_phone" placeholder="Phone" required style="padding: 5px; border: 1px solid #ccc; border-radius: 10px;">
            <br>
            <br>
          
            <input type='text' id='owner_email' name='owner_email' placeholder="Email" required style='padding: 5px; border: 1px solid #ccc; border-radius: 10px;'>
            <br>
            <br>
            <button type='submit' name='submitClaim' style='background-color: #cc0000; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;'>Submit</button>
        </form>
    </div>
</div>
    <script>
    // Get the popup
    var popup = document.getElementById('claimPopup');

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

    var claimButtons = document.querySelectorAll('.claim-button');

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
        titleElement.textContent = 'Request to Claim Item: ' + itemName;
        titleElement.style.fontWeight = 'bold';
        titleElement.style.fontSize = '23px';
        titleElement.style.color = 'gray'; 

        var itemIdInt = parseInt(itemId);

        // Set the item ID in the hidden input field
        var itemIdInput = document.getElementById('itemIdInput');
        itemIdInput.value = isNaN(itemIdInt) ? '' : itemIdInt;
    


        var imageElement = document.querySelector('.popup-content img');
        imageElement.src = itemImage;
        imageElement.alt = itemName + ' Image';
        imageElement.style.maxWidth = '280px';
        imageElement.style.maxHeight = '280px';

        
        // Set the item ID in the hidden input field
        var itemIdInput = document.getElementById('itemIdInput');
        itemIdInput.value = itemId;


    }
</script>

</body>
</html>
