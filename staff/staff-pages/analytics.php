<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Gym System Admin</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../../css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="../../css/fullcalendar.css" />
    <link rel="stylesheet" href="../../css/matrix-style.css" />
    <link rel="stylesheet" href="../../css/matrix-media.css" />
    <link rel="stylesheet" href="../../css/system-polish.css" />
    <link href="../../font-awesome/css/fontawesome.css" rel="stylesheet" />
    <link href="../../font-awesome/css/all.css" rel="stylesheet" />
    <link rel="stylesheet" href="../../css/jquery.gritter.css" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
</head>

<body>

    <!--Header-part-->
        <?php include '../includes/header-content.php'; ?>
<!--close-Header-part-->

    <!--top-Header-menu-->
    <?php include '../includes/header.php' ?>
    <!--close-top-Header-menu-->

    <!--sidebar-menu-->
    <?php $page = 'chart';
    include '../includes/sidebar.php' ?>
    <!--sidebar-menu-->

    <div id="content">
        <div id="content-header">
            <div id="breadcrumb"> <a href="index.php" title="Go to Home" class="tip-bottom"><i class="fas fa-home"></i> Home</a> <a href="#" class="current">Analytics</a> </div>
            <h1>Analytics Reports</h1>
        </div>
        <div class="container-fluid">
            <hr>

            <form method="GET" class="form-inline" style="margin-bottom:15px;">
                <?php
                $from = isset($_GET['from']) ? $_GET['from'] : date('Y-m-01');
                $to = isset($_GET['to']) ? $_GET['to'] : date('Y-m-d');
                ?>
                <label>From</label>
                <input type="date" name="from" value="<?php echo htmlspecialchars($from); ?>" class="input-medium" style="margin:0 8px;">
                <label>To</label>
                <input type="date" name="to" value="<?php echo htmlspecialchars($to); ?>" class="input-medium" style="margin:0 8px;">
                <button type="submit" class="btn btn-success polish-btn">Apply</button>
            </form>

            <div class="row-fluid">
                <div class="span6">
                    <div class="widget-box polish-card">
                        <div class="widget-title"> <span class="icon"> <i class="fas fa-chart-pie"></i> </span>
                            <h5>Income vs Expenses</h5>
                        </div>
                        <div class="widget-content">
                            <canvas id="incomeExpenseChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="span6">
                    <div class="widget-box polish-card">
                        <div class="widget-title"> <span class="icon"> <i class="fas fa-chart-line"></i> </span>
                            <h5>Renewals vs Expiries</h5>
                        </div>
                        <div class="widget-content">
                            <canvas id="renewalChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row-fluid" style="margin-top:15px;">
                <div class="span12">
                    <div class="widget-box polish-card">
                        <div class="widget-title"> <span class="icon"> <i class="fas fa-dumbbell"></i> </span>
                            <h5>Service Revenue Breakdown</h5>
                        </div>
                        <div class="widget-content">
                            <canvas id="serviceRevenueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!--Footer-part-->
    <div class="row-fluid">
        <div id="footer" class="span12"> <?php echo date("Y"); ?> &copy; M*A GYM System Developed By Abdikafi</div>
    </div>

    <style>
        #footer {
            color: white;
        }
    </style>

    <?php
    include "dbcon.php";

    $branch_id = $_SESSION['branch_id'];

    $income_q = mysqli_query($con, "SELECT COALESCE(SUM(paid_amount),0) total FROM payment_history WHERE branch_id='$branch_id' AND paid_date BETWEEN '$from' AND '$to'");
    $income = mysqli_fetch_assoc($income_q)['total'];

    $expense_q = mysqli_query($con, "SELECT COALESCE(SUM(amount),0) total FROM expenses WHERE branch_id='$branch_id' AND date BETWEEN '$from' AND '$to'");
    $total_expense = mysqli_fetch_assoc($expense_q)['total'];

    $renew_q = mysqli_query($con, "SELECT COUNT(*) total FROM payment_history WHERE branch_id='$branch_id' AND paid_date BETWEEN '$from' AND '$to'");
    $renewals = mysqli_fetch_assoc($renew_q)['total'];

    $expiry_q = mysqli_query($con, "SELECT COUNT(*) total FROM members WHERE branch_id='$branch_id' AND expiry_date BETWEEN '$from' AND '$to'");
    $expiries = mysqli_fetch_assoc($expiry_q)['total'];

    $svc_labels = [];
    $svc_values = [];
    $svc_q = mysqli_query($con, "SELECT services, COALESCE(SUM(paid_amount),0) total FROM payment_history WHERE branch_id='$branch_id' AND paid_date BETWEEN '$from' AND '$to' GROUP BY services ORDER BY total DESC");
    while ($s = mysqli_fetch_assoc($svc_q)) {
        $svc_labels[] = $s['services'];
        $svc_values[] = (float)$s['total'];
    }

    ?>

    <script src="../../js/jquery.min.js"></script>
    <script src="../../js/jquery.ui.custom.js"></script>
    <script src="../../js/bootstrap.min.js"></script>
    <script src="../../js/matrix.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Income vs Expense Chart
        const ctx1 = document.getElementById('incomeExpenseChart').getContext('2d');
        new Chart(ctx1, {
            type: 'doughnut',
            data: {
                labels: ['Total Income', 'Total Expenses'],
                datasets: [{
                    data: [<?php echo $income; ?>, <?php echo $total_expense; ?>],
                    backgroundColor: ['#28b779', '#da542e'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true
            }
        });

        // Renewals vs Expiries
        const ctx2 = document.getElementById('renewalChart').getContext('2d');
        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: ['Renewals', 'Expiries'],
                datasets: [{
                    label: 'Count',
                    data: [<?php echo (int)$renewals; ?>, <?php echo (int)$expiries; ?>],
                    backgroundColor: ['#16a34a', '#dc2626'],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Service Revenue Breakdown
        const ctx3 = document.getElementById('serviceRevenueChart').getContext('2d');
        new Chart(ctx3, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($svc_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($svc_values); ?>,
                    backgroundColor: ['#2563eb', '#16a34a', '#f59e0b', '#dc2626', '#7c3aed', '#14b8a6']
                }]
            },
            options: {
                responsive: true
            }
        });
    </script>
</body>

</html>