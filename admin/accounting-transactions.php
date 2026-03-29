<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header('location:../index.php');
  exit;
}
include 'dbcon.php';
require_once dirname(__FILE__) . '/includes/accounting_engine.php';
acc_bootstrap_tables($con);

$page = 'accounting';

$year = date('Y');
$startDate = "$year-01-01";
$endDate = "$year-12-31";


// Use global branch filter from session

// Use global branch filter from session (match Member List logic)
$selected_branch = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : 0;
$branch_where = $selected_branch > 0 ? " AND branch_id = $selected_branch " : "";


// Remove per-page branch dropdown (now global in header)

$rows = acc_trial_balance_rows($con, 1, date('Y-m-d'));

$payment_rows = [];
$expense_rows = [];
$equipment_rows = [];
$txn_rows = [];
$owner_rows = [];
$liability_rows = [];
$liability_codes = ['2000', '2100']; // Add more liability account codes if needed

$payment_total = 0;
$expense_total = 0;
$equipment_total = 0;
$txn_debit_total = 0;
$txn_credit_total = 0;
$owner_total = 0;
$liability_total = 0;

$payments = mysqli_query($con, "SELECT id, invoice_no, fullname, paid_amount, paid_date, recorded_by FROM payment_history WHERE paid_date BETWEEN '$startDate' AND '$endDate' $branch_where ORDER BY id DESC LIMIT 200");
if ($payments) {
  while ($row = mysqli_fetch_assoc($payments)) {
    $payment_rows[] = $row;
    $payment_total += (float)($row['paid_amount'] ?? 0);
  }
}

$expenses = mysqli_query($con, "SELECT id, name, category, amount, date FROM expenses WHERE date BETWEEN '$startDate' AND '$endDate' $branch_where ORDER BY id DESC LIMIT 200");
if ($expenses) {
  while ($row = mysqli_fetch_assoc($expenses)) {
    $expense_rows[] = $row;
    $expense_total += (float)($row['amount'] ?? 0);
  }
}

$equipment = mysqli_query($con, "SELECT id, name, amount, quantity, date, vendor FROM equipment WHERE date BETWEEN '$startDate' AND '$endDate' $branch_where ORDER BY id DESC LIMIT 200");
if ($equipment) {
  while ($row = mysqli_fetch_assoc($equipment)) {
    $equipment_rows[] = $row;
    $equipment_total += (float)($row['amount'] ?? 0);
  }
}

$txnLog = mysqli_query($con, "SELECT txn_date, source_table, source_id, description, account_code, debit, credit FROM transactions_log WHERE txn_date BETWEEN '$startDate' AND '$endDate' $branch_where ORDER BY id DESC LIMIT 500");
if ($txnLog) {
  while ($row = mysqli_fetch_assoc($txnLog)) {
    $txn_rows[] = $row;
    $txn_debit_total += (float)($row['debit'] ?? 0);
    $txn_credit_total += (float)($row['credit'] ?? 0);
  }
}

$owner_res = mysqli_query($con, "SELECT amount FROM owner_capital_contributions WHERE contribution_date BETWEEN '$startDate' AND '$endDate' $branch_where");
if ($owner_res) {
  while ($row = mysqli_fetch_assoc($owner_res)) {
    $owner_rows[] = $row;
    $owner_total += (float)($row['amount'] ?? 0);
  }
}

// Liabilities from accounting (journal_lines)
$liability_total = 0;
$liability_rows = [];
// Read detailed liabilities from liabilities table (like liabilities.php)
$liabilities_res = mysqli_query($con, "SELECT id, name, amount, due_date, created_at FROM liabilities WHERE created_at BETWEEN '$startDate' AND '$endDate' $branch_where ORDER BY id DESC LIMIT 100");
if ($liabilities_res) {
  while ($row = mysqli_fetch_assoc($liabilities_res)) {
    $liability_rows[] = $row;
    $liability_total += (float)($row['amount'] ?? 0);
  }
}

