<?php
include __DIR__ . '/../dbcon.php';

if (!isset($conn) && isset($con)) {
    $conn = $con;
}

if (!$conn) {
    die("Connection Failed");
}

$sql = "SELECT SUM(amount) FROM members";
$amountsum = mysqli_query($conn, $sql) or die(mysqli_error($conn));
$row_amountsum = mysqli_fetch_assoc($amountsum);
echo $row_amountsum['SUM(amount)'];
?>
