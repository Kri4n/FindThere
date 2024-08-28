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
                echo '<script>alert("Lost item report submitted successfully."); window.location.href = "displaylostitems.php";</script>';
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
