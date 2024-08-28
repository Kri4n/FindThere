<?php
$servername = "localhost";
$username = "root";
$password = "findthere123";
$database = "findthere";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$searchQuery = isset($_POST['search']) ? $_POST['search'] : '';

$returnedItemsQuery = "SELECT * FROM returned_items 
                      WHERE returned_item LIKE '%$searchQuery%' OR 
                            returned_itemdetails LIKE '%$searchQuery%' OR rnotes LIKE '%$searchQuery%' OR
                            returnedto LIKE '%$searchQuery%'";
$returnedItemsResult = mysqli_query($conn, $returnedItemsQuery);

$output = "<tr>
    <th>ID</th>
    <th>Returned Item</th>
    <th>Details</th>
    <th>Returned To</th>
    <th>Email</th>
    <th>Phone</th>
    <th>Date Returned</th>
    <th>Image</th>
    <th>Notes</th>
    <th>Action</th>
</tr>";

while ($returnedItem = mysqli_fetch_assoc($returnedItemsResult)) {
    $output .= "<tr>";
    $output .= "<td>{$returnedItem['id']}</td>";
    $output .= "<td>{$returnedItem['returned_item']}</td>";
    $output .= "<td>{$returnedItem['returned_itemdetails']}</td>";
    $output .= "<td>{$returnedItem['returnedto']}</td>";
    $output .= "<td>{$returnedItem['email']}</td>";
    $output .= "<td>{$returnedItem['phone']}</td>";
    $output .= "<td>{$returnedItem['datereturned']}</td>";
    $output .= "<td><img src='uploads/{$returnedItem['returned_itemimg']}' alt='Returned Item Image'></td>";
    $output .= "<td>{$returnedItem['rnotes']}</td>";
    $output .= "<td>";
    $output .= "<button onclick='deleteItem({$returnedItem['id']})'>Delete</button>";
    $output .= "</td>";
    $output .= "</tr>";
}

echo $output;
?>

