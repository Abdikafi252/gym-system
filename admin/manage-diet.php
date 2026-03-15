<?php
session_start();
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

    <!--sidebar-menu-->
    <?php $page = 'diet-plan';
    include 'includes/sidebar.php' ?>
    <!--sidebar-menu-->

    <div id="content">
        <div id="content-header">
            <div id="breadcrumb"> <a href="index.php" title="Go to Home" class="tip-bottom"><i class="fas fa-home"></i> Home</a> <a href="#" class="current">Diet Plans</a> </div>
            <h1>Manage Diet Plans</h1>
        </div>
        <div class="container-fluid">
            <hr>
            <div class="row-fluid">
                <div class="span12">
                    <div class="widget-box">
                        <div class="widget-title"> <span class="icon"> <i class="fas fa-utensils"></i> </span>
                            <h5>Member Diet Plans</h5>
                        </div>
                        <div class="widget-content nopadding">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Member Name</th>
                                        <th>Plan Name</th>
                                        <th>Duration</th>
                                        <th>Plan Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    include "dbcon.php";
                                    $qry = "SELECT members.user_id, members.fullname, members.contact, diet_plans.id as plan_id, diet_plans.plan_name, diet_plans.plan_duration 
                        FROM members 
                        LEFT JOIN diet_plans ON members.user_id = diet_plans.member_id";
                                    $result = mysqli_query($con, $qry);
                                    $cnt = 1;
                                    while ($row = mysqli_fetch_array($result)) {
                                        echo "<tr>";
                                        echo "<td>" . $cnt . "</td>";
                                        echo "<td>" . $row['fullname'] . "<br><small class='text-muted'>" . $row['contact'] . "</small></td>";

                                        if ($row['plan_id']) {
                                            $pName = $row['plan_name'] ? $row['plan_name'] : 'Legacy Plan / Custom';
                                            $pDur = $row['plan_duration'] ? $row['plan_duration'] : 'N/A';
                                            echo "<td>" . $pName . "</td>";
                                            echo "<td>" . $pDur . "</td>";
                                            echo "<td><span class='label label-success'>Assigned</span></td>";
                                            echo "<td><div class='text-center'><a href='edit-diet.php?id=" . $row['user_id'] . "' class='btn btn-info btn-mini' title='Edit Plan'><i class='fas fa-edit'></i> Edit</a> <a href='remove-diet.php?id=" . $row['plan_id'] . "' class='btn btn-danger btn-mini' title='Remove Plan' onclick='return confirm(\"Are you sure you want to remove this diet plan?\")'><i class='fas fa-trash'></i></a></div></td>";
                                        } else {
                                            echo "<td>-</td>";
                                            echo "<td>-</td>";
                                            echo "<td><span class='label label-warning'>Not Assigned</span></td>";
                                            echo "<td><div class='text-center'><a href='add-diet.php?id=" . $row['user_id'] . "' class='btn btn-success btn-mini' title='Assign Plan'><i class='fas fa-plus'></i> Assign</a></div></td>";
                                        }
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

    <!--Footer-part-->
    <div class="row-fluid">
        <div id="footer" class="span12"> <?php echo date("Y"); ?> &copy; M * A GYM System Developed By Abdikafi</div>
    </div>

    <style>
        #footer {
            color: white;
        }
    </style>

    <script src="../js/jquery.min.js"></script>
    <script src="../js/jquery.ui.custom.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/jquery.uniform.js"></script>
    <script src="../js/select2.min.js"></script>
    <script src="../js/jquery.dataTables.min.js"></script>
    <script src="../js/matrix.js"></script>
    <script src="../js/matrix.tables.js"></script>
</body>

</html>