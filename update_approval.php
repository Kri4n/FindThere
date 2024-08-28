<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] == 'approveRequest') {
        $notificationId = $_POST['notificationId'];

        // Move the found-item to the returned_items table
        $updateQuery = "INSERT INTO returned_items SELECT * FROM found_items WHERE id = (SELECT item_id FROM claims WHERE id = $notificationId)";
        $deleteQuery = "DELETE FROM found_items WHERE id = (SELECT item_id FROM claims WHERE id = $notificationId)";

        if (mysqli_query($conn, $updateQuery) && mysqli_query($conn, $deleteQuery)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update database']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
