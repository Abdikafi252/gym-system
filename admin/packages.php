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
    <link rel="stylesheet" href="../css/system-polish.css" />
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
    <?php $page = 'packages';
    include 'includes/sidebar.php' ?>
    <!--sidebar-menu-->

    <div id="content">
        <div id="content-header">
            <div id="breadcrumb"> <a href="index.php" title="Go to Home" class="tip-bottom"><i class="fas fa-home"></i> Home</a> <a href="#" class="current">Manage Packages</a> </div>
            <h1>Packages</h1>
        </div>
        <div class="container-fluid">
            <hr>
            <div class="row-fluid">
                <div class="span12">

                    <div class='widget-box'>
                        <div class='widget-title'> <span class='icon'> <i class='fas fa-th'></i> </span>
                            <h5>Add New Package</h5>
                        </div>
                        <div class='widget-content nopadding'>
                            <form action='packages.php' method='POST' class='form-horizontal'>
                                <div class='control-group'>
                                    <label class='control-label'>Package Name :</label>
                                    <div class='controls'>
                                        <input type='text' class='span11' name='packagename' placeholder='e.g. 1 Month standard' required />
                                    </div>
                                </div>
                                <div class='control-group'>
                                    <label class='control-label'>Duration (Months) :</label>
                                    <div class='controls'>
                                        <input type='number' class='span11' name='duration' placeholder='e.g. 1' required />
                                    </div>
                                </div>
                                <div class='control-group'>
                                    <label class='control-label'>Amount :</label>
                                    <div class='controls'>
                                        <div class='input-append'>
                                            <span class='add-on'>$</span>
                                            <input type='number' placeholder='50' name='amount' class='span11' required>
                                        </div>
                                    </div>
                                </div>
                                <div class='control-group'>
                                    <label class='control-label'>Description :</label>
                                    <div class='controls'>
                                        <input type='text' class='span11' name='description' placeholder='Short description' />
                                    </div>
                                </div>
                                <div class='form-actions text-center'>
                                    <button type='submit' name='submit' class='btn btn-success'>Add Package</button>
                                </div>
                            </form>
                        </div>
                        <?php
                        if (isset($_POST['submit'])) {
                            include 'dbcon.php';
                            $packagename = $_POST['packagename'];
                            $duration = $_POST['duration'];
                            $amount = $_POST['amount'];
                            $description = $_POST['description'];

                            $qry = "INSERT INTO packages (packagename, duration, amount, description) VALUES ('$packagename', '$duration', '$amount', '$description')";
                            $result = mysqli_query($con, $qry);

                            if ($result) {
                                echo "<div class='container-fluid'>";
                                echo "<div class='row-fluid'>";
                                echo "<div class='span12'>";
                                echo "<div class='alert alert-success alert-block'> <a class='close' data-dismiss='alert' href='#'>×</a>";
                                echo "<h4 class='alert-heading'>Success!</h4>";
                                echo "Package added successfully.";
                                echo "</div></div></div></div>";
                            } else {
                                echo "<div class='container-fluid'>";
                                echo "<div class='row-fluid'>";
                                echo "<div class='span12'>";
                                echo "<div class='alert alert-error alert-block'> <a class='close' data-dismiss='alert' href='#'>×</a>";
                                echo "<h4 class='alert-heading'>Error!</h4>";
                                echo "Error adding package: " . mysqli_error($con);
                                echo "</div></div></div></div>";
                            }
                        }
                        ?>
                    </div>

                    <div class="widget-box">
                        <div class="widget-title"> <span class="icon"> <i class="fas fa-th"></i> </span>
                            <h5>Packages List</h5>
                        </div>
                        <!-- Search/Filter Bar for Packages -->
                        <div class="row-fluid">
                            <div class="span6 offset3">
                                <form id="packageSearchForm" class="form-inline" style="margin-bottom: 18px; display: flex; gap: 10px; align-items: center;">
                                    <input type="text" id="packageSearchInput" class="form-control" placeholder="Search by name, duration, or description..." style="flex: 1; min-width: 120px;" />
                                    <button type="button" class="btn btn-info" onclick="filterPackages()"><i class="fas fa-search"></i> Search</button>
                                    <button type="button" class="btn btn-secondary" onclick="resetPackageFilter()"><i class="fas fa-undo"></i> Reset</button>
                                </form>
                            </div>
                        </div>
                        <div class="widget-content nopadding">
                            <table class="table table-bordered table-striped" id="packagesTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Package Name</th>
                                        <th>Duration</th>
                                        <th>Amount</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    include "dbcon.php";
                                    $qry = "SELECT * FROM packages";
                                    $result = mysqli_query($con, $qry);
                                    $cnt = 1;
                                    $errorMsg = '';
                                    if (!$result) {
                                        $errorMsg = 'Error loading packages: ' . htmlspecialchars(mysqli_error($con));
                                    }
                                    if ($errorMsg) {
                                        echo "<tr><td colspan='6'><div class='alert alert-danger' style='margin: 10px;'>$errorMsg</div></td></tr>";
                                    }
                                    $rowCount = 0;
                                    while ($row = mysqli_fetch_array($result)) {
                                        $rowCount++;
                                        echo "<tr>";
                                        echo "<td>" . $cnt . "</td>";
                                        echo "<td>" . htmlspecialchars($row['packagename']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['duration']) . " Month(s)</td>";
                                        echo "<td>$" . htmlspecialchars($row['amount']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                                        echo "<td><div class='text-center'><a href='edit-package.php?id=" . (int)$row['id'] . "'><i class='fas fa-edit' style='color:#28b779'></i> Edit</a> | <a href='remove-package.php?id=" . (int)$row['id'] . "' style='color:#F66;' onclick='return confirm(\"Are you sure?\")'><i class='fas fa-trash'></i> Remove</a></div></td>";
                                        echo "</tr>";
                                        $cnt++;
                                    }
                                    if ($rowCount === 0 && !$errorMsg) {
                                        echo "<tr><td colspan='6'><div class='text-center' style='padding:18px;color:#64748b;'>No packages found yet.</div></td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <script>
                            // Client-side filter for packages
                            function filterPackages() {
                                var input = document.getElementById('packageSearchInput').value.toLowerCase();
                                var table = document.getElementById('packagesTable');
                                var rows = table.querySelectorAll('tbody tr');
                                if (!input) {
                                    rows.forEach(function(row) { row.style.display = ''; });
                                    return;
                                }
                                rows.forEach(function(row) {
                                    var cells = row.querySelectorAll('td');
                                    var name = cells[1]?.textContent.toLowerCase() || '';
                                    var duration = cells[2]?.textContent.toLowerCase() || '';
                                    var desc = cells[4]?.textContent.toLowerCase() || '';
                                    if (name.includes(input) || duration.includes(input) || desc.includes(input)) {
                                        row.style.display = '';
                                    } else {
                                        row.style.display = 'none';
                                    }
                                });
                            }

                            function resetPackageFilter() {
                                document.getElementById('packageSearchInput').value = '';
                                filterPackages();
                            }
                        </script>
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