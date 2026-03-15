<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//the isset function to check username is already loged in and stored on the session
if (!isset($_SESSION['user_id'])) {
  header('location:../index.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Gym System Staff A/C</title>
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
  <?php $page = "payment";
  include '../includes/sidebar.php' ?>
  <!--sidebar-menu-->

  <div id="content">
    <div id="content-header">
      <div id="breadcrumb"> <a href="index.php" title="Tag Bogga Hore" class="tip-bottom"><i class="icon-home"></i> Bogga Hore</a> <a href="payment.php">Lacag Bixinta</a> <a href="#" class="current">Natiijooyinka Raadinta</a> </div>
      <h1 class="text-center">Lacag Bixinta Xubnaha Diiwaangashan <i class="icon icon-group"></i></h1>
    </div>
    <div class="container-fluid">
      <hr>
      <div class="row-fluid">
        <div class="span12">

          <div class='widget-box'>
            <div class='widget-title'> <span class='icon'> <i class='icon-th'></i> </span>
              <h5>Jadwalka Lacag Bixinta Xubnaha</h5>
              <form id="custom-search-form" role="search" method="POST" action="search-result.php" class="form-search form-horizontal pull-right">
                <div class="input-append span12">
                  <input type="text" class="search-query" placeholder="Raadi" name="search" required>
                  <button type="submit" class="btn"><i class="icon-search"></i></button>
                </div>
              </form>
            </div>

            <div class='widget-content nopadding'>
              <?php
              include "dbcon.php";

              $search = isset($_POST['search']) ? trim($_POST['search']) : '';
              $branch_id = $_SESSION['branch_id'];
              $search_esc = mysqli_real_escape_string($con, $search);

              $qry = "SELECT ph.*, m.photo, m.status, m.address
                      FROM payment_history ph
                      LEFT JOIN members m ON m.user_id = ph.user_id
                      WHERE ph.branch_id='$branch_id'
                        AND (
                          ph.fullname LIKE '%$search_esc%'
                          OR ph.services LIKE '%$search_esc%'
                          OR ph.paid_date LIKE '%$search_esc%'
                          OR IFNULL(m.address, '') LIKE '%$search_esc%'
                        )
                      ORDER BY ph.paid_date DESC, ph.id DESC";
              $result = mysqli_query($con, $qry);
              $cnt = 1;

              if ($result && mysqli_num_rows($result) > 0) {
                echo "<table class='table table-bordered table-striped table-hover'>
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Sawirka</th>
                    <th>Magaca Buuxa</th>
                    <th>Taariikhda Lacagta u dambaysay</th>
                    <th>Lacagta</th>
                    <th>Adeegga La Doortay</th>
                    <th>Qorshaha</th>
                    <th>Cinwaanka Macmiilka</th>
                    <th>Falka</th>
                  </tr>
                </thead>";

                while ($row = mysqli_fetch_array($result)) {
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

                  echo "<tbody>
                    <tr>
                      <td><div class='text-center'>" . $cnt . "</div></td>
                      <td><div class='text-center'><img src='" . $photo_path . "' alt='Member' style='width:45px;height:45px;border-radius:50%;object-fit:cover;'></div></td>
                      <td><div class='text-center'>" . htmlspecialchars($row['fullname']) . "</div></td>
                      <td><div class='text-center'>" . htmlspecialchars($row['paid_date']) . "</div></td>
                      <td><div class='text-center'>$" . htmlspecialchars($row['paid_amount']) . "</div></td>
                      <td><div class='text-center'>" . htmlspecialchars($row['services']) . "</div></td>
                      <td><div class='text-center'>" . htmlspecialchars($row['plan']) . " Bilood</div></td>
                      <td><div class='text-center'>" . htmlspecialchars($row['address'] ?? '') . "</div></td>
                      <td><div class='text-center'><a href='user-payment.php?id=" . $row['user_id'] . "'><button class='btn btn-success btn-mini'><i class='icon icon-money'></i> Bixi Lacagta</button></a></div></td>
                    </tr>
                  </tbody>";

                  $cnt++;
                }

                echo "</table>";
              } else {
                echo "<div class='error_ex'>
                  <h3>Opps, Natiijooyin lama helin!!</h3>
                  <p>Waxaa u muuqata inaanay jirin rikoor noocaas ah oo laga heli karo keydka macluumaadkayaga.</p>
                  <a class='btn btn-danger btn-big' href='payment.php'>Dib u Noqo</a>
                </div>";
              }
              ?>
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
  </style>

  <!--end-Footer-part-->

  <style>
    #custom-search-form {
      margin: 0;
      margin-top: 5px;
      padding: 0;
    }

    #custom-search-form .search-query {
      padding-right: 3px;
      padding-right: 4px \9;
      padding-left: 3px;
      padding-left: 4px \9;
      /* IE7-8 doesn't have border-radius, so don't indent the padding */

      margin-bottom: 0;
      -webkit-border-radius: 3px;
      -moz-border-radius: 3px;
      border-radius: 3px;
    }

    #custom-search-form button {
      border: 0;
      background: none;
      /** belows styles are working good */
      padding: 2px 5px;
      margin-top: 2px;
      position: relative;
      left: -28px;
      /* IE7-8 doesn't have border-radius, so don't indent the padding */
      margin-bottom: 0;
      -webkit-border-radius: 3px;
      -moz-border-radius: 3px;
      border-radius: 3px;
    }

    .search-query:focus+button {
      z-index: 3;
    }
  </style>

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