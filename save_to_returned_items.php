<?php
// Include your database connection code here if not already included
$servername = "localhost";
$username = "root";
$password = "findthere123";
$database = "findthere";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $notificationId = mysqli_real_escape_string($conn, $_POST['notificationId']);

    $insertQuery = "INSERT INTO returned_items (returned_item, returned_itemimg, returned_itemdetails, rnotes, returnedto, owner_address, owner_course, phone, email, datereturned)
        SELECT returned_item, returned_itemimg, returned_itemdetails, rnotes, returnedto, owner_address, owner_course, phone, email, CURRENT_TIMESTAMP
        FROM (
            SELECT found_items.item AS returned_item, found_items.image AS returned_itemimg, found_items.details AS returned_itemdetails, found_items.notes AS rnotes,
                claims.owner AS returnedto, null AS owner_address, null AS owner_course, claims.owner_phone AS phone, claims.owner_email as email
            FROM claims 
            LEFT JOIN found_items ON claims.item_id = found_items.id 
            WHERE claims.id = $notificationId
            UNION ALL
            SELECT lost_items.item AS returned_item, lost_items.image AS returned_itemimg, lost_items.details AS returned_itemdetails, lost_items.notes AS rnotes,
                lost_items.name AS returnedto, return_forms.address, return_forms.course, lost_items.phone, lost_items.email
            FROM return_forms 
            LEFT JOIN lost_items ON return_forms.lostitem_id = lost_items.id
            WHERE return_forms.id = $notificationId
        ) AS combined_data";

$resultInsert = mysqli_query($conn, $insertQuery);

if ($resultInsert) {
   
    $deleteFoundItemsQuery = "DELETE FROM found_items WHERE id IN (SELECT item_id FROM claims WHERE id = $notificationId)";
    $resultDeleteFoundItems = mysqli_query($conn, $deleteFoundItemsQuery);

    if ($resultDeleteFoundItems) {
        $deleteClaimsQuery = "DELETE FROM claims WHERE id = $notificationId";
        $resultDeleteClaims = mysqli_query($conn, $deleteClaimsQuery);

        if ($resultDeleteClaims) {
            $deleteLostItemsQuery = "DELETE FROM lost_items WHERE id IN (SELECT lostitem_id FROM return_forms WHERE id = $notificationId)";
            $resultDeleteLostItems = mysqli_query($conn, $deleteLostItemsQuery);

            if ($resultDeleteLostItems) {
                $deleteReturnFormsQuery = "DELETE FROM return_forms WHERE id = $notificationId";
                $resultDeleteReturnForms = mysqli_query($conn, $deleteReturnFormsQuery);

                // If all deletions are successful, you can update the status or perform other actions
                if ($resultDeleteReturnForms) {
                    echo "Request approved, item saved to returned items records.";
                }
            }
        }
    }
}
} else {
// If not a POST request, handle accordingly
echo "Invalid request method.";
}

// Close the database connection
mysqli_close($conn);
?>
