<?php
if (isset($_POST['submitlostitem'])) {
    // Database connection (replace with your database credentials)
    $servername = "localhost";
    $username = "root";
    $password = "findthere123";
    $database = "findthere";

    $conn = mysqli_connect($servername, $username, $password, $database);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Handle form submission
    $item = isset($_POST['item']) ? mysqli_real_escape_string($conn, $_POST['item']) : '';
    $details = isset($_POST['details']) ? mysqli_real_escape_string($conn, $_POST['details']) : '';
    $name = isset($_POST['name']) ? mysqli_real_escape_string($conn, $_POST['name']) : '';
    $email = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : '';
    $phone = isset($_POST['phone']) ? mysqli_real_escape_string($conn, $_POST['phone']) : '';

    // Handle the uploaded image
    $image_name = isset($_FILES['image']['name']) ? mysqli_real_escape_string($conn, $_FILES['image']['name']) : '';
    $image_tmp_name = isset($_FILES['image']['tmp_name']) ? $_FILES['image']['tmp_name'] : '';

    if (!empty($item) && !empty($details) && !empty($name) && !empty($email) && !empty($phone) && !empty($image_name) && !empty($image_tmp_name)) {
        // Continue processing only if all required fields are provided

        $datelost = date("Y-m-d H:i:s", strtotime($_POST['datelost']));
        $notes = isset($_POST['notes']) ? mysqli_real_escape_string($conn, $_POST['notes']) : '';

        // Define the folder where the image will be stored
        $image_destination = "uploads/" . $image_name;

        if (move_uploaded_file($image_tmp_name, $image_destination)) {
            // Insert data into the database
            $sql = "INSERT INTO lost_items (item, details, name, email, phone, datelost, image, notes)
                    VALUES ('$item', '$details', '$name', '$email', '$phone', '$datelost', '$image_name', '$notes')";

            if (mysqli_query($conn, $sql)) {
                echo '<script>alert("Lost item report added successfully."); window.location.href = "lostitemreports.php";</script>';
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        } else {
            echo "Error moving the uploaded file.";
        }
    } else {
        echo "Please fill out all required fields.";
    }
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost Item Submission</title>
    <style>
        body {
        font-family: Arial, sans-serif;
        background-image: url('questionmark.jpg'); /* Set the background image */
        background-size: cover; /* Adjust the size to cover the entire background */
        background-repeat: no-repeat; /* Prevent the image from repeating */
        margin: 0;
        padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
        }

        label {
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        input[type="file"],
        input[type="details"],
        input[type="datetime-local"],
        textarea {
            width: 96%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 15px;
        }

        input[type="file"] {
            border: none;
        }

        textarea {
            height: 150px;
        }

        input[type="submit"] {
            background-color: #cc0000;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #ff0000;
        }
        input[type="submit"] {
            margin-top: -500px;
            margin-left: 460px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="back-button">
            <a href="lostitemreports.php">
                <img src="blackbackbutton.png" alt="Back" style="width: 55px; height: 55px;"> 
            </a>
        </div>
        <h1>Report Lost Item</h1>
        <form method="POST" action="" enctype="multipart/form-data">
            <label for="item">Item</label>
            <input type="text" id="item" name="item" required>

            <label for="details">Details</label>
            <input type="details" id="details" name="details" required>

            <label for="name">Name</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="phone">Phone No.</label>
            <input type="text" id="phone" name="phone" required>

            <label for="datelost">Date Lost</label>
            <input type="datetime-local" id="datelost" name="datelost" required>

            <label for="image">Upload Image</label>
            <input type="file" id="image" name="image" accept="image/*">

            <label for="notes">Notes</label>
            <textarea id="notes" name="notes"></textarea>

            <input type="submit" name="submitlostitem" value="Submit Lost Item">
        </form>
    </div> 
</body>
</html>
