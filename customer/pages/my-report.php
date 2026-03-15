<?php
session_start();
include 'dbcon.php';
include "session.php";

// Check specifically for user_id to ensure data is fetched
if (!isset($_SESSION['user_id'])) {
  header("location:../index.php");
  exit();
}

$uid = $_SESSION['user_id'];
$qry = "SELECT * FROM members WHERE user_id='$uid'";
$result = mysqli_query($con, $qry);
$row = mysqli_fetch_array($result);

if (!$row) {
  die("Xogtaada lama heli karo. Fadlan la xiriir Maamulka.");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title>M * A GYM System - My Report</title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../css/bootstrap.min.css" />
  <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
  <link rel="stylesheet" href="../css/matrix-style.css" />
  <link rel="stylesheet" href="../css/matrix-media.css" />
  <link rel="stylesheet" href="../css/system-polish.css" />
  <link href="../font-awesome/css/all.css" rel="stylesheet" />
  <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
  <style>
    .report-top-grid {
      display: flex;
      gap: 18px;
      align-items: stretch;
      margin-bottom: 18px;
    }

    .report-brand-card,
    .report-summary-card {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 18px;
      box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
    }

    .report-brand-card {
      padding: 18px 20px;
    }

    .report-brand-title {
      margin: 0 0 8px;
      font-size: 28px;
      font-weight: 800;
      letter-spacing: 0.16em;
      color: #0f172a;
    }

    .report-brand-meta {
      margin: 0;
      color: #475569;
      line-height: 1.7;
      font-size: 14px;
    }

    .report-summary-card {
      padding: 10px;
    }

    .report-actions {
      padding: 0 0 14px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 14px;
    }

    .report-actions .pull-right {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      justify-content: flex-end;
      margin-left: auto;
    }

    .report-actions .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      min-height: 44px;
      padding: 0 18px;
      border-radius: 14px;
      font-weight: 800;
      box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
    }

    .report-btn-back {
      background: linear-gradient(135deg, #e2e8f0 0%, #f8fafc 100%) !important;
      color: #1e293b !important;
      border: 1px solid #d8e1ee !important;
    }

    .report-btn-print {
      background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%) !important;
      color: #ffffff !important;
      border: 1px solid #0f766e !important;
    }

    .report-shell {
      margin-top: 0;
    }

    .report-card {
      border-radius: 20px;
      overflow: hidden;
      border: 1px solid #e5e7eb;
      box-shadow: 0 16px 36px rgba(15, 23, 42, 0.08);
    }

    .report-mini-card {
      margin-top: 15px;
      border: 1px solid #e2e8f0;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 10px 26px rgba(15, 23, 42, 0.05);
    }

    .report-metrics .span3 {
      padding-top: 8px;
      padding-bottom: 8px;
      box-sizing: border-box;
    }

    .report-bottom {
      margin-top: 20px;
      display: flex;
      align-items: center;
    }

    .report-stamp {
      display: inline-block;
      text-align: center;
    }

    .report-stamp img {
      width: 124px;
      display: block;
      margin: 0 auto;
    }

    .report-summary-card .table {
      margin-bottom: 0;
    }

    .report-summary-card .table th,
    .report-summary-card .table td {
      vertical-align: middle;
      white-space: normal;
    }

    @media (max-width: 767px) {
      .report-top-grid {
        flex-direction: column;
        gap: 12px;
      }

      .report-brand-card,
      .report-summary-card {
        border-radius: 16px;
      }

      .report-brand-card {
        padding: 14px 16px;
      }

      .report-brand-title {
        font-size: 22px;
        letter-spacing: 0.12em;
      }

      .report-brand-meta {
        font-size: 13px;
      }

      .report-actions {
        padding: 10px 0 0;
        flex-direction: column;
        align-items: stretch;
      }

      .report-actions .pull-right {
        width: 100%;
        display: grid;
        grid-template-columns: 1fr;
        gap: 10px;
      }

      .report-actions .btn {
        width: 100%;
        text-align: center;
        min-height: 46px;
      }

      .report-shell {
        margin-top: 18px;
      }

      .report-card .widget-content {
        padding: 14px;
      }

      .report-card table td,
      .report-card table th,
      .report-summary-card table td,
      .report-summary-card table th {
        white-space: normal;
      }

      .report-summary-card {
        overflow-x: auto;
      }

      .report-metrics .span3 {
        border-right: 0 !important;
        border-bottom: 1px solid #f1f5f9;
      }

      .report-metrics .span3:last-child {
        border-bottom: 0;
      }

      .report-bottom {
        flex-direction: column;
        gap: 18px;
        align-items: flex-start;
      }

      .report-bottom .span6,
      .report-bottom .text-right {
        text-align: left !important;
      }

      .report-stamp {
        width: 100%;
      }
    }
  </style>
