<?php
$servername = "localhost";
$username = "root";
$password = "findthere123";
$database = "findthere";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['submitClaim'])) {
    $item_id = $_POST['item_id'];
    $owner = $_POST['owner'];
    $address = $_POST['address'];
    $course = $_POST['course'];
    $owner_phone = $_POST['owner_phone'];
    $owner_email = $_POST['owner_email'];

    // Insert data into the 'claims' table
    $sql = "INSERT INTO claims (item_id, owner, address, course, owner_phone, owner_email, claim_status) VALUES ('$item_id', '$owner', '$address', '$course', '$owner_phone', '$owner_email','Wants to claim the item')";

    if (mysqli_query($conn, $sql)) {
        // Display an alert and reload the page using JavaScript
        echo '<script>alert("Claim request submitted successfully."); window.location.href = "displayfounditems.php";</script>';
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

    // Close the database connection
    mysqli_close($conn);
}
?>
