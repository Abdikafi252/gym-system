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
$postedBy = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
$sessBranch = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : 0;
$results = [];

if (isset($_POST['sync_all'])) {
    $results['Payments'] = acc_sync_payment_history($con, date('Y-m-d'), $postedBy, $sessBranch);
    $results['Expenses'] = acc_sync_expenses($con, $postedBy, $sessBranch);
    $results['Equipment'] = acc_sync_equipment($con, $postedBy, $sessBranch);
    $results['Owner Capital'] = acc_sync_owner_capital($con, $postedBy, $sessBranch);
}

if (isset($_POST['sync_owner'])) {
    $results['Owner Capital'] = acc_sync_owner_capital($con, $postedBy, $sessBranch);
}

if (isset($_POST['sync_payments'])) {
    $results['Payments'] = acc_sync_payment_history($con, date('Y-m-d'), $postedBy, $sessBranch);
}

if (isset($_POST['sync_expenses'])) {
    $results['Expenses'] = acc_sync_expenses($con, $postedBy, $sessBranch);
}

if (isset($_POST['sync_equipment'])) {
    $results['Equipment'] = acc_sync_equipment($con, $postedBy, $sessBranch);
}

if (isset($_POST['repair_all'])) {
  $results['Payments'] = acc_rebuild_payment_history($con, $postedBy, $sessBranch);
  $results['Expenses'] = acc_rebuild_expenses($con, $postedBy, $sessBranch);
  $results['Equipment'] = acc_rebuild_equipment($con, $postedBy, $sessBranch);
  $results['Owner Capital'] = acc_rebuild_owner_capital($con, $postedBy, $sessBranch);
}

if (isset($_POST['repair_owner'])) {
    $results['Owner Capital'] = acc_rebuild_owner_capital($con, $postedBy, $sessBranch);
}

if (isset($_POST['repair_payments'])) {
  $results['Payments'] = acc_rebuild_payment_history($con, $postedBy, $sessBranch);
}

if (isset($_POST['repair_expenses'])) {
  $results['Expenses'] = acc_rebuild_expenses($con, $postedBy, $sessBranch);
}

if (isset($_POST['repair_equipment'])) {
  $results['Equipment'] = acc_rebuild_equipment($con, $postedBy, $sessBranch);
}

$counts = [
    'payments' => 0,
    'expenses' => 0,
    'equipment' => 0,
    'owner' => 0,
    'journal' => 0,
    'txlog' => 0,
];

$branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : 0;
$br_f = $branch_id > 0 ? " WHERE branch_id = $branch_id" : "";

