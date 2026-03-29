<?php
session_start();
//the isset function to check username is already loged in and stored on the session
if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
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
    <link href="../../font-awesome/css/fontawesome.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../css/jquery.gritter.css" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
</head>

<body>

    <!--Header-part-->
  <?php include '../includes/header-content.php'; ?>
  <!--close-Header-part-->

    <!--top-Header-menu-->
    <?php include '../includes/header.php'; ?>
    <!--close-top-Header-menu-->
    <!--start-top-serch-->

    <!--sidebar-menu-->
    <?php $page = 'c-p-r';
    include '../includes/sidebar.php'; ?>
    <!--sidebar-menu-->

    <div id="content">
        <div id="content-header">
            <div id="breadcrumb"> <a href="index.php" title="Go to Home" class="tip-bottom"><i class="fas fa-home"></i> Home</a> <a href="progress-report.php" class="current">Member Reports</a> </div>
            <h1 class="text-center">Progress Report <i class="fas fa-tasks"></i></h1>
        </div>
        <div class="container-fluid">
            <div class="row-fluid">
                <div class="span12">
                    <div class="widget-box">
                        <?php
                        include 'dbcon.php';
                        $id = $_GET['id'];
                        $qry = "select * from members where user_id='$id'";
                        $result = mysqli_query($con, $qry);
                        while ($row = mysqli_fetch_array($result)) {
                        ?>

                            <div class="widget-content">
                                <div class="row-fluid">
                                    <div class="span4">
                                        <table class="">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <h4>M * A</h4>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Busley, Bondheere, Mogadishu, Somalia</td>
                                                </tr>

                                                <tr>
                                                    <td>Tel: 252-610-000-000</td>
                                                </tr>
                                                <tr>
                                                    <td>Email: support@M*Agym.com</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="span8">
                                        <!-- Basic Membership Info -->
                                        <table class="table table-bordered table-invoice-full table-striped">
                                            <thead>
                                                <tr>
                                                    <th class="head0">Membership ID</th>
                                                    <th class="head1">Services Taken</th>
                                                    <th class="head0">Plans (Upto)</th>
                                                    <th class="head1">Last Update Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="text-center">PGC-SS-<?php echo $row['user_id']; ?></div>
                                                    </td>
                                                    <td>
                                                        <div class="text-center"><?php echo $row['services']; ?></div>
                                                    </td>
                                                    <td>
                                                        <div class="text-center"><?php echo $row['plan'] == 0 ? 'NONE' : $row['plan'] . ' Month/s'; ?></div>
                                                    </td>
                                                    <td>
                                                        <div class="text-center"><strong style="color: #2e3192;"><?php echo date('d-M-Y', strtotime($row['progress_date'])); ?></strong></div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        <div class="row-fluid" style="margin-top: 15px;">
                                            <!-- Core Metrics Section -->
                                            <div class="span6">
                                                <div class="widget-box" style="border:1px solid #e2e8f0; border-radius:8px;">
                                                    <div class="widget-title" style="background:#f8fafc; border-bottom:1px solid #e2e8f0; border-radius:8px 8px 0 0;">
                                                        <span class="icon"><i class="fas fa-balance-scale" style="color:#0284c7;"></i></span>
                                                        <h5 style="color:#334155;">Weight & Core Metrics</h5>
                                                    </div>
                                                    <div class="widget-content" style="background:#fff; border-radius:0 0 8px 8px; padding:15px;">
                                                        <table class="table table-bordered table-striped">
                                                            <tbody>
                                                                <tr>
                                                                    <td><strong>Initial Weight</strong></td>
                                                                    <td class="text-right"><?php echo $row['ini_weight']; ?> KG</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Current Weight</strong></td>
                                                                    <td class="text-right"><?php echo $row['curr_weight']; ?> KG</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Height</strong></td>
                                                                    <td class="text-right"><?php echo isset($row['height']) && $row['height'] > 0 ? $row['height'] . ' Meters' : 'N/A'; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Body Fat</strong></td>
                                                                    <td class="text-right"><?php echo isset($row['fat']) && $row['fat'] > 0 ? $row['fat'] . ' %' : 'N/A'; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="2" class="text-center" style="background:#f8fafc;">
                                                                        <strong>BMI Calculation:</strong><br>
                                                                        <?php
                                                                        $bmi = 0;
                                                                        $bmiText = "N/A";
                                                                        $bmiColor = "#64748b";
                                                                        if (isset($row['height']) && $row['height'] > 0 && isset($row['curr_weight']) && $row['curr_weight'] > 0) {
                                                                            $heightM = $row['height'];
                                                                            $bmi = $row['curr_weight'] / ($heightM * $heightM);

                                                                            if ($bmi < 18.5) {
                                                                                $bmiText = "Underweight";
                                                                                $bmiColor = "#ea580c";
                                                                            } else if ($bmi >= 18.5 && $bmi <= 24.9) {
                                                                                $bmiText = "Normal";
                                                                                $bmiColor = "#16a34a";
                                                                            } else if ($bmi >= 25 && $bmi <= 29.9) {
                                                                                $bmiText = "Overweight";
                                                                                $bmiColor = "#ca8a04";
                                                                            } else {
                                                                                $bmiText = "Obese";
                                                                                $bmiColor = "#dc2626";
                                                                            }
                                                                            echo "<span style='font-size:16px; font-weight:bold; color:#333;'>" . number_format($bmi, 1) . "</span><br>";
                                                                            echo "<span style='color: " . $bmiColor . "; font-weight:bold;'>" . $bmiText . "</span>";
                                                                        } else {
                                                                            echo "<span style='color:#ca8a04;'>Height/Weight required</span>";
                                                                        }
                                                                        ?>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Body Measurements Section -->
                                            <div class="span6">
                                                <div class="widget-box" style="border:1px solid #e2e8f0; border-radius:8px;">
                                                    <div class="widget-title" style="background:#f8fafc; border-bottom:1px solid #e2e8f0; border-radius:8px 8px 0 0;">
                                                        <span class="icon"><i class="fas fa-ruler-combined" style="color:#ea580c;"></i></span>
                                                        <h5 style="color:#334155;">Body Measurements (CM)</h5>
                                                    </div>
                                                    <div class="widget-content" style="background:#fff; border-radius:0 0 8px 8px; padding:15px;">
                                                        <table class="table table-bordered table-striped">
                                                            <tbody>
                                                                <tr>
                                                                    <td><strong>Chest</strong></td>
                                                                    <td class="text-right"><?php echo isset($row['chest']) && $row['chest'] > 0 ? $row['chest'] . ' CM' : 'N/A'; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Waist</strong></td>
                                                                    <td class="text-right"><?php echo isset($row['waist']) && $row['waist'] > 0 ? $row['waist'] . ' CM' : 'N/A'; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Neck</strong></td>
                                                                    <td class="text-right"><?php echo isset($row['neck']) && $row['neck'] > 0 ? $row['neck'] . ' CM' : 'N/A'; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Hip</strong></td>
                                                                    <td class="text-right"><?php echo isset($row['hip']) && $row['hip'] > 0 ? $row['hip'] . ' CM' : 'N/A'; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Arms</strong></td>
                                                                    <td class="text-right"><?php echo isset($row['arms']) && $row['arms'] > 0 ? $row['arms'] . ' CM' : 'N/A'; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Thighs</strong></td>
                                                                    <td class="text-right"><?php echo isset($row['thigh']) && $row['thigh'] > 0 ? $row['thigh'] . ' CM' : 'N/A'; ?></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end of span 8 -->

                                </div>

                                <div class="row-fluid">
                                    <div class="pull-left">
                                        <br>

                                        <h4>GYM Member: <strong><?php echo $row['fullname']; ?></strong></h4>
                                        <p style="font-size: 14px;">Thank you for choosing our services.</p>
                                    </div>
                                    <div class="pull-right text-center">
                                        <h4>Approved By:</h4>
                                        <img src="../../img/report/stamp-sample.png" width="124px;" alt="Stamp">
                                        <p class="text-center" style="font-size: 12px; color: #888;">Note: AutoGenerated</p>
                                    </div>
                                </div>

                            </div>
                    </div>

                </div>

            </div>
        <?php
                        }
        ?>
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
    </script>
</body>

</html>