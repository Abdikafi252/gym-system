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
  <?php $page = 'payment';
  include 'includes/sidebar.php' ?>
  <!--sidebar-menu-->

  <?php
  include 'dbcon.php';
  $id = $_GET['id'];
  $qry = "select * from members where user_id='$id'";
  $result = mysqli_query($conn, $qry);
  while ($row = mysqli_fetch_array($result)) {
  ?>

    <div id="content">
      <div id="content-header">
        <div id="breadcrumb"> <a href="index.php" title="Go to Home" class="tip-bottom"><i class="fas fa-home"></i> Home</a> <a href="payment.php">Payments</a> <a href="#" class="current">Invoice</a> </div>
        <h1>Payment Form</h1>
      </div>


      <div class="container-fluid" style="margin-top:-38px;">
        <div class="row-fluid">
          <div class="span12">
            <div class="widget-box">
              <div class="widget-title"> <span class="icon"> <i class="fas fa-money"></i> </span>
                <h5>Payment</h5>
              </div>
              <div class="widget-content">
                <div class="row-fluid">
                  <div class="span5">
                    <table class="">
                      <tbody>
                        <tr>
                          <td><img src="../img/logo.jpg" alt="Gym Logo" style="width:130px;height:130px;border-radius:50%;border:4px dashed #dc2626;padding:10px;object-fit:contain;transform:rotate(-12deg);opacity:.9;box-shadow:0 0 0 6px rgba(220,38,38,.12);"></td>
                        </tr>
                        <tr>
                          <td>
                            <h4>GYM System</h4>
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
                            <td>Service:</td>
                            <input type="hidden" name="services" value="<?php echo $row['services']; ?>">
                            <td><strong><?php echo $row['services']; ?></strong></td>
                          </tr>
                          <tr>
                            <td>Monthly Amount:</td>
                            <td><input id="amount" type="number" name="amount" value='<?php echo $row['amount']; ?>' /></td>
                          </tr>

                          <input type="hidden" name="paid_date" value="<?php echo $row['paid_date']; ?>">

                          <td class="width30">Plan:</td>
                          <td class="width70">
                            <div class="controls">
                              <select name="plan" required="required" id="select">
                                <option value="1" selected="selected">1 Month</option>
                                <option value="3">3 Months</option>
                                <option value="6">6 Months</option>
                                <option value="12">1 Year</option>
                                <option value="0">No Expiry</option>

                              </select>
                            </div>



                          </td>

                          <tr>

                          </tr>
                          <td class="width30">Member Status:</td>
                          <td class="width70">
                            <div class="controls">
                              <select name="status" required="required" id="select">
                                <option value="Active" selected="selected">Active</option>
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
                    </div>

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