<?php
$servername = "localhost";
$username = "root";
$password = "findthere123";
$database = "findthere";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $itemId = mysqli_real_escape_string($conn, $_POST['item_id']);
    $item = mysqli_real_escape_string($conn, $_POST['item']);
    $details = mysqli_real_escape_string($conn, $_POST['details']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $dateLost = mysqli_real_escape_string($conn, $_POST['datelost']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);

    if (isset($_FILES['newImage']) && $_FILES['newImage']['error'] == 0) {
        $newImage = $_FILES['newImage'];
        $imagePath = "uploads/" . basename($newImage['name']);

        // Validate and move the uploaded file
        if (move_uploaded_file($newImage['tmp_name'], $imagePath)) {
            $imagePathWithoutPrefix = basename($imagePath);

            // Update the database with the new image path
            $updateQuery = "UPDATE lost_items SET 
                            item='$item', 
                            details='$details', 
                            name='$name', 
                            email='$email', 
                            phone='$phone', 
                            datelost='$dateLost', 
                            image='$imagePathWithoutPrefix', 
                            notes='$notes' 
                            WHERE id='$itemId'";
            
            if (mysqli_query($conn, $updateQuery)) {
                echo "Item updated successfully";
            } else {
                echo "Error updating item: " . mysqli_error($conn);
            }
        } else {
            echo "Error moving uploaded file";
        }
    } else {
        // Update the database without changing the image path
        $updateQuery = "UPDATE lost_items SET 
                        item='$item', 
                        details='$details', 
                        name='$name', 
                        email='$email', 
                        phone='$phone', 
                        datelost='$dateLost', 
                        notes='$notes' 
                        WHERE id='$itemId'";
        
        if (mysqli_query($conn, $updateQuery)) {
            echo "Item updated successfully";
        } else {
            echo "Error updating item: " . mysqli_error($conn);
        }
    }
}

mysqli_close($conn);
?>
