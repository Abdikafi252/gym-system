<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
    exit;
}

include "dbcon.php";

$from = isset($_GET['from']) ? $_GET['from'] : date('Y-m-01');
$to = isset($_GET['to']) ? $_GET['to'] : date('Y-m-d');

$income_q = mysqli_query($con, "SELECT COALESCE(SUM(paid_amount),0) total FROM payment_history WHERE paid_date BETWEEN '$from' AND '$to'");
$income = (float)mysqli_fetch_assoc($income_q)['total'];

$expense_q = mysqli_query($con, "SELECT COALESCE(SUM(amount),0) total FROM expenses WHERE date BETWEEN '$from' AND '$to'");
$total_expense = (float)mysqli_fetch_assoc($expense_q)['total'];

$renew_q = mysqli_query($con, "SELECT COUNT(*) total FROM payment_history WHERE paid_date BETWEEN '$from' AND '$to'");
$renewals = (int)mysqli_fetch_assoc($renew_q)['total'];

$expiry_q = mysqli_query($con, "SELECT COUNT(*) total FROM members WHERE expiry_date BETWEEN '$from' AND '$to'");
$expiries = (int)mysqli_fetch_assoc($expiry_q)['total'];

$branch_labels = [];
$branch_values = [];
$branch_rev_qry = "SELECT b.branch_name, COALESCE(SUM(ph.paid_amount),0) total_revenue
                   FROM branches b
                   LEFT JOIN payment_history ph ON b.id = ph.branch_id AND ph.paid_date BETWEEN '$from' AND '$to'
                   GROUP BY b.id
                   ORDER BY total_revenue DESC";
$branch_rev_res = mysqli_query($con, $branch_rev_qry);
$total_all_branches = 0;
while ($br = mysqli_fetch_assoc($branch_rev_res)) {
    $branch_labels[] = $br['branch_name'];
    $branch_values[] = (float)$br['total_revenue'];
    $total_all_branches += (float)$br['total_revenue'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Gym System Admin</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="../css/matrix-style.css" />
    <link rel="stylesheet" href="../css/matrix-media.css" />
    <link rel="stylesheet" href="../css/system-polish.css" />
    <link href="../font-awesome/css/all.css" rel="stylesheet" />
</head>
<body>
<?php include 'includes/header-content.php'; ?>
<?php include 'includes/topheader.php' ?>
<?php $page = 'chart'; include 'includes/sidebar.php' ?>

<div id="content">
    <div id="content-header">
        <div id="breadcrumb"><a href="index.php" class="tip-bottom"><i class="fas fa-home"></i> Home</a> <a href="#" class="current">Analytics</a></div>
        <h1>Analytics Reports</h1>
    </div>
    <div class="container-fluid">
        <hr>

        <form method="GET" class="form-inline" style="margin-bottom:15px;">
            <label>From</label>
            <input type="date" name="from" value="<?php echo htmlspecialchars($from); ?>" class="input-medium" style="margin:0 8px;">
            <label>To</label>
            <input type="date" name="to" value="<?php echo htmlspecialchars($to); ?>" class="input-medium" style="margin:0 8px;">
            <button type="submit" class="btn btn-success polish-btn">Apply</button>
        </form>

        <div class="row-fluid">
            <div class="span6">
                <div class="widget-box polish-card">
                    <div class="widget-title"><span class="icon"><i class="fas fa-chart-pie"></i></span><h5>Income vs Expenses</h5></div>
                    <div class="widget-content"><canvas id="incomeExpenseChart"></canvas></div>
                </div>
            </div>
            <div class="span6">
                <div class="widget-box polish-card">
                    <div class="widget-title"><span class="icon"><i class="fas fa-chart-line"></i></span><h5>Renewals vs Expiries</h5></div>
                    <div class="widget-content"><canvas id="renewalChart"></canvas></div>
                </div>
            </div>
        </div>

        <div class="row-fluid" style="margin-top:15px;">
            <div class="span12">
                <div class="widget-box polish-card">
                    <div class="widget-title"><span class="icon"><i class="fas fa-building"></i></span><h5>Branch Revenue Distribution</h5></div>
                    <div class="widget-content"><canvas id="branchRevenueChart"></canvas></div>
                </div>
            </div>
        </div>

        <div class="row-fluid" style="margin-top:15px;">
            <div class="span12">
                <div class="widget-box">
                    <div class="widget-title"><span class="icon"><i class="fas fa-table"></i></span><h5>Branch Revenue Table</h5></div>
                    <div class="widget-content nopadding">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr><th>Branch</th><th>Total Revenue</th></tr>
                            </thead>
                            <tbody>
                            <?php foreach ($branch_labels as $idx => $branch_name): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($branch_name); ?></td>
                                    <td style="color:#059669;font-weight:700;">$<?php echo number_format($branch_values[$idx], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr><th style="text-align:right;">Grand Total:</th><th style="color:#dc2626;">$<?php echo number_format($total_all_branches, 2); ?></th></tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="row-fluid"><div id="footer" class="span12" style="color:white;"><?php echo date("Y"); ?> &copy; M * A GYM System Developed By Abdikafi</div></div>

<script src="../js/jquery.min.js"></script>
<script src="../js/jquery.ui.custom.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/matrix.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const incomeCtx = document.getElementById('incomeExpenseChart').getContext('2d');
new Chart(incomeCtx, {
    type: 'doughnut',
    data: { labels: ['Income', 'Expenses'], datasets: [{ data: [<?php echo $income; ?>, <?php echo $total_expense; ?>], backgroundColor: ['#16a34a', '#dc2626'] }] },
    options: { responsive: true }
});

const renewCtx = document.getElementById('renewalChart').getContext('2d');
new Chart(renewCtx, {
    type: 'bar',
    data: { labels: ['Renewals', 'Expiries'], datasets: [{ data: [<?php echo $renewals; ?>, <?php echo $expiries; ?>], backgroundColor: ['#2563eb', '#f59e0b'] }] },
    options: { responsive: true }
});

const branchCtx = document.getElementById('branchRevenueChart').getContext('2d');
new Chart(branchCtx, {
    type: 'pie',
    data: { labels: <?php echo json_encode($branch_labels); ?>, datasets: [{ data: <?php echo json_encode($branch_values); ?>, backgroundColor: ['#2563eb','#16a34a','#f59e0b','#dc2626','#7c3aed','#14b8a6'] }] },
    options: { responsive: true }
});
</script>
</body>
</html>
