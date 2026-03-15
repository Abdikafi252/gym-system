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
                        <div class='widget-title'> <span class='icon'> <i class='fas fa-th'></i> </span>
                            <h5>Add New Expense</h5>
                        </div>
                        <div class='widget-content nopadding'>
                            <form action='expenses.php' method='POST' class='form-horizontal'>
                                <div class='control-group'>
                                    <label class='control-label'>Expense Name :</label>
                                    <div class='controls'>
                                        <select name="name" class="span11" required>
                                            <option value="" disabled selected>Select Expense</option>
                                            <optgroup label="Bills">
                                                <option value="Electricity Bill">Electricity Bill</option>
                                                <option value="Water Bill">Water Bill</option>
                                                <option value="Internet Bill">Internet Bill</option>
                                                <option value="Rent Payment">Rent Payment</option>
                                                <option value="Generator Fuel">Generator Fuel</option>
                                                <option value="Waste Collection Fee">Waste Collection Fee</option>
                                            </optgroup>
                                            <optgroup label="Salaries">
                                                <option value="Gym Trainer Salary">Gym Trainer Salary</option>
                                                <option value="Receptionist Salary">Receptionist Salary</option>
                                                <option value="Cleaner Salary">Cleaner Salary</option>
                                                <option value="Security Guard Salary">Security Guard Salary</option>
                                                <option value="Manager Salary">Manager Salary</option>
                                            </optgroup>
                                            <optgroup label="Maintenance">
                                                <option value="Equipment Maintenance">Equipment Maintenance</option>
                                                <option value="Air Conditioner Repair">Air Conditioner Repair</option>
                                                <option value="Plumbing Repair">Plumbing Repair</option>
                                                <option value="Painting & Renovation">Painting & Renovation</option>
                                            </optgroup>
                                            <optgroup label="Equipment">
                                                <option value="Dumbbells Purchase">Dumbbells Purchase</option>
                                            </optgroup>
                                            <optgroup label="Marketing">
                                                <option value="Facebook Ads">Facebook Ads</option>
                                                <option value="Banner Printing">Banner Printing</option>
                                                <option value="Promotional T-Shirts">Promotional T-Shirts</option>
                                                <option value="Social Media Promotion">Social Media Promotion</option>
                                            </optgroup>
                                            <optgroup label="Administration">
                                                <option value="Software Subscription">Software Subscription</option>
                                                <option value="License Renewal">License Renewal</option>
                                                <option value="Printing Costs">Printing Costs</option>
                                                <option value="Bank Charges">Bank Charges</option>
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                                <div class='control-group'>
                                    <label class='control-label'>Category :</label>
                                    <div class='controls'>
                                        <select name="category" class="span11" required>
                                            <option value="" disabled selected>Select Category</option>
                                            <option value="Bills">Bills</option>
                                            <option value="Salaries">Salaries</option>
                                            <option value="Maintenance">Maintenance</option>
                                            <option value="Equipment">Equipment</option>
                                            <option value="Marketing">Marketing</option>
                                            <option value="Administration">Administration</option>
                                        </select>
                                    </div>
                                </div>
                                <div class='control-group'>
                                    <label class='control-label'>Amount :</label>
                                    <div class='controls'>
                                        <div class='input-append'>
                                            <span class='add-on'>$</span>
                                            <input type='number' placeholder='100' name='amount' class='span11' required>
                                        </div>
                                    </div>
                                </div>
                                <div class='control-group'>
                                    <label class='control-label'>Date :</label>
                                    <div class='controls'>
                                        <input type='date' class='span11' name='date' value="<?php echo date('Y-m-d'); ?>" required />
                                    </div>
                                </div>
                                <div class='form-actions text-center'>
                                    <button type='submit' name='submit' class='btn btn-success'>Add Expense</button>
                                </div>
                            </form>
                        </div>
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
                                        echo "<td><div class='text-center'><a href='remove-expense.php?id=" . $row['id'] . "' style='color:#F66;' onclick='return confirm(\"Are you sure?\")'><i class='fas fa-trash'></i> Remove</a></div></td>";
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
        <div id="footer" class="span12"> <?php echo date("Y"); ?> &copy; M * A GYM System Developed By Abdikafi</div>
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