<?php
$servername = "localhost";
$username = "root";
$password = "findthere123";
$database = "findthere";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the item ID from the GET request
$itemId = $_GET['id'];

// Prepare and execute the SQL query to delete the item from lost_items table
$lostItemsQuery = "DELETE FROM lost_items WHERE id = ?";
$lostItemsStmt = $conn->prepare($lostItemsQuery);
$lostItemsStmt->bind_param("i", $itemId);

// Prepare and execute the SQL query to delete the item from found_items table
$foundItemsQuery = "DELETE FROM found_items WHERE id = ?";
$foundItemsStmt = $conn->prepare($foundItemsQuery);
$foundItemsStmt->bind_param("i", $itemId);

// Prepare and execute the SQL query to delete the item from returned_items table
$returnedItemsQuery = "DELETE FROM returned_items WHERE id = ?";
$returnedItemsStmt = $conn->prepare($returnedItemsQuery);
$returnedItemsStmt->bind_param("i", $itemId);

$disposedItemsQuery = "DELETE FROM disposed_items WHERE iddisposed_items = ?";
$disposedItemsStmt = $conn->prepare($disposedItemsQuery);
$disposedItemsStmt->bind_param("i", $itemId);

// Perform the deletions
$deletionSuccess = $lostItemsStmt->execute() && $foundItemsStmt->execute() && $returnedItemsStmt->execute() && $disposedItemsStmt->execute();

$response = array();
if ($deletionSuccess) {
    // Deletion successful
    $response['success'] = true;
    $response['message'] = "Item deleted successfully";
} else {
    // Error in deletion
    $response['success'] = false;
    $response['message'] = "Error deleting item: " . $conn->error;
}

// Close the prepared statements and database connection
$lostItemsStmt->close();
$foundItemsStmt->close();
$returnedItemsStmt->close();
$disposedItemsStmt->close();
$conn->close();

// Return JSON response
echo json_encode($response);
?>