</head>

<body>
  <!--top-Header-menu-->
  <?php include '../includes/topheader.php' ?>
  <!--sidebar-menu-->
  <?php $page = "report";
  include '../includes/sidebar.php' ?>

  <div id="content">
    <div id="content-header">
      <div id="breadcrumb">
        <a href="index.php" title="Go to Home" class="tip-bottom"><i class="fas fa-home"></i> Home</a>
        <a href="my-report.php" class="current">My Report</a>
      </div>
    </div>
    <div class="container-fluid print-container report-shell">
      <div class="row-fluid">
        <div class="span12">
          <div class="widget-box report-card polish-card">

            <div class="widget-content">
              <div class="report-actions d-print-none">
                <div class="pull-right">
                  <a href="index.php" class="btn btn-large report-btn-back"><i class="fas fa-arrow-left"></i> Ku Noqo Menuga</a>
                  <button onclick="window.print();" class="btn btn-large report-btn-print"><i class="fas fa-print"></i> Daabac</button>
                </div>
              </div>

              <div class="report-top-grid">
                <div class="span4 report-brand-card">
                  <h4 class="report-brand-title">M * A</h4>
                  <p class="report-brand-meta">Busley, Bondheere, Mogadishu, Somalia</p>
                  <p class="report-brand-meta">Tel: 252-610-000-000</p>
                  <p class="report-brand-meta">Email: support@M*Agym.com</p>
                </div>

                <div class="span8 report-summary-card">
                  <!-- Membership & Attendance Info -->
                  <table class="table table-bordered table-invoice-full table-striped">
                    <thead>
                      <tr>
                        <th class="head0">Membership ID</th>
                        <th class="head1">Services Taken</th>
                        <th class="head0">Plans (Upto)</th>
                        <th class="head1">Attendance</th>
                        <th class="head0">Member Since</th>
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
                          <div class="text-center"><?php echo $row['plan']; ?> Month/s</div>
                        </td>
                        <td>
                          <div class="text-center">
                            <?php
                            $attendance_qry = mysqli_query($con, "SELECT COUNT(*) as total FROM attendance WHERE user_id='$uid'");
                            $attendance_data = mysqli_fetch_array($attendance_qry);
                            echo '<strong>' . $attendance_data['total'] . '</strong> Day/s';
                            ?>
                          </div>
                        </td>
                        <td>
                          <div class="text-center"><?php echo $row['dor']; ?></div>
                        </td>
                      </tr>
                    </tbody>
                  </table>

                  <!-- Financial Summary -->
                  <div class="widget-box report-mini-card" style="background:#fff;">
                    <div class="widget-title" style="background:#f8fafc; border-bottom:1px solid #e2e8f0; border-radius:16px 16px 0 0;">
                      <span class="icon"><i class="fas fa-wallet" style="color:#16a34a;"></i></span>
                      <h5 style="color:#334155;">Financial Summary</h5>
                    </div>
                    <div class="widget-content" style="background:#fff; border-radius:0 0 16px 16px; padding:15px;">
                      <table class="table table-bordered table-striped" style="margin-bottom:0;">
                        <thead>
                          <tr>
                            <th style="width:60%;">Description</th>
                            <th class="text-right">Amount</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td><strong>TOTAL AMOUNT</strong></td>
                            <td class="text-right" style="color:#1e293b; font-weight:700;">$<?php echo $row['amount'] + $row['discount_amount']; ?></td>
                          </tr>
                          <tr>
                            <td><strong>DISCOUNT</strong></td>
                            <td class="text-right" style="color:#ca8a04; font-weight:700;">$<?php echo $row['discount_amount']; ?></td>
                          </tr>
                          <tr>
                            <td><strong>PAID AMOUNT</strong></td>
                            <td class="text-right" style="color:#16a34a; font-weight:700;">$<?php echo $row['paid_amount']; ?></td>
                          </tr>
                          <tr>
                            <td><strong>REMAINING</strong></td>
                            <td class="text-right" style="color:#dc2626; font-weight:700;">$<?php echo $row['amount'] - $row['paid_amount']; ?></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>

                  <!-- Progress Dashboard -->
                  <div class="row-fluid" style="margin-top: 15px;">
                    <!-- Core Metrics Section -->
                    <div class="span6">
                      <div class="widget-box" style="border:1px solid #e2e8f0; border-radius:16px; overflow:hidden; box-shadow:0 10px 26px rgba(15, 23, 42, 0.05);">
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
                                  if (isset($row['height']) && $row['height'] > 0 && isset($row['curr_weight']) && $row['curr_weight'] > 0) {
                                    $heightM = $row['height'];
                                    $bmi = $row['curr_weight'] / ($heightM * $heightM);

                                    if ($bmi < 18.5) {
                                      $bmiText = "Miisaan yar (Underweight)";
                                      $bmiColor = "#ea580c";
                                    } else if ($bmi >= 18.5 && $bmi <= 24.9) {
                                      $bmiText = "Miisaan caadi (Normal)";
                                      $bmiColor = "#16a34a";
                                    } else if ($bmi >= 25 && $bmi <= 29.9) {
                                      $bmiText = "Miisaan dheeraad (Overweight)";
                                      $bmiColor = "#ca8a04";
                                    } else {
                                      $bmiText = "Cayil (Obese)";
                                      $bmiColor = "#dc2626";
                                    }
                                    echo "<span style='font-size:16px; font-weight:bold; color:#333;'>" . number_format($bmi, 1) . "</span><br>";
                                    echo "<span style='color: " . $bmiColor . "; font-weight:bold;'>" . $bmiText . "</span>";
                                  } else {
                                    echo "<span style='color:#ca8a04;'>Height required for BMI</span>";
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
                      <div class="widget-box" style="border:1px solid #e2e8f0; border-radius:16px; overflow:hidden; box-shadow:0 10px 26px rgba(15, 23, 42, 0.05);">
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

              <div class="row-fluid report-bottom">
                <div class="span6">
                  <h4 style="color:#334155;">Xaaladda Xubinnimada: <span style="color:<?php echo $row['status'] == 'Active' ? '#16a34a' : '#dc2626'; ?>;"><?php echo $row['status'] == 'Active' ? 'Shaqaynaysaa' : 'Waqtigu ka dhacay'; ?></span></h4>
                  <p style="color:#64748b;">Waad ku mahadsantahay doorashada adeegyadayada M * A GYM.</p>
                </div>
                <div class="span6 text-right">
                  <div class="report-stamp">
                    <h5 style="margin-bottom:5px; color:#334155;">Approved By:</h5>
                    <img src="../../img/report/stamp-sample.png" alt="Stamp">
                    <p style="font-size:11px; color:#94a3b8; margin-top:5px;">Note: AutoGenerated Official Report</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
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

      .d-print-none {
        display: none !important;
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