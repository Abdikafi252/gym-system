<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
    exit();
}
?>
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
    <link href="../../font-awesome/css/fontawesome.css" rel="stylesheet" />
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

    <!--sidebar-menu-->
    <?php $page = 'announcement';
    include '../includes/sidebar.php' ?>
    <!--sidebar-menu-->
    <div id="content">
        <div id="content-header">
            <div id="breadcrumb"> <a href="index.php" title="Tag Bogga Hore" class="tip-bottom"><i class="fas fa-home"></i> Bogga Hore</a><a href="announcement.php" class="current">Ogeysiisyada</a> </div>
            <h1>Ogeysiiska</h1>
        </div>
        <div class="container-fluid">
            <hr>
            <?php if (in_array($_SESSION['designation'], ['Manager', 'Cashier', 'Trainer', 'Trainer Assistant'])) { ?>
                <a href="manage-announcement.php"><button class="btn btn-danger" type="button">Maamul Ogeysiisyadaada</button></a>
                <div class="row-fluid">
                    <div class="widget-box">
                        <div class="widget-title"> <span class="icon"> <i class="fas fa-align-justify"></i> </span>
                            <h5>Samee Ogeysiisyo</h5>
                        </div>
                        <div class="widget-content">
                            <div class="control-group">
                                <form action="post-announcement.php" method="POST">
                                    <div class="controls">
                                        <textarea class="span12" name="message" rows="6" placeholder="Halkan ku qor qoraalka ..."></textarea>
                                    </div>
                                    <div class="controls">
                                        <h5><label for="Announce Date">Taariikhda:
                                                <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>"></h5> </label>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-info btn-large">Faafi Hadda</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <div class="row-fluid">
                <div class="span12">
                    <div class="widget-box">
                        <div class="widget-title"> <span class="icon"> <i class="fas fa-bullhorn"></i> </span>
                            <h5>Ogeysiisyada Xarunta</h5>
                        </div>
                        <div class="widget-content nopadding">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Taariikhda</th>
                                        <th>Fariinta</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    include "dbcon.php";
                                    $branch_id = $_SESSION['branch_id'];
                                    $qry = "SELECT * FROM announcements WHERE branch_id = '$branch_id' ORDER BY date DESC";
                                    $result = mysqli_query($conn, $qry);
                                    $cnt = 1;
                                    while ($row = mysqli_fetch_array($result)) {
                                        echo "<tr>";
                                        echo "<td>" . $cnt . "</td>";
                                        echo "<td>" . $row['date'] . "</td>";
                                        echo "<td>" . $row['message'] . "</td>";
                                        echo "</tr>";
                                        $cnt++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>


    <!--end-main-container-part-->

    <!--Footer-part-->

    <div class="row-fluid">
        <div id="footer" class="span12"> <?php echo date("Y"); ?> &copy; M * A GYM System Developed By Abdikafi </div>
    </div>

    <style>
        #footer {
            color: white;
        }
    </style>
    <!--end-Footer-part-->

    <script src="../../js/jquery.min.js"></script>
    <script src="../../js/jquery.ui.custom.js"></script>
    <script src="../../js/bootstrap.min.js"></script>
    <script src="../../js/matrix.js"></script>
</body>

</html>