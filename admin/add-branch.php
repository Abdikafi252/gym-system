<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Add Branch | M * A GYM System</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="../css/matrix-style.css" />
    <link rel="stylesheet" href="../css/matrix-media.css" />
    <link href="../font-awesome/css/all.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
</head>

<body>

    <?php include 'includes/header-content.php'; ?>
    <?php include 'includes/topheader.php' ?>
    <?php $page = 'manage-branches';
    include 'includes/sidebar.php' ?>

    <div id="content">
        <div id="content-header">
            <div id="breadcrumb">
                <a href="index.php" title="Go to Home" class="tip-bottom"><i class="fas fa-home"></i> Home</a>
                <a href="manage-branches.php">Manage Branches</a>
                <a href="add-branch.php" class="current">Add Branch</a>
            </div>
            <h1>Add New Gym Branch <i class="fas fa-plus"></i></h1>
        </div>
        <div class="container-fluid">
            <hr>
            <div class="row-fluid">
                <div class="span6">
                    <div class="widget-box">
                        <div class="widget-title"> <span class="icon"> <i class="fas fa-align-justify"></i> </span>
                            <h5>Branch Details</h5>
                        </div>
                        <div class="widget-content nopadding">
                            <form action="actions/add-branch-req.php" method="POST" class="form-horizontal">
                                <div class="control-group">
                                    <label class="control-label">Branch Name :</label>
                                    <div class="controls">
                                        <input type="text" class="span11" name="branch_name" placeholder="Branch Name" required />
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">Address :</label>
                                    <div class="controls">
                                        <textarea class="span11" name="address" placeholder="Branch Address" required></textarea>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">Contact Number :</label>
                                    <div class="controls">
                                        <input type="text" class="span11" name="contact" placeholder="Contact Number" required />
                                    </div>
                                </div>
                                <div class="form-actions text-center">
                                    <button type="submit" class="btn btn-success">Save Branch</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="../js/jquery.min.js"></script>
    <script src="../js/jquery.ui.custom.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/matrix.js"></script>
</body>

</html>