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
$postedBy = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Delete
    if (isset($_POST['delete_draw']) && isset($_POST['delete_id'])) {
        $deleteId = intval($_POST['delete_id']);
        acc_delete_source_postings($con, 'owner_draw', $deleteId);
        mysqli_query($con, "DELETE FROM owner_draw WHERE id='$deleteId'");
        header('Location: accounting-owner-draw.php?msg=Deleted.&type=success');
        exit;
    }

    // Save new draw
    if (isset($_POST['save_draw'])) {
        $date     = !empty($_POST['draw_date'])    ? $_POST['draw_date']    : date('Y-m-d');
        $amount   = isset($_POST['amount'])        ? (float)$_POST['amount'] : 0;
        $purpose  = isset($_POST['purpose'])       ? trim($_POST['purpose']) : 'Owner Withdrawal';
        $ref      = isset($_POST['reference_no'])  ? trim($_POST['reference_no']) : '';
        $notes    = isset($_POST['notes'])         ? trim($_POST['notes'])   : '';
        $branch_id = isset($_POST['branch_id'])    ? (int)$_POST['branch_id'] : 0;

        if ($amount <= 0) {
            header('Location: accounting-owner-draw.php?msg=Amount+must+be+greater+than+0.&type=error');
            exit;
        }

        // Insert into owner_draw table
        $ins = mysqli_query($con, "INSERT INTO owner_draw (draw_date, amount, purpose, reference_no, notes, posted_by, branch_id)
            VALUES ('$date', '$amount', '" . mysqli_real_escape_string($con, $purpose) . "', '" . mysqli_real_escape_string($con, $ref) . "', '" . mysqli_real_escape_string($con, $notes) . "', '$postedBy', $branch_id)");

        if ($ins) {
            $drawId = mysqli_insert_id($con);
            $year   = (int)date('Y', strtotime($date));

            // Journal: Dr Owner Drawing / Cr Cash
            // Owner Drawing (3200) is a contra-equity account — increases with Debit
            $lines = [
              ['account_code' => '3200', 'debit' => $amount, 'credit' => 0, 'line_memo' => $purpose . ' (Owner Draw)'],
              ['account_code' => '1000', 'debit' => 0, 'credit' => $amount, 'line_memo' => $purpose . ' (Cash Out)'],
            ];
            acc_create_entry($con, $date, $purpose, 'owner_draw', $drawId, $lines, 0, $branch_id, 0, $postedBy);

            // No need to log to transactions_log again — acc_create_entry already handles this

            header('Location: accounting-owner-draw.php?msg=Recorded+successfully.&type=success');
        } else {
            header('Location: accounting-owner-draw.php?msg=An+error+occurred.&type=error');
        }
        exit;
    }
}

// Redirect message
if (isset($_GET['msg'])) {
    $msg     = $_GET['msg'];
    $msgType = (isset($_GET['type']) && $_GET['type'] === 'error') ? 'error' : 'success';
}

// Summary stats
$tbRows   = acc_trial_balance_rows($con, 1, date('Y-m-d'));
$cashBal  = 0;
$drawTotal = 0;
foreach ($tbRows as $r) {
    if ($r['code'] === '1000') $cashBal   = (float)$r['trial_debit'] - (float)$r['trial_credit'];
    if ($r['code'] === '3200') $drawTotal = (float)$r['trial_debit'] - (float)$r['trial_credit'];
}

