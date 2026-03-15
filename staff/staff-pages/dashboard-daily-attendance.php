<?php
include '../dbcon.php';
$today = date('Y-m-d');
$query = "SELECT COUNT(*) as total_count FROM attendance WHERE curr_date = '$today'";
$res = mysqli_query($con, $query);
$row = mysqli_fetch_array($res);
echo $row['total_count'];