// 2. Unearned Revenue (Advance Payments)
$unearned = mysqli_query($con, "SELECT id, invoice_no, fullname, plan, amount, paid_amount, discount_amount, paid_date FROM payment_history WHERE paid_date BETWEEN '$startDate' AND '$endDate' $branch_where ORDER BY id DESC");
if ($unearned) {
  while ($row = mysqli_fetch_assoc($unearned)) {
    $amount = (float)($row['paid_amount'] ?? 0);
    $discount = (float)($row['discount_amount'] ?? 0);
    $gross_amount = (!empty($row['amount']) && (float)$row['amount'] > 0) ? (float)$row['amount'] : $amount;
    if ($gross_amount < ($amount + $discount)) {
      $gross_amount = $amount + $discount;
    }

    $plan_months = (isset($row['plan']) && (int)$row['plan'] > 0) ? (int)$row['plan'] : 1;
    $monthly_rate = $gross_amount / $plan_months;
    $advance_amount = $gross_amount - $monthly_rate;

    if ($advance_amount > 0) {
      $liability_rows[] = [
        'id' => 'PH-' . $row['id'],
        'name' => 'Advance Payment (' . $row['fullname'] . ')',
        'amount' => $advance_amount,
        'due_date' => 'N/A',
        'created_at' => $row['paid_date']
      ];
      $liability_total += $advance_amount;
    }
  }
}


// --- Accurate Balance Sheet Calculation ---
// Get balances for all accounts (Asset, Liability, Equity) using unified engine
$bs = acc_balance_sheet($con, date('Y-m-d'));

$isData = acc_income_statement($con, date('Y-01-01'), date('Y-12-31'));
$net_income = $isData['net_income'];

