<?php
include __DIR__ . '/../../dbcon.php';

if (!isset($conn) && isset($con)) {
    $conn = $con;
}

if (!$conn) {
    die("Connection Failed");
}

$sql = "SELECT * FROM members WHERE status='Active'";
$query = mysqli_query($conn, $sql);
echo mysqli_num_rows($query);
?>
