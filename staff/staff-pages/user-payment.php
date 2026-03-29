<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

  <?php
  include 'dbcon.php';
  $id = $_GET['id'];
  $qry = "select * from members where user_id='$id'";
  $result = mysqli_query($conn, $qry);
  while ($row = mysqli_fetch_array($result)) {
    // Fetch the official rate for the member's service
    $service_name = $row['services'];
    $rate_query = mysqli_query($conn, "SELECT charge FROM rates WHERE name = '$service_name'");
    $rate_row = mysqli_fetch_assoc($rate_query);
    $official_rate = ($rate_row && $rate_row['charge']) ? $rate_row['charge'] : $row['amount'];
    $photo_path = '../../img/demo/user-default.png';
    if (!empty($row['photo'])) {
      if (file_exists('../../img/members/' . $row['photo'])) {
        $photo_path = '../../img/members/' . $row['photo'];
      } else if (strpos($row['photo'], 'uploads/') === 0) {
        $photo_path = '../../' . $row['photo'];
      } else if (file_exists('../../uploads/' . $row['photo'])) {
        $photo_path = '../../uploads/' . $row['photo'];
      }
    }

    $status_label = 'Pending';
    if ($row['status'] == 'Active') {
      $status_label = 'Active';
    } else if ($row['status'] == 'Expired') {
      $status_label = 'Expired';
    }
  ?>

    <div id="content">
      <div id="content-header">
        <div id="breadcrumb"> <a href="index.php" title="Go to Home Page" class="tip-bottom"><i class="icon-home"></i> Home</a> <a href="payment.php">Payments</a> <a href="#" class="current">Invoice</a> </div>
        <h1>Payment Form</h1>
      </div>


      <div class="container-fluid" style="margin-top:-38px;">
        <div class="row-fluid">
          <div class="span12">
            <div class="widget-box">
              <div class="widget-title"> <span class="icon"> <i class="icon-money"></i> </span>
                <h5>Payment</h5>
              </div>
              <div class="widget-content">
                <div class="row-fluid">
                  <div class="span5">
                    <table class="">
                      <tbody>
                        <tr>
                          <td><img src="../../img/logo.jpg" alt="Gym Logo" style="width:130px;height:130px;border-radius:50%;border:4px dashed #dc2626;padding:10px;object-fit:contain;transform:rotate(-12deg);opacity:.9;box-shadow:0 0 0 6px rgba(220,38,38,.12);"></td>
                        </tr>
                        <tr>
                          <td>
                            <h4>M * A</h4>
                          </td>
                        </tr>
                        <tr>
                          <td>Busley, Bondheere, Mogadishu, Somalia</td>
                        </tr>

                        <tr>
                          <td>Tell-252-610-000-000</td>
                        </tr>
                        <tr>
                          <td>Email: support@M*Agym.com</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>


                  <div class="span7">
                    <table class="table table-bordered table-invoice">

                      <tbody>
                        <form action="userpay.php" method="POST">
                          <tr>
                          <tr>
                            <td class="width30">Full Name:</td>
                            <input type="hidden" name="fullname" value="<?php echo $row['fullname']; ?>">
                            <td class="width70"><strong><?php echo $row['fullname']; ?></strong></td>
                          </tr>
                          <tr>
                            <td>Customer Image:</td>
                            <td><img src="<?php echo $photo_path; ?>" alt="Member" style="width:70px;height:70px;border-radius:50%;object-fit:cover;"></td>
                          </tr>
                          <tr>
                            <td>Current Member Status:</td>
                            <td><strong><?php echo $status_label; ?></strong></td>
                          </tr>
                          <tr>
                            <td>Service:</td>
                            <input type="hidden" name="services" value="<?php echo $row['services']; ?>">
                            <td><strong><?php echo $row['services']; ?></strong></td>
                          </tr>
                          <tr>
                            <td>Monthly Fee:</td>
                            <td><input id="amount" type="number" name="amount" value='<?php echo $row['amount']; ?>' /></td>
                          </tr>

                          <input type="hidden" name="paid_date" value="<?php echo $row['paid_date']; ?>">

                          <td class="width30">Plan:</td>
                          <td class="width70">
                            <div class="controls">
                              <select name="plan" required="required" id="planSelect">
                                <option value="1" selected="selected">One Month</option>
                                <option value="3">Three Months</option>
                                <option value="6">Six Months</option>
                                <option value="12">One Year</option>
                                <option value="0">Never Expires</option>
                              </select>
                            </div>
                          </td>

                          <tr>

                          </tr>
                          <td class="width30">Member Status:</td>
                          <td class="width70">
                            <div class="controls">
                              <select name="status" required="required" id="select">
                                <option value="Active" <?php echo ($row['status'] == 'Pending') ? 'selected="selected"' : ''; ?>>Active (Accept)</option>
                                <option value="Expired">Expired</option>

                              </select>
                            </div>


                          </td>
                          </tr>
                      </tbody>

                    </table>
                  </div>


                </div> <!-- row-fluid ends here -->


                <div class="row-fluid">
                  <div class="span12">


                    <hr>
                    <div class="text-center">
                      <!-- user's ID is hidden here -->

                      <input type="hidden" name="id" value="<?php echo $row['user_id']; ?>">

                      <button class="btn btn-success btn-large" type="SUBMIT" href="">Pay Now</button>

                      </form>
                    </div><!-- span12 ends here -->
                  </div><!-- row-fluid ends here -->

                <?php
              }
                ?>
                </div><!-- widget-content ends here -->


              </div><!-- widget-box ends here -->
            </div><!-- span12 ends here -->
          </div> <!-- row-fluid ends here -->
        </div> <!-- container-fluid ends here -->
      </div> <!-- div id content ends here -->



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
        // Dynamic Amount Calculation
        document.addEventListener('DOMContentLoaded', function() {
          const planSelect = document.getElementById('planSelect');
          const amountInput = document.getElementById('amount');
          const officialRate = <?php echo (float)$official_rate; ?>;

          planSelect.addEventListener('change', function() {
            const planValue = parseInt(this.value);
            let totalAmount = officialRate;

            if (planValue === 3) {
              totalAmount = officialRate * 3;
            } else if (planValue === 6) {
              totalAmount = officialRate * 6;
            } else if (planValue === 12) {
              totalAmount = officialRate * 12;
            } else if (planValue === 0) {
              totalAmount = officialRate;
            }

            amountInput.value = totalAmount;
          });
        });

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