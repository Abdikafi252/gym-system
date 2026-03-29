<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//the isset function to check username is already loged in and stored on the session
if (!isset($_SESSION['user_id'])) {
    header('location:../../index.php');
}
include "dbcon.php";

$user_id = $_SESSION['user_id'];
$qry = "SELECT * FROM staffs WHERE user_id='$user_id'";
$result = mysqli_query($con, $qry);
$row = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>M*A GYM System</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../../css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="../../css/matrix-style.css" />
    <link rel="stylesheet" href="../../css/matrix-media.css" />
    <link href="../../font-awesome/css/fontawesome.css" rel="stylesheet" />
    <link href="../../font-awesome/css/all.css" rel="stylesheet" />
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
    <?php $page = 'profile';
    include '../includes/sidebar.php' ?>
    <!--sidebar-menu-->

    <div id="content">
        <div id="content-header">
            <div id="breadcrumb"> <a href="index.php" title="Go to Home" class="tip-bottom"><i class="fas fa-home"></i> Home</a> <a href="profile.php" class="current">Profile Settings</a> </div>
            <h1 class="text-center">Staff Profile Settings <i class="fas fa-user-cog"></i></h1>
        </div>
        <div class="container-fluid">
            <hr>
            <div class="row-fluid">
                <div class="span8 offset2">
                    <div class="widget-box">
                        <div class="widget-title"> <span class="icon"> <i class="fas fa-user-edit"></i> </span>
                            <h5>Update Staff Profile</h5>
                        </div>
                        <div class="widget-content nopadding">
                            <form action="update-profile-req.php" method="POST" class="form-horizontal" enctype="multipart/form-data">
                                <div class="control-group">
                                    <label class="control-label">Current Profile Picture :</label>
                                    <div class="controls">
                                        <?php
                                        $display_photo = !empty($row['photo']) ? "../../img/staff/" . $row['photo'] : "../../img/demo/av1.jpg";
                                        ?>
                                        <img src="<?php echo $display_photo; ?>" alt="Profile" style="width: 100px; height: 100px; border-radius: 50%; border: 3px solid #eee; object-fit: cover;">
                                        <span class="help-block">Note: Profile picture can only be changed by Admin.</span>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label">Full Name :</label>
                                    <div class="controls">
                                        <input type="text" class="span11" name="fullname" value="<?php echo htmlspecialchars($row['fullname']); ?>" required />
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label">Username :</label>
                                    <div class="controls">
                                        <input type="text" class="span11" name="username" value="<?php echo htmlspecialchars($row['username']); ?>" required />
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label">New Password :</label>
                                    <div class="controls">
                                        <input type="password" class="span11" name="password" placeholder="Leave blank to keep current password" />
                                    </div>
                                </div>

                                <div class="form-actions text-center">
                                    <button type="submit" name="update_profile" class="btn btn-success">Update Profile</button>
                                </div>
                            </form>
                        </div>
                    </div>
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

    <script src="../../js/jquery.min.js"></script>
    <script src="../../js/bootstrap.min.js"></script>
    <script src="../../js/matrix.js"></script>
</body>

</html>