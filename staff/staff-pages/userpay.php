<?php
session_start();
$app_debug = getenv('APP_DEBUG');
$is_debug = ($app_debug === '1' || strtolower((string)$app_debug) === 'true');
ini_set('display_errors', $is_debug ? 1 : 0);
ini_set('display_startup_errors', $is_debug ? 1 : 0);
error_reporting($is_debug ? E_ALL : 0);

//the isset function to check username is already loged in and stored on the session
if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
}
require_once __DIR__ . '/../../includes/security_core.php';
$_SESSION['designation'] = current_designation();
if (!in_array($_SESSION['designation'], ['Manager', 'Cashier'])) {
    header('location:index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>M*A GYM System</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../../css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="../../css/fullcalendar.css" />
    <link rel="stylesheet" href="../../css/matrix-style.css" />
    <link rel="stylesheet" href="../../css/matrix-media.css" />
    <link href="../../font-awesome/css/all.css" rel="stylesheet" />
    <link rel="stylesheet" href="../../css/jquery.gritter.css" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Courier+Prime:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <style>
        .thermal-receipt {
            width: 320px;
            background: #fff;
            padding: 12px;
            color: #000;
            margin: 0 auto;
            box-sizing: border-box;
            font-family: 'Courier Prime', monospace;
            border: 1px solid #eee;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .thermal-header { text-align: center; margin-bottom: 12px; }
        .thermal-header h2 { margin: 0; font-size: 24px; }
        .thermal-header p { margin: 4px 0; font-size: 14px; }
        .thermal-divider { border-top: 1.5px dashed #000; margin: 12px 0; }
        .thermal-row { display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 6px; }
        .thermal-table { width: 100%; font-size: 14px; border-collapse: collapse; margin: 12px 0; }
        .thermal-table th { border-top: 1.5px dashed #000; border-bottom: 1.5px dashed #000; padding: 8px 0; text-align: left; }
        .thermal-table td { padding: 8px 0; }
        .thermal-total-row { display: flex; justify-content: space-between; font-size: 18px; font-weight: bold; border-top: 1.5px dashed #000; padding-top: 8px; margin-top: 8px; }
        .thermal-footer { text-align: center; margin-top: 20px; font-size: 13px; border-top: 1.5px dashed #000; padding-top: 12px; }
        
        @media print {
            body * { visibility: hidden; }
            .print-container, .print-container * { visibility: visible; }
            .print-container { position: absolute; left: 0; top: 0; width: 320px !important; }
            .d-print-none { display: none !important; }
            @page { size: 80mm auto; margin: 0; }
        }
    </style>
</head>

<body>

    <!--Header-part-->
  <?php include '../includes/header-content.php'; ?>
  <!--close-Header-part-->


    <!--top-Header-menu-->
    <?php include '../includes/header.php' ?>
    <!--close-top-Header-menu-->
    <!--start-top-serch-->
    <!-- <div id="search">
  <input type="hidden" placeholder="Search here..."/>
  <button type="submit" class="tip-bottom" title="Search"><i class="icon-search icon-white"></i></button>
</div> -->
    <!--close-top-serch-->

    <!--sidebar-menu-->
    <?php $page = 'payment';
    include '../includes/sidebar.php' ?>
    <!--sidebar-menu-->

    <div id="content">
        <div id="content-header">
            <div id="breadcrumb"> <a href="index.php" title="Go to Home Page" class="tip-bottom"><i class="icon-home"></i> Home</a> <a href="payment.php" class="tip-bottom">Payments</a> <a href="#" class="current">Pay Money</a> </div>
            <h1>Payment</h1>
        </div>
        <form role="form" action="index.php" method="POST">
            <?php

            if (isset($_POST['amount'])) {

                $fullname = $_POST['fullname'];
                $paid_date = $_POST['paid_date'];
                $services = $_POST["services"];
                $amount = $_POST["amount"];
                $plan = $_POST["plan"];
                $status = $_POST["status"];
                $id = $_POST['id'];

                $amountpayable = $amount * $plan;

                include 'dbcon.php';
                require_once __DIR__ . '/../../includes/audit_helper.php';

                mysqli_query($con, "CREATE TABLE IF NOT EXISTS payment_history (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    invoice_no VARCHAR(50) NULL,
                    user_id INT NOT NULL,
                    fullname VARCHAR(255) NOT NULL,
                    amount DECIMAL(10,2) DEFAULT 0,
                    paid_amount DECIMAL(10,2) DEFAULT 0,
                    discount_amount DECIMAL(10,2) DEFAULT 0,
                    discount_type VARCHAR(20) DEFAULT 'amount',
                    plan INT DEFAULT 1,
                    services VARCHAR(255) DEFAULT '',
                    paid_date DATE,
                    expiry_date DATE,
                    branch_id INT DEFAULT 0,
                    recorded_by VARCHAR(100) DEFAULT 'Staff',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )");
                mysqli_query($con, "ALTER TABLE payment_history ADD COLUMN IF NOT EXISTS invoice_no VARCHAR(50) NULL");
                mysqli_query($con, "ALTER TABLE payment_history ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
                mysqli_query($con, "ALTER TABLE members ADD COLUMN IF NOT EXISTS created_by VARCHAR(100) NULL");
                mysqli_query($con, "ALTER TABLE members ADD COLUMN IF NOT EXISTS updated_by VARCHAR(100) NULL");
                mysqli_query($con, "ALTER TABLE members ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP NULL DEFAULT NULL");

                // Fetch current expiry date to decide extension logic
                $check_qry = mysqli_query($con, "SELECT expiry_date FROM members WHERE user_id='$id'");
                $member_row = mysqli_fetch_assoc($check_qry);
                $current_expiry = $member_row['expiry_date'];
                $today_date = date('Y-m-d');

                // If current expiry is in the future, extend from that. Otherwise, start from today.
                $base_date = ($current_expiry > $today_date) ? $current_expiry : $today_date;
                $new_expiry = date('Y-m-d', strtotime("+$plan months", strtotime($base_date)));

                // Retrieve existing discount and paid amounts for the history record
                $check_history = mysqli_query($con, "SELECT fullname, discount_type, discount_amount, paid_amount, branch_id FROM members WHERE user_id='$id'");
                $hist_row = mysqli_fetch_assoc($check_history);

                // Insert into payment_history table
                $fullname_esc = mysqli_real_escape_string($con, $hist_row['fullname']);
                $discount_type = $hist_row['discount_type'];
                $discount_amount = $hist_row['discount_amount'];
                $paid_amount = $hist_row['paid_amount'];
                $branch_id = $hist_row['branch_id'];
                $staff_name = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'Staff';
                $invoice_no = 'GMS' . date('YmdHis') . rand(100, 999) . $id;

                $history_qry = "INSERT INTO payment_history (invoice_no, user_id, fullname, amount, paid_amount, discount_amount, discount_type, plan, services, paid_date, expiry_date, branch_id, recorded_by) 
                                VALUES ('$invoice_no', '$id', '$fullname_esc', '$amountpayable', '$paid_amount', '$discount_amount', '$discount_type', '$plan', '$services', '$today_date', '$new_expiry', '$branch_id', '$staff_name')";
                mysqli_query($con, $history_qry);

                // Receipt Verification QR Setup
                $verify_code = strtoupper(substr(hash('sha256', $invoice_no . '|' . $today_date . '|' . $paid_amount), 0, 12));
                $verify_payload = rawurlencode("Invoice:$invoice_no|Verify:$verify_code|Member:$id");
                $qr_url = "https://quickchart.io/qr?size=150&text=$verify_payload";

                //update query
                $staff_name_esc = mysqli_real_escape_string($con, $staff_name);
                $qry = "UPDATE members SET amount='$amountpayable', plan='$plan', status='$status', paid_date='$today_date', expiry_date='$new_expiry', reminder = '0', updated_by='$staff_name_esc', updated_at=NOW() WHERE user_id='$id'";
                $result = mysqli_query($con, $qry); //query executes

                if (!$result) { ?>

                    <h3 class="text-center">Something went wrong!</h3>

                <?php } else { ?>

                    <?php
                    $actorId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '0';
                    audit_log($con, 'staff', $actorId, 'member_renewal_payment', 'member', $id, 'Renewal paid. Invoice: ' . $invoice_no . ', Plan: ' . $plan . ' month(s)');
                    ?>

                    <?php if ($status == 'Active') { ?>

                        <div class="print-container">
                            <div id="print-area" class="thermal-receipt">
                                <div class="thermal-header">
                                    <h2>M*A GYM</h2>
                                    <p>Busley, Bondheere, Mogadishu</p>
                                    <p>Tel: 252-610-000-000</p>
                                </div>

                                <div class="thermal-divider"></div>

                                <div class="thermal-row">
                                    <span>Invoice #:</span>
                                    <span><?php echo $invoice_no; ?></span>
                                </div>
                                <div class="thermal-row">
                                    <span>Date:</span>
                                    <span><?php echo date("Y-m-d H:i"); ?></span>
                                </div>
                                <div class="thermal-row" style="flex-wrap: wrap;">
                                    <span>Member:</span>
                                    <span style="text-align:right"><?php echo $fullname; ?></span>
                                </div>
                                <div class="thermal-row">
                                    <span>Member ID:</span>
                                    <span>PGC-<?php echo $id; ?></span>
                                </div>

                                <div class="thermal-divider"></div>

                                <table class="thermal-table">
                                    <thead>
                                        <tr>
                                            <th>SERVICE / PLAN</th>
                                            <th style="text-align:right">AMT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <?php echo $services; ?><br>
                                                <small>(<?php echo $plan ?> Month/s)</small>
                                            </td>
                                            <td style="text-align:right">$<?php echo number_format((float)$amountpayable, 2) ?></td>
                                        </tr>
                                    </tbody>
                                </table>

                                <div class="thermal-total-row">
                                    <span>TOTAL PAID:</span>
                                    <span>$<?php echo number_format((float)$amountpayable, 2); ?></span>
                                </div>

                                <div class="thermal-divider"></div>
                                <div style="text-align:center; padding: 10px 0;">
                                    <img src="<?php echo $qr_url; ?>" style="width:120px; height:120px;">
                                    <p style="font-size:10px; margin-top:5px;">VERIFY: <?php echo $verify_code; ?></p>
                                </div>

                                <div class="thermal-footer">
                                    <p>*** THANK YOU! ***</p>
                                    <p>Official Receipt - Power by M*A</p>
                                    <p><?php echo date("d/m/Y H:i:s"); ?></p>
                                </div>
                            </div>
                            
                            <div class="text-center d-print-none" style="margin-top: 20px;">
                                <button type="button" class="btn btn-danger" onclick="window.print()"><i class="fas fa-print"></i> [ PRINT SLIP ]</button>
                                <button type="button" class="btn btn-primary" onclick="generatePOSPDF('POS_Receipt_<?php echo $invoice_no; ?>')"><i class="fas fa-download"></i> [ DOWNLOAD PDF ]</button>
                            </div>
                        </div>

                    <?php } else { ?>

                        <div class='error_ex'>
                            <h1>409</h1>
                            <h3>It seems you have deactivated the customer's account!</h3>
                            <p>The selected member's account will not be reactivated until the next payment.</p>
                            <a class='btn btn-danger btn-big' href='payment.php'>Go Back</a>
                        </div>

                    <?php } ?>

                <?php   }
            } else { ?>
                <h3>YOU ARE NOT AUTHORIZED TO ACCESS THIS PAGE. GO BACK TO <a href='index.php'> DASHBOARD </a></h3>
            <?php }
            ?>


        </form>
    </div>
    </div>
    </div>
    </div>

    <!--end-main-container-part-->

    <!--Footer-part-->

    <div class="row-fluid">
        <div id="footer" class="span12"> <?php echo date("Y"); ?> &copy; M*A GYM System Developed By Abdikafi</a> </div>
    </div>

    <style>
        #footer {
            color: white;
        }

        body {
            -webkit-font-smoothing: antialiased;
            -webkit-text-size-adjust: none;
            width: 100% !important;
            height: 100%;
            line-height: 1.6;
        }
    </style>

    <!--end-Footer-part-->

    <script src="../../js/excanvas.min.js"></script>
    <script src="../../js/jquery.min.js"></script>
    <script src="../../js/jquery.ui.custom.js"></script>
    <script src="../../js/bootstrap.min.js"></script>
    <script src="../../js/jquery.flot.min.js"></script>
    <script src="../../js/jquery.flot.resize.min.js"></script>
    <script src="../../js/jquery.peity.min.js"></script>
    <script src="../../js/fullcalendar.min.js"></script>
    <script src="../../js/matrix.js"></script>
    <script src="../../js/matrix.dashboard.js"></script>
    <script src="../../js/jquery.gritter.min.js"></script>
    <script src="../../js/matrix.interface.js"></script>
    <script src="../../js/matrix.chat.js"></script>
    <script src="../../js/jquery.validate.js"></script>
    <script src="../../js/matrix.form_validation.js"></script>
    <script src="../../js/jquery.wizard.js"></script>
    <script src="../../js/jquery.uniform.js"></script>
    <script src="../../js/select2.min.js"></script>
    <script src="../../js/matrix.popover.js"></script>
    <script src="../../js/jquery.dataTables.min.js"></script>
    <script src="../../js/matrix.tables.js"></script>

    <script type="text/javascript">
        // This function is called from the pop-up menus to transfer to
        // a different page. Ignore if the value returned is a null string:
        function goPage(newURL) {

            // if url is empty, skip the menu dividers and reset the menu selection to default
            if (newURL != "") {

                // if url is "-", it is this page -- reset the menu:
                if (newURL == "-") {
                    resetMenu();
                }
                // else, send page to designated URL            
                else {
                    document.location.href = newURL;
                }
            }
        }

        // resets the menu selection upon entry to this page:
        function resetMenu() {
            document.gomenu.selector.selectedIndex = 2;
        }

        function generatePOSPDF(filename) {
            var element = document.getElementById('print-area');
            var opt = {
                margin:       0,
                filename:     filename + '.pdf',
                image:        { type: 'jpeg', quality: 1 },
                html2canvas:  { scale: 2, useCORS: true },
                jsPDF:        { unit: 'in', format: [3.15, 12], orientation: 'portrait' }
            };
            document.body.classList.add('generating-pdf');
            html2pdf().from(element).set(opt).save().then(function() {
                document.body.classList.remove('generating-pdf');
            });
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</body>

</html>