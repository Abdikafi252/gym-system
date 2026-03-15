<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//the isset function to check username is already loged in and stored on the session
if (!isset($_SESSION['user_id'])) {
  header('location:../index.php');
  exit();
}
?>
<!-- Visit codeastro.com for more projects -->
<!DOCTYPE html>
<html lang="en">

<head>
  <title>M * A GYM System</title>
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

  <?php $page = "equipment";
  include '../includes/sidebar.php' ?>

  <div id="content">
    <div id="content-header">
      <div id="breadcrumb"> <a href="index.php" title="Tag Bogga Hore" class="tip-bottom"><i class="icon-home"></i> Bogga Hore</a> <a href="#" class="tip-bottom">Maamul Qalabka</a> <a href="#" class="current">Ku dar Qalab</a> </div>
      <h1>Foomka Gelitaanka Qalabka</h1>
    </div>
    <form role="form" action="index.php" method="POST">
      <?php

      if (isset($_POST['name'])) {
        $name = $_POST["name"];
        $amount = $_POST["amount"];
        $vendor = $_POST["vendor"];
        $description = $_POST["description"];
        $date = $_POST["date"];
        $quantity = $_POST["quantity"];
        $address = $_POST["address"];
        $contact = $_POST["contact"];
        $branch_id = $_SESSION['branch_id'];

        include 'dbcon.php';
        //code after connection is successfull
        $qry = "insert into equipment(name,description,amount,vendor,address,contact,date,quantity,branch_id) values ('$name','$description','$amount','$vendor','$address','$contact','$date','$quantity','$branch_id')";
        $result = mysqli_query($conn, $qry); //query executes

        if (!$result) {
          echo "<div class='container-fluid'>";
          echo "<div class='row-fluid'>";
          echo "<div class='span12'>";
          echo "<div class='widget-box'>";
          echo "<div class='widget-title'> <span class='icon'> <i class='icon-info-sign'></i> </span>";
          echo "<h5>Dhambaal Khalad ah</h5>";
          echo "</div>";
          echo "<div class='widget-content'>";
          echo "<div class='error_ex'>";
          echo "<h1 style='color:maroon;'>Khalad 404</h1>";
          echo "<h3>Khalad ayaa dhacay intii lagu guda jiray gelinta faahfaahintaada</h3>";
          echo "<p>Fadlan isku day markale</p>";
          echo "<a class='btn btn-warning btn-big'  href='equipment-entry.php'>Dib u noqo</a> </div>";
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
          echo "<div class='widget-title'> <span class='icon'> <i class='icon-info-sign'></i> </span>";
          echo "<h5>Dhambaal</h5>";
          echo "</div>";
          echo "<div class='widget-content'>";
          echo "<div class='error_ex'>";
          echo "<h1>Guul</h1>";
          echo "<h3>Diiwaanka qalabka waa lagu daray!</h3>";
          echo "<p>Faahfaahintii la codsaday waa lagu daray. Fadlan guji badhanka si aad dib ugu noqoto.</p>";
          echo "<a class='btn btn-inverse btn-big'  href='equipment.php'>Dib u noqo</a> </div>";
          echo "</div>";
          echo "</div>";
          echo "</div>";
          echo "</div>";
          echo "</div>";
        }
      } else {
        echo "<h3>MA ADID FASAXAAD INAAD BOGGAN RAACDO. DIB U NOQO <a href='index.php'> DASHBOARD-KA </a></h3>";
      }


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
</body>

</html>