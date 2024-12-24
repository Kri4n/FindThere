<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap">
    <title>Display Returned Items</title>
    <link rel="stylesheet" href="./index.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
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
                    echo "<div class='item-container'>";
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

    <footer class="p-5">ALTT © 2023</footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
