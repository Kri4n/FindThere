<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap">
    <title>View Lost Items</title>
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
            $sql = "SELECT * FROM lost_items WHERE item LIKE '%$searchTerm%' OR details LIKE '%$searchTerm%' OR notes LIKE '%$searchTerm%' OR name LIKE '%$searchTerm%'";
        } else {
            $sql = "SELECT * FROM lost_items";
        }

        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<div class='item-container'>";
                echo "<img src='uploads/{$row['image']}' alt='Lost Item Image' width='100' height='100' />";
                echo "<h3>{$row['item']}</h3>";
                echo "<p>{$row['details']}</p>";
                echo "<p>Lost by: {$row['name']}</p>";
                echo "<p>Contact: {$row['email']} | {$row['phone']}</p>";
                echo "<p>Notes: {$row['notes']}</p>";

                $dateTimeLost = date('F j, Y h:i:s A', strtotime($row['datelost']));
                echo "<p>Date Lost: " . date('F j, Y', strtotime($row['datelost'])) . "</p>";
                echo "<p>Time Lost: " . date('h:i A', strtotime($row['datelost'])) . "</p>";

                echo "<button class='btn btn-danger return-button' data-bs-toggle='modal' data-bs-target='#returnModal' data-item-id='{$row['id']}' data-item-name='{$row['item']}' data-item-image='uploads/{$row['image']}'>Return Item</button>";
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

<footer class="p-5">ALTT © 2023</footer>

<!-- Bootstrap Modal -->
<div class="modal fade" id="returnModal" tabindex="-1" aria-labelledby="returnModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="returnModalLabel">Return Item Form</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img src="" alt="" id="modalItemImage" class="img-fluid d-block mx-auto mb-3" style="max-width: 280px; max-height: 280px;">
                <form id="returnForm" action="return_item.php" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <input type="hidden" id="modalItemId" name="lostitem_id" value="">
                    <div class="mb-3">
                        <input type="text" id="returnerInput" name="returner" class="form-control" placeholder="Name" required>
                    </div>
                    <div class="mb-3">
                        <input type="text" id="returnAddressInput" name="return_address" class="form-control" placeholder="Address" required>
                    </div>
                    <div class="mb-3">
                        <input type="text" id="returnCourseInput" name="return_course" class="form-control" placeholder="Course (Optional)">
                    </div>
                    <div class="mb-3">
                        <input type="text" id="returnPhoneInput" name="return_phone" class="form-control" placeholder="Phone No." required>
                    </div>
                    <div class="mb-3">
                        <input type="email" id="returnEmailInput" name="return_email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="mb-3">
                        <textarea id="returnMessageInput" name="return_message" class="form-control" rows="4" placeholder="Message"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="proofImageInput" class="form-label">Proof Image</label>
                        <input type="file" id="proofImageInput" name="proof_image" class="form-control" accept="uploads/*" required>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" name="submitReturn" class="btn btn-danger">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    var returnModal = document.getElementById('returnModal');

    returnModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var itemId = button.getAttribute('data-item-id');
        var itemName = button.getAttribute('data-item-name');
        var itemImage = button.getAttribute('data-item-image');

        var modalTitle = returnModal.querySelector('.modal-title');
        var modalItemImage = document.getElementById('modalItemImage');
        var modalItemId = document.getElementById('modalItemId');

        modalTitle.textContent = 'Return Item: ' + itemName;
        modalItemImage.src = itemImage;
        modalItemId.value = itemId;
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
