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
    <style>
        .expense-card {
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .expense-card .widget-title {
            background: #111827;
            color: #fff;
        }

        .expense-card .control-label {
            font-weight: 700;
            color: #374151;
        }

        .expense-card .controls input,
        .expense-card .controls select {
            border-radius: 10px;
            border: 1px solid #d1d5db;
            padding: 8px 12px !important;
            background: #ffffff !important;
            color: #111827 !important;
            min-height: 42px;
            height: auto !important;
            line-height: 1.4 !important;
            box-sizing: border-box;
            font-size: 14px;
            vertical-align: middle;
        }

        .expense-card .controls input:focus,
        .expense-card .controls select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
            outline: none;
        }

        .expense-card .controls input::placeholder {
            color: #6b7280;
            opacity: 1;
        }

        .expense-card .controls select option,
        .expense-card .controls optgroup {
            color: #111827;
            background: #ffffff;
        }

        .expense-card .controls select {
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }

        .expense-card .controls input[type="date"] {
            padding-top: 8px !important;
            padding-bottom: 8px !important;
        }

        .expense-card .input-append input {
            color: #111827 !important;
            background: #ffffff !important;
            min-height: 42px;
            line-height: 1.4 !important;
        }

        .expense-card .input-append .add-on {
            color: #374151;
            background: #f3f4f6;
            border-color: #d1d5db;
            height: 42px;
            line-height: 22px;
        }

        .expense-card .controls input::-webkit-input-placeholder {
            color: #6b7280;
        }

        .expense-card .controls input::-moz-placeholder {
            color: #6b7280;
            opacity: 1;
        }

        .salary-box {
            background: #f8fafc;
            border: 1px dashed #93c5fd;
            border-radius: 10px;
            padding: 10px;
            margin: 6px 0 12px;
        }
    </style>
</head>

