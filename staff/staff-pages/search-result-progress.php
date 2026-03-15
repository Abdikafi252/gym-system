<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
}
include "dbcon.php";
$branch_id = $_SESSION['branch_id'];
$search = $_POST['search'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>M * A GYM System</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../../css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="../../css/matrix-style.css" />
    <link rel="stylesheet" href="../../css/matrix-media.css" />
    <link href="../../font-awesome/css/all.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
</head>

<body>
    <?php include '../includes/header-content.php'; ?>
    <?php include '../includes/header.php' ?>
    <?php $page = "progress";
    include '../includes/sidebar.php' ?>
    <div id="content">
        <div id="content-header">
            <div id="breadcrumb"> <a href="index.php" title="Tag Bogga Hore" class="tip-bottom"><i class="fas fa-home"></i> Bogga Hore</a> <a href="customer-progress.php">Horumarka Macmiilka</a> <a href="#" class="current">Raadinta</a> </div>
            <h1 class="text-center">Natiijada Raadinta Horumarka <i class="fas fa-tasks"></i></h1>
        </div>
        <div class="container-fluid">
            <hr>
            <div class="row-fluid">
                <div class="span12">
                    <div class='widget-box'>
                        <div class='widget-title'> <span class='icon'> <i class='fas fa-th'></i> </span>
                            <h5>Jadwalka Xubnaha</h5>
                        </div>
                        <div class='widget-content nopadding'>
                            <?php
                            $qry = "SELECT * FROM members WHERE (fullname LIKE '%$search%' OR user_id LIKE '%$search%') AND branch_id = '$branch_id'";
                            $result = mysqli_query($conn, $qry);
                            $cnt = 1;
                            if (mysqli_num_rows($result) == 0) {
                                echo "<div class='error_ex'><h1>403</h1><h3>Natiijo lama helin!</h3><p>Ma jiro xubin lala helay '$search' oo ku sugan laantaada.</p><a class='btn btn-danger btn-big' href='customer-progress.php'>Dib u Noqo</a> </div>";
                            } else {
                                echo "<table class='table table-bordered table-hover'>
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Magaca Buuxa</th>
                    <th>Adeegga La Doortay</th>
                    <th>Qorshaha</th>
                    <th>Falka</th>
                  </tr>
                </thead>";
                                while ($row = mysqli_fetch_array($result)) {
                                    echo "<tbody> 
                  <td><div class='text-center'>" . $cnt . "</div></td>
                  <td><div class='text-center'>" . $row['fullname'] . "</div></td>
                  <td><div class='text-center'>" . $row['services'] . "</div></td>
                  <td><div class='text-center'>" . $row['plan'] . " Bilood</div></td>
                  <td><div class='text-center'><a href='update-progress.php?id=" . $row['user_id'] . "'><button class='btn btn-warning btn'> Cusboonaysii Horumarka</button></a></div></td>
                </tbody>";
                                    $cnt++;
                                }
                                echo "</table>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div id="footer" class="span12"> <?php echo date("Y"); ?> &copy; M * A GYM System </div>
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