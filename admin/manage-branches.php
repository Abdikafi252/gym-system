<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Manage Branches | M*A GYM System</title>
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
                <a href="manage-branches.php" class="current">Manage Branches</a>
            </div>
            <h1 class="text-center">Manage Gym Branches <i class="fas fa-building"></i></h1>
        </div>
        <div class="container-fluid">
            <hr>
            <div class="row-fluid">
                <div class="span12">
                    <a href="add-branch.php"><button class="btn btn-primary">Add New Branch</button></a>
                    <div class='widget-box'>
                        <div class='widget-title'> <span class='icon'> <i class='fas fa-th'></i> </span>
                            <h5>Branches Table</h5>
                        </div>
                        <div class='widget-content nopadding'>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Branch Name</th>
                                        <th>Address</th>
                                        <th>Contact</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    include 'dbcon.php';
                                    $qry = "SELECT * FROM branches ORDER BY id DESC";
                                    $result = mysqli_query($con, $qry);
                                    $i = 1;
                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>
                                            <td>" . $i++ . "</td>
                                            <td>" . htmlspecialchars($row['branch_name']) . "</td>
                                            <td>" . htmlspecialchars($row['address']) . "</td>
                                            <td>" . htmlspecialchars($row['contact']) . "</td>
                                            <td>
                                                <a href='edit-branch.php?id=" . $row['id'] . "' class='btn btn-warning btn-mini'><i class='fas fa-edit'></i> Edit</a>
                                                <a href='actions/delete-branch.php?id=" . $row['id'] . "' class='btn btn-danger btn-mini' onclick='return confirm(\"Are you sure you want to delete this branch?\")'><i class='fas fa-trash'></i> Delete</a>
                                            </td>
                                        </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5' class='text-center'>No branches found.</td></tr>";
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

    <?php include 'includes/footer.php'; ?>

    <script src="../js/jquery.min.js"></script>
    <script src="../js/jquery.ui.custom.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/matrix.js"></script>
</body>

</html>