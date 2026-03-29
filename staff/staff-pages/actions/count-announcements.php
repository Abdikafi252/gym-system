<?php
include __DIR__ . '/../../dbcon.php';

if (!isset($conn) && isset($con)) {
    $conn = $con;
}

$sql = "SELECT * FROM announcements";
$query = mysqli_query($conn, $sql);
echo mysqli_num_rows($query);
?>