<body>

    <!--Header-part-->
    <?php include 'includes/header-content.php'; ?>
    <!--close-Header-part-->

    <!--top-Header-menu-->
    <?php include 'includes/topheader.php' ?>
    <!--close-top-Header-menu-->

    <!--sidebar-menu-->
    <?php $page = 'expenses';
    include 'includes/sidebar.php' ?>
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

                    <?php
                    include 'dbcon.php';
                    require_once __DIR__ . '/includes/accounting_engine.php';
                    acc_bootstrap_tables($con);
                    mysqli_query($con, "ALTER TABLE expenses ADD COLUMN IF NOT EXISTS category VARCHAR(50) DEFAULT 'General'");
                    mysqli_query($con, "ALTER TABLE expenses ADD COLUMN IF NOT EXISTS salary_for_staff_id INT NULL");
                    mysqli_query($con, "ALTER TABLE expenses ADD COLUMN IF NOT EXISTS salary_for_name VARCHAR(255) NULL");
                    $staff_qry = mysqli_query($con, "SELECT user_id, fullname, designation FROM staffs ORDER BY fullname ASC");
                    ?>

                    <div class='widget-box expense-card'>
                        <div class='widget-title'> <span class='icon'> <i class='fas fa-th'></i> </span>
                            <h5>Add New Expense</h5>
                        </div>
                        <div class='widget-content nopadding'>
                            <form action='expenses.php' method='POST' class='form-horizontal'>
                                <div class='control-group'>
                                    <label class='control-label'>Expense Name :</label>
                                    <div class='controls'>
                                        <select name="name" id="expense-name" class="span11" required>
                                            <option value="" disabled selected>Select Expense</option>
                                            <optgroup label="Bills">
                                                <option value="Electricity Bill" data-category="Bills">Electricity Bill</option>
                                                <option value="Water Bill" data-category="Bills">Water Bill</option>
                                                <option value="Internet Bill" data-category="Bills">Internet Bill</option>
                                                <option value="Rent Payment" data-category="Bills">Rent Payment</option>
                                                <option value="Generator Fuel" data-category="Bills">Generator Fuel</option>
                                                <option value="Waste Collection Fee" data-category="Bills">Waste Collection Fee</option>
                                            </optgroup>
                                            <optgroup label="Salaries">
                                                <option value="Gym Trainer Salary" data-category="Salaries">Gym Trainer Salary</option>
                                                <option value="Receptionist Salary" data-category="Salaries">Receptionist Salary</option>
                                                <option value="Cleaner Salary" data-category="Salaries">Cleaner Salary</option>
                                                <option value="Security Guard Salary" data-category="Salaries">Security Guard Salary</option>
                                                <option value="Manager Salary" data-category="Salaries">Manager Salary</option>
                                            </optgroup>
                                            <optgroup label="Maintenance">
                                                <option value="Equipment Maintenance" data-category="Maintenance">Equipment Maintenance</option>
                                                <option value="Air Conditioner Repair" data-category="Maintenance">Air Conditioner Repair</option>
                                                <option value="Plumbing Repair" data-category="Maintenance">Plumbing Repair</option>
                                                <option value="Painting & Renovation" data-category="Maintenance">Painting & Renovation</option>
                                            </optgroup>
                                            <optgroup label="Equipment">
                                                <option value="Dumbbells Purchase" data-category="Equipment">Dumbbells Purchase</option>
                                            </optgroup>
                                            <optgroup label="Marketing">
                                                <option value="Facebook Ads" data-category="Marketing">Facebook Ads</option>
                                                <option value="Banner Printing" data-category="Marketing">Banner Printing</option>
                                                <option value="Promotional T-Shirts" data-category="Marketing">Promotional T-Shirts</option>
                                                <option value="Social Media Promotion" data-category="Marketing">Social Media Promotion</option>
                                            </optgroup>
                                            <optgroup label="Administration">
                                                <option value="Software Subscription" data-category="Administration">Software Subscription</option>
                                                <option value="License Renewal" data-category="Administration">License Renewal</option>
                                                <option value="Printing Costs" data-category="Administration">Printing Costs</option>
                                                <option value="Bank Charges" data-category="Administration">Bank Charges</option>
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                                <div class='control-group'>
                                    <label class='control-label'>Category :</label>
                                    <div class='controls'>
                                        <select name="category" id="expense-category" class="span11" required>
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

                                <div class='control-group' id='salary-recipient-wrap'>
                                    <label class='control-label'>Salary For :</label>
                                    <div class='controls'>
                                        <div class='salary-box'>
                                            <select name="salary_for_staff_id" id="salary-for-staff" class="span11">
                                                <option value="">Select Staff</option>
                                                <?php
                                                if ($staff_qry && mysqli_num_rows($staff_qry) > 0) {
                                                    while ($st = mysqli_fetch_assoc($staff_qry)) {
                                                        echo "<option value='" . (int)$st['user_id'] . "'>" . htmlspecialchars($st['fullname']) . " (" . htmlspecialchars($st['designation']) . ")</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                            <span class='help-block'>Optional: if the expense is Salaries, select the staff member who received the payment.</span>
                                        </div>
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
                                <div class='control-group'>
                                    <label class='control-label'>Branch :</label>
                                    <div class='controls'>
                                        <select name="branch_id" class="span11" required>
                                            <option value="" disabled selected>Select Branch</option>
                                            <?php 
                                            $br_res = mysqli_query($con, "SELECT * FROM branches");
                                            while($b = mysqli_fetch_assoc($br_res)) {
                                                echo "<option value='".$b['id']."'>".htmlspecialchars($b['branch_name'])."</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class='form-actions text-center'>
                                    <button type='submit' name='submit' class='btn btn-success'>Add Expense</button>
                                </div>
                            </form>
                        </div>
                        <?php
                        if (isset($_POST['submit'])) {
                            $name = $_POST['name'];
                            $category = $_POST['category'];
                            $amount = $_POST['amount'];
                            $date = $_POST['date'];
                            $salary_for_staff_id = isset($_POST['salary_for_staff_id']) && $_POST['salary_for_staff_id'] !== '' ? (int)$_POST['salary_for_staff_id'] : null;
                            $salary_for_name = null;

                            if ($category === 'Salaries' && !empty($salary_for_staff_id)) {
                                $staff_name_q = mysqli_query($con, "SELECT fullname, designation FROM staffs WHERE user_id='" . $salary_for_staff_id . "' LIMIT 1");
                                if ($staff_name_q && mysqli_num_rows($staff_name_q) > 0) {
                                    $staff_row = mysqli_fetch_assoc($staff_name_q);
                                    $salary_for_name = $staff_row['fullname'];
                                    $staff_designation = $staff_row['designation'];
                                }
                            }

                            $salary_for_name_sql = $salary_for_name ? "'" . mysqli_real_escape_string($con, $salary_for_name) . "'" : "NULL";
                            $salary_for_staff_sql = $salary_for_staff_id ? "'" . $salary_for_staff_id . "'" : "NULL";
                            $branch_id_val = isset($_POST['branch_id']) ? (int)$_POST['branch_id'] : (isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : 0);
                            $qry = "INSERT INTO expenses (name, category, amount, date, salary_for_staff_id, salary_for_name, branch_id) VALUES ('" . mysqli_real_escape_string($con, $name) . "', '" . mysqli_real_escape_string($con, $category) . "', '" . mysqli_real_escape_string($con, $amount) . "', '" . mysqli_real_escape_string($con, $date) . "', $salary_for_staff_sql, $salary_for_name_sql, $branch_id_val)";
                            $result = mysqli_query($con, $qry);

                            if ($result) {
                                $expense_id = mysqli_insert_id($con);
                                $expense_acc_code = acc_expense_account_code_from_category($category);
                                $accMemo = 'Expense: ' . $name . ' (' . $category . ')';
                                // If Salaries and not marked as paid, record as liability (Salaries Payable)
                                if ($category === 'Salaries' && empty($_POST['salary_paid'])) {
                                    acc_create_entry_once(
                                        $con,
                                        $date,
                                        $accMemo . ' (Accrued)',
                                        'expense',
                                        (string)$expense_id,
                                        [
                                            ['account_code' => '5000', 'debit' => (float)$amount, 'credit' => 0, 'line_memo' => $accMemo . ' (Accrued)'],
                                            ['account_code' => '2100', 'debit' => 0, 'credit' => (float)$amount, 'line_memo' => $accMemo . ' (Accrued Liability)']
                                        ],
                                        0,
                                        $branch_id_val,
                                        0,
                                        'Admin'
                                    );
                                } else {
                                    acc_create_entry_once(
                                        $con,
                                        $date,
                                        $accMemo,
                                        'expense',
                                        (string)$expense_id,
                                        [
                                            ['account_code' => $expense_acc_code, 'debit' => (float)$amount, 'credit' => 0, 'line_memo' => $accMemo],
                                            ['account_code' => '1000', 'debit' => 0, 'credit' => (float)$amount, 'line_memo' => $accMemo]
                                        ],
                                        0,
                                        $branch_id_val,
                                        0,
                                        'Admin'
                                    );
                                }

                                if ($category === 'Salaries' && !empty($salary_for_name)) {
                                    $salary_note = "Salary paid to " . $salary_for_name . " (" . ($staff_designation ?? 'Staff') . ") - Amount: $" . $amount . " on " . $date;
                                    mysqli_query($con, "CREATE TABLE IF NOT EXISTS announcements (
                                      id INT AUTO_INCREMENT PRIMARY KEY,
                                      message TEXT NOT NULL,
                                      date DATE NOT NULL
                                    )");
                                    $safe_note = mysqli_real_escape_string($con, $salary_note);
                                    mysqli_query($con, "INSERT INTO announcements(message, date) VALUES('$safe_note', CURDATE())");
                                }

                                echo "<div class='container-fluid'>";
                                echo "<div class='row-fluid'>";
                                echo "<div class='span12'>";
                                echo "<div class='alert alert-success alert-block'> <a class='close' data-dismiss='alert' href='#'>×</a>";
                                echo "<h4 class='alert-heading'>Success!</h4>";
                                echo "Expense added successfully.";
                                if ($category === 'Salaries' && !empty($salary_for_name)) {
                                    echo "<br><strong>Message sent:</strong> " . htmlspecialchars($salary_for_name) . " has been paid the salary.";
                                }
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

                    <div class="widget-box expense-card">
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
                                        <th>Paid To (Staff)</th>
                                        <th>Branch</th>
                                        <th>Amount</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    include "dbcon.php";
                                    $branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : 0;
                                    $branch_where = $branch_id > 0 ? " WHERE branch_id = " . $branch_id : "";
                                    $qry = "SELECT * FROM expenses" . $branch_where . " ORDER BY date DESC";
                                    $result = mysqli_query($con, $qry);
                                    $cnt = 1;
                                    while ($row = mysqli_fetch_array($result)) {
                                        echo "<tr>";
                                        echo "<td>" . $cnt . "</td>";
                                        echo "<td>" . $row['name'] . "</td>";
                                        echo "<td><span class='label label-info'>" . $row['category'] . "</span></td>";
                                        echo "<td>" . $row['date'] . "</td>";
                                        $paid_to = !empty($row['salary_for_name']) ? htmlspecialchars($row['salary_for_name']) : '-';
                                        echo "<td>" . $paid_to . "</td>";
                                        $bid = (int)$row['branch_id'];
                                        $br_n = mysqli_query($con, "SELECT branch_name FROM branches WHERE id='$bid'");
                                        $br_r = mysqli_fetch_assoc($br_n);
                                        echo "<td>" . htmlspecialchars($br_r ? $br_r['branch_name'] : 'System') . "</td>";
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
    <!-- <script src="../js/jquery.uniform.js"></script> -->
    <!-- <script src="../js/select2.min.js"></script> -->
    <script src="../js/jquery.dataTables.min.js"></script>
    <script src="../js/matrix.js"></script>
    <script src="../js/matrix.tables.js"></script>
    <script>
        (function() {
            var expenseName = document.getElementById('expense-name');
            var category = document.getElementById('expense-category');
            var staffSelect = document.getElementById('salary-for-staff');

            function syncCategoryFromExpense() {
                if (!expenseName || !category) return;
                var selectedOption = expenseName.options[expenseName.selectedIndex];
                if (selectedOption && selectedOption.getAttribute('data-category')) {
                    category.value = selectedOption.getAttribute('data-category');
                }
                salaryRequirementRule();
            }

            function salaryRequirementRule() {
                if (!category || !staffSelect) return;
                staffSelect.required = category.value === 'Salaries';
            }

            if (expenseName) {
                expenseName.addEventListener('change', syncCategoryFromExpense);
            }

            if (category) {
                category.addEventListener('change', salaryRequirementRule);
                salaryRequirementRule();
            }
        })();
    </script>
</body>

</html>