// Draw history
$draws = [];
$branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : 0;
$branch_where = $branch_id > 0 ? " WHERE branch_id = $branch_id " : "";
$res = mysqli_query($con, "SELECT * FROM owner_draw $branch_where ORDER BY draw_date DESC, id DESC LIMIT 100");
if ($res) { while ($row = mysqli_fetch_assoc($res)) $draws[] = $row; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Owner Draw - Accounting</title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../css/bootstrap.min.css" />
  <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
  <link rel="stylesheet" href="../css/matrix-style.css" />
  <link rel="stylesheet" href="../css/matrix-media.css" />
  <link href="../font-awesome/css/all.css" rel="stylesheet" />
  <style>
    .draw-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:16px; margin-bottom:22px; }
    .draw-stat { background:#fff; border:1px solid #dde7f1; border-radius:16px; padding:18px; box-shadow:0 6px 20px rgba(0,0,0,.06); }
    .draw-stat .label { color:#64748b; font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.05em; }
    .draw-stat .value { margin-top:6px; font-size:26px; font-weight:900; color:#0f172a; }
    .draw-hero { margin-bottom:20px; padding:24px; border-radius:20px; color:#fff; background:linear-gradient(135deg,#1e1b4b 0%,#7c3aed 60%,#db2777 100%); box-shadow:0 14px 35px rgba(0,0,0,.15); }
    .draw-hero h2 { margin:0 0 6px; font-size:28px; }
    .draw-hero p  { margin:0; color:rgba(255,255,255,.85); }
  </style>
</head>
<body>
<?php include 'includes/header-content.php'; ?>
<?php include 'includes/topheader.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<div id="content">
  <div id="content-header">
    <div id="breadcrumb">
      <a href="index.php"><i class="fas fa-home"></i> Home</a>
      <a href="accounting-cycle.php">Accounting</a>
      <a href="#" class="current">Owner Draw</a>
    </div>
    <h1>Owner Draw / Withdrawal</h1>
  </div>

  <div class="container-fluid"><hr>

    <div class="draw-hero">
      <h2><i class="fas fa-hand-holding-usd"></i> Owner Draw</h2>
      <p>When you withdraw money from the business for personal use (salary, personal expenses, etc.), record it here. Accounting entry: Dr Owner Drawing → Cr Cash.</p>
    </div>

    <?php if ($msg !== '') { ?>
      <div class="alert <?php echo $msgType === 'success' ? 'alert-success' : 'alert-error'; ?>"><?php echo htmlspecialchars($msg); ?></div>
    <?php } ?>

    <div class="draw-grid">
      <div class="draw-stat">
        <div class="label"><i class="fas fa-money-bill-wave"></i> Cash Balance</div>
        <div class="value" style="color:<?php echo $cashBal < 0 ? '#dc2626' : '#16a34a'; ?>">$<?php echo number_format($cashBal, 2); ?></div>
      </div>
      <div class="draw-stat">
        <div class="label"><i class="fas fa-arrow-down"></i> Total Withdrawn (YTD)</div>
        <div class="value" style="color:#7c3aed;">$<?php echo number_format($drawTotal, 2); ?></div>
      </div>
      <div class="draw-stat">
        <div class="label"><i class="fas fa-list"></i> Draw Entries</div>
        <div class="value"><?php echo count($draws); ?></div>
      </div>
    </div>

    <div class="row-fluid">
      <div class="span5">
        <div class="widget-box">
          <div class="widget-title"><span class="icon"><i class="fas fa-pen"></i></span><h5>Record Owner Withdrawal</h5></div>
          <div class="widget-content">
            <form method="post" class="form-horizontal">
              <div class="control-group">
                <label class="control-label">📅 Date</label>
                <div class="controls"><input type="date" name="draw_date" value="<?php echo date('Y-m-d'); ?>" required></div>
              </div>
              <div class="control-group">
                <label class="control-label">💰 Amount</label>
                <div class="controls"><input type="number" step="0.01" min="0.01" name="amount" placeholder="e.g. 500" required></div>
              </div>
              <div class="control-group">
                <label class="control-label">🎯 Purpose</label>
                <div class="controls">
                  <select name="purpose">
                    <option value="Owner Salary">Owner Salary</option>
                    <option value="Personal Expense">Personal Expense</option>
                    <option value="Owner Withdrawal">Owner Withdrawal</option>
                    <option value="Other">Other</option>
                  </select>
                </div>
              </div>
              <div class="control-group">
                <label class="control-label">📋 Reference</label>
                <div class="controls"><input type="text" name="reference_no" placeholder="e.g. DRW-001"></div>
              </div>
              <div class="control-group">
                <label class="control-label">📝 Notes</label>
                <div class="controls"><textarea name="notes" rows="3" placeholder="Additional details..."></textarea></div>
              </div>
              <div class="control-group">
                <label class="control-label">🏢 Branch</label>
                <div class="controls">
                  <select name="branch_id" class="span12" required>
                    <option value="" disabled selected>Select Branch</option>
                    <?php 
                      $br_res = mysqli_query($con, "SELECT * FROM branches");
                      while($b = mysqli_fetch_assoc($br_res)) {
                        echo "<option value='".$b['id']."'>".htmlspecialchars($b['branch_name'])."</option>";
                      }
                    ?>
                  </select>
                </div>
              </div>
              <div class="form-actions text-center">
                <button class="btn btn-primary" name="save_draw" value="1">
                  <i class="fas fa-save"></i> Record
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="span7">
        <div class="widget-box">
          <div class="widget-title"><span class="icon"><i class="fas fa-history"></i></span><h5>Draw History</h5></div>
          <div class="widget-content nopadding">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Date</th>
                  <th>Branch</th>
                  <th>Purpose</th>
                  <th>Reference</th>
                  <th>Amount</th>
                  <th>By</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($draws)) foreach ($draws as $d) { ?>
                <tr>
                  <td><?php echo (int)$d['id']; ?></td>
                  <td><?php echo htmlspecialchars($d['draw_date']); ?></td>
                  <td>
                    <?php 
                      $bid = (int)$d['branch_id'];
                      $br_n = mysqli_query($con, "SELECT branch_name FROM branches WHERE id='$bid'");
                      $br_r = mysqli_fetch_assoc($br_n);
                      echo htmlspecialchars($br_r ? $br_r['branch_name'] : 'System');
                    ?>
                  </td>
                  <td><?php echo htmlspecialchars($d['purpose']); ?></td>
                  <td><?php echo htmlspecialchars($d['reference_no']); ?></td>
                  <td><strong>$<?php echo number_format((float)$d['amount'], 2); ?></strong></td>
                  <td><?php echo htmlspecialchars($d['posted_by']); ?></td>
                  <td>
                    <form method="post" style="display:inline; margin:0;">
                      <input type="hidden" name="delete_id" value="<?php echo (int)$d['id']; ?>">
                      <button type="submit" name="delete_draw" class="btn btn-danger btn-mini" onclick="return confirm('Are you sure?');">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  </td>
                </tr>
                <?php } else { ?>
                <tr><td colspan="7" style="text-align:center; color:#888;">No draw recorded yet.</td></tr>
                <?php } ?>
              </tbody>
              <?php if (!empty($draws)) { ?>
              <tfoot>
                <tr class="info">
                  <th colspan="4" style="text-align:right;">Total Draw</th>
                  <th>$<?php echo number_format(array_sum(array_column($draws, 'amount')), 2); ?></th>
                  <th colspan="2"></th>
                </tr>
              </tfoot>
              <?php } ?>
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