$opening_bal_rows = acc_trial_balance_rows($con, 1, null, $year - 1);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title>Analyze Transactions - Accounting</title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../css/bootstrap.min.css" />
  <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
  <link rel="stylesheet" href="../css/matrix-style.css" />
  <link rel="stylesheet" href="../css/matrix-media.css" />
  <link href="../font-awesome/css/all.css" rel="stylesheet" />
  <style>
    .txn-shell {
      padding: 10px 4px 24px;
    }

    .txn-hero {
      position: relative;
      overflow: hidden;
      padding: 30px 24px;
      margin-bottom: 24px;
      border-radius: 28px;
      background:
        radial-gradient(circle at top right, rgba(255, 255, 255, .24) 0, rgba(255, 255, 255, 0) 34%),
        linear-gradient(135deg, #0f172a 0%, #0f766e 45%, #f59e0b 100%);
      color: #fff;
      box-shadow: 0 22px 44px rgba(15, 23, 42, 0.20);
    }

    .txn-hero:after {
      content: '';
      position: absolute;
      right: -40px;
      top: -30px;
      width: 180px;
      height: 180px;
      border-radius: 999px;
      background: rgba(255, 255, 255, .10);
      box-shadow: -120px 120px 0 rgba(255, 255, 255, .06);
    }

    .txn-hero h2 {
      margin: 0 0 10px;
      font-size: 31px;
      line-height: 1.05;
      letter-spacing: -0.03em;
    }

    .txn-hero p {
      margin: 0;
      max-width: 760px;
      color: rgba(255, 255, 255, .88);
      font-size: 14px;
      line-height: 1.7;
    }

    .txn-hero-actions {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
      margin-top: 18px;
      position: relative;
      z-index: 1;
    }

    .txn-hero-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 11px 18px;
      border-radius: 999px;
      text-decoration: none;
      font-weight: 800;
      font-size: 13px;
      transition: transform .18s ease, box-shadow .18s ease, background-color .18s ease;
    }

    .txn-hero-btn.primary {
      background: #ffffff;
      color: #0f172a;
      box-shadow: 0 14px 28px rgba(15, 23, 42, 0.18);
    }

    .txn-hero-btn.secondary {
      background: rgba(255, 255, 255, .12);
      color: #ffffff;
      border: 1px solid rgba(255, 255, 255, .18);
    }

    .txn-hero-btn:hover {
      color: inherit;
      text-decoration: none;
      transform: translateY(-1px);
    }

    .txn-summary-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 20px;
      margin-bottom: 32px;
    }

    .txn-summary-card {
      position: relative;
      overflow: hidden;
      background: #ffffff;
      border: 1px solid rgba(226, 232, 240, 0.8);
      border-radius: 24px;
      padding: 24px;
      box-shadow: 0 10px 25px rgba(15, 23, 42, 0.04);
      transition: all 0.3s ease;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      min-height: 140px;
    }

    .txn-summary-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 20px 50px rgba(15, 23, 42, 0.1);
      border-color: #cbd5e1;
    }

    .txn-summary-card:before {
      content: '';
      position: absolute;
      inset: 0 auto 0 0;
      width: 6px;
      background: #2563eb;
    }

    .txn-summary-card.warm:before { background: #f59e0b; }
    .txn-summary-card.cool:before { background: #0d9488; }
    .txn-summary-card.dark:before { background: #0f172a; }
    .txn-summary-card.liability-card:before { background: #e11d48; }
    .txn-summary-card.netincome-card:before { background: #16a34a; }
    .txn-summary-card.balance-card:before { background: #818cf8; }
    .txn-summary-card.owner-card:before { background: #db2777; }


    .txn-summary-label {
      color: #64748b;
      font-size: 13px;
      font-weight: 800;
      letter-spacing: .06em;
      text-transform: uppercase;
      margin-bottom: 8px;
    }

    .txn-summary-value {
      color: #0f172a;
      font-size: 32px;
      font-weight: 900;
      line-height: 1;
      letter-spacing: -0.02em;
    }

    .txn-summary-meta {
      margin-top: 12px;
      font-size: 14px;
      color: #475569;
      font-weight: 700;
    }

    .txn-panels {
      display: grid;
      grid-template-columns: 1fr;
      gap: 18px;
      margin-bottom: 18px;
    }

    .txn-panel {
      border-radius: 24px;
      overflow: hidden;
      background: #fff;
      border: 1px solid #dfe7f2;
      box-shadow: 0 16px 38px rgba(15, 23, 42, 0.08);
    }

    .txn-panel-head {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 14px;
      padding: 18px 18px 14px;
      background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
      border-bottom: 1px solid #e7edf5;
    }

    .txn-panel-title {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .txn-panel-icon {
      width: 46px;
      height: 46px;
      border-radius: 16px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
      color: #fff;
      box-shadow: 0 12px 24px rgba(15, 23, 42, 0.14);
    }

    .txn-panel-icon.payments {
      background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%);
    }

    .txn-panel-icon.expenses {
      background: linear-gradient(135deg, #f59e0b 0%, #fb7185 100%);
    }

    .txn-panel-icon.equipment {
      background: linear-gradient(135deg, #2563eb 0%, #38bdf8 100%);
    }

    .txn-panel-icon.log {
      background: linear-gradient(135deg, #0f172a 0%, #334155 100%);
    }

    .txn-panel-title h4 {
      margin: 0 0 4px;
      color: #0f172a;
      font-size: 18px;
      font-weight: 900;
    }

    .txn-panel-title p {
      margin: 0;
      color: #64748b;
      font-size: 12px;
      line-height: 1.6;
    }

    .txn-panel-badge {
      padding: 8px 12px;
      border-radius: 999px;
      background: #eef6ff;
      color: #0f172a;
      font-size: 12px;
      font-weight: 800;
      white-space: nowrap;
    }

    .txn-table-wrap {
      max-height: 420px;
      overflow: auto;
    }

    .txn-table-wrap table {
      margin-bottom: 0;
    }

    .txn-table-wrap thead th {
      position: sticky;
      top: 0;
      z-index: 1;
    }

    .txn-money {
      font-weight: 800;
      white-space: nowrap;
    }

    .txn-money.positive {
      color: #047857;
    }

    .txn-money.negative {
      color: #b45309;
    }

    .txn-money.neutral {
      color: #1d4ed8;
    }

    .txn-source-badge {
      display: inline-flex;
      align-items: center;
      padding: 6px 10px;
      border-radius: 999px;
      font-size: 11px;
      font-weight: 800;
      letter-spacing: .03em;
      text-transform: uppercase;
      background: #eff6ff;
      color: #1d4ed8;
    }

    .txn-source-badge.expense {
      background: #fff7ed;
      color: #c2410c;
    }

    .txn-source-badge.equipment {
      background: #ecfeff;
      color: #0f766e;
    }

    .txn-empty {
      padding: 30px 20px;
      text-align: center;
      color: #64748b;
      font-size: 13px;
    }

    .txn-log-panel .txn-table-wrap {
      max-height: 560px;
    }

    @media (max-width: 767px) {
      .txn-shell {
        padding-left: 0;
        padding-right: 0;
      }

      .txn-hero {
        padding: 22px 18px 20px;
        border-radius: 22px;
      }

      .txn-hero h2 {
        font-size: 26px;
      }

      .txn-panel-head {
        padding: 16px 14px 12px;
      }

      .txn-panel-title {
        align-items: flex-start;
      }

      .txn-table-wrap {
        max-height: none;
      }
    }
  </style>
</head>

<body>
  <?php include 'includes/header-content.php'; ?>
  <?php include 'includes/topheader.php'; ?>
  <?php include 'includes/sidebar.php'; ?>
  <div id="content">
    <div id="content-header">
      <div id="breadcrumb"><a href="index.php"><i class="fas fa-home"></i> Home</a> <a href="accounting-cycle.php">Accounting</a> <a href="#" class="current">Analyze Transactions</a></div>
      <h1>Analyze Transactions</h1>
    </div>
    <div class="container-fluid txn-shell">
      <hr>
      <div class="txn-hero">
        <h2>Source Data, Cleanly Mapped</h2>
        <div class="txn-hero-actions">
          <a href="accounting-sync.php" class="txn-hero-btn primary"><i class="fas fa-rotate"></i> Historical Sync</a>
          <a href="accounting-journal.php" class="txn-hero-btn secondary"><i class="fas fa-book"></i> Open Journal</a>
          <a href="accounting-trial-balance.php?mode=adjusted" class="txn-hero-btn secondary"><i class="fas fa-scale-balanced"></i> Trial Balance</a>
        </div>
      </div>

      <div class="txn-summary-grid">
        <div class="txn-summary-card">
          <div class="txn-summary-label">Payments Captured</div>
          <div class="txn-summary-value"><?php echo count($payment_rows); ?></div>
          <div class="txn-summary-meta">Visible total: $<?php echo number_format($payment_total, 2); ?></div>
        </div>
        <div class="txn-summary-card warm">
          <div class="txn-summary-label">Expenses Captured</div>
          <div class="txn-summary-value"><?php echo count($expense_rows); ?></div>
          <div class="txn-summary-meta">Visible total: $<?php echo number_format($expense_total, 2); ?></div>
        </div>
        <div class="txn-summary-card cool">
          <div class="txn-summary-label">Equipment Captured</div>
          <div class="txn-summary-value"><?php echo count($equipment_rows); ?></div>
          <div class="txn-summary-meta">Visible asset total: $<?php echo number_format($equipment_total, 2); ?></div>
        </div>
        <div class="txn-summary-card owner-card">
          <div class="txn-summary-label">Owner Capital</div>
          <div class="txn-summary-value"><?php echo count($owner_rows); ?></div>
          <div class="txn-summary-meta">Visible total: $<?php echo number_format($owner_total, 2); ?></div>
        </div>
        <div class="txn-summary-card netincome-card">
          <div class="txn-summary-label">Net Income</div>
          <div class="txn-summary-value">$<?php echo number_format($net_income, 2); ?></div>
          <div class="txn-summary-meta">(Revenue - Expenses)</div>
        </div>
        <div class="txn-summary-card balance-card">
          <div class="txn-summary-label">Balance Sheet</div>
          <div class="txn-summary-value">Assets: $<?php echo number_format($bs['total_assets'], 2); ?></div>
          <div class="txn-summary-meta">Liabilities + Equity: $<?php echo number_format($bs['total_liabilities'] + $bs['total_equity'] + $net_income, 2); ?></div>
        </div>
        <div class="txn-summary-card dark">
          <div class="txn-summary-label">Transactions Log</div>
          <div class="txn-summary-value"><?php echo count($txn_rows); ?></div>
          <div class="txn-summary-meta">Debit $<?php echo number_format($txn_debit_total, 2); ?> / Credit $<?php echo number_format($txn_credit_total, 2); ?></div>
        </div>
      </div>

      <div class="txn-panels">
        <div class="txn-panel">
          <div class="txn-panel-head">
            <div class="txn-panel-title">
              <span class="txn-panel-icon payments"><i class="fas fa-hand-holding-usd"></i></span>
              <div>
                <h4>Member Payments</h4>
                <p>Latest receipts captured from membership collections.</p>
              </div>
            </div>
            <span class="txn-panel-badge"><?php echo count($payment_rows); ?> rows</span>
          </div>
          <div class="txn-table-wrap">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Invoice</th>
                  <th>Member</th>
                  <th>Amount</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($payment_rows)) {
                  foreach ($payment_rows as $r) { ?>
                    <tr>
                      <td><?php echo (int)$r['id']; ?></td>
                      <td><?php echo htmlspecialchars($r['invoice_no']); ?></td>
                      <td><?php echo htmlspecialchars($r['fullname']); ?></td>
                      <td class="txn-money positive">$<?php echo number_format((float)$r['paid_amount'], 2); ?></td>
                      <td><?php echo htmlspecialchars($r['paid_date']); ?></td>
                    </tr>
                  <?php }
                } else { ?>
                  <tr>
                    <td colspan="5" class="txn-empty">No payment records found.</td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>

        <div class="txn-panel">
          <div class="txn-panel-head">
            <div class="txn-panel-title">
              <span class="txn-panel-icon expenses"><i class="fas fa-money-bill-wave"></i></span>
              <div>
                <h4>Expenses</h4>
                <p>Outgoing costs already linked to accounting categories.</p>
              </div>
            </div>
            <span class="txn-panel-badge"><?php echo count($expense_rows); ?> rows</span>
          </div>
          <div class="txn-table-wrap">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Category</th>
                  <th>Amount</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($expense_rows)) {
                  foreach ($expense_rows as $r) { ?>
                    <tr>
                      <td><?php echo (int)$r['id']; ?></td>
                      <td><?php echo htmlspecialchars($r['name']); ?></td>
                      <td><?php echo htmlspecialchars($r['category']); ?></td>
                      <td class="txn-money negative">$<?php echo number_format((float)$r['amount'], 2); ?></td>
                      <td><?php echo htmlspecialchars($r['date']); ?></td>
                    </tr>
                  <?php }
                } else { ?>
                  <tr>
                    <td colspan="5" class="txn-empty">No expense records found.</td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>

        <div class="txn-panel">
          <div class="txn-panel-head">
            <div class="txn-panel-title">
              <span class="txn-panel-icon equipment"><i class="fas fa-dumbbell"></i></span>
              <div>
                <h4>Equipment Purchases</h4>
                <p>Owner-funded asset purchases that feed the equipment account.</p>
              </div>
            </div>
            <span class="txn-panel-badge"><?php echo count($equipment_rows); ?> rows</span>
          </div>
          <div class="txn-table-wrap">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Vendor</th>
                  <th>Amount</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($equipment_rows)) {
                  foreach ($equipment_rows as $r) { ?>
                    <tr>
                      <td><?php echo (int)$r['id']; ?></td>
                      <td><?php echo htmlspecialchars($r['name']); ?></td>
                      <td><?php echo htmlspecialchars($r['vendor']); ?></td>
                      <td class="txn-money neutral">$<?php echo number_format((float)$r['amount'], 2); ?></td>
                      <td><?php echo htmlspecialchars($r['date']); ?></td>
                    </tr>
                  <?php }
                } else { ?>
                  <tr>
                    <td colspan="5" class="txn-empty">No equipment records found.</td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="txn-panel txn-log-panel">
        <div class="txn-panel-head">
          <div class="txn-panel-title">
            <span class="txn-panel-icon log"><i class="fas fa-stream"></i></span>
            <div>
              <h4>Diiwaanka Xisaabaadka</h4>
              <p>Raad-raaca isbeddelada si toos ah loo diiwaangeliyay oo muujinaya sida xogta asalka ah ugu wareegto xisaabaadka.</p>
            </div>
          </div>
          <span class="txn-panel-badge">Debit $<?php echo number_format($txn_debit_total, 2); ?> / Credit $<?php echo number_format($txn_credit_total, 2); ?></span>
        </div>
        <div class="txn-table-wrap">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Date</th>
                <th>Source</th>
                <th>Source ID</th>
                <th>Description</th>
                <th>Account</th>
                <th>Debit</th>
                <th>Credit</th>
              </tr>
            </thead>
            <tbody>
              <?php

              // Visual Opening Balance Row
              $ob_d = 0; $ob_c = 0;
              if (!empty($opening_bal_rows)) {
                  foreach($opening_bal_rows as $ob) {
                      if ($ob['account_type'] == 'Revenue' || $ob['account_type'] == 'Expense') continue;
                      $ob_d += (float)$ob['trial_debit'];
                      $ob_c += (float)$ob['trial_credit'];
                  }
              }
              if ($ob_d > 0 || $ob_c > 0) {
              ?>
                <tr style="background: #fdf2f2; border-bottom: 2px solid #fee2e2;">
                  <td><?php echo $startDate; ?></td>
                  <td><span class="txn-source-badge">OPENING</span></td>
                  <td>-</td>
                  <td><strong>Beginning Balance</strong></td>
                  <td><em>All Permanent Accounts</em></td>
                  <td class="txn-money positive">$<?php echo number_format($ob_d, 2); ?></td>
                  <td class="txn-money negative">$<?php echo number_format($ob_c, 2); ?></td>
                </tr>
              <?php } ?>
              
              <?php if (!empty($txn_rows)) {
                foreach ($txn_rows as $r) {
                  $isTestRow =
                    ($r['txn_date'] === '2026-03-17') &&
                    (trim($r['description']) === '03/17/2027') &&
                    ($r['account_code'] == '1000') &&
                    (number_format((float)$r['debit'], 2) === '1.00') &&
                    (number_format((float)$r['credit'], 2) === '1.00');
                  if ($isTestRow) continue;
                  $source_class = htmlspecialchars($r['source_table']) === 'expense' ? 'expense' : (htmlspecialchars($r['source_table']) === 'equipment' ? 'equipment' : '');
                  // Custom description logic
                  $desc = htmlspecialchars($r['description']);
                  // Expense: show as "Expense: Name (Category)"
                  if ($r['source_table'] === 'expense') {
                    $expQ = mysqli_query($con, "SELECT name, category FROM expenses WHERE id='" . intval($r['source_id']) . "'");
                    if ($expQ && ($exprow = mysqli_fetch_assoc($expQ))) {
                      $desc = 'Expense: ' . htmlspecialchars($exprow['name']) . ' (' . htmlspecialchars($exprow['category']) . ')';
                    }
                  }
                  // Payment: show as "Payment: Monthly Membership (Name)"
                  elseif ($r['source_table'] === 'payment_history') {
                    $memberName = '-';
                    $packageName = '';
                    $memberQ = mysqli_query($con, "SELECT fullname, amount, plan FROM payment_history WHERE id='" . intval($r['source_id']) . "'");
                    if ($memberQ && ($mrow = mysqli_fetch_assoc($memberQ))) {
                      $memberName = htmlspecialchars($mrow['fullname']);
                      $amount = isset($mrow['amount']) ? $mrow['amount'] : '';
                      $plan = isset($mrow['plan']) ? $mrow['plan'] : '';
                      // Fetch package name
                      $packageQ = mysqli_query($con, "SELECT packagename FROM packages WHERE amount='" . mysqli_real_escape_string($con, $amount) . "' AND duration='" . mysqli_real_escape_string($con, $plan) . "' LIMIT 1");
                      if ($packageQ && ($prow = mysqli_fetch_assoc($packageQ))) {
                        $packageName = htmlspecialchars($prow['packagename']);
                      }
                    }
                    if ($packageName !== '') {
                      $desc = 'Payment: ' . $packageName . ' (' . $memberName . ')';
                    } else {
                      $desc = 'Payment: Monthly Membership (' . $memberName . ')';
                    }
                  }
                  // Equipment: show as "Equipment: Name (Owner Capital)"
                  elseif ($r['source_table'] === 'equipment') {
                    $equipName = '-';
                    $equipQ = mysqli_query($con, "SELECT name FROM equipment WHERE id='" . intval($r['source_id']) . "'");
                    if ($equipQ && ($erow = mysqli_fetch_assoc($equipQ))) {
                      $equipName = htmlspecialchars($erow['name']);
                    }
                    $desc = 'Equipment: ' . $equipName . ' (Owner Capital)';
                  }
                  // Owner capital: show as "Owner Capital Contribution (Name)"
                  elseif ($r['source_table'] === 'owner_capital_contributions') {
                    $ownerName = 'Owner';
                    $ownerQ = mysqli_query($con, "SELECT funded_by FROM owner_capital_contributions WHERE id='" . intval($r['source_id']) . "'");
                    if ($ownerQ && ($orow = mysqli_fetch_assoc($ownerQ))) {
                      $ownerName = htmlspecialchars($orow['funded_by']);
                    }
                    $desc = 'Owner Capital Contribution (' . $ownerName . ')';
                  }
                  // Capital: fallback for old entries
                  elseif ($r['account_code'] == '3000') {
                    $desc = 'Capital: Previous Investment';
                  }
              ?>
                  <tr>
                    <td><?php echo htmlspecialchars($r['txn_date']); ?></td>
                    <td><span class="txn-source-badge <?php echo $source_class; ?>"><?php echo htmlspecialchars($r['source_table']); ?></span></td>
                    <td><?php echo htmlspecialchars($r['source_id']); ?></td>
                    <td><?php echo $desc; ?></td>
                    <td>
                      <?php
                      $accountNames = [
                        '1000' => 'Cash (Asset)',
                        '1100' => 'Accounts Receivable (Asset)',
                        '1200' => 'Prepaid Expenses (Asset)',
                        '1500' => 'Gym Equipment (Asset)',
                        '2000' => 'Accounts Payable (Liability)',
                        '2100' => 'Salaries Payable (Liability)',
                        '2200' => 'Unearned Revenue (Liability)',
                        '3000' => "Owner's Equity / Capital (Equity)",
                        '3100' => 'Retained Earnings (Equity)',
                        '4000' => 'Membership Revenue (Revenue)',
                        '4100' => 'Membership Discounts (Revenue)',
                        '4200' => 'Other Revenue (Revenue)',
                        '5000' => 'Salaries Expense (Expense)',
                        '5100' => 'Rent Expense (Expense)',
                        '5200' => 'Utilities Expense (Expense)',
                        '5300' => 'Equipment Expense (Expense)',
                        '5400' => 'Marketing Expense (Expense)',
                        '5500' => 'Administrative Expense (Expense)',
                        '5600' => 'Depreciation Expense (Expense)',
                      ];
                      // Always show Membership Revenue for payment_history account_code 4000
                      if ($r['source_table'] === 'payment_history' && $r['account_code'] === '4000') {
                        echo 'Membership Revenue (Revenue)';
                      } else {
                        echo isset($accountNames[$r['account_code']]) ? $accountNames[$r['account_code']] : htmlspecialchars($r['account_code']);
                      }
                      ?>
                    </td>
                    <td class="txn-money positive">$<?php echo number_format((float)$r['debit'], 2); ?></td>
                    <td class="txn-money negative">$<?php echo number_format((float)$r['credit'], 2); ?></td>
                  </tr>
                <?php }
              } else { ?>
                <tr>
                  <td colspan="7" class="txn-empty">No transactions log entries found yet.</td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Ledger by Account Section -->
      <!-- Recent Capital Contributions Section (with Edit/Delete) -->
    </div>
  </div>
  <script src="../js/jquery.min.js"></script>
  <script src="../js/bootstrap.min.js"></script>
  <script src="../js/matrix.js"></script>
</body>

</html>