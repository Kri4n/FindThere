<?php
if (isset($_POST['submitfounditem'])) {
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
    $foundby = isset($_POST['foundby']) ? mysqli_real_escape_string($conn, $_POST['foundby']) : '';
    $email = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : '';
    $phone = isset($_POST['phone']) ? mysqli_real_escape_string($conn, $_POST['phone']) : '';

    // Handle the uploaded image
    $image_name = isset($_FILES['image']['name']) ? mysqli_real_escape_string($conn, $_FILES['image']['name']) : '';
    $image_tmp_name = isset($_FILES['image']['tmp_name']) ? $_FILES['image']['tmp_name'] : '';

    if (!empty($item) && !empty($details) && !empty($foundby) && !empty($email) && !empty($phone) && !empty($image_name) && !empty($image_tmp_name)) {
        // Continue processing only if all required fields are provided

        $datefound = date("Y-m-d H:i:s", strtotime($_POST['datefound']));
        $notes = isset($_POST['notes']) ? mysqli_real_escape_string($conn, $_POST['notes']) : '';

        // Define the folder where the image will be stored
        $image_destination = "uploads/" . $image_name;

        if (move_uploaded_file($image_tmp_name, $image_destination)) {
            // Insert data into the database
            $sql = "INSERT INTO found_items (item, details, foundby, email, phone, datefound, image, notes)
                    VALUES ('$item', '$details', '$foundby', '$email', '$phone', '$datefound', '$image_name', '$notes')";

            if (mysqli_query($conn, $sql)) {
                echo '<script>alert("Found item report submitted successfully."); window.location.href = "displayfounditems.php";</script>';
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        } else {
            echo "Error moving the uploaded file.";
        }
    } else {
        echo "Please fill out all required fields.";
    }

    // Close the database connection
    mysqli_close($conn);
}
?>


