<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
    exit;
}
include 'dbcon.php';
require_once 'includes/db_helper.php';
require_once 'includes/accounting_engine.php';

$page = 'yearly-history';

// Handle Year Selection
$selected_year = isset($_GET['year']) ? (int)$_GET['year'] : null;
$period = null;

if ($selected_year) {
    $period = safe_fetch_assoc($con, "SELECT * FROM fiscal_periods WHERE period_year=?", "i", [$selected_year]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>M*A GYM System - Yearly History</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="../css/matrix-style.css" />
    <link rel="stylesheet" href="../css/matrix-media.css" />
    <link href="../font-awesome/css/fontawesome.css" rel="stylesheet" />
    <link href="../font-awesome/css/all.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
    <style>
        .year-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; text-align: center; transition: all 0.3s; margin-bottom: 20px; }
        .year-card:hover { transform: translateY(-5px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); border-color: #3b82f6; }
        .year-card h3 { margin: 0; color: #1e293b; font-size: 24px; }
        .year-card .status { font-size: 11px; text-transform: uppercase; font-weight: 700; display: block; margin-top: 5px; }
        .status-closed { color: #059669; }
        .status-open { color: #7c3aed; }
        .report-header { background: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 20px; }
    </style>
</head>
<body>

<?php include 'includes/header-content.php'; ?>
<?php include 'includes/topheader.php' ?>
<?php include 'includes/sidebar.php' ?>

<div id="content">
    <div id="content-header">
        <div id="breadcrumb"> <a href="index.php" title="Go to Home" class="tip-bottom"><i class="fas fa-home"></i> Home</a> <a href="#" class="current">Yearly History</a> </div>
        <h1 class="text-center">Yearly Financial History <i class="fas fa-history"></i></h1>
    </div>

    <div class="container-fluid">
        <hr>

        <?php if (!$selected_year): ?>
            <!-- Year Selection Grid -->
            <div class="row-fluid">
                <?php
                $periods = safe_fetch_all($con, "SELECT * FROM fiscal_periods ORDER BY period_year DESC");
                if (empty($periods)) {
                    echo "<div class='alert alert-info text-center'>No fiscal periods found. Historical data will appear here once years are closed.</div>";
                }
                foreach ($periods as $p):
                    $status_class = ($p['status'] == 'closed') ? 'status-closed' : 'status-open';
                ?>
                <div class="span3">
                    <div class="year-card">
                        <i class="fas fa-folder-open fa-3x" style="color:#94a3b8; margin-bottom:15px;"></i>
                        <h3><?php echo $p['period_year']; ?></h3>
                        <span class="status <?php echo $status_class; ?>"><?php echo strtoupper($p['status']); ?></span>
                        <hr>
                        <a href="?year=<?php echo $p['period_year']; ?>" class="btn btn-primary btn-block">View Reports</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Selected Year Reports -->
            <div class="row-fluid">
                <div class="span12">
                    <a href="accounting-yearly-history.php" class="btn btn-mini btn-default"><i class="fas fa-arrow-left"></i> Back to All Years</a>
                    <div class="report-header text-center">
                        <h2>Financial Statement for Fiscal Year <?php echo $selected_year; ?></h2>
                        <p class="text-muted">Period: <?php echo date('M d, Y', strtotime($period['start_date'])); ?> to <?php echo date('M d, Y', strtotime($period['end_date'])); ?></p>
                        <?php if($period['status'] == 'closed'): ?>
                            <span class="badge badge-success">CLOSED AT <?php echo date('M d, Y H:i', strtotime($period['closed_at'])); ?> BY <?php echo htmlspecialchars($period['closed_by']); ?></span>
                        <?php else: ?>
                            <span class="badge badge-warning">CURRENT OPEN PERIOD</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="row-fluid">
                <!-- Income Statement Snapshot -->
                <div class="span6">
                    <div class="widget-box">
                        <div class="widget-title"> <span class="icon"><i class="fas fa-chart-line"></i></span>
                            <h5>Income Statement Snapshot</h5>
                        </div>
                        <div class="widget-content">
                            <?php 
                            // Using dates from the period to generate a snapshot
                            $is_data = acc_income_statement($con, $period['start_date'], $period['end_date']);
                            ?>
                            <table class="table table-bordered">
                                <thead>
                                    <tr><th>Category</th><th>Amount</th></tr>
                                </thead>
                                <tbody>
                                    <tr class="info"><td><strong>TOTAL REVENUE</strong></td><td class="text-right"><strong>$<?php echo number_format($is_data['total_revenue'], 2); ?></strong></td></tr>
                                    <tr class="error"><td><strong>TOTAL EXPENSES</strong></td><td class="text-right"><strong>$<?php echo number_format($is_data['total_expenses'], 2); ?></strong></td></tr>
                                    <tr class="success" style="font-size:16px;"><td><strong>NET INCOME</strong></td><td class="text-right"><strong>$<?php echo number_format($is_data['net_income'], 2); ?></strong></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Balance Sheet Snapshot -->
                <div class="span6">
                    <div class="widget-box">
                        <div class="widget-title"> <span class="icon"><i class="fas fa-balance-scale"></i></span>
                            <h5>Balance Sheet Snapshot (Year End)</h5>
                        </div>
                        <div class="widget-content">
                            <?php 
                            $bs_data = acc_balance_sheet($con, $period['end_date']);
                            ?>
                            <table class="table table-bordered">
                                <thead>
                                    <tr><th>Category</th><th>Amount</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td><strong>TOTAL ASSETS</strong></td><td class="text-right"><strong>$<?php echo number_format($bs_data['total_assets'], 2); ?></strong></td></tr>
                                    <tr><td><strong>TOTAL LIABILITIES</strong></td><td class="text-right"><strong>$<?php echo number_format($bs_data['total_liabilities'], 2); ?></strong></td></tr>
                                    <tr><td><strong>TOTAL EQUITY</strong></td><td class="text-right"><strong>$<?php echo number_format($bs_data['total_equity'], 2); ?></strong></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row-fluid">
                <div class="span12">
                    <div class="widget-box text-center" style="padding:20px;">
                        <button class="btn btn-info" onclick="window.print()"><i class="fas fa-print"></i> Print Annual Statement</button>
                        <button class="btn btn-success" onclick="generatePremiumPDF('Annual_Statement_<?php echo $selected_year; ?>')"><i class="fas fa-download"></i> Download PDF</button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="row-fluid">
    <div id="footer" class="span12" style="color:white;"> <?php echo date("Y"); ?> &copy; M*A GYM System Developed By Abdikafi </div>
</div>

<script src="../js/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/matrix.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    function generatePremiumPDF(filename) {
        var element = document.querySelector('.container-fluid');
        var opt = {
            margin:       0.3,
            filename:     filename + '.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2 },
            jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
        };
        html2pdf().from(element).set(opt).save();
    }
</script>
</body>
</html>
