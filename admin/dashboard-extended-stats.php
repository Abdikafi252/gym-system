<?php
include 'dbcon.php';
// We assume $con is already defined in the parent file

if (session_status() === PHP_SESSION_NONE) session_start();
$selected_branch = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : 0;
$branch_filter = $selected_branch > 0 ? " AND branch_id = $selected_branch" : "";
$branch_filter_where = $selected_branch > 0 ? " WHERE branch_id = $selected_branch" : "";

$today = date('Y-m-d');
$first_day_of_month = date('Y-m-01');
$last_7_days = date('Y-m-d', strtotime('-7 days'));

// 1. Active Members (Active status AND not expired)
$q1 = mysqli_query($con, "SELECT COUNT(*) as total FROM members WHERE status = 'Active' AND expiry_date >= '$today'" . $branch_filter);
if ($q1) {
    $r1 = mysqli_fetch_assoc($q1);
    $active_members = $r1['total'] ? (int)$r1['total'] : 0;
} else {
    $active_members = 0;
}

$q2 = mysqli_query($con, "SELECT COUNT(*) as total FROM members WHERE status != 'Deleted'" . $branch_filter);
if ($q2) {
    $r2 = mysqli_fetch_assoc($q2);
    $total_members = $r2['total'] ? $r2['total'] : 0;
} else {
    $total_members = 0;
}

// 3. Total Income (Actual money received)
$q3 = mysqli_query($con, "SELECT COALESCE(SUM(paid_amount),0) as total FROM payment_history" . $branch_filter_where);
$r3 = mysqli_fetch_assoc($q3);
$total_income = $r3['total'] ? $r3['total'] : 0;

// 4. Announcements
$q4 = mysqli_query($con, "SELECT COUNT(*) as total FROM announcements" . $branch_filter_where);
if ($q4) {
    $r4 = mysqli_fetch_assoc($q4);
    $total_announcements = $r4['total'];
} else {
    $total_announcements = 0;
}

// 5. Today's Income
$q5 = mysqli_query($con, "SELECT COALESCE(SUM(paid_amount),0) as total FROM payment_history WHERE paid_date = '$today'" . $branch_filter);
$r5 = mysqli_fetch_assoc($q5);
$today_collection = $r5['total'] ? $r5['total'] : 0;

// 6. Weekly Income
$q6 = mysqli_query($con, "SELECT COALESCE(SUM(paid_amount),0) as total FROM payment_history WHERE paid_date >= '$last_7_days'" . $branch_filter);
$r6 = mysqli_fetch_assoc($q6);
$weekly_collection = $r6['total'] ? $r6['total'] : 0;

// 7. Monthly Income
$q7 = mysqli_query($con, "SELECT COALESCE(SUM(paid_amount),0) as total FROM payment_history WHERE paid_date >= '$first_day_of_month'" . $branch_filter);
$r7 = mysqli_fetch_assoc($q7);
$monthly_collection = $r7['total'] ? $r7['total'] : 0;

// 8. Monthly Expenses
$q8_1 = mysqli_query($con, "SELECT COALESCE(SUM(amount),0) as total FROM expenses WHERE date BETWEEN '$first_day_of_month' AND '$today'" . $branch_filter);
$r8_1 = mysqli_fetch_assoc($q8_1);
$monthly_expenses = isset($r8_1['total']) ? (float)$r8_1['total'] : 0;

// 9. Today's Renewals
$q9 = mysqli_query($con, "SELECT COUNT(*) as total FROM members WHERE paid_date = '$today' AND dor != '$today' AND status != 'Deleted'" . $branch_filter);
$r9 = mysqli_fetch_assoc($q9);
$today_renewal = $r9['total'];

// 10. Monthly Renewals
$q10 = mysqli_query($con, "SELECT COUNT(*) as total FROM members WHERE paid_date >= '$first_day_of_month' AND dor < '$first_day_of_month' AND status != 'Deleted'" . $branch_filter);
$r10 = mysqli_fetch_assoc($q10);
$monthly_renewal = $r10['total'];

// 11. Pending Renewals
$q11 = mysqli_query($con, "SELECT COUNT(*) as total FROM members WHERE expiry_date < '$today' AND status != 'Deleted'" . $branch_filter);
$r11 = mysqli_fetch_assoc($q11);
$pending_renewal = $r11['total'];

// 12. Pending Balance (Total Net - Paid for all members)
$q12 = mysqli_query($con, "SELECT SUM(amount - paid_amount) as total FROM members WHERE status = 'Active' AND amount > paid_amount" . $branch_filter);
$r12 = mysqli_fetch_assoc($q12);
$pending_balance = $r12['total'] ? (float)$r12['total'] : 0;

