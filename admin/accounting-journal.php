<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
    exit;
}
include 'dbcon.php';
require_once __DIR__ . '/includes/accounting_engine.php';

$page = 'accounting';
$msg = '';
$msgType = 'success';

$selectedYear = date('Y');

// Branch filter logic
$branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : 0;
$branch_where = $branch_id > 0 ? " AND je.branch_id = " . $branch_id : '';

// Fetch all branches for dropdown
$branches = [];
$branch_res = mysqli_query($con, "SELECT id, branch_name FROM branches ORDER BY branch_name ASC");
while ($row = mysqli_fetch_assoc($branch_res)) {
  $branches[] = $row;
}
$isStaffManager = (isset($_SESSION['designation']) && $_SESSION['designation'] == 'Manager');
$sessBranch = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : 0;

if (isset($_POST['post_entry'])) {
    $entryDate = !empty($_POST['entry_date']) ? $_POST['entry_date'] : date('Y-m-d');
    $memo = isset($_POST['memo']) ? trim($_POST['memo']) : '';
    $isAdjustment = isset($_POST['is_adjustment']) ? 1 : 0;
    $branch_id = isset($_POST['branch_id']) ? (int)$_POST['branch_id'] : 0;

    // For staff managers, force their own branch
    if ($isStaffManager && $sessBranch > 0) {
      $branch_id = $sessBranch;
    }

    $accountCodes = isset($_POST['account_code']) ? $_POST['account_code'] : [];
    $debits = isset($_POST['debit']) ? $_POST['debit'] : [];
    $credits = isset($_POST['credit']) ? $_POST['credit'] : [];

    $lines = [];
    for ($i = 0; $i < count($accountCodes); $i++) {
        $code = trim($accountCodes[$i]);
        $dr = isset($debits[$i]) ? (float)$debits[$i] : 0;
        $cr = isset($credits[$i]) ? (float)$credits[$i] : 0;
        if ($code === '' || ($dr == 0 && $cr == 0)) {
            continue;
        }
        $lines[] = [
            'account_code' => $code,
            'debit' => $dr,
            'credit' => $cr,
            'line_memo' => $memo
        ];
    }

    $postedBy = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
    $res = acc_create_entry($con, $entryDate, $memo, 'manual', 'journal-' . time(), $lines, $isAdjustment, $branch_id, 0, $postedBy);

    if ($res['ok']) {
        $msg = 'Journal entry posted successfully. Entry #' . $res['entry_id'];
        $msgType = 'success';
    } else {
        $msg = 'Failed: ' . $res['message'];
        $msgType = 'error';
    }
}

