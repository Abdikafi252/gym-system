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
$msg = '';
$msgType = 'success';

if (isset($_POST['run_close'])) {
  $year = (int)$_POST['close_year'];
  $postedBy = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
  $sessBranch = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : 0;
  $res = acc_close_year($con, $year, $postedBy, $sessBranch);
  if ($res['ok']) {
    $msg = 'Closing complete for year ' . $year . '. Entry #' . $res['entry_id'] . ', Net Income: $' . number_format((float)$res['net_income'], 2);
    $msgType = 'success';
  } else {
    $msg = $res['message'];
    $msgType = 'error';
  }
}

// Handle manual unearned revenue journal entry
if (isset($_POST['unearned_submit'])) {
  $amount = isset($_POST['unearned_amount']) ? (float)$_POST['unearned_amount'] : 0;
  $memo = isset($_POST['unearned_memo']) ? $_POST['unearned_memo'] : 'Unearned Revenue Liability';
  $res = acc_post_unearned_revenue($con, $amount, $memo);
  if ($res['ok']) {
    $msg = 'Unearned revenue entry posted successfully!';
    $msgType = 'success';
  } else {
    $msg = 'Error: ' . $res['message'];
    $msgType = 'error';
  }
}
$periods = mysqli_query($con, "SELECT * FROM fiscal_periods ORDER BY period_year DESC");
$postRowsAll = acc_trial_balance_rows($con, 1, date('Y-m-d'));
$postRows = [];
$netIncome = 0;

// Pass 1: separate permanent accounts and calculate net income from temporary accounts
foreach ($postRowsAll as $r) {
  if ($r['account_type'] === 'Revenue' || $r['account_type'] === 'Expense') {
    $netIncome += ((float)$r['trial_credit'] - (float)$r['trial_debit']);
    continue;
  }
  $postRows[] = $r;
}

// Pass 2: find or create Retained Earnings and apply net income
$foundRE = false;
foreach ($postRows as $k => $r) {
  if ($r['code'] === '3100') {
    $foundRE = true;
    $currentBal = ((float)$r['trial_credit'] - (float)$r['trial_debit']) + $netIncome;
    if ($currentBal > 0) {
      $postRows[$k]['trial_credit'] = $currentBal;
      $postRows[$k]['trial_debit'] = 0;
    } else {
      $postRows[$k]['trial_debit'] = abs($currentBal);
      $postRows[$k]['trial_credit'] = 0;
    }
    break;
  }
}

if (!$foundRE && abs($netIncome) > 0.01) {
  $postRows[] = [
    'code' => '3100',
    'name' => 'Retained Earnings',
    'account_type' => 'Equity',
    'trial_credit' => $netIncome > 0 ? $netIncome : 0,
    'trial_debit' => $netIncome < 0 ? abs($netIncome) : 0
  ];
}

// Pass 3: Summarize final totals (excluding true zeroes)
$finalPostRows = [];
$totalDr = 0;
$totalCr = 0;
foreach ($postRows as $r) {
  if ((float)$r['trial_debit'] == 0 && (float)$r['trial_credit'] == 0) continue;
  $finalPostRows[] = $r;
  $totalDr += (float)$r['trial_debit'];
  $totalCr += (float)$r['trial_credit'];
}
$postRows = $finalPostRows;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title>Closing Entries - Accounting</title>
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
    <div id="content-header">
      <div id="breadcrumb"><a href="index.php"><i class="fas fa-home"></i> Home</a> <a href="accounting-cycle.php">Accounting</a> <a href="#" class="current">Closing</a></div>
      <h1>Closing Entries and Post-Closing Trial Balance</h1>
    </div>
    <div class="container-fluid">
      <hr>
      <?php if ($msg !== '') { ?>
        <div class="alert <?php echo $msgType === 'success' ? 'alert-success' : 'alert-error'; ?>"><?php echo htmlspecialchars($msg); ?></div>
      <?php } ?>

      <div class="row-fluid">
        <div class="span5">
          <div class="widget-box">
            <div class="widget-title"><span class="icon"><i class="fas fa-door-closed"></i></span>
              <h5>Run Year-End Close</h5>
            </div>
            <div class="widget-content">
              <form method="post">
                <label>Year to close</label>
                <input type="number" class="span12" name="close_year" min="2020" max="2099" value="<?php echo date('Y') - 1; ?>" required>
                <p style="margin-top:10px;">This posts closing entries for Revenue and Expense accounts into Retained Earnings.</p>
                <button class="btn btn-danger" name="run_close" value="1" onclick="return confirm('Run year-end closing entries?');">Run Closing</button>
              </form>
            </div>
          </div>

          <div class="widget-box">
            <div class="widget-title"><span class="icon"><i class="fas fa-calendar"></i></span>
              <h5>Closed Fiscal Periods</h5>
            </div>
            <div class="widget-content nopadding">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Year</th>
                    <th>Status</th>
                    <th>Closed At</th>
                    <th>By</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($periods) {
                    while ($p = mysqli_fetch_assoc($periods)) { ?>
                      <tr>
                        <td><?php echo (int)$p['period_year']; ?></td>
                        <td><?php echo htmlspecialchars($p['status']); ?></td>
                        <td><?php echo htmlspecialchars($p['closed_at']); ?></td>
                        <td><?php echo htmlspecialchars($p['closed_by']); ?></td>
                      </tr>
                  <?php }
                  } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="span7" id="post-closing">
          <div class="widget-box">
            <div class="widget-title"><span class="icon"><i class="fas fa-balance-scale"></i></span>
              <h5>Post-Closing Trial Balance (Permanent Accounts Only)</h5>
            </div>
            <div class="widget-content nopadding">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Code</th>
                    <th>Account</th>
                    <th>Type</th>
                    <th>Debit</th>
                    <th>Credit</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($postRows as $r) { ?>
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
                  <tr>
                    <th colspan="3" style="text-align:right;">Totals</th>
                    <th>$<?php echo number_format($totalDr, 2); ?></th>
                    <th>$<?php echo number_format($totalCr, 2); ?></th>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="../js/jquery.min.js"></script>
  <script src="../js/bootstrap.min.js"></script>
  <script src="../js/matrix.js"></script>
</body>

</html>