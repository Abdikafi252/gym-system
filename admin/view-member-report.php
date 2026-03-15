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
  <?php $page = "member-repo";
  include 'includes/sidebar.php' ?>
  <!--sidebar-menu-->

  <div id="content">
    <div id="content-header">
      <div id="breadcrumb"> <a href="index.php" title="Go to Home" class="tip-bottom"><i class="fas fa-home"></i> Home</a> <a href="member-report.php" class="current">Member Reports</a> </div>
      <h1 class="text-center">Member's Report <i class="fas fa-file"></i></h1>
    </div>
    <div class="container-fluid print-container">
      <div class="row-fluid">
        <div class="span12">
          <div class="widget-box">
            <?php
            include 'dbcon.php';
            $id = $_GET['id'];
            $qry = "select * from members where user_id='$id'";
            $result = mysqli_query($conn, $qry);
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
                    <table class="table table-bordered table-invoice-full">
                      <thead>
                        <tr>
                          <th class="head0">Membership ID</th>
                          <th class="head1">Services Taken</th>
                          <th class="head0 right">My Plans (Upto)</th>
                          <th class="head1 right">Address</th>
                          <th class="head0 right">Charge</th>
                          <th class="head0 right">Attendance Count</th>
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
                            <div class="text-center"><?php if ($row['plan'] == 0) {
                                                        echo 'NONE';
                                                      } else {
                                                        echo $row['plan'] . ' Month/s';
                                                      } ?></div>
                          </td>
                          <td>
                            <div class="text-center"><?php echo $row['address']; ?></div>
                          </td>
                          <td>
                            <div class="text-center"><?php echo '$' . $row['amount']; ?></div>
                          </td>
                          <td>
                            <div class="text-center"><?php echo $row['attendance_count']; ?> Day/s</div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    <?php
                    // Calculation of Base amount
                    $base_amount = $row['amount'];
                    if (isset($row['discount_type']) && $row['discount_type'] == 'percent') {
                      if (isset($row['discount_amount']) && $row['discount_amount'] > 0 && $row['discount_amount'] < 100) {
                        $base_amount = $row['amount'] / (1 - ($row['discount_amount'] / 100));
                      }
                    } else {
                      $base_amount = $row['amount'] + (isset($row['discount_amount']) ? $row['discount_amount'] : 0);
                    }
                    $discount_in_dollars = $base_amount - $row['amount'];
                    $paid = isset($row['paid_amount']) ? $row['paid_amount'] : $row['amount'];
                    $remaining = max(0, $row['amount'] - $paid);
                    ?>
                    <table class="table table-bordered table-invoice-full">
                      <thead>
                        <tr>
                          <th class="head0 right">Total Amount</th>
                          <th class="head1 right">Discount</th>
                          <th class="head0 right">Paid Amount</th>
                          <th class="head1 right">Remaining Balance</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>
                            <div class="text-center">$<?php echo number_format($base_amount, 2); ?></div>
                          </td>
                          <td>
                            <div class="text-center">$<?php echo number_format($discount_in_dollars, 2); ?></div>
                          </td>
                          <td>
                            <div class="text-center">$<?php echo number_format($paid, 2); ?></div>
                          </td>
                          <td>
                            <div class="text-center">$<?php echo number_format($remaining, 2); ?></div>
                          </td>
                        </tr>
                      </tbody>
                    </table>

                    <div class="text-center" style="margin-top: 15px;">
                      <em><a href="#" class="tip-bottom" title="Registration Date" style="font-size:15px; color:#555;">Member Since: <?php echo $row['dor']; ?> </a></em>
                    </div>
                  </div> <!-- end of span 12 -->

                </div>

                <div class="row-fluid">
                  <div class="pull-left">
                    <h4>Xubinta <?php echo $row['fullname']; ?>,<br /> <br /> Xubinnimadu hadda waa <?php echo $row['status'] == 'Active' ? 'Shaqaynaysaa' : 'Waqtigu ka dhacay'; ?>! <br /></h4>
                    <p>Waad ku mahadsantahay doorashada adeegyadayada.</p>
                  </div>
                  <div class="pull-right">
                    <h4><span>Approved By:</span></h4>
                    <img src="../img/report/stamp-sample.png" style="width: 124px;" alt="Stamp">
                    <p class="text-center">Note: AutoGenerated</p>
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

    <div class="text-center d-print-none">
      <a href="members-report.php" class="btn btn-info"><i class="fas fa-arrow-left"></i> Ku Noqo Menuga</a>
      <button class="btn btn-danger" onclick="window.print()"><i class="fas fa-print"></i> Daabac</button>
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