$accounts = mysqli_query($con, "SELECT code, name, account_type FROM chart_of_accounts WHERE is_active=1 ORDER BY code");
$entries_res = mysqli_query($con, "SELECT je.id, je.entry_date, je.memo, je.is_closing, je.status, je.posted_by,
                jl.account_id, coa.code, coa.name, jl.debit, jl.credit
              FROM journal_entries je
              JOIN journal_lines jl ON jl.journal_entry_id = je.id
              JOIN chart_of_accounts coa ON coa.id = jl.account_id
              WHERE je.period_year = $selectedYear AND je.status='posted' $branch_where
              ORDER BY je.entry_date DESC, je.id DESC, jl.id ASC LIMIT 200");

$opening_bal_rows = acc_trial_balance_rows($con, 1, null, $selectedYear - 1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Journal - Accounting</title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../css/bootstrap.min.css" />
  <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
  <link rel="stylesheet" href="../css/matrix-style.css" />
  <link rel="stylesheet" href="../css/matrix-media.css" />
  <link href="../font-awesome/css/all.css" rel="stylesheet" />
</head>
<body>

<?php include 'includes/topheader.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<div id="content">
  <?php
    // Account summary cards
   
    $summaryRows = acc_trial_balance_rows($con, 1);
    $ownerCapital = 0;
    $cash = 0;
    $equipment = 0;

    foreach ($summaryRows as $row) {
      if ($row['code'] === '1000') {
        $cash = (float)$row['trial_debit'] - (float)$row['trial_credit'];
      }
      if ($row['code'] === '1500') {
        $equipment = (float)$row['trial_debit'] - (float)$row['trial_credit'];
      }
    }
    // Owner Capital: Only CASH contributions from owner_capital_contributions table
    // Equipment-funded equity is NOT counted here — it's tracked under Equipment Value
    $capRes = mysqli_query($con, "SELECT COALESCE(SUM(amount),0) AS total FROM owner_capital_contributions");
    $ownerCapital = ($capRes && ($capRow = mysqli_fetch_assoc($capRes))) ? (float)$capRow['total'] : 0;
    // Net Profit from Income Statement (Accrual)
    $isData = acc_income_statement($con, "$selectedYear-01-01", "$selectedYear-12-31");
    $netProfit = $isData['net_income'];
  ?>
  <div id="content-header"><div id="breadcrumb"><a href="index.php"><i class="fas fa-home"></i> Home</a> <a href="accounting-cycle.php">Accounting</a> <a href="#" class="current">Journal</a></div><h1>Journal Entries / Adjustments</h1></div>
  <div class="container-fluid">
    <div class="row-fluid" style="display:flex;gap:16px;flex-wrap:wrap;justify-content:space-between;">
      <div class="card-summary" style="flex:1 1 220px;min-width:220px;max-width:340px;background:#f8fafc;border-radius:16px;padding:20px 18px 16px 18px;box-shadow:0 2px 8px #0001;margin-bottom:16px;display:flex;align-items:center;">
        <div style="font-size:2.2em;color:#4f46e5;margin-right:16px;"><i class="fas fa-user-tie"></i></div>
        <div>
          <div style="font-size:1.1em;font-weight:600;color:#64748b;">Owner Capital <span style="font-size:0.9em;color:#888;">(Contributed Only)</span></div>
          <div style="font-size:1.7em;font-weight:700;color:#222;">$<?php echo number_format($ownerCapital, 2); ?></div>
        </div>
      </div>
      <div class="card-summary" style="flex:1 1 220px;min-width:220px;max-width:340px;background:#f0fdf4;border-radius:16px;padding:20px 18px 16px 18px;box-shadow:0 2px 8px #0001;margin-bottom:16px;display:flex;align-items:center;">
        <div style="font-size:2.2em;color:#16a34a;margin-right:16px;"><i class="fas fa-money-bill-wave"></i></div>
        <div>
          <div style="font-size:1.1em;font-weight:600;color:#64748b;">Cash Balance</div>
          <div style="font-size:1.7em;font-weight:700;color:#222;">$<?php echo number_format($cash, 2); ?></div>
        </div>
      </div>
      <div class="card-summary" style="flex:1 1 220px;min-width:220px;max-width:340px;background:#f1f5f9;border-radius:16px;padding:20px 18px 16px 18px;box-shadow:0 2px 8px #0001;margin-bottom:16px;display:flex;align-items:center;">
        <div style="font-size:2.2em;color:#eab308;margin-right:16px;"><i class="fas fa-dumbbell"></i></div>
        <div>
          <div style="font-size:1.1em;font-weight:600;color:#64748b;">Equipment Value</div>
          <div style="font-size:1.7em;font-weight:700;color:#222;">$<?php echo number_format($equipment, 2); ?></div>
        </div>
      </div>
      <div class="card-summary" style="flex:1 1 220px;min-width:220px;max-width:340px;background:#f0f9ff;border-radius:16px;padding:20px 18px 16px 18px;box-shadow:0 2px 8px #0001;margin-bottom:16px;display:flex;align-items:center;">
        <div style="font-size:2.2em;color:#0ea5e9;margin-right:16px;"><i class="fas fa-chart-line"></i></div>
        <div>
          <div style="font-size:1.1em;font-weight:600;color:#64748b;">Profit <span style="font-size:0.9em;color:#888;">(Net Profit)</span></div>
          <div style="font-size:1.7em;font-weight:700;color:#16a34a;">$<?php echo number_format($netProfit, 2); ?></div>
        </div>
      </div>
    </div>
    <hr>
  </div>
    <?php if ($msg !== '') { ?>
      <div class="alert <?php echo $msgType === 'success' ? 'alert-success' : 'alert-error'; ?>"><?php echo htmlspecialchars($msg); ?></div>
    <?php } ?>
    
    <div class="row-fluid">
      <div class="span12">
        <div class="widget-box">
          <div class="widget-title"><span class="icon"><i class="fas fa-book"></i></span><h5>Recent Journal Entries</h5></div>
          <div class="widget-content nopadding">
            <?php if ($_SESSION['designation'] == 'Cashier'): ?>
              <div class="alert alert-info text-center" style="margin: 24px;">
                <i class="fas fa-info-circle"></i> <strong>View Only:</strong> Cashier role cannot add, edit, or delete journal entries.
              </div>
            <?php endif; ?>
            <div style="overflow-x:auto;width:100%">
              <table class="table table-bordered table-striped" style="min-width:1100px;">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>ID</th>
                    <th>Memo</th>
                    <th>Account</th>
                    <th style="text-align:right;">Debit</th>
                    <th style="text-align:right;">Credit</th>
                    <th>Posted By</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  if ($entries_res && mysqli_num_rows($entries_res) > 0) {
                    $lastId = null;
                    while($j = mysqli_fetch_assoc($entries_res)) { 
                      $isNewEntry = ($j['id'] !== $lastId);
                      $border = $isNewEntry ? 'border-top:2px solid #ddd;' : '';
                    ?>
                      <tr style="<?php echo $border; ?>">
                        <td><?php echo $isNewEntry ? $j['entry_date'] : ''; ?></td>
                        <td><?php echo $isNewEntry ? '#'.$j['id'] : ''; ?></td>
                        <td><?php echo $isNewEntry ? '<strong>'.htmlspecialchars($j['memo']).'</strong>' : ''; ?></td>
                        <td>
                          <?php
                            // Show 'Membership Revenue' for membership payment transactions
                            if (
                              (stripos($j['memo'], 'membership') !== false || stripos($j['memo'], 'Payment: Monthly Membership') !== false)
                              && ($j['code'] === '4000' || $j['name'] === 'Membership Revenue')
                            ) {
                              echo '4000 - Membership Revenue';
                            } else {
                              echo $j['code'] . ' - ' . htmlspecialchars($j['name']);
                            }
                          ?>
                        </td>
                        <td style="text-align:right;"><?php echo $j['debit'] > 0 ? '$'.number_format($j['debit'], 2) : '-'; ?></td>
                        <td style="text-align:right;"><?php echo $j['credit'] > 0 ? '$'.number_format($j['credit'], 2) : '-'; ?></td>
                        <td><?php echo $isNewEntry ? htmlspecialchars($j['posted_by']) : ''; ?></td>
                      </tr>
                    <?php $lastId = $j['id']; } 
                  } else {
                    echo '<tr><td colspan="7" style="text-align:center;padding:24px;color:#888;">No journal entries found for this year.</td></tr>';
                  } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="../js/jquery.min.js"></script><script src="../js/bootstrap.min.js"></script><script src="../js/matrix.js"></script>
</body>
</html>

