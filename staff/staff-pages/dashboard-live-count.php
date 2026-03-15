<?php
include '../dbcon.php';
$today = date('Y-m-d');
$query = "SELECT COUNT(*) as live_count FROM attendance WHERE check_out IS NULL AND DATE(check_in) = '$today'";
$res = mysqli_query($con, $query);
$row = mysqli_fetch_array($res);
echo $row['live_count'];
