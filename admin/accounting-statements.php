<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
    exit;
}
include 'dbcon.php';
require_once __DIR__ . '/includes/accounting_engine.php';
acc_bootstrap_tables($con);

$page = 'accounting';
$from = isset($_GET['from']) ? $_GET['from'] : date('Y-m-01');
$to = isset($_GET['to']) ? $_GET['to'] : date('Y-m-d');

$is = acc_income_statement($con, $from, $to);
$bs = acc_balance_sheet($con, $to);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Financial Statements - Accounting</title>
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
  <div id="content-header"><div id="breadcrumb"><a href="index.php"><i class="fas fa-home"></i> Home</a> <a href="accounting-cycle.php">Accounting</a> <a href="#" class="current">Statements</a></div><h1>Financial Statements</h1></div>
  <div class="container-fluid"><hr>
    <form method="get" class="form-inline" style="margin-bottom:12px;">
      <label>From</label> <input type="date" name="from" value="<?php echo htmlspecialchars($from); ?>">
      <label>To</label> <input type="date" name="to" value="<?php echo htmlspecialchars($to); ?>">
      <button class="btn btn-primary">Generate</button>
    </form>

    <div class="row-fluid">
      <div class="span6">
        <div class="widget-box"><div class="widget-title"><span class="icon"><i class="fas fa-chart-line"></i></span><h5>Income Statement</h5></div>
          <div class="widget-content">
            <h4>Revenues</h4>
            <table class="table table-bordered table-striped">
              <?php foreach ($is['revenues'] as $r) { ?>
                <tr><td><?php echo htmlspecialchars($r['code'] . ' - ' . $r['name']); ?></td><td style="text-align:right;">$<?php echo number_format((float)$r['amount'], 2); ?></td></tr>
              <?php } ?>
              <tr><th>Total Revenue</th><th style="text-align:right;">$<?php echo number_format((float)$is['total_revenue'], 2); ?></th></tr>
            </table>
            <h4>Expenses</h4>
            <table class="table table-bordered table-striped">
              <?php foreach ($is['expenses'] as $r) { ?>
                <tr><td><?php echo htmlspecialchars($r['code'] . ' - ' . $r['name']); ?></td><td style="text-align:right;">$<?php echo number_format((float)$r['amount'], 2); ?></td></tr>
              <?php } ?>
              <tr><th>Total Expense</th><th style="text-align:right;">$<?php echo number_format((float)$is['total_expense'], 2); ?></th></tr>
              <tr><th>Net Income</th><th style="text-align:right; color:<?php echo $is['net_income'] >= 0 ? '#065f46' : '#b91c1c'; ?>;">$<?php echo number_format((float)$is['net_income'], 2); ?></th></tr>
            </table>
          </div>
        </div>
      </div>

      <div class="span6">
        <div class="widget-box"><div class="widget-title"><span class="icon"><i class="fas fa-landmark"></i></span><h5>Balance Sheet (as of <?php echo htmlspecialchars($to); ?>)</h5></div>
          <div class="widget-content">
            <h4>Assets</h4>
            <table class="table table-bordered table-striped">
              <?php foreach ($bs['assets'] as $a) { ?>
                <tr><td><?php echo htmlspecialchars($a['code'] . ' - ' . $a['name']); ?></td><td style="text-align:right;">$<?php echo number_format((float)$a['amount'], 2); ?></td></tr>
              <?php } ?>
              <tr><th>Total Assets</th><th style="text-align:right;">$<?php echo number_format((float)$bs['total_assets'], 2); ?></th></tr>
            </table>
            <h4>Liabilities + Equity</h4>
            <table class="table table-bordered table-striped">
              <?php
                $owner_equity = 0;
                $retained_earnings = 0;
                foreach ($bs['liabilities'] as $l) {
                  echo '<tr><td>' . htmlspecialchars($l['code'] . ' - ' . $l['name']) . '</td><td style="text-align:right;">$' . number_format((float)$l['amount'], 2) . '</td></tr>';
                }
                foreach ($bs['equity'] as $e) {
                  if ($e['code'] == '3000') $owner_equity = (float)$e['amount'];
                  if ($e['code'] == '3100') $retained_earnings = (float)$e['amount'];
                  if ($e['code'] == '3200') $owner_drawing = (float)$e['amount'];
                  echo '<tr><td>' . htmlspecialchars($e['code'] . ' - ' . $e['name']) . '</td><td style="text-align:right;">$' . number_format((float)$e['amount'], 2) . '</td></tr>';
                }
                $current_earnings = (float)$is['net_income'];
              ?>
              <tr><th>Total Liabilities</th><th style="text-align:right;">$<?php echo number_format((float)$bs['total_liabilities'], 2); ?></th></tr>
              <tr><th>Current Year Earnings</th><th style="text-align:right;">$<?php echo number_format($current_earnings, 2); ?></th></tr>
              <tr><th>Total Equity</th><th style="text-align:right;">$<?php echo number_format($owner_equity + $retained_earnings + $owner_drawing + $current_earnings, 2); ?></th></tr>
              <tr><th>Total L+E</th><th style="text-align:right;">$<?php echo number_format((float)($bs['total_liabilities'] + $owner_equity + $retained_earnings + $owner_drawing + $current_earnings), 2); ?></th></tr>
            </table>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
<script src="../js/jquery.min.js"></script><script src="../js/bootstrap.min.js"></script><script src="../js/matrix.js"></script>
</body>
</html>
