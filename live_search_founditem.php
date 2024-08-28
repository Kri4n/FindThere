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

$foundItemsQuery = "SELECT * FROM found_items 
                  WHERE item LIKE '%$searchQuery%' OR notes LIKE '%$searchQuery%' OR
                        details LIKE '%$searchQuery%' OR 
                        foundby LIKE '%$searchQuery%'";
$foundItemsResult = mysqli_query($conn, $foundItemsQuery);

// Generate the updated table HTML
$output = "<tr>
    <th>ID</th>
    <th>Item</th>
    <th>Details</th>
    <th>Found By</th>
    <th>Email</th>
    <th>Phone</th>
    <th>Date Found</th>
    <th>Image</th>
    <th>Notes</th>
    <th>Action</th>
</tr>";

while ($foundItem = mysqli_fetch_assoc($foundItemsResult)) {
    $output .= "<tr>";
    $output .= "<td>{$foundItem['id']}</td>";
    $output .= "<td>{$foundItem['item']}</td>";
    $output .= "<td>{$foundItem['details']}</td>";
    $output .= "<td>{$foundItem['foundby']}</td>";
    $output .= "<td>{$foundItem['email']}</td>";
    $output .= "<td>{$foundItem['phone']}</td>";
    $output .= "<td>{$foundItem['datefound']}</td>";
    $output .= "<td><img src='uploads/{$foundItem['image']}' alt='Found Item Image'></td>";
    $output .= "<td>{$foundItem['notes']}</td>";
    $output .= "<td>";
    $output .= "<button onclick='showEditPopup({$foundItem['id']}, \"{$foundItem['item']}\", \"{$foundItem['details']}\", \"{$foundItem['foundby']}\", \"{$foundItem['email']}\", \"{$foundItem['phone']}\", \"{$foundItem['datefound']}\", \"{$foundItem['image']}\", \"{$foundItem['notes']}\")' 
        style='background-color: gray; margin-bottom: 10px;'>Edit</button>";
    $output .= "<button onclick='disposeItem({$foundItem['id']})' style='background-color: green; margin-bottom: 10px;'>Dispose</button>";
    $output .= "<button onclick='deleteItem({$foundItem['id']})'>Delete</button>";
    $output .= "</td>";
    $output .= "</tr>";
}

echo $output;
?>
