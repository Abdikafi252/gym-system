<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Gym System Admin</title>
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
    <?php $page = 'packages';
    include '../includes/sidebar.php' ?>
    <!--sidebar-menu-->

    <?php
    include 'dbcon.php';
    $id = $_GET['id'];
    $qry = "SELECT * FROM packages WHERE id='$id'";
    $result = mysqli_query($con, $qry);
    $row = mysqli_fetch_array($result);
    ?>

    <div id="content">
        <div id="content-header">
            <div id="breadcrumb"> <a href="index.php" title="Go to Home" class="tip-bottom"><i class="fas fa-home"></i> Home</a> <a href="packages.php" class="tip-bottom">Packages</a> <a href="#" class="current">Edit Package</a> </div>
            <h1>Edit Package</h1>
        </div>
        <div class="container-fluid">
            <hr>
            <div class="row-fluid">
                <div class="span6">
                    <div class="widget-box">
                        <div class="widget-title"> <span class="icon"> <i class="fas fa-edit"></i> </span>
                            <h5>Edit Package Info</h5>
                        </div>
                        <div class="widget-content nopadding">
                            <form action="edit-package.php?id=<?php echo $id; ?>" method="POST" class="form-horizontal">
                                <div class="control-group">
                                    <label class="control-label">Package Name :</label>
                                    <div class="controls">
                                        <input type="text" class="span11" name="packagename" value="<?php echo $row['packagename']; ?>" required />
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">Duration (Months) :</label>
                                    <div class="controls">
                                        <input type="number" class="span11" name="duration" value="<?php echo $row['duration']; ?>" required />
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">Amount :</label>
                                    <div class="controls">
                                        <div class="input-append">
                                            <span class="add-on">$</span>
                                            <input type="number" class="span11" name="amount" value="<?php echo $row['amount']; ?>" required />
                                        </div>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">Description :</label>
                                    <div class="controls">
                                        <input type="text" class="span11" name="description" value="<?php echo $row['description']; ?>" />
                                    </div>
                                </div>
                                <div class="form-actions text-center">
                                    <button type="submit" name="update" class="btn btn-success">Update Package</button>
                                    <a href="packages.php" class="btn btn-primary">Go Back</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    if (isset($_POST['update'])) {
        $packagename = $_POST['packagename'];
        $duration = $_POST['duration'];
        $amount = $_POST['amount'];
        $description = $_POST['description'];

        $update_qry = "UPDATE packages SET packagename='$packagename', duration='$duration', amount='$amount', description='$description' WHERE id='$id'";
        $result = mysqli_query($con, $update_qry);

        if ($result) {
            echo "<script>alert('Package Updated Successfully!'); window.location.href='packages.php';</script>";
        } else {
            echo "<script>alert('Error Updating Package!');</script>";
        }
    }
    ?>

    <div class="row-fluid">
        <div id="footer" class="span12"> <?php echo date("Y"); ?> &copy; M*A GYM System Developed By Abdikafi</div>
    </div>

    <style>
        #footer {
            color: white;
        }
    </style>

    <script src="../../js/jquery.min.js"></script>
    <script src="../../js/jquery.ui.custom.js"></script>
    <script src="../../js/bootstrap.min.js"></script>
    <script src="../../js/jquery.uniform.js"></script>
    <script src="../../js/select2.min.js"></script>
    <script src="../../js/matrix.js"></script>
</body>

</html>