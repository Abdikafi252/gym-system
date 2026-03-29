<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
    exit;
}
include 'dbcon.php';
require_once __DIR__ . '/includes/accounting_engine.php';

$page = 'debug_balance_sheet';
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$year = date('Y', strtotime($date));

// Get trial balance rows
$rows = acc_trial_balance_rows($con, 1, $date);

$assets = 0;
$liabilities = 0;
$owner_equity = 0;
$retained_earnings = 0;
$current_year_earnings = 0;
$owner_drawing = 0;
$unearned_revenue = 0;
$accounts_receivable = 0;
$gym_equipment = 0;
$cash = 0;

foreach ($rows as $row) {
    $code = $row['code'];
    $balance = (float)$row['total_debit'] - (float)$row['total_credit'];
    switch ($code) {
        case '1000': $cash = $balance; $assets += $balance; break;
        case '1100': $accounts_receivable = $balance; $assets += $balance; break;
        case '1500': $gym_equipment = $balance; $assets += $balance; break;
        case '2200': $unearned_revenue = $balance; $liabilities += $balance; break;
        case '3000': $owner_equity = $balance; break;
        case '3100': $retained_earnings = $balance; break;
        case '3200': $owner_drawing = $balance; break;
        case '4000': $current_year_earnings += $balance; break;
        case '4100': $current_year_earnings += $balance; break;
        case '5000': $current_year_earnings -= $balance; break;
        case '5200': $current_year_earnings -= $balance; break;
    }
}

$total_equity = $owner_equity + $retained_earnings + $owner_drawing + $current_year_earnings;
$total_liabilities_equity = $liabilities + $total_equity;

$diff = $assets - $total_liabilities_equity;

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Debug Balance Sheet</title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../css/bootstrap.min.css" />
  <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
  <link rel="stylesheet" href="../css/matrix-style.css" />
  <link rel="stylesheet" href="../css/matrix-media.css" />
  <link href="../font-awesome/css/all.css" rel="stylesheet" />
</head>
<body>
<?php include 'includes/header-content.php'; ?>
<?php include 'includes/topheader.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<div id="content">
  <div class="container-fluid">
    <h1>Debug Balance Sheet</h1>
    <table class="table table-bordered table-striped">
      <thead><tr><th>Type</th><th>Account</th><th>Amount</th></tr></thead>
      <tbody>
        <tr><td>Asset</td><td>Cash (1000)</td><td>$<?php echo number_format($cash,2); ?></td></tr>
        <tr><td>Asset</td><td>Accounts Receivable (1100)</td><td>$<?php echo number_format($accounts_receivable,2); ?></td></tr>
        <tr><td>Asset</td><td>Gym Equipment (1500)</td><td>$<?php echo number_format($gym_equipment,2); ?></td></tr>
        <tr><td>Liability</td><td>Unearned Revenue (2200)</td><td>$<?php echo number_format($unearned_revenue,2); ?></td></tr>
        <tr><td>Equity</td><td>Owner Equity (3000)</td><td>$<?php echo number_format($owner_equity,2); ?></td></tr>
        <tr><td>Equity</td><td>Retained Earnings (3100)</td><td>$<?php echo number_format($retained_earnings,2); ?></td></tr>
        <tr><td>Equity</td><td>Owner Drawing (3200)</td><td>$<?php echo number_format($owner_drawing,2); ?></td></tr>
        <tr><td>Equity</td><td>Current Year Earnings</td><td>$<?php echo number_format($current_year_earnings,2); ?></td></tr>
      </tbody>
    </table>
    <h3>Total Assets: $<?php echo number_format($assets,2); ?></h3>
    <h3>Total Liabilities + Equity: $<?php echo number_format($total_liabilities_equity,2); ?></h3>
    <h3 style="color:<?php echo ($diff==0?'green':'red'); ?>;">Difference: $<?php echo number_format($diff,2); ?></h3>
    <?php if ($diff != 0) { ?>
      <div class="alert alert-danger">Balance Sheet is NOT balanced. Farqiga: $<?php echo number_format($diff,2); ?>.<br>Hubi Owner Drawing, Retained Earnings, ama Current Year Earnings.</div>
    <?php } else { ?>
      <div class="alert alert-success">Balance Sheet waa balance-gareysan!</div>
    <?php } ?>
  </div>
</div>
<script src="../js/jquery.min.js"></script><script src="../js/bootstrap.min.js"></script><script src="../js/matrix.js"></script>
</body>
</html>
