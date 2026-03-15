<?php

$servername = "localhost";
$uname = "root";
$pass = "";
$db = "gymnsb";

$conn = mysqli_connect($servername, $uname, $pass, $db);

if (!$conn) {
    die("Connection Failed");
}

// Income
$sql_income = "SELECT COALESCE(SUM(paid_amount),0) FROM members";
$result_income = mysqli_query($conn, $sql_income);
$row_income = mysqli_fetch_array($result_income);
$income = (float)$row_income[0];

// Expenses from expenses table only
$sql_exp = "SELECT COALESCE(SUM(amount),0) FROM expenses";
$result_exp = mysqli_query($conn, $sql_exp);
$row_exp = mysqli_fetch_array($result_exp);
$total_expense = (float)$row_exp[0];

$net_profit = $income - $total_expense;
echo $net_profit;
