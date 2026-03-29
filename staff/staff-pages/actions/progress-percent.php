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

$ini_weight = $row['ini_weight'];
$curr_weight = $row['curr_weight'];

if ($ini_weight > 0) {
    $percent = (($curr_weight - $ini_weight) / $ini_weight) * 100;
    echo round($percent, 1) . "%";
} else {
    echo "0%";
}
?>
