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
    $itemId = mysqli_real_escape_string($conn, $_POST['id']);
    $item = mysqli_real_escape_string($conn, $_POST['item']);
    $details = mysqli_real_escape_string($conn, $_POST['details']);
    $foundBy = mysqli_real_escape_string($conn, $_POST['foundby']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $dateFound = mysqli_real_escape_string($conn, $_POST['datefound']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);

    // You should handle file uploads securely, including proper validation and sanitization.
    // For simplicity, this example assumes the file upload is handled securely.

    // Check if a new image file is uploaded
    if (isset($_FILES['newImage']) && $_FILES['newImage']['error'] == 0) {
        $newImage = $_FILES['newImage'];
        $imagePath = "uploads/" . basename($newImage['name']);

        // Validate and move the uploaded file
        if (move_uploaded_file($newImage['tmp_name'], $imagePath)) {
            $imagePathWithoutPrefix = basename($imagePath);
            $updateQuery = "UPDATE found_items SET 
                            item='$item', 
                            details='$details', 
                            foundby='$foundBy', 
                            email='$email', 
                            phone='$phone', 
                            datefound='$dateFound', 
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
        $updateQuery = "UPDATE found_items SET 
                        item='$item', 
                        details='$details', 
                        foundby='$foundBy', 
                        email='$email', 
                        phone='$phone', 
                        datefound='$dateFound', 
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
