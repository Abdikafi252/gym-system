<?php
session_start();
include "dbcon.php";
include "session.php";

if (!isset($_SESSION['user_id'])) {
    header("location:../index.php");
    exit();
}

$uid = $_SESSION['user_id'];
$qry = "SELECT * FROM members WHERE user_id='$uid'";
$result = mysqli_query($con, $qry);
$row = mysqli_fetch_array($result);

if (!$row) {
    die("Your data could not be found. Please contact administration.");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>M*A GYM System - My Payments</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="../css/matrix-style.css" />
    <link rel="stylesheet" href="../css/matrix-media.css" />
    <link rel="stylesheet" href="../../css/system-polish.css" />
    <link href="../font-awesome/css/all.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/jquery.gritter.css" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
</head>

<body>

    <!--Header-part-->
    <!-- Logo removed per user request -->
    <!--close-Header-part-->

    <!--top-Header-menu-->
    <?php include '../includes/topheader.php' ?>
    <!--close-top-Header-menu-->

    <!--sidebar-menu-->
    <?php $page = "payments";
    include '../includes/sidebar.php' ?>
    <!--sidebar-menu-->

    <div id="content">
        <div id="content-header">
            <div id="breadcrumb">
                <a href="index.php" title="Go to Home" class="tip-bottom"><i class="fas fa-home"></i> Home</a>
                <a href="my-payments.php" class="current">My Payments</a>
            </div>
        </div>

        <div class="container-fluid" style="margin-top: 50px;">
            <div class="row-fluid">
                <div class="span12">
                    <?php if ($row['status'] != 'Active') { ?>
                        <div class="alert alert-warning" style="margin-bottom: 15px; border-radius: 8px;">
                            Your account is currently <strong><?php echo htmlspecialchars($row['status']); ?></strong>, but you can still view your payment history and print receipts each month.
                        </div>
                    <?php } ?>
                        <div class="widget-box" style="border-radius: 20px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); overflow:hidden;">
                            <div class="widget-title" style="background:#f8fafc; padding: 15px 20px;">
                                <span class="icon" style="margin-right: 15px; color:#16a34a;"> <i class="fas fa-history" style="font-size: 20px;"></i> </span>
                                <h4 style="margin: 0; color: #0f172a; font-weight:800; display: inline-block;">Payment History</h4>

                                <form method="GET" action="my-payments.php" class="pull-right" style="margin: 0; display: flex; gap: 10px; align-items: center;">
                                    <?php
                                    $filter_month = isset($_GET['month']) ? $_GET['month'] : '';
                                    $filter_year = isset($_GET['year']) ? $_GET['year'] : '';
                                    ?>
                                    <select name="month" style="width: 140px; margin-bottom: 0; border-radius: 6px;">
                                        <option value="">Select Month</option>
                                        <?php
                                        $months = array(
                                            '01' => 'January',
                                            '02' => 'February',
                                            '03' => 'March',
                                            '04' => 'April',
                                            '05' => 'May',
                                            '06' => 'June',
                                            '07' => 'July',
                                            '08' => 'August',
                                            '09' => 'September',
                                            '10' => 'October',
                                            '11' => 'November',
                                            '12' => 'December'
                                        );
                                        foreach ($months as $num => $name) {
                                            $selected = ($filter_month == $num) ? 'selected' : '';
                                            echo "<option value='$num' $selected>$name</option>";
                                        }
                                        ?>
                                    </select>
                                    <select name="year" style="width: 100px; margin-bottom: 0; border-radius: 6px;">
                                        <option value="">Year</option>
                                        <?php
                                        $current_year = date('Y');
                                        for ($y = $current_year; $y >= $current_year - 5; $y--) {
                                            $selected = ($filter_year == $y) ? 'selected' : '';
                                            echo "<option value='$y' $selected>$y</option>";
                                        }
                                        ?>
                                    </select>
                                    <button type="submit" class="btn btn-success" style="border-radius: 6px; font-weight:bold;">Search</button>
                                </form>
                            </div>
                            <div class="widget-content nopadding">
                                <table class="table table-bordered table-striped table-hover data-table" style="width: 100%; margin-bottom: 0;">
                                    <thead>
                                        <tr style="background:#f1f5f9;">
                                            <th style="padding: 15px; text-align: left; color:#475569;"># Invoice</th>
                                            <th style="padding: 15px; text-align: left; color:#475569;">Date</th>
                                            <th style="padding: 15px; text-align: left; color:#475569;">Service</th>
                                            <th style="padding: 15px; text-align: left; color:#475569;">Plan</th>
                                            <th style="padding: 15px; text-align: left; color:#475569;">Covered Months</th>
                                            <th style="padding: 15px; text-align: right; color:#475569;">Amount</th>
                                            <th style="padding: 15px; text-align: center; color:#475569;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Fetch history for this user
                                        $hist_qry = "SELECT * FROM payment_history WHERE user_id='$uid' ORDER BY paid_date DESC, id DESC";
                                        $hist_res = mysqli_query($con, $hist_qry);

                                        $found_rows = 0;
                                        if (mysqli_num_rows($hist_res) > 0) {
                                            while ($hist = mysqli_fetch_array($hist_res)) {
                                                $plan = intval($hist['plan']);
                                                if ($plan == 0) $plan = 1; // Safeguard

                                                $start_time = strtotime($hist['paid_date']);
                                                $covered_months = [];
                                                $is_match = false;

                                                for ($i = 0; $i < $plan; $i++) {
                                                    $cur_m = date('m', strtotime("+$i months", $start_time));
                                                    $cur_y = date('Y', strtotime("+$i months", $start_time));
                                                    $month_label = date('M Y', strtotime("+$i months", $start_time));
                                                    $covered_months[] = "<span class='badge badge-info' style='background:#e0f2fe; color:#0284c7; margin-right:3px; margin-bottom:3px;'>$month_label</span>";

                                                    // Check if this month matches filter
                                                    if ($filter_month != '' && $filter_year != '') {
                                                        if ($filter_month == $cur_m && $filter_year == $cur_y) {
                                                            $is_match = true;
                                                        }
                                                    } else if ($filter_month != '') {
                                                        if ($filter_month == $cur_m) $is_match = true;
                                                    } else if ($filter_year != '') {
                                                        if ($filter_year == $cur_y) $is_match = true;
                                                    } else {
                                                        $is_match = true; // No filter
                                                    }
                                                }

                                                if (!$is_match) continue; // Skip if it doesn't match the filter

                                                $found_rows++;
                                                $invoice_num = !empty($hist['invoice_no']) ? $hist['invoice_no'] : ("GMS_" . $hist['user_id'] . date('Ym', strtotime($hist['paid_date'])) . $hist['id']);
                                        ?>
                                                <tr>
                                                    <td style="padding: 15px; vertical-align: middle;"><strong>#<?php echo $invoice_num; ?></strong></td>
                                                    <td style="padding: 15px; vertical-align: middle;"><?php echo date("d M Y", strtotime($hist['paid_date'])); ?></td>
                                                    <td style="padding: 15px; vertical-align: middle;"><?php echo $hist['services']; ?></td>
                                                    <td style="padding: 15px; vertical-align: middle;"><?php echo $hist['plan']; ?> Month/s</td>
                                                    <td style="padding: 15px; vertical-align: middle;"><?php echo implode(" ", $covered_months); ?></td>
                                                    <td style="padding: 15px; vertical-align: middle; text-align: right; font-weight: bold; color: #16a34a;">
                                                        $<?php echo number_format($hist['paid_amount'], 2); ?>
                                                    </td>
                                                    <td style="padding: 15px; vertical-align: middle; text-align: center;">
                                                        <a href="print-receipt.php?id=<?php echo $hist['id']; ?>" class="btn btn-primary btn-sm" style="border-radius: 6px; font-weight:bold; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);" target="_blank">
                                                            <i class="fas fa-print"></i> Print
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php
                                            }
                                        }

                                        if ($found_rows == 0) {
                                            ?>
                                            <tr>
                                                <td colspan="7" style="padding: 30px; text-align: center; color: #64748b;">
                                                    <i class="fas fa-folder-open" style="font-size: 40px; color:#cbd5e1; margin-bottom: 15px; display:block;"></i>
                                                    No payment history found.
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>

    <!--Footer-part-->
    <div class="row-fluid d-print-none">
        <div id="footer" class="span12" style="color:white;"> <?php echo date("Y"); ?> &copy; M*A GYM System Developed By Abdikafi</div>
    </div>

    <style>
        .premium-card {
            border-radius: 20px !important;
            border: 1px solid #f1f5f9 !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05) !important;
            background: #fff;
        }

        @media print {
            body * {
                visibility: hidden;
                background: #fff !important;
            }

            .print-container,
            .print-container * {
                visibility: visible;
                color: #000 !important;
            }

            .print-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .d-print-none {
                display: none !important;
            }

            .main {
                box-shadow: none !important;
                border: none !important;
            }
        }
    </style>

    <script src="../js/excanvas.min.js"></script>
    <script src="../js/jquery.min.js"></script>
    <script src="../js/jquery.ui.custom.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/matrix.js"></script>
</body>

</html>