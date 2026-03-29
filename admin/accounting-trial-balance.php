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
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'adjusted';
$includeAdjustments = $mode === 'unadjusted' ? 0 : 1;
$year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
$rows = acc_trial_balance_rows($con, $includeAdjustments, null, $year);

$availableYears = [];
$yRes = mysqli_query($con, "SELECT DISTINCT period_year FROM journal_entries ORDER BY period_year DESC");
while($yRow = mysqli_fetch_assoc($yRes)) {
    $availableYears[] = (int)$yRow['period_year'];
}
if (!in_array((int)date('Y'), $availableYears)) {
    $availableYears[] = (int)date('Y');
    rsort($availableYears);
}

$totalDr = 0;
$totalCr = 0;
foreach ($rows as $r) {
    $totalDr += (float)$r['trial_debit'];
    $totalCr += (float)$r['trial_credit'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Trial Balance - Accounting</title>
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
  <div id="content-header"><div id="breadcrumb"><a href="index.php"><i class="fas fa-home"></i> Home</a> <a href="accounting-cycle.php">Accounting</a> <a href="#" class="current">Trial Balance</a></div><h1><?php echo $includeAdjustments ? 'Adjusted' : 'Unadjusted'; ?> Trial Balance</h1></div>
  <div class="container-fluid"><hr>
    <div class="row-fluid"><div class="span12">
      <div class="widget-box"><div class="widget-title"><span class="icon"><i class="fas fa-balance-scale"></i></span><h5>Trial Balance</h5></div>
        <div class="widget-content">
          <div class="row-fluid" style="margin-bottom:15px; display:flex; align-items:center; gap:10px;">
            <div class="span4">
              <form method="get" action="accounting-trial-balance.php" id="yearForm" style="margin:0;">
                <input type="hidden" name="mode" value="<?php echo htmlspecialchars($mode); ?>">
                <label style="display:inline-block; margin-right:5px;">Fiscal Year:</label>
                <select name="year" onchange="document.getElementById('yearForm').submit();" style="width:120px;">
                  <?php foreach ($availableYears as $y) { ?>
                    <option value="<?php echo $y; ?>" <?php echo $y === $year ? 'selected' : ''; ?>><?php echo $y; ?></option>
                  <?php } ?>
                </select>
              </form>
            </div>
            <div class="span8" style="text-align:right;">
              <a class="btn btn-mini <?php echo !$includeAdjustments ? 'btn-primary' : ''; ?>" href="accounting-trial-balance.php?year=<?php echo $year; ?>&mode=unadjusted">Unadjusted</a>
              <a class="btn btn-mini <?php echo $includeAdjustments ? 'btn-primary' : ''; ?>" href="accounting-trial-balance.php?year=<?php echo $year; ?>&mode=adjusted">Adjusted</a>
            </div>
          </div>
          <table class="table table-bordered table-striped">
            <thead><tr><th>Code</th><th>Account</th><th>Type</th><th>Debit</th><th>Credit</th></tr></thead>
            <tbody>
              <?php foreach ($rows as $r) { if ((float)$r['trial_debit'] == 0 && (float)$r['trial_credit'] == 0) { continue; } ?>
                <tr>
                  <td><?php echo htmlspecialchars($r['code']); ?></td>
                  <td><?php echo htmlspecialchars($r['name']); ?></td>
                  <td><?php echo htmlspecialchars($r['account_type']); ?></td>
                  <td>$<?php echo number_format((float)$r['trial_debit'], 2); ?></td>
                  <td>$<?php echo number_format((float)$r['trial_credit'], 2); ?></td>
                </tr>
              <?php } ?>
            </tbody>
            <tfoot>
              <tr><th colspan="3" style="text-align:right;">Totals</th><th>$<?php echo number_format($totalDr, 2); ?></th><th>$<?php echo number_format($totalCr, 2); ?></th></tr>
              <tr><th colspan="3" style="text-align:right;">Difference</th><th colspan="2" style="color:<?php echo abs($totalDr - $totalCr) < 0.01 ? '#065f46' : '#b91c1c'; ?>;">$<?php echo number_format(abs($totalDr - $totalCr), 2); ?></th></tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div></div>
  </div>
</div>
<script src="../js/jquery.min.js"></script><script src="../js/bootstrap.min.js"></script><script src="../js/matrix.js"></script>
</body>
</html>
