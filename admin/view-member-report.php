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
  <link rel="stylesheet" href="../css/bootstrap.min.css" />
  <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
  <link rel="stylesheet" href="../css/fullcalendar.css" />
  <link rel="stylesheet" href="../css/matrix-style.css" />
  <link rel="stylesheet" href="../css/matrix-media.css" />
  <link href="../font-awesome/css/fontawesome.css" rel="stylesheet" />
  <link href="../font-awesome/css/all.css" rel="stylesheet" />
  <link rel="stylesheet" href="../css/jquery.gritter.css" />
  <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" href="../css/premium-print.css" />
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
    <div class="container-fluid print-container" id="print-area">
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

              <div class="print-container">
                  <div id="print-area" class="premium-document">
                      <div class="premium-header">
                          <div class="premium-brand">
                              <h1>M*A GYM</h1>
                              <p>Busley, Bondheere, Mogadishu, Somalia</p>
                              <p>Tel: 252-610-000-000 | Email: support@M*Agym.com</p>
                          </div>
                          <div class="premium-meta">
                              <h2>MEMBER REPORT</h2>
                              <p><strong>Member Name:</strong> <?php echo $row['fullname']; ?></p>
                              <p><strong>Member ID:</strong> PGC-SS-<?php echo $row['user_id']; ?></p>
                              <p><strong>Generated On:</strong> <?php echo date("F j, Y"); ?></p>
                          </div>
                      </div>

                      <h3 style="color:#0f172a; margin-bottom:15px; font-size:18px; font-weight:700;">Subscription Details</h3>
                      <table class="premium-table">
                          <thead>
                              <tr>
                                  <th>Services Taken</th>
                                  <th>Duration</th>
                                  <th>Address</th>
                                  <th class="right">Attendance</th>
                              </tr>
                          </thead>
                          <tbody>
                              <tr>
                                  <td><strong><?php echo $row['services']; ?></strong></td>
                                  <td><?php echo $row['plan'] == 0 ? 'NONE' : $row['plan'] . ' Month(s)'; ?></td>
                                  <td><?php echo $row['address']; ?></td>
                                  <td class="right highlight"><?php echo $row['attendance_count']; ?> Day(s)</td>
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
                      
                      <div class="premium-summary-container">
                          <table class="premium-summary-table">
                              <tr>
                                  <td>Base Amount</td>
                                  <td>$<?php echo number_format($base_amount, 2); ?></td>
                              </tr>
                              <tr>
                                  <td>Discount</td>
                                  <td style="color:#ca8a04;">-$<?php echo number_format($discount_in_dollars, 2); ?></td>
                              </tr>
                              <tr>
                                  <td>Amount Paid</td>
                                  <td style="color:#16a34a;">$<?php echo number_format($paid, 2); ?></td>
                              </tr>
                              <tr class="total balance-due">
                                  <td>Balance Due</td>
                                  <td>$<?php echo number_format($remaining, 2); ?></td>
                              </tr>
                          </table>
                      </div>

                      <div class="premium-footer">
                          <div class="premium-notes">
                              <h3>Status: <span style="color: <?php echo $row['status'] == 'Active' ? '#16a34a' : '#dc2626'; ?>"><?php echo $row['status'] == 'Active' ? 'ACTIVE' : 'EXPIRED'; ?></span></h3>
                              <p>Member Since: <?php echo date("F j, Y", strtotime($row['dor'])); ?></p>
                              <p>Thank you for choosing our services.</p>
                          </div>
                      </div>

                      <!-- New Payment History Section -->
                      <h3 style="color:#0f172a; margin-top:30px; margin-bottom:15px; font-size:18px; font-weight:700;">Transaction History</h3>
                      <table class="premium-table">
                          <thead>
                              <tr>
                                  <th>Date</th>
                                  <th>Invoice #</th>
                                  <th>Service</th>
                                  <th>Plan</th>
                                  <th class="right">Amount Paid</th>
                                  <th class="right d-print-none">Action</th>
                              </tr>
                          </thead>
                          <tbody>
                              <?php
                              $history_res = mysqli_query($conn, "SELECT * FROM payment_history WHERE user_id='$id' ORDER BY paid_date DESC");
                              if (mysqli_num_rows($history_res) > 0) {
                                  while ($hrow = mysqli_fetch_assoc($history_res)) {
                              ?>
                                  <tr>
                                      <td><?php echo date("d/m/Y", strtotime($hrow['paid_date'])); ?></td>
                                      <td><?php echo !empty($hrow['invoice_no']) ? $hrow['invoice_no'] : '-'; ?></td>
                                      <td><?php echo $hrow['services']; ?></td>
                                      <td><?php echo $hrow['plan']; ?> Month(s)</td>
                                      <td class="right"><strong>$<?php echo number_format($hrow['paid_amount'], 2); ?></strong></td>
                                      <td class="right d-print-none">
                                          <a href="print-receipt.php?id=<?php echo $hrow['id']; ?>" class="btn btn-mini btn-info" target="_blank"><i class="fas fa-print"></i> Slip</a>
                                      </td>
                                  </tr>
                              <?php
                                  }
                              } else {
                                  echo '<tr><td colspan="6" style="text-align:center;">No payment records found.</td></tr>';
                              }
                              ?>
                          </tbody>
                      </table>

                      <div class="premium-footer">
                          <div class="premium-signature">
                              <img src="../img/report/stamp-sample.png" alt="Official Stamp">
                              <p>Official Record</p>
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

    <div class="text-center d-print-none">
      <a href="members-report.php" class="btn btn-info"><i class="fas fa-arrow-left"></i> Back to Menu</a>
      <button type="button" class="btn btn-danger" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
      <button type="button" class="btn btn-primary" onclick="generatePremiumPDF('Member_Report_<?php echo $id; ?>')"><i class="fas fa-download"></i> Download PDF</button>
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

    function generatePremiumPDF(filename) {
        var element = document.getElementById('print-area');
        var opt = {
            margin:       0,
            filename:     filename + '.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true },
            jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
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