<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
    exit;
}
include 'dbcon.php';
require_once 'includes/db_helper.php';
require_once 'includes/accounting_engine.php';
require_once 'includes/lang.php';

// Sync accruals on page load to ensure data is fresh
acc_sync_payroll_accruals($con);

$page = 'payroll';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>M*A GYM System - <?php echo __('payroll'); ?></title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="../css/matrix-style.css" />
    <link rel="stylesheet" href="../css/matrix-media.css" />
    <link href="../font-awesome/css/fontawesome.css" rel="stylesheet" />
    <link href="../font-awesome/css/all.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
    <style>
        .salary-badge { background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 4px; font-weight: 700; }
        .debt-badge { background: #fee2e2; color: #991b1b; padding: 4px 8px; border-radius: 4px; font-weight: 700; }
        .last-pay-badge { background: #f1f5f9; color: #475569; padding: 4px 8px; border-radius: 4px; font-size: 11px; }
        .btn-pay { background: #22c55e !important; color: white !important; border: none !important; font-weight: 600; }
        .btn-pay:hover { background: #16a34a !important; }
    </style>
</head>
<body>

<?php include 'includes/header-content.php'; ?>
<?php include 'includes/topheader.php' ?>
<?php include 'includes/sidebar.php' ?>

<div id="content">
    <div id="content-header">
        <div id="breadcrumb"> <a href="index.php" title="Go to Home" class="tip-bottom"><i class="fas fa-home"></i> <?php echo __('dashboard'); ?></a> <a href="#" class="current"><?php echo __('payroll'); ?></a> </div>
        <h1 class="text-center"><?php echo __('payroll'); ?> Management <i class="fas fa-money-check-alt"></i></h1>
    </div>
    
    <div class="container-fluid">
        <hr>
        
        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <button class="close" data-dismiss="alert">×</button>
                <strong>Success!</strong> Payroll has been posted successfully.
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <button class="close" data-dismiss="alert">×</button>
                <strong>Error!</strong> <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <div class="row-fluid">
            <div class="span12">
                <div class="widget-box">
                    <div class="widget-title"> <span class="icon"><i class="fas fa-users"></i></span>
                        <h5>Staff Salary List</h5>
                    </div>
                    <div class="widget-content nopadding">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Photo</th>
                                    <th><?php echo __('fullname'); ?></th>
                                    <th>Designation</th>
                                    <th>Branch</th>
                                    <th><?php echo __('salary'); ?></th>
                                    <th>Debt/Owed</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!function_exists('acc_get_staff_payable_balance')) {
                                    function acc_get_staff_payable_balance($con, $staff_id) {
                                        $staff_id = (int)$staff_id;
                                        // 1. Sum up all accruals (earned debt)
                                        $q_acc = mysqli_query($con, "SELECT SUM(amount) as total FROM payroll_accruals WHERE staff_id = $staff_id");
                                        $accrued = ($q_acc && $row = mysqli_fetch_assoc($q_acc)) ? (float)$row['total'] : 0;

                                        // 2. Sum up all payments made
                                        $q_pay = mysqli_query($con, "SELECT SUM(amount) as total FROM payroll WHERE staff_id = $staff_id");
                                        $paid = ($q_pay && $row = mysqli_fetch_assoc($q_pay)) ? (float)$row['total'] : 0;

                                        return max(0, $accrued - $paid);
                                    }
                                }
                                $branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : 0;
                                $branch_where = $branch_id > 0 ? " WHERE s.branch_id = " . $branch_id : "";
                                $staffs = safe_fetch_all($con, "SELECT s.*, b.branch_name, (SELECT MAX(payment_date) FROM payroll p WHERE p.staff_id = s.user_id) as last_pay 
                                                             FROM staffs s 
                                                             LEFT JOIN branches b ON s.branch_id = b.id 
                                                             $branch_where 
                                                             ORDER BY s.fullname ASC");
                                foreach ($staffs as $staff):
                                    $photo = $staff['photo'];
                                    $photo_path = (!empty($photo) && file_exists("../img/staff/" . $photo)) ? "../img/staff/" . $photo : "../img/staff/default.png";
                                ?>
                                <tr>
                                    <td class="text-center"><img src="<?php echo $photo_path; ?>" style="width:40px; height:40px; border-radius:50%; object-fit:cover;"></td>
                                    <td><?php echo htmlspecialchars($staff['fullname']); ?></td>
                                    <td><?php echo htmlspecialchars($staff['designation']); ?></td>
                                    <td><span class="badge badge-info"><?php echo htmlspecialchars($staff['branch_name'] ?? 'Global / System'); ?></span></td>
                                    <td><span class="salary-badge">$<?php echo number_format($staff['salary'], 2); ?></span></td>
                                    <td>
                                        <?php 
                                        $debt = acc_get_staff_payable_balance($con, $staff['user_id']);
                                        if($debt > 0): ?>
                                            <span class="debt-badge">$<?php echo number_format($debt, 2); ?></span>
                                        <?php else: ?>
                                            <span class="badge badge-success">No Debt</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form action="actions/process-payroll.php" method="POST" style="margin:0;">
                                            <input type="hidden" name="staff_id" value="<?php echo $staff['user_id']; ?>">
                                            <input type="hidden" name="amount" value="<?php echo max(0, $debt); ?>">
                                            <?php if($debt > 0): ?>
                                            <button type="submit" class="btn btn-pay btn-mini" onclick="return confirm('Pay $<?php echo number_format($debt, 2); ?> to <?php echo addslashes($staff['fullname']); ?>?')">
                                                <i class="fas fa-hand-holding-usd"></i> Pay Balance
                                            </button>
                                            <?php else: ?>
                                            <button class="btn btn-mini disabled" disabled>Paid Up</button>
                                            <?php endif; ?>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row-fluid">
    <div id="footer" class="span12" style="color:white;"> <?php echo date("Y"); ?> &copy; M*A GYM System Developed By Abdikafi </div>
</div>

<script src="../js/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/matrix.js"></script>
</body>
</html>