// 13. Today's Expiry
$q13 = mysqli_query($con, "SELECT COUNT(*) as total FROM members WHERE expiry_date = '$today' AND status != 'Deleted'" . $branch_filter);
$r13 = mysqli_fetch_assoc($q13);
$today_plan_expiry = $r13['total'];

// 14. Members Present Today (Only Active & Not Expired)
$q14 = mysqli_query($con, "SELECT COUNT(DISTINCT a.member_id) as total FROM attendance a 
                           JOIN members m ON a.member_id = m.user_id 
                           WHERE DATE(a.check_in) = '$today' 
                           AND m.status = 'Active' 
                           AND m.expiry_date >= '$today'" . ($selected_branch > 0 ? " AND m.branch_id = $selected_branch" : ""));
if ($q14) {
    $r14 = mysqli_fetch_assoc($q14);
    $member_present = $r14['total'] ? (int)$r14['total'] : 0;
} else {
    $member_present = 0;
}

// 14b. Monthly Net Profit
$monthly_net_profit = $monthly_collection - $monthly_expenses;

// 15. New Registrations
$q15 = mysqli_query($con, "SELECT COUNT(*) as total FROM members WHERE dor = '$today' AND status != 'Deleted'" . $branch_filter);
$r15 = mysqli_fetch_assoc($q15);
$new_membership = $r15['total'];

// 16. Total Staff
$q16 = mysqli_query($con, "SELECT COUNT(*) as total FROM staffs" . $branch_filter_where);
if ($q16) {
    $r16 = mysqli_fetch_assoc($q16);
    $total_staff = $r16['total'];
} else {
    $total_staff = 0;
}

// 17. Equipment Ready
$q17 = mysqli_query($con, "SELECT COUNT(*) as total FROM equipment" . $branch_filter_where);
if ($q17) {
    $r17 = mysqli_fetch_assoc($q17);
    $available_equipment = $r17['total'];
} else {
    $available_equipment = 0;
}

// 18. Total Expenses (Expenses only; equipment is tracked as an asset)
$q18_1 = mysqli_query($con, "SELECT SUM(amount) as total FROM expenses" . $branch_filter_where);
$r18_1 = mysqli_fetch_assoc($q18_1);
$total_expenses_all = $r18_1['total'] ? $r18_1['total'] : 0;

$total_expenses = $total_expenses_all;

// 19. Net Profit = Total Income - Total Expenses
$net_profit = $total_income - $total_expenses;

// 20. Trainers
$q20 = mysqli_query($con, "SELECT COUNT(*) as total FROM staffs WHERE (designation = 'Trainer' OR designation = 'Tababare')" . $branch_filter);
if ($q20) {
    $r20 = mysqli_fetch_assoc($q20);
    $total_trainers = $r20['total'];
} else {
    $total_trainers = 0;
}

?>
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 25px;
    }

    .stat-card {
        background: #fff;
        border-radius: 20px;
        padding: 18px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: 0 16px 34px rgba(15, 23, 42, 0.08);
        border: 1px solid #eef2f7;
        text-decoration: none;
        transition: 0.2s all ease;
        cursor: pointer;
        min-height: 92px;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 18px 38px rgba(15, 23, 42, 0.12);
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-right: 15px;
        flex-shrink: 0;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6);
    }

    .stat-icon.blue {
        background: #e0f2fe;
        color: #0284c7;
    }

    .stat-icon.orange {
        background: #ffedd5;
        color: #ea580c;
    }

    .stat-icon.pink {
        background: #fce7f3;
        color: #be185d;
    }

    .stat-icon.green {
        background: #dcfce7;
        color: #16a34a;
    }

    .stat-icon.gray {
        background: #f3f4f6;
        color: #4b5563;
    }

    .stat-icon.purple {
        background: #f3e8ff;
        color: #9333ea;
    }

    .stat-icon.yellow {
        background: #fef9c3;
        color: #ca8a04;
    }

    .stat-icon.red {
        background: #fee2e2;
        color: #dc2626;
    }

    .stat-content {
        flex-grow: 1;
        text-align: left;
        min-width: 0;
    }

    .stat-title {
        font-size: 13px;
        color: #6b7280;
        margin: 0 0 5px 0 !important;
        line-height: 1.2;
        font-weight: 500;
        font-family: 'Open Sans', sans-serif;
        text-transform: none;
    }

    .stat-value {
        font-size: 22px;
        font-weight: bold;
        color: #111827;
        margin: 0 !important;
        line-height: 1.2;
        font-family: 'Open Sans', sans-serif;
        text-transform: none;
        word-break: break-word;
    }

    .stat-value.blue-text {
        color: #0284c7;
    }

    .stat-value.orange-text {
        color: #ea580c;
    }

    .stat-value.pink-text {
        color: #be185d;
    }

    .stat-value.green-text {
        color: #16a34a;
    }

    .stat-value.purple-text {
        color: #9333ea;
    }

    .stat-value.yellow-text {
        color: #ca8a04;
    }

    .stat-value.red-text {
        color: #dc2626;
    }

    a.stat-card:hover h4 {
        color: #6b7280;
    }

    /* keep title color from changing on hover */
    @media (max-width: 1200px) {
        .stats-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 900px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 767px) {
        .stats-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .stat-card {
            padding: 12px;
            border-radius: 16px;
            min-height: 84px;
            gap: 10px;
            border: 1px solid #e3ebf7;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.08);
        }

        .stat-icon {
            width: 36px;
            height: 36px;
            font-size: 16px;
            margin-right: 0;
        }

        .stat-content {
            min-width: 0;
        }

        .stat-title {
            font-size: 11px;
            line-height: 1.25;
            margin-bottom: 4px !important;
        }

        .stat-value {
            font-size: 16px;
            line-height: 1.15;
            font-weight: 800;
        }
    }

    @media (max-width: 479px) {
        .stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .stat-card {
            min-height: 82px;
            padding: 11px;
        }

        .stat-icon {
            width: 34px;
            height: 34px;
            font-size: 14px;
        }

        .stat-title {
            font-size: 10px;
        }

        .stat-value {
            font-size: 15px;
        }
    }
