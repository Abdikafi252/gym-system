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

// Fetch all closed fiscal periods
// Fetch all years that have data in the transaction log OR are in fiscal_periods
$periods = mysqli_query($con, "SELECT DISTINCT period_year FROM (
    SELECT YEAR(txn_date) as period_year FROM transactions_log
    UNION
    SELECT period_year FROM fiscal_periods
) as all_years ORDER BY period_year DESC");

$selectedYear = isset($_GET['year']) ? (int)$_GET['year'] : null;

// Branch filter logic
$branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : 0;
$branch_where = $branch_id > 0 ? " AND branch_id = " . $branch_id : '';

// Fetch all branches for dropdown
$branches = [];
$branch_res = mysqli_query($con, "SELECT id, branch_name FROM branches ORDER BY branch_name ASC");
while ($row = mysqli_fetch_assoc($branch_res)) {
  $branches[] = $row;
}
$yearData = null;

if ($selectedYear) {
    // Check if period exists in fiscal_periods
    $checkRes = mysqli_query($con, "SELECT * FROM fiscal_periods WHERE period_year = $selectedYear");
    $periodInfo = mysqli_fetch_assoc($checkRes);
    
    $from = $selectedYear . '-01-01';
    $to = $selectedYear . '-12-31';

    $txns = mysqli_query($con, "SELECT txn_date, source_table, source_id, description, account_code, debit, credit 
                  FROM transactions_log 
                  WHERE txn_date BETWEEN '$from' AND '$to' $branch_where
                  ORDER BY txn_date ASC, id ASC");
    
    $journals = mysqli_query($con, "SELECT je.id, je.entry_date, je.memo, je.is_closing, 
                                            jl.account_id, coa.code, coa.name, jl.debit, jl.credit
                                    FROM journal_entries je
                                    JOIN journal_lines jl ON jl.journal_entry_id = je.id
                                    JOIN chart_of_accounts coa ON coa.id = jl.account_id
                                    WHERE je.period_year = $selectedYear AND je.status='posted'
                                    ORDER BY je.entry_date ASC, je.id ASC, jl.id ASC");

    $yearData = [
        'period' => $periodInfo, // Might be null if not yet closed
        'opening_balance' => acc_trial_balance_rows($con, 1, null, $selectedYear - 1),
        'trial_balance' => acc_trial_balance_rows($con, 1, $to),
        'income_statement' => acc_income_statement($con, $from, $to),
        'balance_sheet' => acc_balance_sheet($con, $to),
        'transactions' => $txns,
        'journals' => $journals
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Accounting History - Gym System</title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../css/bootstrap.min.css" />
  <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
  <link rel="stylesheet" href="../css/matrix-style.css" />
  <link rel="stylesheet" href="../css/matrix-media.css" />
  <link href="../font-awesome/css/all.css" rel="stylesheet" />
  <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" href="../css/premium-print.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <style>
    .year-pill { cursor: pointer; padding: 10px 20px; background: #eee; border-radius: 20px; display: inline-block; margin: 5px; font-weight: bold; transition: 0.3s; text-decoration: none; color: #333; }
    .year-pill:hover { background: #28b779; color: white; }
    .year-pill.active { background: #28b779; color: white; border: 2px solid #1a8a5a; }
    
    @media print {
        .d-print-none { display: none !important; }
    }
  </style>
</head>
<body>
<?php include 'includes/header-content.php'; ?>
<?php include 'includes/topheader.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div id="content">
  <div id="content-header">
    <div id="breadcrumb">
      <a href="index.php" title="Go to Home" class="tip-bottom"><i class="fas fa-home"></i> Home</a> 
      <a href="accounting-cycle.php">Accounting</a> 
      <a href="#" class="current">History</a>
    </div>
    <h1>Diiwaanka Xisaabaadka (Yearly History)</h1>
  </div>

  <div class="container-fluid"><hr>
    <div class="row-fluid">
      <div class="span12">
        <div class="widget-box">
          <div class="widget-title"> <span class="icon"> <i class="fas fa-history"></i> </span>
            <h5>Fiscal Year Archives</h5>
          </div>
          <div class="widget-content">
            <div style="text-align: center; margin-bottom: 30px;">
              <p>Select a year to view all historical transactions and reports:</p>
              <?php

              // Get the min and max year from transactions_log or fiscal_periods
              $rangeRes = mysqli_query($con, "SELECT MIN(y) as min_y, MAX(y) as max_y FROM (
                  SELECT YEAR(txn_date) as y FROM transactions_log
                  UNION
                  SELECT period_year FROM fiscal_periods
              ) as all_y");
              $range = mysqli_fetch_assoc($rangeRes);
              $minY = (int)($range['min_y'] ?? date('Y'));
              $maxY = (int)($range['max_y'] ?? date('Y'));

              if ($minY > 0) {
                for ($y = $maxY; $y >= $minY; $y--) {
                  $active = ($selectedYear === $y) ? 'active' : '';
                  echo "<a href='accounting-history.php?year=$y' class='year-pill $active'>$y</a>";
                }
              } else {
                echo "<div class='alert alert-info'>No historical data found.</div>";
              }
              ?>
            </div>

            <?php if (!$selectedYear) { ?>
              <div class="row-fluid print-container" id="print-highlights">
                <div class="span12 premium-document">
                  <div class="premium-header">
                      <div class="premium-brand">
                          <h1>M*A GYM</h1>
                          <p>Busley, Bondheere, Mogadishu, Somalia</p>
                          <p>Tel: 252-610-000-000</p>
                      </div>
                      <div class="premium-meta">
                          <h2>ANNUAL ACCOUNTING HIGHLIGHTS</h2>
                          <p><strong>Generated On:</strong> <?php echo date("F j, Y"); ?></p>
                      </div>
                  </div>

                  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                    <h4 style="margin:0;">Yearly Financial Performance</h4>
                    <div class="d-print-none">
                      <button class="btn btn-mini btn-info" onclick="window.print();"><i class="fas fa-print"></i> Print</button>
                      <button class="btn btn-mini btn-success" onclick="downloadPDF('print-highlights', 'yearly-highlights');"><i class="fas fa-file-pdf"></i> PDF</button>
                    </div>
                  </div>
                  
                  <table class="premium-table">
                    <thead>
                      <tr>
                        <th>Fiscal Year</th>
                        <th>Status</th>
                        <th class="right">Revenue</th>
                        <th class="right">Net Profit</th>
                        <th class="right">Total Assets</th>
                        <th class="right">Total Equity</th>
                        <th class="right d-print-none">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php 
                      for ($y = $maxY; $y >= $minY; $y--) {
                        $f = $y . '-01-01'; $t = $y . '-12-31';
                        $is = acc_income_statement($con, $f, $t);
                        $bs = acc_balance_sheet($con, $t);
                        $pRes = mysqli_query($con, "SELECT status FROM fiscal_periods WHERE period_year = $y");
                        $pRow = mysqli_fetch_assoc($pRes);
                        $status = ($pRow && $pRow['status'] === 'closed') ? 'CLOSED' : 'ACTIVE';
                        $statusLabel = ($status === 'CLOSED') ? 'success' : 'warning';
                        
                        $totalEq = $bs['total_equity'];
                        if ($status !== 'CLOSED') $totalEq += $is['net_income'];
                      ?>
                        <tr>
                          <td><strong><?php echo $y; ?></strong></td>
                          <td><span class="label label-<?php echo $statusLabel; ?>"><?php echo $status; ?></span></td>
                          <td class="right">$<?php echo number_format($is['total_revenue'], 2); ?></td>
                          <td class="right <?php echo ($is['net_income'] < 0 ? 'text-error' : ''); ?>">$<?php echo number_format($is['net_income'], 2); ?></td>
                          <td class="right">$<?php echo number_format($bs['total_assets'], 2); ?></td>
                          <td class="right">$<?php echo number_format($totalEq, 2); ?></td>
                          <td class="right d-print-none"><a href="accounting-history.php?year=<?php echo $y; ?>" class="btn btn-mini btn-info">View Full Details</a></td>
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            <?php } ?>

            <?php if ($yearData) { ?>
              <div class="print-container" id="report-content">
                <div class="premium-document">
                    <div class="premium-header">
                        <div class="premium-brand">
                            <h1>M*A GYM</h1>
                            <p>Busley, Bondheere, Mogadishu</p>
                            <p>Fiscal Archive</p>
                        </div>
                        <div class="premium-meta">
                            <h2>FISCAL YEAR REPORT <?php echo $selectedYear; ?></h2>
                            <p><strong>Status:</strong> <?php echo ($yearData['period'] && $yearData['period']['status'] === 'closed') ? 'CLOSED' : 'ACTIVE / OPEN'; ?></p>
                            <p><strong>Generated:</strong> <?php echo date("d/m/Y"); ?></p>
                        </div>
                    </div>

                    <div class="d-print-none" style="margin-bottom:20px; text-align:right;">
                        <button class="btn btn-mini btn-info" onclick="window.print();"><i class="fas fa-print"></i> Print Report</button>
                        <button class="btn btn-mini btn-success" onclick="downloadPDF('report-content', 'history-report-<?php echo $selectedYear; ?>');"><i class="fas fa-file-pdf"></i> Download PDF</button>
                    </div>

                    <div class="widget-box" style="border:none; box-shadow:none;">
                      <div class="widget-title d-print-none">
                        <ul class="nav nav-tabs">
                          <li class="active"><a data-toggle="tab" href="#tab-summary">Summary & Statements</a></li>
                          <li><a data-toggle="tab" href="#tab-opening">Opening balance (Jan 1)</a></li>
                          <li><a data-toggle="tab" href="#tab-txns">All Transactions</a></li>
                          <li><a data-toggle="tab" href="#tab-journal">Journal Entries</a></li>
                          <li><a data-toggle="tab" href="#tab-tb">Post-Closing Trial Balance</a></li>
                        </ul>
                      </div>
                      <div class="widget-content tab-content" style="padding:0;">
                        <!-- Summary & Statements -->
                        <div id="tab-summary" class="tab-pane active">
                          <div class="row-fluid">
                            <div class="span6">
                              <h3 style="border-bottom: 2px solid #0f172a; padding-bottom: 8px;">Income Statement</h3>
                              <table class="premium-table">
                                <thead><tr><th>Category</th><th class="right">Total</th></tr></thead>
                                <tbody>
                                  <tr style="background:#f8fafc;"><td colspan="2"><strong>REVENUES</strong></td></tr>
                                  <?php foreach ($yearData['income_statement']['revenues'] as $r) { ?>
                                    <tr><td><?php echo $r['name']; ?></td><td class="right">$<?php echo number_format($r['amount'], 2); ?></td></tr>
                                  <?php } ?>
                                  <tr style="background:#f1f5f9;"><th>Total Revenue</th><th class="right">$<?php echo number_format($yearData['income_statement']['total_revenue'], 2); ?></th></tr>
                                  <tr style="background:#fff1f2;"><td colspan="2"><strong>EXPENSES</strong></td></tr>
                                  <?php foreach ($yearData['income_statement']['expenses'] as $e) { ?>
                                    <tr><td><?php echo $e['name']; ?></td><td class="right">$<?php echo number_format($e['amount'], 2); ?></td></tr>
                                  <?php } ?>
                                  <tr style="background:#f1f5f9;"><th>Total Expense</th><th class="right">$<?php echo number_format($yearData['income_statement']['total_expense'], 2); ?></th></tr>
                                  <tr class="highlight-row"><th>Net Profit/Loss</th><th class="right" style="font-size:1.2em;">$<?php echo number_format($yearData['income_statement']['net_income'], 2); ?></th></tr>
                                </tbody>
                              </table>

                              <h3 style="border-bottom: 2px solid #0f172a; padding-bottom: 8px; margin-top:30px;">Balance Sheet (as of Dec 31)</h3>
                              <table class="premium-table">
                                <thead><tr><th>Category</th><th class="right">Total</th></tr></thead>
                                <tbody>
                                  <tr style="background:#f0fdf4;"><td colspan="2"><strong>ASSETS</strong></td></tr>
                                  <?php foreach ($yearData['balance_sheet']['assets'] as $a) { ?>
                                    <tr><td><?php echo $a['name']; ?></td><td class="right">$<?php echo number_format($a['amount'], 2); ?></td></tr>
                                  <?php } ?>
                                  <tr style="background:#f1f5f9;"><th>Total Assets</th><th class="right">$<?php echo number_format($yearData['balance_sheet']['total_assets'], 2); ?></th></tr>
                                  <tr style="background:#fdf2f2;"><td colspan="2"><strong>LIABILITIES & EQUITY</strong></td></tr>
                                  <?php foreach ($yearData['balance_sheet']['liabilities'] as $l) { ?>
                                    <tr><td><?php echo $l['name']; ?></td><td class="right">$<?php echo number_format($l['amount'], 2); ?></td></tr>
                                  <?php } ?>
                                  <?php foreach ($yearData['balance_sheet']['equity'] as $e) { ?>
                                    <tr><td><?php echo $e['name']; ?></td><td class="right">$<?php echo number_format($e['amount'], 2); ?></td></tr>
                                  <?php } ?>
                                    <?php 
                                    $isClosed = ($yearData['period']['status'] === 'closed');
                                    if (!$isClosed) { ?>
                                      <tr><td style="font-style:italic;">Current Earnings (Unposted)</td><td class="right">$<?php echo number_format($yearData['income_statement']['net_income'], 2); ?></td></tr>
                                    <?php } ?>
                                    <tr class="highlight-row">
                                      <th>Total L+E</th>
                                      <th class="right">$<?php 
                                        $totalLE = $yearData['balance_sheet']['total_liabilities'] + $yearData['balance_sheet']['total_equity'];
                                        if (!$isClosed) $totalLE += $yearData['income_statement']['net_income'];
                                        echo number_format($totalLE, 2); 
                                      ?></th>
                                    </tr>
                                </tbody>
                              </table>
                            </div>
                            <div class="span6">
                                <h3 style="border-bottom: 2px solid #0f172a; padding-bottom: 8px;">Audit Information</h3>
                                <div style="background:#f8fafc; padding:20px; border-radius:12px; border:1px solid #e2e8f0;">
                                    <?php if ($yearData['period']) { ?>
                                        <p><strong>Fiscal Status:</strong> <span class="label label-success">CLOSED</span></p>
                                        <p><strong>Closed Date:</strong> <?php echo date("d/m/Y", strtotime($yearData['period']['closed_at'])); ?></p>
                                        <p><strong>Officer:</strong> <?php echo $yearData['period']['closed_by']; ?></p>
                                        <hr>
                                        <p style="font-size:12px; color:#64748b;">This archive represents the fixed financial state of the gym for the year <?php echo $selectedYear; ?>. All temporary accounts were successfully reset to zero.</p>
                                    <?php } else { ?>
                                        <p><strong>Fiscal Status:</strong> <span class="label label-warning" style="background:#f59e0b;">OPEN / ACTIVE</span></p>
                                        <p><strong>Reporting Period:</strong> 01/01/<?php echo $selectedYear; ?> - Present</p>
                                        <hr>
                                        <p style="font-size:12px; color:#64748b;">This is a real-time report. The fiscal period has not been closed yet. Final adjustments may be required.</p>
                                    <?php } ?>
                                </div>

                                <div class="premium-signature" style="margin-top:50px;">
                                    <img src="../img/report/stamp-sample.png" style="width:120px;" alt="Official Stamp">
                                    <p>Accountant Signature</p>
                                </div>
                            </div>
                          </div>
                        </div>

                        <!-- Opening Balance -->
                        <div id="tab-opening" class="tab-pane">
                          <h3 style="border-bottom: 2px solid #0f172a; padding-bottom: 8px;">Beginning Balances (Jan 1, <?php echo $selectedYear; ?>)</h3>
                          <table class="premium-table">
                            <thead><tr><th>Code</th><th>Account Name</th><th>Type</th><th class="right">Debit</th><th class="right">Credit</th></tr></thead>
                            <tbody>
                              <?php 
                              $od = 0; $oc = 0;
                              if (!empty($yearData['opening_balance'])) {
                                foreach ($yearData['opening_balance'] as $ob) { 
                                  if ((float)$ob['trial_debit'] == 0 && (float)$ob['trial_credit'] == 0) continue;
                                  if ($ob['account_type'] == 'Revenue' || $ob['account_type'] == 'Expense') continue;
                                  $od += (float)$ob['trial_debit'];
                                  $oc += (float)$ob['trial_credit'];
                              ?>
                                  <tr>
                                    <td><?php echo $ob['code']; ?></td>
                                    <td><?php echo $ob['name']; ?></td>
                                    <td><?php echo $ob['account_type']; ?></td>
                                    <td class="right">$<?php echo number_format($ob['trial_debit'], 2); ?></td>
                                    <td class="right">$<?php echo number_format($ob['trial_credit'], 2); ?></td>
                                  </tr>
                              <?php 
                                }
                              } else {
                                  echo '<tr><td colspan="5" style="text-align:center;">No prior historical data found.</td></tr>';
                              }
                              ?>
                            </tbody>
                            <tfoot>
                              <tr style="background:#f1f5f9;">
                                <th colspan="3" class="right">Opening Totals</th>
                                <th class="right">$<?php echo number_format($od, 2); ?></th>
                                <th class="right">$<?php echo number_format($oc, 2); ?></th>
                              </tr>
                            </tfoot>
                          </table>
                        </div>

                        <!-- All Transactions -->
                        <div id="tab-txns" class="tab-pane">
                          <h3 style="border-bottom: 2px solid #0f172a; padding-bottom: 8px;">Detailed Transactions Archive</h3>
                          <table class="premium-table">
                            <thead><tr><th>Date</th><th>Source</th><th>Description</th><th>Account</th><th class="right">Debit</th><th class="right">Credit</th></tr></thead>
                            <tbody>
                              <?php 
                              mysqli_data_seek($yearData['transactions'], 0);
                              if (mysqli_num_rows($yearData['transactions']) > 0) {
                                while($t = mysqli_fetch_assoc($yearData['transactions'])) { ?>
                                <tr>
                                  <td><?php echo $t['txn_date']; ?></td>
                                  <td><span class="label"><?php echo strtoupper($t['source_table']); ?></span></td>
                                  <td><?php echo $t['description']; ?></td>
                                  <td><?php echo $t['account_code']; ?></td>
                                  <td class="right">$<?php echo number_format($t['debit'], 2); ?></td>
                                  <td class="right">$<?php echo number_format($t['credit'], 2); ?></td>
                                </tr>
                              <?php } 
                              } else {
                                echo '<tr><td colspan="6" style="text-align:center;">No transactions found for this year.</td></tr>';
                              } ?>
                            </tbody>
                          </table>
                        </div>

                        <!-- Journal Entries -->
                        <div id="tab-journal" class="tab-pane">
                          <h3 style="border-bottom: 2px solid #0f172a; padding-bottom: 8px;">General Journal Archive</h3>
                          <table class="premium-table">
                            <thead><tr><th>Date</th><th>Entry ID</th><th>Memo</th><th>Account</th><th class="right">Debit</th><th class="right">Credit</th></tr></thead>
                            <tbody>
                              <?php 
                              mysqli_data_seek($yearData['journals'], 0);
                              if (mysqli_num_rows($yearData['journals']) > 0) {
                                $lastId = null;
                                while($j = mysqli_fetch_assoc($yearData['journals'])) { 
                                  $isNewEntry = ($j['id'] !== $lastId);
                                  $border = $isNewEntry ? 'border-top:2px solid #e2e8f0;' : '';
                                ?>
                                  <tr style="<?php echo $border; ?>">
                                    <td><?php echo $isNewEntry ? $j['entry_date'] : ''; ?></td>
                                    <td><?php echo $isNewEntry ? '#'.$j['id'] : ''; ?></td>
                                    <td><?php echo $isNewEntry ? '<strong>'.$j['memo'].'</strong>' : ''; ?></td>
                                    <td><?php echo $j['code'] . ' - ' . $j['name']; ?></td>
                                    <td class="right"><?php echo $j['debit'] > 0 ? '$'.number_format($j['debit'], 2) : '-'; ?></td>
                                    <td class="right"><?php echo $j['credit'] > 0 ? '$'.number_format($j['credit'], 2) : '-'; ?></td>
                                  </tr>
                                <?php $lastId = $j['id']; } 
                              } else {
                                echo '<tr><td colspan="6" style="text-align:center;">No journal entries found for this year.</td></tr>';
                              } ?>
                            </tbody>
                          </table>
                        </div>

                        <!-- Post-Closing Trial Balance -->
                        <div id="tab-tb" class="tab-pane">
                          <h3 style="border-bottom: 2px solid #0f172a; padding-bottom: 8px;">Post-Closing Trial Balance</h3>
                          <table class="premium-table">
                            <thead><tr><th>Code</th><th>Account Name</th><th>Type</th><th class="right">Debit</th><th class="right">Credit</th></tr></thead>
                            <tbody>
                              <?php 
                              $td = 0; $tc = 0;
                              $hasTd = false;
                              foreach ($yearData['trial_balance'] as $tb) { 
                                if ((float)$tb['trial_debit'] == 0 && (float)$tb['trial_credit'] == 0) continue;
                                if ($tb['account_type'] == 'Revenue' || $tb['account_type'] == 'Expense') continue;
                                $hasTd = true;
                                $td += (float)$tb['trial_debit'];
                                $tc += (float)$tb['trial_credit'];
                              ?>
                                <tr>
                                  <td><?php echo $tb['code']; ?></td>
                                  <td><?php echo $tb['name']; ?></td>
                                  <td><?php echo $tb['account_type']; ?></td>
                                  <td class="right">$<?php echo number_format($tb['trial_debit'], 2); ?></td>
                                  <td class="right">$<?php echo number_format($tb['trial_credit'], 2); ?></td>
                                </tr>
                              <?php } 
                              if (!$hasTd) {
                                echo '<tr><td colspan="5" style="text-align:center;">No trial balance data available.</td></tr>';
                              }
                              ?>
                            </tbody>
                            <tfoot>
                              <tr style="background:#f1f5f9;">
                                <th colspan="3" class="right">Final Totals</th>
                                <th class="right">$<?php echo number_format($td, 2); ?></th>
                                <th class="right">$<?php echo number_format($tc, 2); ?></th>
                              </tr>
                            </tfoot>
                          </table>
                        </div>
                      </div>
                    </div>
                </div>
              </div>
            <?php } elseif ($selectedYear) { ?>
              <div class="alert alert-danger">Sanadkan xog lagama hayo (No data found).</div>
            <?php } ?>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

</div>
<script src="../js/jquery.min.js"></script><script src="../js/bootstrap.min.js"></script><script src="../js/matrix.js"></script>
<script>
$(document).ready(function(){
    // Sync active tab with URL hash on load
    var hash = window.location.hash;
    if (hash) {
        $('.nav-tabs a[href="' + hash + '"]').tab('show');
    }

    // Update URL hash when tab is clicked
    $('.nav-tabs a').on('shown.bs.tab', function (e) {
        window.location.hash = e.target.hash;
    });
});

function downloadPDF(elementId, filename) {
    const element = document.getElementById(elementId);
    const opt = {
        margin:       [0.5, 0.3, 0.5, 0.3], // top, left, bottom, right
        filename:     filename + '.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { 
            scale: 2, 
            useCORS: true,
            letterRendering: true,
            scrollY: 0
        },
        jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' },
        pagebreak:    { mode: ['avoid-all', 'css', 'legacy'] }
    };

    document.body.classList.add('generating-pdf');
    
    html2pdf().set(opt).from(element).save().then(() => {
        document.body.classList.remove('generating-pdf');
    });
}
</script>
</body>
</html>
