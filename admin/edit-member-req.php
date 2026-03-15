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
  <?php $page = 'members-update';
  include 'includes/sidebar.php' ?>
  <!--sidebar-menu-->
  <div id="content">
    <div id="content-header">
      <div id="breadcrumb"> <a href="index.html" title="Go to Home" class="tip-bottom"><i class="fas fa-home"></i> Home</a> <a href="#" class="tip-bottom">Manamge Members</a> <a href="#" class="current">Add Members</a> </div>
      <h1>Update Member Details</h1>
    </div>
    <form role="form" action="index.php" method="POST">
      <?php

      if (isset($_POST['fullname'])) {
        $fullname = $_POST["fullname"];
        $username = $_POST["username"];
        $dor = $_POST["dor"];
        $gender = $_POST["gender"];
        $services = $_POST["services"];
        $amount = $_POST["amount"];
        $plan = $_POST["plan"];
        $address = $_POST["address"];
        $contact = $_POST["contact"];
        $biometric_id = $_POST["biometric_id"];
        $expiry_date = $_POST["expiry_date"];
        $id = $_POST["id"];

        $batch = isset($_POST["batch"]) ? $_POST["batch"] : '';
        $email = isset($_POST["email"]) ? $_POST["email"] : '';
        $aadhar = isset($_POST["aadhar"]) ? $_POST["aadhar"] : '';
        $pan = isset($_POST["pan"]) ? $_POST["pan"] : '';
        $discount_type = isset($_POST["discount_type"]) ? $_POST["discount_type"] : 'amount';
        $discount_amount = isset($_POST["discount_amount"]) ? $_POST["discount_amount"] : 0;
        $paid_amount = isset($_POST["paid_amount"]) ? $_POST["paid_amount"] : 0;
        $comments = isset($_POST["comments"]) ? $_POST["comments"] : '';
        $trainer_type = isset($_POST["trainer_type"]) ? $_POST["trainer_type"] : 'General Training';

        $totalamount = isset($_POST["total_amount"]) ? $_POST["total_amount"] : ($amount * $plan);
        $branch_id = isset($_POST["branch_id"]) ? $_POST["branch_id"] : 1;

        include 'dbcon.php';

        // Handle Photo Update
        $photo_query_part = "";
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
          // Get old photo to delete
          $old_photo_qry = "SELECT photo FROM members WHERE user_id='$id'";
          $old_photo_res = mysqli_query($con, $old_photo_qry);
          $old_photo_row = mysqli_fetch_array($old_photo_res);
          $old_photo = $old_photo_row['photo'];

          $target_dir = "../img/members/";
          $file_ext = pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION);
          $photo_name = "member_" . time() . "_" . $biometric_id . "." . $file_ext;
          $target_file = $target_dir . $photo_name;

          if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $photo_query_part = ", photo='$photo_name'";
            // Delete old photo file if it exists
            if (!empty($old_photo) && file_exists($target_dir . $old_photo)) {
              unlink($target_dir . $old_photo);
            }
          }
        }

        // update query
        $id_doc_query_part = "";
        $id_doc_type = isset($_POST['id_doc_type']) ? $_POST['id_doc_type'] : '';

        if (!empty($id_doc_type)) {
          $id_doc_query_part .= ", id_doc_type='$id_doc_type'";
        }

        if (isset($_FILES['id_document']) && $_FILES['id_document']['error'] == 0) {
          $id_target_dir = "../img/members/";
          $id_file_ext = pathinfo($_FILES["id_document"]["name"], PATHINFO_EXTENSION);
          $id_doc_name = "iddoc_" . time() . "_" . $biometric_id . "." . $id_file_ext;
          $id_target_file = $id_target_dir . $id_doc_name;

          if (move_uploaded_file($_FILES["id_document"]["tmp_name"], $id_target_file)) {
            $id_doc_query_part .= ", id_document='$id_doc_name'";

            // Delete old document if exists
            if (isset($_POST['existing_id_document']) && !empty($_POST['existing_id_document'])) {
              $old_doc = $_POST['existing_id_document'];
              if (file_exists($id_target_dir . $old_doc)) {
                unlink($id_target_dir . $old_doc);
              }
            }
          }
        } elseif (isset($_POST['remove_id_document']) && $_POST['remove_id_document'] == '1') {
          // User requested removal of the existing document
          $id_doc_query_part .= ", id_document='', id_doc_type=''";
          if (isset($_POST['existing_id_document']) && !empty($_POST['existing_id_document'])) {
            $old_doc = $_POST['existing_id_document'];
            if (file_exists("../img/members/" . $old_doc)) {
              unlink("../img/members/" . $old_doc);
            }
          }
        }

        $qry = "update members set fullname='$fullname', username='$username',dor='$dor', gender='$gender', services='$services', amount='$totalamount', plan='$plan', address='$address', contact='$contact', biometric_id='$biometric_id', expiry_date='$expiry_date', batch='$batch', email='$email', aadhar='$aadhar', pan='$pan', discount_type='$discount_type', discount_amount='$discount_amount', paid_amount='$paid_amount', comments='$comments', trainer_type='$trainer_type', branch_id='$branch_id' $photo_query_part $id_doc_query_part where user_id='$id'";
        $result = mysqli_query($con, $qry); //query executes

        if (!$result) {
          echo "<div class='container-fluid'>";
          echo "<div class='row-fluid'>";
          echo "<div class='span12'>";
          echo "<div class='widget-box'>";
          echo "<div class='widget-title'> <span class='icon'> <i class='fas fa-info'></i> </span>";
          echo "<h5>Error Message</h5>";
          echo "</div>";
          echo "<div class='widget-content'>";
          echo "<div class='error_ex'>";
          echo "<h1 style='color:maroon;'>Error 404</h1>";
          echo "<h3>Error occured while updating your details</h3>";
          echo "<p>Please Try Again</p>";
          echo "<a class='btn btn-warning btn-big'  href='edit-member.php'>Go Back</a> </div>";
          echo "</div>";
          echo "</div>";
          echo "</div>";
          echo "</div>";
          echo "</div>";
        } else {

          echo "<div class='container-fluid'>";
          echo "<div class='row-fluid'>";
          echo "<div class='span12'>";
          echo "<div class='widget-box'>";
          echo "<div class='widget-title'> <span class='icon'> <i class='fas fa-info'></i> </span>";
          echo "<h5>Message</h5>";
          echo "</div>";
          echo "<div class='widget-content'>";
          echo "<div class='error_ex'>";
          echo "<h1>Success</h1>";
          echo "<h3>Member details has been updated!</h3>";
          echo "<p>The requested details are updated. Please click the button to go back.</p>";
          echo "<a class='btn btn-inverse btn-big'  href='members.php'>Go Back</a> </div>";
          echo "</div>";
          echo "</div>";
          echo "</div>";
          echo "</div>";
          echo "</div>";
        }
      } else {
        echo "<h3>YOU ARE NOT AUTHORIZED TO REDIRECT THIS PAGE. GO BACK to <a href='index.php'> DASHBOARD </a></h3>";
      }
      ?>


    </form>
  </div>
  </div>
  </div>
  </div>

  <!--end-main-container-part-->

  <!--Footer-part-->
  <!-- Visit codeastro.com for more projects -->
  <div class="row-fluid">
    <div id="footer" class="span12"> <?php echo date("Y"); ?> &copy; M * A GYM System Developed By Abdikafi</a> </div>
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