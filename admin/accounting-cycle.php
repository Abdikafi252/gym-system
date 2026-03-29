<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
    exit;
}
include 'dbcon.php';
require_once __DIR__ . '/includes/accounting_engine.php';
acc_bootstrap_tables($con);


// Use fiscal year start/end dates
$year = date('Y');
$yearStart = "$year-01-01";
$yearEnd = "$year-12-31";

$selected_branch = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : 0;
$branch_filter = $selected_branch > 0 ? " AND branch_id = $selected_branch" : "";

$paymentsRes = mysqli_query($con, "SELECT COUNT(*) c, COALESCE(SUM(paid_amount),0) amt FROM payment_history WHERE paid_date BETWEEN '$yearStart' AND '$yearEnd' $branch_filter");
$payments = $paymentsRes ? mysqli_fetch_assoc($paymentsRes) : ['c' => 0, 'amt' => 0];

$expensesRes = mysqli_query($con, "SELECT COUNT(*) c, COALESCE(SUM(amount),0) amt FROM expenses WHERE date BETWEEN '$yearStart' AND '$yearEnd' $branch_filter");
$expenses = $expensesRes ? mysqli_fetch_assoc($expensesRes) : ['c' => 0, 'amt' => 0];

$tbRows = acc_trial_balance_rows($con, 1, $yearEnd);
$totalDr = 0;
$totalCr = 0;
foreach ($tbRows as $r) {
  $totalDr += (float)$r['trial_debit'];
  $totalCr += (float)$r['trial_credit'];
}