</style>

<div class="stats-grid">
    <!-- ROW 1 (Core Stats) -->
    <a href="index.php" class="stat-card">
        <div class="stat-icon green"><i class="fas fa-user-check"></i></div>
        <div class="stat-content">
            <h4 class="stat-title"><?php echo __('active_members'); ?></h4>
            <h3 class="stat-value green-text"><?php echo number_format($active_members); ?></h3>
        </div>
    </a>
    <a href="members.php" class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-users"></i></div>
        <div class="stat-content">
            <h4 class="stat-title"><?php echo __('total_members'); ?></h4>
            <h3 class="stat-value orange-text"><?php echo number_format($total_members); ?></h3>
        </div>
    </a>
    <a href="payment.php" class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-dollar-sign"></i></div>
        <div class="stat-content">
            <h4 class="stat-title"><?php echo __('revenue'); ?></h4>
            <h3 class="stat-value blue-text">$<?php echo number_format($total_income); ?></h3>
        </div>
    </a>
    <a href="announcement.php" class="stat-card">
        <div class="stat-icon red"><i class="fas fa-bullhorn"></i></div>
        <div class="stat-content">
            <h4 class="stat-title"><?php echo __('total_announcements'); ?></h4>
            <h3 class="stat-value red-text"><?php echo number_format($total_announcements); ?></h3>
        </div>
    </a>

    <!-- ROW 2 (Important Overviews) -->
    <a href="equipment.php" class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-dumbbell"></i></div>
        <div class="stat-content">
            <h4 class="stat-title"><?php echo __('available_equipment'); ?></h4>
            <h3 class="stat-value blue-text"><?php echo number_format($available_equipment); ?></h3>
        </div>
    </a>
    <a href="expenses.php" class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-file-invoice-dollar"></i></div>
        <div class="stat-content">
            <h4 class="stat-title"><?php echo __('expenses'); ?></h4>
            <h3 class="stat-value orange-text">$<?php echo number_format($total_expenses); ?></h3>
        </div>
    </a>
    <a href="reports.php" class="stat-card">
        <div class="stat-icon green"><i class="fas fa-money-check-alt"></i></div>
        <div class="stat-content">
            <h4 class="stat-title"><?php echo __('monthly_net_profit'); ?></h4>
            <h3 class="stat-value green-text">$<?php echo number_format($monthly_net_profit); ?></h3>
        </div>
    </a>
    <a href="renewal-due-report.php" class="stat-card">
        <div class="stat-icon red"><i class="fas fa-clock"></i></div>
        <div class="stat-content">
            <h4 class="stat-title"><?php echo __('today_expiry'); ?></h4>
            <h3 class="stat-value red-text"><?php echo number_format($today_plan_expiry); ?></h3>
        </div>
    </a>
    <a href="staffs.php" class="stat-card">
        <div class="stat-icon gray"><i class="fas fa-user-ninja"></i></div>
        <div class="stat-content">
            <h4 class="stat-title"><?php echo __('trainers'); ?></h4>
            <h3 class="stat-value"><?php echo number_format($total_trainers); ?></h3>
        </div>
    </a>

    <!-- ROW 3 (Collections) -->
    <a href="payment.php" class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-money-bill-wave"></i></div>
        <div class="stat-content">
            <h4 class="stat-title"><?php echo __('today_income'); ?></h4>
            <h3 class="stat-value blue-text">$<?php echo number_format($today_collection); ?></h3>
        </div>
    </a>
    <a href="payment.php" class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-money-bill"></i></div>
        <div class="stat-content">
            <h4 class="stat-title"><?php echo __('weekly_income'); ?></h4>
            <h3 class="stat-value orange-text">$<?php echo number_format($weekly_collection); ?></h3>
        </div>
    </a>
    <a href="payment.php" class="stat-card">
        <div class="stat-icon pink"><i class="fas fa-arrow-down"></i></div>
        <div class="stat-content">
            <h4 class="stat-title"><?php echo __('monthly_income'); ?></h4>
            <h3 class="stat-value pink-text">$<?php echo number_format($monthly_collection); ?></h3>
        </div>
    </a>
    <a href="expenses.php" class="stat-card">
        <div class="stat-icon gray"><i class="fas fa-arrow-up"></i></div>
        <div class="stat-content">
            <h4 class="stat-title"><?php echo __('monthly_expenses'); ?></h4>
            <h3 class="stat-value">$<?php echo number_format($monthly_expenses); ?></h3>
        </div>
    </a>

    <!-- ROW 3 (Renewals & Balance) -->
    <a href="renewal-due-report.php" class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-sync"></i></div>
        <div class="stat-content">
            <h4 class="stat-title"><?php echo __('today_renewals'); ?></h4>
            <h3 class="stat-value purple-text"><?php echo number_format($today_renewal); ?></h3>
        </div>
    </a>
    <a href="renewal-due-report.php" class="stat-card">
        <div class="stat-icon pink"><i class="fas fa-sync"></i></div>
        <div class="stat-content">
            <h4 class="stat-title"><?php echo __('monthly_renewals'); ?></h4>
            <h3 class="stat-value pink-text"><?php echo number_format($monthly_renewal); ?></h3>
        </div>
    </a>
    <a href="renewal-due-report.php" class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-sync"></i></div>
        <div class="stat-content">
            <h4 class="stat-title"><?php echo __('pending_renewals'); ?></h4>
            <h3 class="stat-value blue-text"><?php echo number_format($pending_renewal); ?></h3>
        </div>
    </a>
    <a href="payment.php" class="stat-card">
        <div class="stat-icon green"><i class="fas fa-credit-card"></i></div>
        <div class="stat-content">
            <h4 class="stat-title"><?php echo __('pending_balance'); ?></h4>
            <h3 class="stat-value green-text">$<?php echo number_format($pending_balance); ?></h3>
        </div>
    </a>

    <!-- ROW 4 (Misc) -->
    <a href="reports.php" class="stat-card">
        <div class="stat-icon green"><i class="fas fa-wallet"></i></div>
        <div class="stat-content">
            <h4 class="stat-title"><?php echo __('net_income'); ?></h4>
            <h3 class="stat-value green-text">$<?php echo number_format($net_profit); ?></h3>
        </div>
    </a>

    <a href="attendance-report.php" class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-users-cog"></i></div>
        <div class="stat-content">
            <h4 class="stat-title"><?php echo __('present_members'); ?></h4>
            <h3 class="stat-value orange-text"><?php echo number_format($member_present); ?></h3>
        </div>
    </a>
    <a href="members.php" class="stat-card">
        <div class="stat-icon yellow"><i class="fas fa-user-plus"></i></div>
        <div class="stat-content">
            <h4 class="stat-title"><?php echo __('new_registrations'); ?></h4>
            <h3 class="stat-value yellow-text"><?php echo number_format($new_membership); ?></h3>
        </div>
    </a>
    <a href="staffs.php" class="stat-card">
        <div class="stat-icon gray"><i class="fas fa-id-badge"></i></div>
        <div class="stat-content">
            <h4 class="stat-title"><?php echo __('total_staff'); ?></h4>
            <h3 class="stat-value"><?php echo number_format($total_staff); ?></h3>
        </div>
    </a>
</div>