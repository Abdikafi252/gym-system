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
    <?php $page = 'expenses';
    include '../includes/sidebar.php' ?>
    <!--sidebar-menu-->

    <div id="content">
        <div id="content-header">
            <div id="breadcrumb"> <a href="index.php" title="Go to Home" class="tip-bottom"><i class="fas fa-home"></i> Home</a> <a href="#" class="current">Manage Expenses</a> </div>
            <h1>Expenses</h1>
        </div>
        <div class="container-fluid">
            <hr>
            <div class="row-fluid">
                <div class="span12">

                    <div class='widget-box'>
                        <?php if ($_SESSION['designation'] == 'Cashier'): ?>
                        <div class='widget-title'> <span class='icon'> <i class='fas fa-th'></i> </span>
                            <h5>Add New Expense</h5>
                        </div>
                        <div class='widget-content nopadding text-center' style='padding: 24px;'>
                            <div class='alert alert-info'>
                                <i class='fas fa-info-circle'></i> <strong>View Only:</strong> Cashier role cannot add new expenses.
                            </div>
                        </div>
                        <?php else: ?>
                        <div class='widget-title'> <span class='icon'> <i class='fas fa-th'></i> </span>
                            <h5>Add New Expense</h5>
                        </div>
                        <div class='widget-content nopadding'>
                            <form action='expenses.php' method='POST' class='form-horizontal'>
                                ...existing code...
                            </form>
                        </div>
                        <?php endif; ?>
                        <?php
                        if (isset($_POST['submit'])) {
                            include 'dbcon.php';
                            $name = $_POST['name'];
                            $category = $_POST['category'];
                            $amount = $_POST['amount'];
                            $date = $_POST['date'];
                            $branch_id = $_SESSION['branch_id'];

                            $qry = "INSERT INTO expenses (name, category, amount, date, branch_id) VALUES ('$name', '$category', '$amount', '$date', '$branch_id')";
                            $result = mysqli_query($con, $qry);

                            if ($result) {
                                echo "<div class='container-fluid'>";
                                echo "<div class='row-fluid'>";
                                echo "<div class='span12'>";
                                echo "<div class='alert alert-success alert-block'> <a class='close' data-dismiss='alert' href='#'>×</a>";
                                echo "<h4 class='alert-heading'>Success!</h4>";
                                echo "Expense added successfully.";
                                echo "</div></div></div></div>";
                            } else {
                                echo "<div class='container-fluid'>";
                                echo "<div class='row-fluid'>";
                                echo "<div class='span12'>";
                                echo "<div class='alert alert-error alert-block'> <a class='close' data-dismiss='alert' href='#'>×</a>";
                                echo "<h4 class='alert-heading'>Error!</h4>";
                                echo "Error adding expense: " . mysqli_error($con);
                                echo "</div></div></div></div>";
                            }
                        }
                        ?>
                    </div>

                    <div class="widget-box">
                        <div class="widget-title"> <span class="icon"> <i class="fas fa-th"></i> </span>
                            <h5>Expenses List</h5>
                        </div>
                        <div class="widget-content nopadding">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Expense</th>
                                        <th>Category</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    include "dbcon.php";
                                    $branch_id = $_SESSION['branch_id'];
                                    $qry = "SELECT * FROM expenses WHERE branch_id = '$branch_id' ORDER BY date DESC";
                                    $result = mysqli_query($con, $qry);
                                    $cnt = 1;
                                    while ($row = mysqli_fetch_array($result)) {
                                        echo "<tr>";
                                        echo "<td>" . $cnt . "</td>";
                                        echo "<td>" . $row['name'] . "</td>";
                                        echo "<td><span class='label label-info'>" . $row['category'] . "</span></td>";
                                        echo "<td>" . $row['date'] . "</td>";
                                        echo "<td>$" . $row['amount'] . "</td>";
                                        if ($_SESSION['designation'] == 'Cashier') {
                                            echo "<td><div class='text-center'><span class='text-muted'>View Only</span></div></td>";
                                        } else {
                                            echo "<td><div class='text-center'><a href='remove-expense.php?id=" . $row['id'] . "' style='color:#F66;' onclick='return confirm(\"Are you sure?\")'><i class='fas fa-trash'></i> Remove</a></div></td>";
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
    </div>

    <!--Footer-part-->
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
    <!-- <script src="../../js/jquery.uniform.js"></script> -->
    <!-- <script src="../../js/select2.min.js"></script> -->
    <script src="../../js/jquery.dataTables.min.js"></script>
    <script src="../../js/matrix.js"></script>
    <script src="../../js/matrix.tables.js"></script>
</body>

</html>