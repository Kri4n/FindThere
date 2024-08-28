<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap">
    <title>Display Returned Items</title>
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

        h2 {
            font-family: 'Montserrat', sans-serif;
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

        .returned-item {
            border: 1px solid #ccc;
            padding: 10px;
            margin: 10px; /* Add margin to create space around each grid item */
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
        <h2>RETURNED ITEMS</h2>
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
                $sql = "SELECT * FROM returned_items WHERE returned_item LIKE '%$searchTerm%' OR returned_itemdetails LIKE '%$searchTerm%' OR rnotes LIKE '%$searchTerm%'
                OR returnedto LIKE '%$searchTerm%'";
            } else {
                $sql = "SELECT * FROM returned_items";
            }
            
            $result = mysqli_query($conn, $sql);
            
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<div class='returned-item'>";
                    echo "<img src='uploads/{$row['returned_itemimg']}' alt='Returned Item Image' width='100' height='100' />";
                    echo "<h3>{$row['returned_item']}</h3>";
                    echo "<p>{$row['returned_itemdetails']}</p>";
                    echo "<p>Returned to: {$row['returnedto']}</p>";
                    echo "<p>Contact: {$row['email']} | {$row['phone']}</p>";
                    echo "<p>Notes: {$row['rnotes']}</p>";

                    $dateTimeReturned = date('F j, Y h:i:s A', strtotime($row['datereturned']));
                    echo "<p>Date Returned: " . date('F j, Y', strtotime($row['datereturned'])) . "</p>";
                    echo "</div>";
                }
            } else {
                if (isset($_POST['search'])) {
                    echo "No results found.";
                } else {
                    echo "No returned items available.";
                }
            }

            // Close the database connection
            mysqli_close($conn);
            ?>
        </div>
    </div>

    <footer>
        ALTT © 2023
    </footer>
</body>
</html>
