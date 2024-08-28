<?php
$servername = "localhost";
$username = "root";
$password = "findthere123";
$database = "findthere";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['submitReturn'])) {
    // Your other form data...
    $name = mysqli_real_escape_string($conn, $_POST['returner']);
    $address = mysqli_real_escape_string($conn, $_POST['return_address']);
    $course = mysqli_real_escape_string($conn, $_POST['return_course']);
    $phone = mysqli_real_escape_string($conn, $_POST['return_phone']);
    $email = mysqli_real_escape_string($conn, $_POST['return_email']);
    $lostItemId = mysqli_real_escape_string($conn, $_POST['lostitem_id']);

    $notificationMessage = "Sent a proof for returning lost item";
    $readStatus = 0;
    $message = mysqli_real_escape_string($conn, $_POST['return_message']);

    // Image Upload
    $targetDir = "uploads/";
    $imageName = basename($_FILES["proof_image"]["name"]);
    $targetFilePath = $targetDir . $imageName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

    if (!empty($_FILES["proof_image"]["name"])) {
        // Allow certain file formats
        $allowedTypes = array("jpg", "jpeg", "png", "gif");
        if (in_array($fileType, $allowedTypes)) {
            // Upload file to the server
            if (move_uploaded_file($_FILES["proof_image"]["tmp_name"], $targetFilePath)) {
                $image = $imageName;

                // Insert data into the 'return_forms' table
                $sql = "INSERT INTO return_forms (name, address, course, phone, email, lostitem_id, notification_message, read_status, image, message)
                        VALUES ('$name', '$address', '$course', '$phone', '$email', '$lostItemId', '$notificationMessage', '$readStatus', '$image', '$message')";

                if (mysqli_query($conn, $sql)) {
                    echo '<script>alert("Submitted successfully."); window.location.href = "displaylostitems.php";</script>';
                } else {
                    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                }
            } else {
                echo "Error uploading file.";
            }
        } else {
            echo "Invalid file format. Allowed formats: jpg, jpeg, png, gif.";
        }
    } else {
        echo "Please select an image file.";
    }

    mysqli_close($conn);
}
?>
