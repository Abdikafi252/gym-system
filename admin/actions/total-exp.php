<?php
include __DIR__ . '/../dbcon.php';

if (!isset($conn) && isset($con)) {
    $conn = $con;
}

if (!$conn) {
    die("Connection Failed");
}

$sql = "SELECT COALESCE(SUM(paid_amount), 0) FROM members";
$amountsum = mysqli_query($conn, $sql) or die(mysqli_error($conn));
$row_amountsum = mysqli_fetch_assoc($amountsum);
echo '$' . number_format($row_amountsum['COALESCE(SUM(paid_amount), 0)'], 2);
?>
