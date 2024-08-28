<?php
// dispose_item.php

$servername = "localhost";
$username = "root";
$password = "findthere123";
$database = "findthere";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$itemId = $_GET['id'];

// Fetch the item details before deletion
$selectQuery = "SELECT * FROM found_items WHERE id = $itemId";
$result = mysqli_query($conn, $selectQuery);

if (!$result) {
    die(json_encode(["success" => false, "message" => "Error fetching item details: " . mysqli_error($conn)]));
}

$foundItem = mysqli_fetch_assoc($result);

// Prepare the insert query with placeholders to avoid SQL injection
$insertQuery = "INSERT INTO disposed_items (item, details, foundby, email, phone, datefound, image, notes)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

// Use prepared statement to bind parameters
$insertStatement = mysqli_prepare($conn, $insertQuery);
mysqli_stmt_bind_param($insertStatement, "ssssssss",
    $foundItem['item'], $foundItem['details'], $foundItem['foundby'],
    $foundItem['email'], $foundItem['phone'], $foundItem['datefound'],
    $foundItem['image'], $foundItem['notes']);

// Execute the prepared statement
$insertResult = mysqli_stmt_execute($insertStatement);

if (!$insertResult) {
    die(json_encode(["success" => false, "message" => "Error inserting item into disposed_items table: " . mysqli_error($conn)]));
}

// Close the prepared statement
mysqli_stmt_close($insertStatement);

// Now, delete the item from found_items table
$deleteQuery = "DELETE FROM found_items WHERE id = $itemId";

$deleteResult = mysqli_query($conn, $deleteQuery);

if (!$deleteResult) {
    die(json_encode(["success" => false, "message" => "Error deleting item from found_items table: " . mysqli_error($conn)]));
}

// Close the database connection
mysqli_close($conn);

// Return a success message
echo json_encode(["success" => true, "message" => "Item disposed successfully"]);
?>
