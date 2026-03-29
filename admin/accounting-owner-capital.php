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
// Post/Redirect/Get pattern for all actions
$msg = '';
$msgType = 'success';
$postedBy = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
$isStaffManager = (isset($_SESSION['designation']) && $_SESSION['designation'] == 'Manager');
$sessBranch = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['delete_capital']) && isset($_POST['delete_id'])) {
    $deleteId = intval($_POST['delete_id']);
    // Delete accounting records for this capital contribution
    require_once __DIR__ . '/includes/accounting_engine.php';
    $delAcc = acc_delete_source_postings($con, 'owner_capital', $deleteId);
    $delQ = mysqli_query($con, "DELETE FROM owner_capital_contributions WHERE id='$deleteId'");
    if ($delQ && (!isset($delAcc['ok']) || $delAcc['ok'])) {
      header('Location: accounting-owner-capital.php?msg=Capital+contribution+successfully+deleted+and+accounting+records+removed.&type=success');
      exit;
    } else {
      $errMsg = isset($delAcc['message']) ? $delAcc['message'] : '';
      header('Location: accounting-owner-capital.php?msg=Capital+contribution+could+not+be+deleted.+$errMsg&type=error');
      exit;
    }
  }
  if (isset($_POST['edit_capital']) && isset($_POST['edit_id'])) {
    $editId = intval($_POST['edit_id']);
    $date = !empty($_POST['edit_contribution_date']) ? $_POST['edit_contribution_date'] : date('Y-m-d');
    $amount = isset($_POST['edit_amount']) ? (float)$_POST['edit_amount'] : 0;
    $reference = isset($_POST['edit_reference_no']) ? trim($_POST['edit_reference_no']) : '';
    $notes = isset($_POST['edit_notes']) ? trim($_POST['edit_notes']) : '';
    $fundedBy = isset($_POST['edit_funded_by']) ? trim($_POST['edit_funded_by']) : 'Owner';
    $branch_id = isset($_POST['edit_branch_id']) ? (int)$_POST['edit_branch_id'] : 0;
    
    // For staff managers, force their own branch
    if ($isStaffManager && $sessBranch > 0) {
      $branch_id = $sessBranch;
    }

    $updQ = mysqli_query($con, "UPDATE owner_capital_contributions SET contribution_date='$date', amount='$amount', reference_no='$reference', notes='$notes', funded_by='$fundedBy', branch_id='$branch_id' WHERE id='$editId'");
    if ($updQ) {
      header('Location: accounting-owner-capital.php?msg=Capital+contribution+successfully+updated.&type=success');
      exit;
    } else {
      header('Location: accounting-owner-capital.php?msg=Capital+contribution+could+not+be+updated.&type=error');
      exit;
    }
  }
  if (isset($_POST['save_capital'])) {
    $date = !empty($_POST['contribution_date']) ? $_POST['contribution_date'] : date('Y-m-d');
    $amount = isset($_POST['amount']) ? (float)$_POST['amount'] : 0;
    $reference = isset($_POST['reference_no']) ? trim($_POST['reference_no']) : '';
    $notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';
    $fundedBy = isset($_POST['funded_by']) ? trim($_POST['funded_by']) : 'Owner';
    $branch_id = isset($_POST['branch_id']) ? (int)$_POST['branch_id'] : 0;

    // For staff managers, force their own branch
    if ($isStaffManager && $sessBranch > 0) {
      $branch_id = $sessBranch;
    }

    $result = acc_record_owner_capital($con, $date, $amount, $reference, $notes, $fundedBy, $postedBy, $branch_id);
    if (!empty($result['ok'])) {
      header('Location: accounting-owner-capital.php?msg=Capital+contribution+successfully+recorded.&type=success');
      exit;
    } else {
      header('Location: accounting-owner-capital.php?msg=' . urlencode($result['message'] ?? 'Capital contribution could not be recorded.') . '&type=error');
      exit;
    }
  }
}
// Show message from redirect
if (isset($_GET['msg'])) {
  $msg = $_GET['msg'];
  $msgType = (isset($_GET['type']) && $_GET['type'] === 'error') ? 'error' : 'success';
}

