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
  <link rel="stylesheet" href="../css/matrix-style.css" />
  <link rel="stylesheet" href="../css/matrix-media.css" />
  <link href="../font-awesome/css/fontawesome.css" rel="stylesheet" />
  <link href="../font-awesome/css/all.css" rel="stylesheet" />
  <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
</head>

<body>

  <!--Header-part-->
  <?php include 'includes/header-content.php'; ?>
  <!--close-Header-part-->

  <!--top-Header-menu-->
  <?php include 'includes/topheader.php' ?>

  
  <!--sidebar-menu-->
  <?php $page = 'staff-management';
  include 'includes/sidebar.php' ?>
  <!--sidebar-menu-->
  <div id="content">
    <div id="content-header">
      <div id="breadcrumb"> <a href="index.php" title="Go to Home" class="tip-bottom"><i class="fas fa-home"></i> Home</a> <a href="staffs.php">Staffs</a> <a href="staffs-entry.php" class="current">Staff Entry</a> </div>
      <h1 class="text-center">GYM's Staff <i class="fas fa-users"></i></h1>
    </div>

    <form role="form" action="index.php" method="POST">
      <?php

      if (isset($_POST['fullname'])) {
        $fullname = $_POST["fullname"];
        $username = $_POST["username"];
        $password = $_POST["password"];
        $email = $_POST["email"];
        $address = $_POST["address"];
        $designation = $_POST["designation"];
        $gender = $_POST["gender"];
        $contact = $_POST["contact"];

        $password = md5($password);

        // Handle Photo Upload
        $photo = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
          $target_dir = "../img/staff/";
          $file_name = time() . "_" . basename($_FILES["image"]["name"]);
          $target_file = $target_dir . $file_name;
          if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $photo = $file_name;
          }
        }

        $branch_id = $_POST['branch_id'];
        $salary = (float)($_POST['salary'] ?? 0);

        include 'dbcon.php';
        require_once 'includes/db_helper.php';
        
        // Ensure created_at and updated_at columns exist
        mysqli_query($con, "ALTER TABLE staffs ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
        mysqli_query($con, "ALTER TABLE staffs ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP");

        $sql = "INSERT INTO staffs (fullname, username, password, email, address, designation, gender, contact, photo, branch_id, salary, created_at) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $params = [$fullname, $username, $password, $email, $address, $designation, $gender, $contact, $photo, $branch_id, $salary];
        $result = safe_query($con, $sql, "sssssssssid", $params);

        if (!$result) {
          $db_error = mysqli_error($con);
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
          echo "<h3>Error occured while submitting your details</h3>";
          echo "<p style='color:red;'>Database Error: $db_error</p>";
          echo "<p>Please Try Again</p>";
          echo "<a class='btn btn-warning btn-big'  href='staffs.php'>Go Back</a> </div>";
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
          echo "<h3>Staff details has been added!</h3>";
          echo "<p>The requested staff details are added to database. Please click the button to go back.</p>";
          echo "<a class='btn btn-inverse btn-big'  href='staffs.php'>Go Back</a> </div>";
          echo "</div>";
          echo "</div>";
          echo "</div>";
          echo "</div>";
          echo "</div>";
        }
        // 
      } else {
        echo "<h3>YOU ARE NOT AUTHORIZED TO REDIRECT THIS PAGE. GO BACK to <a href='index.php'> DASHBOARD </a></h3>";
      }
      ?>
    </form>
  </div>
  </div>
  </div>

  </div>
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
  <script src="../js/jquery.min.js"></script>
  <script src="../js/jquery.ui.custom.js"></script>
  <script src="../js/bootstrap.min.js"></script>
  <script src="../js/jquery.validate.js"></script>
  <script src="../js/jquery.wizard.js"></script>
  <script src="../js/matrix.js"></script>
  <script src="../js/matrix.wizard.js"></script>
</body>

</html>