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

$selectedCode = isset($_GET['account']) ? trim($_GET['account']) : '';
$selectedYear = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

// Branch filter logic
$branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : 0;
$branch_where = $branch_id > 0 ? " AND je.branch_id = " . $branch_id : '';

// Fetch all branches for dropdown
$branches = [];
$branch_res = mysqli_query($con, "SELECT id, branch_name FROM branches ORDER BY branch_name ASC");
while ($row = mysqli_fetch_assoc($branch_res)) {
  $branches[] = $row;
}

$accounts = mysqli_query($con, "SELECT code, name, account_type FROM chart_of_accounts ORDER BY code");

$availableYears = [];
$yRes = mysqli_query($con, "SELECT DISTINCT period_year FROM journal_entries ORDER BY period_year DESC");
while($yRow = mysqli_fetch_assoc($yRes)) { $availableYears[] = (int)$yRow['period_year']; }
if (!in_array((int)date('Y'), $availableYears)) { $availableYears[] = (int)date('Y'); rsort($availableYears); }

$ledgerRows = [];
$openingBalance = 0;
if ($selectedCode !== '') {
    $safeCode = mysqli_real_escape_string($con, $selectedCode);
    $accInfo = mysqli_fetch_assoc(mysqli_query($con, "SELECT account_type FROM chart_of_accounts WHERE code='$safeCode'"));
    $isPermanent = in_array($accInfo['account_type'] ?? '', ['Asset', 'Liability', 'Equity']);

    // If permanent, calculate opening balance from prior years
    if ($isPermanent) {
        $obQ = mysqli_query($con, "SELECT SUM(jl.debit - jl.credit) as bal 
                                  FROM journal_lines jl 
                                  JOIN journal_entries je ON je.id = jl.journal_entry_id 
                                  JOIN chart_of_accounts coa ON coa.id = jl.account_id 
                                  WHERE coa.code='$safeCode' AND je.status='posted' 
                                    AND je.period_year < $selectedYear $branch_where");
        if ($obQ) {
            $obRow = mysqli_fetch_assoc($obQ);
            $openingBalance = (float)($obRow['bal'] ?? 0);
        }
    }

    // Fetch transactions for selected year
    $q = mysqli_query($con, "SELECT je.entry_date, je.memo, jl.debit, jl.credit, coa.code, coa.name, je.is_closing
                            FROM journal_lines jl
                            JOIN journal_entries je ON je.id = jl.journal_entry_id
                            JOIN chart_of_accounts coa ON coa.id = jl.account_id
                            WHERE coa.code='$safeCode' AND je.status='posted' 
                              AND je.period_year = $selectedYear $branch_where
                            ORDER BY je.entry_date ASC, jl.id ASC");
    if ($q) {
        while ($row = mysqli_fetch_assoc($q)) {
            $ledgerRows[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Ledger - Accounting</title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../css/bootstrap.min.css" />
  <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
  <link rel="stylesheet" href="../css/matrix-style.css" />
  <link rel="stylesheet" href="../css/matrix-media.css" />
  <link href="../font-awesome/css/all.css" rel="stylesheet" />
</head>
<body>
<?php

?>
<?php include 'includes/topheader.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<div id="content">
  <div id="content-header"><div id="breadcrumb"><a href="index.php"><i class="fas fa-home"></i> Home</a> <a href="accounting-cycle.php">Accounting</a> <a href="#" class="current">Ledger</a></div><h1>General Ledger</h1></div>
  <div class="container-fluid"><hr>
    <div class="row-fluid"><div class="span12">
      <div class="widget-box"><div class="widget-title"><span class="icon"><i class="fas fa-book-open"></i></span><h5>Ledger by Account</h5></div>
        <div class="widget-content">
          <form method="get" class="form-inline" style="margin-bottom:12px;">
            <label>Year:</label>
            <select name="year" class="span2">
              <?php foreach ($availableYears as $y) { ?>
                <option value="<?php echo $y; ?>" <?php echo $selectedYear === $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
              <?php } ?>
            </select>
            <label style="margin-left:10px;">Account:</label>
            <select name="account" class="span4">
              <option value="">Select account</option>
              <?php mysqli_data_seek($accounts, 0); if ($accounts) { while ($a = mysqli_fetch_assoc($accounts)) { ?>
                <option value="<?php echo htmlspecialchars($a['code']); ?>" <?php echo $selectedCode === $a['code'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($a['code'] . ' - ' . $a['name']); ?></option>
              <?php } } ?>
            </select>
            <button class="btn btn-primary">Open Ledger</button>
          </form>

          <?php if ($selectedCode !== '') { ?>
          <table class="table table-bordered table-striped">
            <thead><tr><th>Date</th><th>Memo</th><th>Debit</th><th>Credit</th><th>Running Balance</th></tr></thead>
            <tbody>
            <?php
            $running = $openingBalance;
            $isCreditNormal = in_array($accInfo['account_type'] ?? '', ['Liability', 'Equity', 'Revenue']);
            
            if ($openingBalance != 0) {
              echo '<tr style="background:#f4f4f4;">';
              echo '<td colspan="2"><strong>Opening Balance (from prior periods)</strong></td>';
              echo '<td>-</td><td>-</td>';
              echo '<td><strong>$' . number_format($isCreditNormal ? -$running : $running, 2) . '</strong></td>';
              echo '</tr>';
            }

            foreach ($ledgerRows as $row) {
              $debit = (float)$row['debit'];
              $credit = (float)$row['credit'];
              $running += ($debit - $credit);

              $displayBal = $isCreditNormal ? -$running : $running;
              $rowClass = !empty($row['is_closing']) ? 'style="color:#999; font-style:italic;"' : '';

              echo "<tr $rowClass>";
              echo '<td>' . htmlspecialchars($row['entry_date']) . '</td>';
              echo '<td>' . htmlspecialchars($row['memo']) . '</td>';
              echo '<td>$' . number_format($debit, 2) . '</td>';
              echo '<td>$' . number_format($credit, 2) . '</td>';
              echo '<td>$' . number_format($displayBal, 2) . '</td>';
              echo '</tr>';
            }
            if (empty($ledgerRows) && $openingBalance == 0) {
               echo '<tr><td colspan="5" style="text-align:center;">No transactions found for this period.</td></tr>';
            }
            ?>
            </tbody>
          </table>
          <?php } ?>
        </div>
      </div>
    </div></div>
  </div>
</div>
<script src="../js/jquery.min.js"></script><script src="../js/bootstrap.min.js"></script><script src="../js/matrix.js"></script>
</body>
</html>
