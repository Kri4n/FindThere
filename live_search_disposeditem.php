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

$disposedItemsQuery = "SELECT * FROM disposed_items 
                      WHERE item LIKE '%$searchQuery%' OR 
                            details LIKE '%$searchQuery%' OR notes LIKE '%$searchQuery%' OR
                            foundby LIKE '%$searchQuery%'";

$disposedItemsResult = mysqli_query($conn, $disposedItemsQuery);

$output = "<tr>
    <th>ID</th>
    <th>Item</th>
    <th>Details</th>
    <th>Found By</th>
    <th>Email</th>
    <th>Phone</th>
    <th>Date Found</th>
    <th>Date Disposed</th>
    <th>Image</th>
    <th>Notes</th>
    <th>Action</th>
</tr>";

while ($disposedItem = mysqli_fetch_assoc($disposedItemsResult)) {
    $output .= "<tr>";
    $output .= "<td>{$disposedItem['iddisposed_items']}</td>";
    $output .= "<td>{$disposedItem['item']}</td>";
    $output .= "<td>{$disposedItem['details']}</td>";
    $output .= "<td>{$disposedItem['foundby']}</td>";
    $output .= "<td>{$disposedItem['email']}</td>";
    $output .= "<td>{$disposedItem['phone']}</td>";
    $output .= "<td>{$disposedItem['datefound']}</td>";
    $output .= "<td>{$disposedItem['datedisposed']}</td>";
    $output .= "<td><img src='uploads/{$disposedItem['image']}' alt='Disposed Item Image'></td>";
    $output .= "<td>{$disposedItem['notes']}</td>";
    $output .= "<td>";
    $output .= "<button onclick='deleteItem({$disposedItem['iddisposed_items']})'>Delete</button>";
    $output .= "</td>";
    $output .= "</tr>";
}

echo $output;
?>