$r = mysqli_query($con, "SELECT COUNT(*) c FROM payment_history" . $br_f);
if ($r) { $counts['payments'] = (int)mysqli_fetch_assoc($r)['c']; }
$r = mysqli_query($con, "SELECT COUNT(*) c FROM expenses" . $br_f);
if ($r) { $counts['expenses'] = (int)mysqli_fetch_assoc($r)['c']; }
$r = mysqli_query($con, "SELECT COUNT(*) c FROM equipment" . $br_f);
if ($r) { $counts['equipment'] = (int)mysqli_fetch_assoc($r)['c']; }
$r = mysqli_query($con, "SELECT COUNT(*) c FROM owner_capital_contributions" . $br_f);
if ($r) { $counts['owner'] = (int)mysqli_fetch_assoc($r)['c']; }
$r = mysqli_query($con, "SELECT COUNT(*) c FROM journal_entries WHERE status='posted'" . ($branch_id > 0 ? " AND branch_id = $branch_id" : ""));
if ($r) { $counts['journal'] = (int)mysqli_fetch_assoc($r)['c']; }
$r = mysqli_query($con, "SELECT COUNT(*) c FROM transactions_log" . ($branch_id > 0 ? " WHERE journal_entry_id IN (SELECT id FROM journal_entries WHERE branch_id = $branch_id)" : ""));
if ($r) { $counts['txlog'] = (int)mysqli_fetch_assoc($r)['c']; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Historical Sync - Accounting</title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../css/bootstrap.min.css" />
  <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
  <link rel="stylesheet" href="../css/matrix-style.css" />
  <link rel="stylesheet" href="../css/matrix-media.css" />
  <link rel="stylesheet" href="../css/system-polish.css" />
  <link href="../font-awesome/css/all.css" rel="stylesheet" />
  <style>
    .sync-shell { padding: 10px 4px 32px; }
    
    .sync-stats { 
      display: grid; 
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
      gap: 20px; 
      margin-bottom: 24px; 
    }

    .sync-stat { 
      position: relative;
      background: #ffffff; 
      border: 1px solid rgba(226, 232, 240, 0.8); 
      border-radius: 20px; 
      padding: 20px; 
      box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
      transition: all 0.3s ease;
      display: flex;
      flex-direction: column;
      justify-content: center;
      min-height: 110px;
    }

    .sync-stat:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
      border-color: #cbd5e1;
    }

    .sync-stat:before {
      content: '';
      position: absolute;
      inset: 0 auto 0 0;
      width: 4px;
      background: #94a3b8;
      border-radius: 20px 0 0 20px;
    }

    .sync-stat.primary:before { background: #2563eb; }
    .sync-stat.success:before { background: #059669; }
    .sync-stat.warning:before { background: #e11d48; }

    .sync-stat .label { 
      display: block; 
      color: #64748b; 
      font-size: 12px; 
      font-weight: 800;
      letter-spacing: .05em;
      text-transform: uppercase;
      margin-bottom: 8px; 
    }

    .sync-stat .value { 
      font-size: 28px; 
      font-weight: 900; 
      color: #0f172a; 
      letter-spacing: -0.01em;
    }

    .sync-actions .btn { 
      margin: 0 8px 10px 0; 
      border-radius: 10px;
      font-weight: 700;
      padding: 8px 16px;
    }

    .result-card { 
      background: #ffffff; 
      border: 1px solid #e2e8f0; 
      border-radius: 20px; 
      padding: 20px; 
      margin-top: 20px; 
      box-shadow: 0 4px 12px rgba(0,0,0,0.02);
    }

    .result-meta { 
      display: flex; 
      gap: 12px; 
      flex-wrap: wrap; 
      margin-top: 14px; 
    }

    .meta-chip { 
      background: #f1f5f9; 
      color: #475569;
      border: 1px solid #e2e8f0; 
      border-radius: 8px; 
      padding: 6px 12px; 
      font-size: 12px; 
      font-weight: 700;
    }

    .error-list { margin: 16px 0 0; padding-left: 20px; color: #dc2626; }
  </style>
</head>
<body>
<?php include 'includes/header-content.php'; ?>
<?php include 'includes/topheader.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<div id="content">
  <div id="content-header">
    <div id="breadcrumb"><a href="index.php"><i class="fas fa-home"></i> Home</a> <a href="accounting-cycle.php">Accounting</a> <a href="#" class="current">Historical Sync</a></div>
    <h1>Historical Sync</h1>
  </div>
  <div class="container-fluid sync-shell">
    <hr>
    <div class="row-fluid">
      <div class="span12">
        <div class="sync-stats">
          <div class="sync-stat primary">
            <span class="label">Historical Payments</span>
            <div class="value"><?php echo number_format($counts['payments']); ?></div>
          </div>
          <div class="sync-stat primary">
            <span class="label">Historical Expenses</span>
            <div class="value"><?php echo number_format($counts['expenses']); ?></div>
          </div>
          <div class="sync-stat primary">
            <span class="label">Equipment Purchases</span>
            <div class="value"><?php echo number_format($counts['equipment']); ?></div>
          </div>
          <div class="sync-stat primary">
            <span class="label">Owner Capital</span>
            <div class="value"><?php echo number_format($counts['owner']); ?></div>
          </div>
          <div class="sync-stat success">
            <span class="label">Posted JEs</span>
            <div class="value"><?php echo number_format($counts['journal']); ?></div>
          </div>
          <div class="sync-stat success">
            <span class="label">TX Log Rows</span>
            <div class="value"><?php echo number_format($counts['txlog']); ?></div>
          </div>
        </div>
        <div class="widget-box">
      <div class="widget-title"><span class="icon"><i class="fas fa-rotate"></i></span><h5>Backfill Existing Source Data</h5></div>
      <div class="widget-content" style="padding:18px;">
        <p>This page reads existing Payments, Expenses, and Equipment tables and posts missing accounting entries only once. Equipment is treated as owner-funded capital unless you later add another funding workflow.</p>
        <form method="post" class="sync-actions">
          <button class="btn btn-primary" name="sync_all" value="1">Sync All</button>
          <button class="btn btn-info" name="sync_payments" value="1">Sync Payments</button>
          <button class="btn btn-warning" name="sync_expenses" value="1">Sync Expenses</button>
          <button class="btn btn-success" name="sync_equipment" value="1">Sync Equipment</button>
          <button class="btn btn-inverse" name="sync_owner" value="1">Sync Owner Capital</button>
          <button class="btn btn-danger" name="repair_all" value="1" onclick="return confirm('Repair and rebuild all source entries?');">Repair All</button>
          <button class="btn" name="repair_payments" value="1">Repair Payments</button>
          <button class="btn" name="repair_expenses" value="1">Repair Expenses</button>
          <button class="btn" name="repair_equipment" value="1">Repair Equipment</button>
          <button class="btn" name="repair_owner" value="1">Repair Owner Capital</button>
        </form>
      </div>
    </div>

    <?php foreach ($results as $label => $result) { ?>
      <div class="result-card">
        <h4 style="margin:0;"><?php echo htmlspecialchars($label); ?></h4>
        <div class="result-meta">
          <span class="meta-chip">Created: <?php echo (int)$result['created']; ?></span>
          <span class="meta-chip">Skipped: <?php echo (int)$result['skipped']; ?></span>
          <span class="meta-chip">Failed: <?php echo (int)$result['failed']; ?></span>
        </div>
        <?php if (!empty($result['messages'])) { ?>
          <ul class="error-list">
            <?php foreach ($result['messages'] as $msg) { ?>
              <li><?php echo htmlspecialchars($msg); ?></li>
            <?php } ?>
          </ul>
        <?php } ?>
      </div>
    <?php } ?>
  </div>
</div>
<script src="../js/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/matrix.js"></script>
</body>
</html>