$capitalRows = [];
$capitalTotal = 0;
$branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : 0;
$branch_where = $branch_id > 0 ? " WHERE oc.branch_id = $branch_id " : "";
$res = mysqli_query($con, "SELECT oc.*, b.branch_name 
                           FROM owner_capital_contributions oc 
                           LEFT JOIN branches b ON oc.branch_id = b.id 
                           $branch_where 
                           ORDER BY oc.contribution_date DESC, oc.id DESC 
                           LIMIT 100");
if ($res) {
  while ($row = mysqli_fetch_assoc($res)) {
    // Filter out test rows
    $isTestRow = (
      $row['contribution_date'] === '2026-03-17' &&
      (number_format((float)$row['amount'], 2) === '1.00') &&
      isset($row['notes']) && trim($row['notes']) === '03/17/2027'
    );
    if ($isTestRow) continue;
    $capitalRows[] = $row;
    $capitalTotal += (float)($row['amount'] ?? 0);
  }
}

$capitalTotal = 0;
$branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : 0;
$branch_where = $branch_id > 0 ? " WHERE branch_id = $branch_id " : "";
$res = mysqli_query($con, "SELECT COALESCE(SUM(amount),0) AS total FROM owner_capital_contributions $branch_where");
if ($res && ($row = mysqli_fetch_assoc($res))) {
    $capitalTotal = (float)$row['total'];
}

$tbRows = acc_trial_balance_rows($con, 1, date('Y-m-d'));
$cashBalance = 0;
$ownerEquity = 0;
foreach ($tbRows as $row) {
    if ($row['code'] === '1000') {
        $cashBalance = (float)$row['trial_debit'] - (float)$row['trial_credit'];
    }
    if ($row['code'] === '3000') {
        $ownerEquity = (float)$row['trial_credit'] - (float)$row['trial_debit'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Owner Capital - Accounting</title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../css/bootstrap.min.css" />
  <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
  <link rel="stylesheet" href="../css/matrix-style.css" />
  <link rel="stylesheet" href="../css/matrix-media.css" />
  <link rel="stylesheet" href="../css/system-polish.css" />
  <link href="../font-awesome/css/all.css" rel="stylesheet" />
  <style>
    .cap-shell { padding: 10px 4px 24px; }
    .cap-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:16px; margin-bottom:18px; }
    .cap-stat { background:#fff; border:1px solid #dde7f1; border-radius:18px; padding:18px; box-shadow:0 14px 30px rgba(15,23,42,.08); }
    .cap-stat .label { color:#64748b; font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:.06em; }
    .cap-stat .value { margin-top:8px; font-size:28px; font-weight:900; color:#0f172a; }
    .cap-hero { margin-bottom:18px; padding:24px; border-radius:24px; color:#fff; background:linear-gradient(135deg,#0f172a 0%,#1d4ed8 42%,#0f766e 100%); box-shadow:0 18px 40px rgba(15,23,42,.18); }
    .cap-hero h2 { margin:0 0 8px; font-size:30px; letter-spacing:-.03em; }
    .cap-hero p { margin:0; max-width:760px; color:rgba(255,255,255,.88); }
    .cap-form .controls input, .cap-form .controls textarea { width:100%; }
  </style>
</head>
<body>
<?php include 'includes/header-content.php'; ?>
<?php include 'includes/topheader.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<div id="content">
  <div id="content-header"><div id="breadcrumb"><a href="index.php"><i class="fas fa-home"></i> Home</a> <a href="accounting-cycle.php">Accounting</a> <a href="#" class="current">Owner Capital</a></div><h1>Owner Capital</h1></div>
  <div class="container-fluid cap-shell"><hr>
    <div class="cap-hero">
      <h2>Owner-Funded Cash</h2>
      <p>Use this page when the owner adds money into the gym as working capital. Each entry posts Dr Cash and Cr Owner Equity so your cash balance stays truthful.</p>
    </div>

    <?php if ($msg !== '') { ?>
      <div class="alert <?php echo $msgType === 'success' ? 'alert-success' : 'alert-error'; ?>"><?php echo htmlspecialchars($msg); ?></div>
    <?php } ?>

    <div class="cap-grid">
      <div class="cap-stat"><div class="label">Cash Balance</div><div class="value">$<?php echo number_format($cashBalance, 2); ?></div></div>
      <div class="cap-stat"><div class="label">Owner Equity</div><div class="value">$<?php echo number_format($ownerEquity, 2); ?></div></div>
      <div class="cap-stat"><div class="label">Capital Entries</div><div class="value"><?php echo count($capitalRows); ?></div></div>
      <div class="cap-stat"><div class="label">Total Capital</div><div class="value">$<?php echo number_format($capitalTotal, 2); ?></div></div>
    </div>

    <div class="row-fluid">
      <div class="span5">
        <div class="widget-box">
          <div class="widget-title"><span class="icon"><i class="fas fa-wallet"></i></span><h5>Record Capital Contribution</h5></div>
          <div class="widget-content">
            <form method="post" class="form-horizontal cap-form">
              <div class="control-group">
                <label class="control-label">Date</label>
                <div class="controls"><input type="date" name="contribution_date" value="<?php echo date('Y-m-d'); ?>" required></div>
              </div>
              <div class="control-group">
                <label class="control-label">Amount</label>
                <div class="controls"><input type="number" step="0.01" min="0.01" name="amount" placeholder="1000" required></div>
              </div>
              <div class="control-group">
                <label class="control-label">Reference</label>
                <div class="controls"><input type="text" name="reference_no" placeholder="e.g. CAP-001"></div>
              </div>
              <div class="control-group">
                <label class="control-label">Funded By</label>
                <div class="controls"><input type="text" name="funded_by" value="Owner" required></div>
              </div>
              <div class="control-group">
                <label class="control-label">Branch</label>
                <div class="controls">
                  <select name="branch_id" class="span12" required <?php echo $isStaffManager ? 'disabled' : ''; ?>>
                    <?php if (!$isStaffManager): ?>
                      <option value="" disabled selected>Select Branch</option>
                      <option value="0">Global / System</option>
                    <?php endif; ?>
                    <?php 
                      $br_res = mysqli_query($con, "SELECT * FROM branches");
                      while($b = mysqli_fetch_assoc($br_res)) {
                        $sel = ($isStaffManager && $b['id'] == $sessBranch) ? 'selected' : '';
                        echo "<option value='".$b['id']."' $sel>".htmlspecialchars($b['branch_name'])."</option>";
                      }
                    ?>
                  </select>
                  <?php if ($isStaffManager): ?>
                    <input type="hidden" name="branch_id" value="<?php echo $sessBranch; ?>">
                  <?php endif; ?>
                </div>
              </div>
              <div class="control-group">
                <label class="control-label">Notes</label>
                <div class="controls"><textarea name="notes" rows="4" placeholder="Working capital for gym operations"></textarea></div>
              </div>
              <div class="form-actions text-center">
                <button class="btn btn-primary" name="save_capital" value="1">Save Capital</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <div class="span7">
        <div class="widget-box">
          <div class="widget-title"><span class="icon"><i class="fas fa-list"></i></span><h5>Recent Capital Contributions</h5></div>
          <div class="widget-content nopadding">
            <table class="table table-bordered table-striped">
              <thead><tr><th>ID</th><th>Date</th><th>Branch</th><th>Reference</th><th>Funded By</th><th>Notes</th><th>Amount</th><th>Action</th></tr></thead>
              <tbody>
                <?php if (!empty($capitalRows)) { foreach ($capitalRows as $row) { ?>
                  <tr>
                    <td><?php echo (int)$row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['contribution_date']); ?></td>
                    <td>
                      <span class="badge badge-info"><?php echo htmlspecialchars($row['branch_name'] ?? 'Global / System'); ?></span>
                    </td>
                    <td><?php echo htmlspecialchars($row['reference_no']); ?></td>
                    <td><?php echo htmlspecialchars($row['funded_by']); ?></td>
                    <td><?php echo htmlspecialchars($row['notes']); ?></td>
                    <td>$<?php echo number_format((float)$row['amount'], 2); ?></td>
                    <td>
                      <form method="post" style="display:inline-block; margin:0;">
                        <input type="hidden" name="delete_id" value="<?php echo (int)$row['id']; ?>">
                        <button type="submit" name="delete_capital" class="btn btn-danger btn-mini" onclick="return confirm('Are you sure you want to delete this capital contribution?');">Delete</button>
                      </form>
                      <button class="btn btn-warning btn-mini" onclick="showEditForm(<?php echo (int)$row['id']; ?>)">Edit</button>
                    </td>
                  </tr>
                  <tr id="edit-row-<?php echo (int)$row['id']; ?>" style="display:none; background:#f9fafb;">
                    <td colspan="7">
                      <form method="post" class="form-inline" style="margin:0;">
                        <input type="hidden" name="edit_id" value="<?php echo (int)$row['id']; ?>">
                        <label>Date: <input type="date" name="edit_contribution_date" value="<?php echo htmlspecialchars($row['contribution_date']); ?>" required></label>
                        <label>Amount: <input type="number" step="0.01" min="0.01" name="edit_amount" value="<?php echo htmlspecialchars($row['amount']); ?>" required></label>
                        <label>Reference: <input type="text" name="edit_reference_no" value="<?php echo htmlspecialchars($row['reference_no']); ?>"></label>
                        <label>Funded By: <input type="text" name="edit_funded_by" value="<?php echo htmlspecialchars($row['funded_by']); ?>" required></label>
                        <label>Notes: <input type="text" name="edit_notes" value="<?php echo htmlspecialchars($row['notes']); ?>"></label>
                        <label>Branch: 
                          <select name="edit_branch_id" class="span2" required style="margin:0;" <?php echo $isStaffManager ? 'disabled' : ''; ?>>
                            <?php if (!$isStaffManager): ?>
                              <option value="0" <?php if($row['branch_id'] == 0) echo 'selected'; ?>>Global</option>
                            <?php endif; ?>
                            <?php 
                              $br_res_edit = mysqli_query($con, "SELECT * FROM branches");
                              while($be = mysqli_fetch_assoc($br_res_edit)) {
                                $sel = ($row['branch_id'] == $be['id']) ? 'selected' : '';
                                echo "<option value='".$be['id']."' $sel>".htmlspecialchars($be['branch_name'])."</option>";
                              }
                            ?>
                          </select>
                          <?php if ($isStaffManager): ?>
                            <input type="hidden" name="edit_branch_id" value="<?php echo $sessBranch; ?>">
                          <?php endif; ?>
                        </label>
                        <button type="submit" name="edit_capital" class="btn btn-success btn-mini">Save</button>
                        <button type="button" class="btn btn-default btn-mini" onclick="hideEditForm(<?php echo (int)$row['id']; ?>)">Cancel</button>
                      </form>
                    </td>
                  </tr>
                <?php } } else { ?>
                  <tr><td colspan="6" style="text-align:center; color:#64748b;">No capital contributions recorded yet.</td></tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="../js/jquery.min.js"></script>
<script>
function showEditForm(id) {
  document.getElementById('edit-row-' + id).style.display = '';
}
function hideEditForm(id) {
  document.getElementById('edit-row-' + id).style.display = 'none';
}
</script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/matrix.js"></script>
</body>
</html>
