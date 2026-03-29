<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
    exit;
}
include 'dbcon.php';
$page = 'liabilities';

$liability_rows = [];
$liability_total = 0;
$branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : 0;
$branch_where = $branch_id > 0 ? " WHERE branch_id = $branch_id " : "";

// 1. Manual Liabilities
$liabilities = mysqli_query($con, "SELECT id, name, amount, due_date, created_at FROM liabilities $branch_where ORDER BY id DESC LIMIT 100");
if ($liabilities) {
    while ($row = mysqli_fetch_assoc($liabilities)) {
        $liability_rows[] = $row;
        $liability_total += (float)($row['amount'] ?? 0);
    }
}

// 2. Unearned Revenue (Advance Payments)
$unearned = mysqli_query($con, "SELECT id, invoice_no, fullname, plan, amount, paid_amount, discount_amount, paid_date FROM payment_history $branch_where ORDER BY id DESC");
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_liability'])) {
    $name = mysqli_real_escape_string($con, $_POST['name'] ?? '');
    $amount = (float)($_POST['amount'] ?? 0);
    $due_date = mysqli_real_escape_string($con, $_POST['due_date'] ?? null);
    if ($name !== '' && $amount > 0) {
        $branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : 0;
        $q = "INSERT INTO liabilities (name, amount, due_date, branch_id) VALUES ('$name', '$amount', " . ($due_date ? "'$due_date'" : "NULL") . ", $branch_id)";
        mysqli_query($con, $q);
        header('Location: liabilities.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Liabilities - Finance</title>
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
  <div id="content-header"><div id="breadcrumb"><a href="index.php"><i class="fas fa-home"></i> Home</a> <a href="#" class="current">Liabilities</a></div><h1>Liabilities</h1></div>
  <div class="container-fluid">
    <div class="row-fluid">
      <div class="span12">
        <div class="widget-box">
          <div class="widget-title"><span class="icon"><i class="fas fa-balance-scale"></i></span><h5>Liabilities List</h5></div>
          <div class="widget-content" style="padding:18px 18px 0 18px;">
            <?php if ($_SESSION['designation'] == 'Cashier'): ?>
            <div class="alert alert-info text-center" style="margin: 24px;">
              <i class="fas fa-info-circle"></i> <strong>View Only:</strong> Cashier role cannot add, edit, or delete liabilities.
            </div>
            <?php else: ?>
            <form method="post" class="form-inline" style="margin-bottom:18px;">
              <input type="hidden" name="add_liability" value="1">
              <input type="text" name="name" placeholder="Liability Name" required style="margin-right:8px;">
              <input type="number" step="0.01" min="0.01" name="amount" placeholder="Amount" required style="margin-right:8px; width:120px;">
              <input type="date" name="due_date" style="margin-right:8px;">
              <button type="submit" class="btn btn-primary">Add Liability</button>
            </form>
            <?php endif; ?>
          </div>
          <div class="widget-content nopadding">
            <table class="table table-bordered table-striped">
              <thead><tr><th>ID</th><th>Name</th><th>Amount</th><th>Due Date</th><th>Created At</th></tr></thead>
              <tbody>
                <?php if (!empty($liability_rows)) { foreach ($liability_rows as $row) { ?>
                  <tr>
                    <td><?php echo htmlspecialchars((string)$row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td>$<?php echo number_format((float)$row['amount'], 2); ?></td>
                    <td><?php echo htmlspecialchars($row['due_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                  </tr>
                <?php } } else { ?>
                  <tr><td colspan="5" style="text-align:center; color:#64748b;">No liabilities found.</td></tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
