<?php
include __DIR__ . '/../dbcon.php';

if (!isset($conn) && isset($con)) {
    $conn = $con;
}

$sql = "SELECT * FROM members";
$query = mysqli_query($conn, $sql);
echo mysqli_num_rows($query);
?>
