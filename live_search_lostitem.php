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

$lostItemsQuery = "SELECT * FROM lost_items WHERE item LIKE '%$searchQuery%' OR 
details LIKE '%$searchQuery%' OR notes LIKE '%$searchQuery%' OR
name LIKE '%$searchQuery%'";
$lostItemsResult = mysqli_query($conn, $lostItemsQuery);

$output = "<tr>
  <th>ID</th>
  <th>Item</th>
  <th>Details</th>
  <th>Lost By</th>
  <th>Email</th>
  <th>Phone</th>
  <th>Date Lost</th>
  <th>Image</th>
  <th>Notes</th>
  <th>Action</th>
</tr>";

while ($lostItem = mysqli_fetch_assoc($lostItemsResult)) {
  $output .= "<tr>";
  $output .= "<td>{$lostItem['id']}</td>";
  $output .= "<td>{$lostItem['item']}</td>";
  $output .= "<td>{$lostItem['details']}</td>";
  $output .= "<td>{$lostItem['name']}</td>";
  $output .= "<td>{$lostItem['email']}</td>";
  $output .= "<td>{$lostItem['phone']}</td>";
  $output .= "<td>{$lostItem['datelost']}</td>";
  $output .= "<td><img src='uploads/{$lostItem['image']}' alt='Lost Item Image'></td>";
  $output .= "<td>{$lostItem['notes']}</td>";
  $output .= "<td>";
  $output .= "<button onclick='showEditPopup({$lostItem['id']}, \"{$lostItem['item']}\", \"{$lostItem['details']}\", \"{$lostItem['name']}\", \"{$lostItem['email']}\", \"{$lostItem['phone']}\", \"{$lostItem['datelost']}\", \"{$lostItem['image']}\", \"{$lostItem['notes']}\")' 
    style='background-color: gray; margin-bottom: 10px;'>Edit</button>";
  $output .= "<button onclick='deleteItem({$lostItem['id']})'>Delete</button>";
  $output .= "</td>";
  $output .= "</tr>";
}

echo $output;
?>
