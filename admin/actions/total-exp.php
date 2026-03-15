<?php

$servername = "localhost";
$uname = "root";
$pass = "";
$db = "gymnsb";

$conn = mysqli_connect($servername, $uname, $pass, $db);

if (!$conn) {
    die("Connection Failed");
}

// Sum of expenses only (source of truth)
$sql_exp = "SELECT COALESCE(SUM(amount),0) FROM expenses";
$result_exp = mysqli_query($conn, $sql_exp);
$row_exp = mysqli_fetch_array($result_exp);
$total_expense = (float)$row_exp[0];
echo $total_expense;
