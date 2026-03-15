<?php
session_start();
//the isset function to check username is already loged in and stored on the session
if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
}
?>
<!-- Visit codeastro.com for more projects -->
<!DOCTYPE html>
<html lang="en">

<head>
    <title>M * A GYM System</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="../css/fullcalendar.css" />
    <link rel="stylesheet" href="../css/matrix-style.css" />
    <link rel="stylesheet" href="../css/matrix-media.css" />
    <link href="../font-awesome/css/fontawesome.css" rel="stylesheet" />
    <link href="../font-awesome/css/all.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/jquery.gritter.css" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
</head>

<body>

    <!--Header-part-->
    <?php include 'includes/header-content.php'; ?>
    <!--close-Header-part-->

    <!-- Visit codeastro.com for more projects -->
    <!--top-Header-menu-->
    <?php include 'includes/topheader.php' ?>
    <!--close-top-Header-menu-->
    <!--start-top-serch-->
    <!-- <div id="search">
  <input type="hidden" placeholder="Search here..."/>
  <button type="submit" class="tip-bottom" title="Search"><i class="icon-search icon-white"></i></button>
</div> -->
    <!--close-top-serch-->

    <!--sidebar-menu-->
    <?php $page = 'payment';
    include 'includes/sidebar.php' ?>
    <!--sidebar-menu-->

    <div id="content">
        <div id="content-header">
            <div id="breadcrumb"> <a href="index.php" title="Go to Home" class="tip-bottom"><i class="fas fa-home"></i> Home</a> <a href="payment.php" class="tip-bottom">Payment</a> <a href="#" class="current">Make Payments</a> </div>
            <h1>Payments</h1>
        </div>
        <form role="form" action="index.php" method="POST">
            <?php

            if (isset($_POST['amount'])) {

                $fullname = $_POST['fullname'];
                $paid_date = $_POST['paid_date'];
                // $p_year = date('Y');
                $services = $_POST["services"];
                $amount = $_POST["amount"];
                $plan = $_POST["plan"];
                $status = $_POST["status"];
                $id = $_POST['id'];


                $amountpayable = $amount * $plan;

                include 'dbcon.php';
                require_once __DIR__ . '/../includes/audit_helper.php';

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
                    recorded_by VARCHAR(100) DEFAULT 'Admin',
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
                $invoice_no = 'GMS' . date('YmdHis') . rand(100, 999) . $id;

                $history_qry = "INSERT INTO payment_history (invoice_no, user_id, fullname, amount, paid_amount, discount_amount, discount_type, plan, services, paid_date, expiry_date, branch_id, recorded_by) 
                                VALUES ('$invoice_no', '$id', '$fullname_esc', '$amountpayable', '$paid_amount', '$discount_amount', '$discount_type', '$plan', '$services', '$today_date', '$new_expiry', '$branch_id', 'Admin')";
                mysqli_query($con, $history_qry);

                //update query
                $qry = "UPDATE members SET amount='$amountpayable', plan='$plan', status='$status', paid_date='$today_date', expiry_date='$new_expiry', reminder='0', updated_by='Admin', updated_at=NOW() WHERE user_id='$id'";
                $result = mysqli_query($con, $qry); //query executes

                if (!$result) { ?>

                    <h3 class="text-center">Something went wrong!</h3>

                <?php } else { ?>

                    <?php
                    $actorId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '0';
                    audit_log($con, 'admin', $actorId, 'member_renewal_payment', 'member', $id, 'Renewal paid. Invoice: ' . $invoice_no . ', Plan: ' . $plan . ' month(s)');
                    ?>

                    <?php if ($status == 'Active') { ?>

                        <table class="body-wrap">
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td class="container" width="600">
                                        <div class="content">
                                            <table class="main" width="100%" cellpadding="0" cellspacing="0">
                                                <tbody>
                                                    <tr>
                                                        <td class="content-wrap aligncenter print-container">
                                                            <table width="100%" cellpadding="0" cellspacing="0">
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="content-block">
                                                                            <h3 class="text-center">Payment Receipt</h3>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="content-block">
                                                                            <table class="invoice">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <div style="float:left">Invoice #<?php echo $invoice_no; ?> <br> 5021 Wetzel Lane, <br>Williamsburg </div>
                                                                                            <div style="float:right"> Last Payment: <?php echo $paid_date ?></div>
                                                                                        </td>
                                                                                    </tr>

                                                                                    <tr>
                                                                                        <td class="text-center" style="font-size:14px;"><b>Member: <?php echo $fullname; ?></b> <br>
                                                                                            Paid On: <?php echo date("F j, Y - g:i a"); ?>
                                                                                        </td>

                                                                                    </tr>

                                                                                    <tr>
                                                                                        <td>
                                                                                            <table class="invoice-items" cellpadding="0" cellspacing="0">
                                                                                                <tbody>

                                                                                                    <tr>
                                                                                                        <td><b>Service Taken</b></td>
                                                                                                        <td class="alignright"><b>Valid Upto</b></td>
                                                                                                    </tr>


                                                                                                    <tr>
                                                                                                        <td><?php echo $services; ?></td>
                                                                                                        <td class="alignright"><?php echo $plan ?> Month/s</td>
                                                                                                    </tr>

                                                                                                    <tr>
                                                                                                        <td><?php echo 'Charge Per Month'; ?></td>
                                                                                                        <td class="alignright"><?php echo '$' . $amount ?></td>
                                                                                                    </tr>


                                                                                                    <tr class="total">
                                                                                                        <td class="alignright" width="80%">Total Amount</td>
                                                                                                        <td class="alignright">$<?php echo $amountpayable; ?></td>
                                                                                                    </tr>
                                                                                                </tbody>
                                                                                            </table>
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td class="content-block text-center">
                                                                            We sincerely appreciate your promptness regarding all payments from your side.
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <div class="footer">
                                                <table width="100%">
                                                    <tbody>
                                                        <tr>
                                                            <td class="aligncenter content-block"><button class="btn btn-danger" onclick="window.print()"><i class="fas fa-print"></i> Print</button></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>

                    <?php } else { ?>

                        <div class='error_ex'>
                            <h1>409</h1>
                            <h3>Looks like you've deactivated the customer's account!</h3>
                            <p>The selected member's account will no longer be ACTIVATED until the next payment.</p>
                            <a class='btn btn-danger btn-big' href='payment.php'>Go Back</a>
                        </div>

                    <?php } ?>

                <?php   }
            } else { ?>
                <h3>YOU ARE NOT AUTHORIZED TO REDIRECT THIS PAGE. GO BACK to <a href='index.php'> DASHBOARD </a></h3>
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
        <div id="footer" class="span12"> <?php echo date("Y"); ?> &copy; M * A GYM System Developed By Abdikafi</a> </div>
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

        /* Let's make sure all tables have defaults */
        table td {
            vertical-align: top;
        }

        /* -------------------------------------
    BODY & CONTAINER
------------------------------------- */


        .body-wrap {
            background-color: #f6f6f6;
            width: 100%;
        }

        .container {
            display: block !important;
            max-width: 600px !important;
            margin: 0 auto !important;
            /* makes it centered */
            clear: both !important;
        }

        .content {
            max-width: 600px;
            margin: 0 auto;
            display: block;
            padding: 20px;
        }

        /* -------------------------------------
    HEADER, FOOTER, MAIN
------------------------------------- */
        .main {
            background: #fff;
            border: 1px solid #e9e9e9;
            border-radius: 3px;
        }

        .content-wrap {
            padding: 20px;
        }



        .footer {
            width: 100%;
            clear: both;
            color: #999;
            padding: 20px;
        }


        /* -------------------------------------
    INVOICE
    Styles for the billing table
------------------------------------- */
        .invoice {
            margin: 22px auto;
            text-align: left;
            width: 80%;
        }

        .invoice td {
            padding: 7px 0;
        }

        .invoice .invoice-items {
            width: 100%;
        }

        .invoice .invoice-items td {
            border-top: #eee 1px solid;
        }

        .invoice .invoice-items .total td {
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
            font-weight: 700;
        }

        /* -------------------------------------
    RESPONSIVE AND MOBILE FRIENDLY STYLES
------------------------------------- */
        @media only screen and (max-width: 640px) {


            h2 {
                font-size: 18px !important;
            }


            .container {
                width: 100% !important;
            }

            .content,
            .content-wrap {
                padding: 10px !important;
            }

            .invoice {
                width: 100% !important;
            }
        }

        @media print {
            body * {
                visibility: hidden;
            }

            .print-container,
            .print-container * {
                visibility: visible;
            }

            .print-container {
                position: absolute;
                left: 0px;
                top: 0px;
                right: 0px;
            }
        }
    </style>

    <!--end-Footer-part-->

    <script src="../js/excanvas.min.js"></script>
    <script src="../js/jquery.min.js"></script>
    <script src="../js/jquery.ui.custom.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/jquery.flot.min.js"></script>
    <script src="../js/jquery.flot.resize.min.js"></script>
    <script src="../js/jquery.peity.min.js"></script>
    <script src="../js/fullcalendar.min.js"></script>
    <script src="../js/matrix.js"></script>
    <script src="../js/matrix.dashboard.js"></script>
    <script src="../js/jquery.gritter.min.js"></script>
    <script src="../js/matrix.interface.js"></script>
    <script src="../js/matrix.chat.js"></script>
    <script src="../js/jquery.validate.js"></script>
    <script src="../js/matrix.form_validation.js"></script>
    <script src="../js/jquery.wizard.js"></script>
    <script src="../js/jquery.uniform.js"></script>
    <script src="../js/select2.min.js"></script>
    <script src="../js/matrix.popover.js"></script>
    <script src="../js/jquery.dataTables.min.js"></script>
    <script src="../js/matrix.tables.js"></script>

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
    </script>
</body>

</html>