$page = 'accounting';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Accounting Cycle - Gym System</title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../css/bootstrap.min.css" />
  <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
  <link rel="stylesheet" href="../css/matrix-style.css" />
  <link rel="stylesheet" href="../css/matrix-media.css" />
  <link rel="stylesheet" href="../css/system-polish.css" />
  <link href="../font-awesome/css/all.css" rel="stylesheet" />
  <style>
    .cycle-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:20px; margin-top:24px; }
    
    .step-card { 
      position: relative;
      background: #ffffff; 
      border: 1px solid #eef2f6; 
      border-radius: 20px; 
      padding: 24px; 
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03); 
      display: flex;
      flex-direction: column;
      height: 100%;
    }
    
    .step-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08);
      border-color: #cbd5e1;
    }

    .step-card h4 { 
      margin: 0 0 12px 0; 
      font-size: 18px; 
      font-weight: 800; 
      color: #0f172a;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .step-card p { 
      margin: 0 0 20px 0; 
      color: #64748b; 
      font-size: 14px; 
      line-height: 1.6; 
      flex-grow: 1;
    }

    .step-link {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-weight: 700;
      font-size: 14px;
      color: #2563eb;
      text-decoration: none;
      transition: gap 0.2s ease;
    }

    .step-link:hover {
      gap: 10px;
      color: #1d4ed8;
      text-decoration: none;
    }

    .cycle-kpi-grid { 
      display: grid; 
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); 
      gap: 20px; 
      margin: 24px 0 32px 0; 
    }

    .cycle-kpi-card { 
      position: relative; 
      overflow: hidden;
      background: #ffffff; 
      border-radius: 24px; 
      padding: 24px; 
      border: 1px solid rgba(226, 232, 240, 0.8); 
      display: flex; 
      flex-direction: column; 
      justify-content: space-between;
      min-height: 160px;
      box-shadow: 0 10px 25px rgba(15, 23, 42, 0.04);
      transition: all 0.3s ease;
    }

    .cycle-kpi-card:hover {
      box-shadow: 0 20px 50px rgba(15, 23, 42, 0.1);
      border-color: #cbd5e1;
    }

    /* Gradient Overlays for premium feel */
    .cycle-kpi-card.payments { border-top: 5px solid #0d9488; }
    .cycle-kpi-card.expenses { border-top: 5px solid #e11d48; }
    .cycle-kpi-card.balance { border-top: 5px solid #2563eb; }

    .cycle-kpi-icon { 
      width: 48px;
      height: 48px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 22px; 
      border-radius: 14px;
      margin-bottom: 16px;
    }

    .payments .cycle-kpi-icon { background: #f0fdfa; color: #0d9488; }
    .expenses .cycle-kpi-icon { background: #fff1f2; color: #e11d48; }
    .balance .cycle-kpi-icon { background: #eff6ff; color: #2563eb; }

    .cycle-kpi-label { 
      font-size: 13px; 
      font-weight: 800; 
      letter-spacing: .05em; 
      color: #64748b;
      text-transform: uppercase;
      margin-bottom: 6px;
    }

    .cycle-kpi-value { 
      font-size: 32px; 
      font-weight: 900; 
      color: #0f172a;
      letter-spacing: -0.02em;
    }

    .cycle-kpi-meta { 
      margin-top: 12px;
      font-size: 14px; 
      font-weight: 700;
      color: #475569;
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .cycle-kpi-meta span { opacity: 0.6; font-weight: 500; }

    .cycle-kpi-card.balance .bad { color: #dc2626; }
    .cycle-kpi-card.balance .ok { color: #059669; }

    @media (max-width: 767px) {
      .cycle-kpi-grid { grid-template-columns: 1fr; }
      .cycle-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>
<?php include 'includes/header-content.php'; ?>
<?php include 'includes/topheader.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<div id="content">
  <div id="content-header">
    <div id="breadcrumb"><a href="index.php" class="tip-bottom"><i class="fas fa-home"></i> Home</a> <a href="#" class="current">Accounting Cycle</a></div>
    <h1>Accounting Cycle</h1>
  </div>
  <div class="container-fluid">
    <hr>
    <div class="row-fluid">
      <div class="span12">
        <div class="widget-box">
          <div class="widget-title"><span class="icon"><i class="fas fa-calculator"></i></span><h5>Accounting Status</h5></div>
          <div class="widget-content">
            <div class="cycle-kpi-grid">
              <div class="cycle-kpi-card payments">
                <div class="cycle-kpi-icon"><i class="fas fa-hand-holding-usd"></i></div>
                <div class="cycle-kpi-label">Month Payments</div>
                <div class="cycle-kpi-value"><?php echo number_format((int)$payments['c']); ?></div>
                <div class="cycle-kpi-meta"><span>Total</span> $<?php echo number_format((float)$payments['amt'], 2); ?></div>
              </div>
              <div class="cycle-kpi-card expenses">
                <div class="cycle-kpi-icon"><i class="fas fa-money-bill-wave"></i></div>
                <div class="cycle-kpi-label">Month Expenses</div>
                <div class="cycle-kpi-value"><?php echo number_format((int)$expenses['c']); ?></div>
                <div class="cycle-kpi-meta"><span>Total</span> $<?php echo number_format((float)$expenses['amt'], 2); ?></div>
              </div>
              <div class="cycle-kpi-card balance">
                <div class="cycle-kpi-icon"><i class="fas fa-scale-balanced"></i></div>
                <div class="cycle-kpi-label">Trial Balance Diff</div>
                <div class="cycle-kpi-value <?php echo abs($totalDr - $totalCr) < 0.01 ? 'ok' : 'bad'; ?>">
                  $<?php echo number_format(abs($totalDr - $totalCr), 2); ?>
                </div>
                <div class="cycle-kpi-meta"><span>Status</span> <?php echo abs($totalDr - $totalCr) < 0.01 ? 'Balanced' : 'Unbalanced'; ?></div>
              </div>
            </div>

            <div class="cycle-grid">
              <div class="step-card">
                <h4><i class="fas fa-search-dollar"></i> Analyze Transactions</h4>
                <p>Review and verify all financial data captured from member payments, expenses, and equipment.</p>
                <a href="accounting-transactions.php" class="step-link">Open Transactions <i class="fas fa-chevron-right"></i></a>
              </div>
              <div class="step-card">
                <h4><i class="fas fa-keyboard"></i> Journal Entries</h4>
                <p>Create and review detailed debit/credit journal entries for manual adjustments and tracking.</p>
                <a href="accounting-journal.php" class="step-link">Open Journal <i class="fas fa-chevron-right"></i></a>
              </div>
              <div class="step-card">
                <h4><i class="fas fa-balance-scale-left"></i> Trial Balance</h4>
                <p>Ensure that your books are balanced by checking the total debits vs total credits before closing.</p>
                <a href="accounting-trial-balance.php?mode=adjusted" class="step-link">Open Trial Balance <i class="fas fa-chevron-right"></i></a>
              </div>
              <div class="step-card">
                <h4><i class="fas fa-file-invoice-dollar"></i> Financial Statements</h4>
                <p>Generate professional Income Statement and Balance Sheet reports for the selected period.</p>
                <a href="accounting-statements.php" class="step-link">Open Statements <i class="fas fa-chevron-right"></i></a>
              </div>
              <div class="step-card">
                <h4><i class="fas fa-calendar-check"></i> Closing Entries</h4>
                <p>Perform the formal year-end or period-end closing process for revenue and expense accounts.</p>
                <a href="accounting-closing.php" class="step-link">Run Closing <i class="fas fa-chevron-right"></i></a>
              </div>
              <div class="step-card">
                <h4><i class="fas fa-history"></i> Accounting History</h4>
                <p>Access the library of all previously closed fiscal years, audit trails, and archived reports.</p>
                <a href="accounting-history.php" class="step-link" style="color:#0d9488;">Open History Archive <i class="fas fa-chevron-right"></i></a>
              </div>
            </div>
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
