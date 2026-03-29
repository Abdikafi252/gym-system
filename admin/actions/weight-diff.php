<?php
include __DIR__ . '/../../dbcon.php';

if (!isset($conn) && isset($con)) {
    $conn = $con;
}

if (!$conn) {
    die("Connection Failed");
}

$id = $_GET['id'];
$sql = "SELECT ini_weight, curr_weight FROM members WHERE user_id='$id'";
$res = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($res);

$diff = $row['curr_weight'] - $row['ini_weight'];
echo $diff > 0 ? "+" . $diff : $diff;
?>
