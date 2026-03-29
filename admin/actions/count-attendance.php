<?php
include __DIR__ . '/../../dbcon.php';

if (!isset($conn) && isset($con)) {
    $conn = $con;
}

$date = date("Y-m-d");
$sql = "SELECT * FROM attendance WHERE curr_date='$date'";
$query = mysqli_query($conn, $sql);
echo mysqli_num_rows($query);
?>
