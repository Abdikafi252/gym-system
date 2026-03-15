<?php
include 'dbcon.php';
// We assume $con is already defined in the parent file

$today = date('Y-m-d');
$first_day_of_month = date('Y-m-01');
$last_7_days = date('Y-m-d', strtotime('-7 days'));

$branch_id = $_SESSION['branch_id'];

// 1. Xubnaha Firfircoon (Active status AND not expired)
$q1 = mysqli_query($con, "SELECT COUNT(*) as total FROM members WHERE branch_id = '$branch_id' AND status = 'Active' AND expiry_date >= '$today'");
$r1 = mysqli_fetch_assoc($q1);
$active_members = $r1['total'];

// 2. Xubnaha Diiwaangashan
$q2 = mysqli_query($con, "SELECT COUNT(*) as total FROM members WHERE branch_id = '$branch_id'");
$r2 = mysqli_fetch_assoc($q2);
$total_members = $r2['total'];

// 3. Dakhliga Guud (Actual money received for branch)
$q3 = mysqli_query($con, "SELECT SUM(paid_amount) as total FROM members WHERE branch_id = '$branch_id'");
$r3 = mysqli_fetch_assoc($q3);
$total_income = $r3['total'] ? $r3['total'] : 0;

// 4. Ogeysiisyada
$q4 = mysqli_query($con, "SELECT COUNT(*) as total FROM announcements");
if ($q4) {
    $r4 = mysqli_fetch_assoc($q4);
    $total_announcements = $r4['total'];
} else {
    $total_announcements = 0;
}

// 5. Dakhliga Maanta
$q5 = mysqli_query($con, "SELECT SUM(paid_amount) as total FROM members WHERE branch_id = '$branch_id' AND paid_date = '$today'");
$r5 = mysqli_fetch_assoc($q5);
$today_collection = $r5['total'] ? $r5['total'] : 0;

// 6. Dakhliga Todobaadka
$q6 = mysqli_query($con, "SELECT SUM(paid_amount) as total FROM members WHERE branch_id = '$branch_id' AND paid_date >= '$last_7_days'");
$r6 = mysqli_fetch_assoc($q6);
$weekly_collection = $r6['total'] ? $r6['total'] : 0;

// 7. Dakhliga Bishaan
$q7 = mysqli_query($con, "SELECT SUM(paid_amount) as total FROM members WHERE paid_date >= '$first_day_of_month'");
$r7 = mysqli_fetch_assoc($q7);
$monthly_collection = $r7['total'] ? $r7['total'] : 0;

// 8. Kharashka Bishaan
$q8_1 = mysqli_query($con, "SELECT COALESCE(SUM(amount),0) as total FROM expenses WHERE date BETWEEN '$first_day_of_month' AND '$today'");
$r8_1 = mysqli_fetch_assoc($q8_1);
$monthly_expenses = isset($r8_1['total']) ? (float)$r8_1['total'] : 0;

// 9. Cusboonaysiinta Maanta
$q9 = mysqli_query($con, "SELECT COUNT(*) as total FROM members WHERE branch_id = '$branch_id' AND paid_date = '$today' AND dor != '$today'");
$r9 = mysqli_fetch_assoc($q9);
$today_renewal = $r9['total'];

// 10. Cusboonaysiinta Bishaan
$q10 = mysqli_query($con, "SELECT COUNT(*) as total FROM members WHERE branch_id = '$branch_id' AND paid_date >= '$first_day_of_month' AND dor < '$first_day_of_month'");
$r10 = mysqli_fetch_assoc($q10);
$monthly_renewal = $r10['total'];

// 11. Cusboonaysiinta Dhiman
$q11 = mysqli_query($con, "SELECT COUNT(*) as total FROM members WHERE branch_id = '$branch_id' AND expiry_date < '$today'");
$r11 = mysqli_fetch_assoc($q11);
$pending_renewal = $r11['total'];

// 12. Haraaga Dhiman (Total - Discount - Paid for branch members)
$q12 = mysqli_query($con, "SELECT SUM(amount - paid_amount) as total FROM members WHERE branch_id = '$branch_id' AND status = 'Active' AND amount > paid_amount");
$r12 = mysqli_fetch_assoc($q12);
$pending_balance = $r12['total'] ? (float)$r12['total'] : 0;

// 13. Dhicitaanka Maanta
$q13 = mysqli_query($con, "SELECT COUNT(*) as total FROM members WHERE branch_id = '$branch_id' AND expiry_date = '$today'");
$r13 = mysqli_fetch_assoc($q13);
$today_plan_expiry = $r13['total'];

// 14. Xubnaha Maanta Jooga
// 14. Xubnaha Maanta Jooga (Only Active & Not Expired - Sync with Admin)
$q14 = mysqli_query($con, "SELECT COUNT(DISTINCT a.member_id) as total FROM attendance a 
                           JOIN members m ON a.member_id = m.user_id 
                           WHERE a.branch_id = '$branch_id' 
                           AND DATE(a.check_in) = '$today'
                           AND m.status = 'Active' 
                           AND m.expiry_date >= '$today'");
if ($q14) {
    $r14 = mysqli_fetch_assoc($q14);
    $member_present = $r14['total'] ? (int)$r14['total'] : 0;
} else {
    $member_present = 0;
}

// Monthly Net Profit
$monthly_net_profit = $monthly_collection - $monthly_expenses;

// 15. Diiwaangelinta Cusub
$q15 = mysqli_query($con, "SELECT COUNT(*) as total FROM members WHERE branch_id = '$branch_id' AND dor = '$today'");
$r15 = mysqli_fetch_assoc($q15);
$new_membership = $r15['total'];

// 16. Wadarta Shaqaalaha
$q16 = mysqli_query($con, "SELECT COUNT(*) as total FROM staffs WHERE branch_id = '$branch_id'");
if ($q16) {
    $r16 = mysqli_fetch_assoc($q16);
    $total_staff = $r16['total'];
} else {
    $total_staff = 0;
}

// 17. Qalabka Diyaar ah
$q17 = mysqli_query($con, "SELECT COUNT(*) as total FROM equipment WHERE branch_id = '$branch_id'");
if ($q17) {
    $r17 = mysqli_fetch_assoc($q17);
    $available_equipment = $r17['total'];
} else {
    $available_equipment = 0;
}

// 18. Wadarta Kharashyada (Equipment + Expenses All Time)
$q18_1 = mysqli_query($con, "SELECT SUM(amount) as total FROM expenses WHERE branch_id = '$branch_id'");
$r18_1 = mysqli_fetch_assoc($q18_1);
$total_expenses_all = $r18_1['total'] ? $r18_1['total'] : 0;

$q18_2 = mysqli_query($con, "SELECT SUM(amount) as total FROM equipment WHERE branch_id = '$branch_id'");
$r18_2 = mysqli_fetch_assoc($q18_2);
$total_equip_all = $r18_2['total'] ? $r18_2['total'] : 0;

$total_expenses = $total_expenses_all + $total_equip_all;

// 19. Foydada Nadiifta (Net) = Wadarta Kharashyada - Foydada Bishaan
$net_profit = $total_expenses - $monthly_net_profit;

// 20. Tababarayaasha
$q20 = mysqli_query($con, "SELECT COUNT(*) as total FROM staffs WHERE branch_id = '$branch_id' AND (designation = 'Trainer' OR designation = 'Tababare')");
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
            <h4 class="stat-title">Xubnaha Firfircoon</h4>
            <h3 class="stat-value green-text"><?php echo number_format($active_members); ?></h3>
        </div>
    </a>
    <a href="members.php" class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-users"></i></div>
        <div class="stat-content">
            <h4 class="stat-title">Xubnaha Diiwaangashan</h4>
            <h3 class="stat-value orange-text"><?php echo number_format($total_members); ?></h3>
        </div>
    </a>

    <?php if (in_array($_SESSION['designation'], ['Manager', 'Cashier'])): ?>
        <a href="payment.php" class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-dollar-sign"></i></div>
            <div class="stat-content">
                <h4 class="stat-title">Dakhliga Guud</h4>
                <h3 class="stat-value blue-text">$<?php echo number_format($total_income); ?></h3>
            </div>
        </a>
    <?php endif; ?>

    <a href="announcement.php" class="stat-card">
        <div class="stat-icon red"><i class="fas fa-bullhorn"></i></div>
        <div class="stat-content">
            <h4 class="stat-title">Ogeysiisyada</h4>
            <h3 class="stat-value red-text"><?php echo number_format($total_announcements); ?></h3>
        </div>
    </a>

    <?php if ($_SESSION['designation'] != 'Cleaner'): ?>
        <!-- ROW 2 (Important Overviews) -->
        <?php if (in_array($_SESSION['designation'], ['Manager'])): ?>
            <a href="equipment.php" class="stat-card">
                <div class="stat-icon blue"><i class="fas fa-dumbbell"></i></div>
                <div class="stat-content">
                    <h4 class="stat-title">Qalabka Diyaar ah</h4>
                    <h3 class="stat-value blue-text"><?php echo number_format($available_equipment); ?></h3>
                </div>
            </a>
        <?php endif; ?>

        <?php if (in_array($_SESSION['designation'], ['Manager'])): ?>
            <a href="expenses.php" class="stat-card">
                <div class="stat-icon orange"><i class="fas fa-file-invoice-dollar"></i></div>
                <div class="stat-content">
                    <h4 class="stat-title">Wadarta Kharashyada</h4>
                    <h3 class="stat-value orange-text">$<?php echo number_format($total_expenses); ?></h3>
                </div>
            </a>
        <?php endif; ?>

        <?php if (in_array($_SESSION['designation'], ['Manager', 'Cashier'])): ?>
            <a href="reports.php" class="stat-card">
                <div class="stat-icon green"><i class="fas fa-money-check-alt"></i></div>
                <div class="stat-content">
                    <h4 class="stat-title">Foydada Bishaan (Net)</h4>
                    <h3 class="stat-value green-text">$<?php echo number_format($monthly_net_profit); ?></h3>
                </div>
            </a>
        <?php endif; ?>

        <?php if (in_array($_SESSION['designation'], ['Manager', 'Cashier', 'Trainer'])): ?>
            <a href="renewal-due-report.php" class="stat-card">
                <div class="stat-icon red"><i class="fas fa-clock"></i></div>
                <div class="stat-content">
                    <h4 class="stat-title">Dhicitaanka Maanta</h4>
                    <h3 class="stat-value red-text"><?php echo number_format($today_plan_expiry); ?></h3>
                </div>
            </a>
        <?php endif; ?>

        <?php if (in_array($_SESSION['designation'], ['Manager'])): ?>
            <a href="staffs.php" class="stat-card">
                <div class="stat-icon gray"><i class="fas fa-user-ninja"></i></div>
                <div class="stat-content">
                    <h4 class="stat-title">Tababarayaasha</h4>
                    <h3 class="stat-value"><?php echo number_format($total_trainers); ?></h3>
                </div>
            </a>
        <?php endif; ?>

        <!-- ROW 3 (Collections) -->
        <?php if (in_array($_SESSION['designation'], ['Manager', 'Cashier'])): ?>
            <a href="payment.php" class="stat-card">
                <div class="stat-icon blue"><i class="fas fa-money-bill-wave"></i></div>
                <div class="stat-content">
                    <h4 class="stat-title">Dakhliga Maanta</h4>
                    <h3 class="stat-value blue-text">$<?php echo number_format($today_collection); ?></h3>
                </div>
            </a>
            <a href="payment.php" class="stat-card">
                <div class="stat-icon orange"><i class="fas fa-money-bill"></i></div>
                <div class="stat-content">
                    <h4 class="stat-title">Dakhliga Todobaadka</h4>
                    <h3 class="stat-value orange-text">$<?php echo number_format($weekly_collection); ?></h3>
                </div>
            </a>
            <a href="payment.php" class="stat-card">
                <div class="stat-icon pink"><i class="fas fa-arrow-down"></i></div>
                <div class="stat-content">
                    <h4 class="stat-title">Dakhliga Bishaan</h4>
                    <h3 class="stat-value pink-text">$<?php echo number_format($monthly_collection); ?></h3>
                </div>
            </a>
        <?php endif; ?>

        <?php if (in_array($_SESSION['designation'], ['Manager'])): ?>
            <a href="expenses.php" class="stat-card">
                <div class="stat-icon gray"><i class="fas fa-arrow-up"></i></div>
                <div class="stat-content">
                    <h4 class="stat-title">Kharashka Bishaan</h4>
                    <h3 class="stat-value">$<?php echo number_format($monthly_expenses); ?></h3>
                </div>
            </a>
        <?php endif; ?>

        <!-- ROW 3 (Renewals & Balance) -->
        <?php if (in_array($_SESSION['designation'], ['Manager', 'Cashier'])): ?>
            <a href="renewal-due-report.php" class="stat-card">
                <div class="stat-icon purple"><i class="fas fa-sync"></i></div>
                <div class="stat-content">
                    <h4 class="stat-title">Cusboonaysiinta Maanta</h4>
                    <h3 class="stat-value purple-text"><?php echo number_format($today_renewal); ?></h3>
                </div>
            </a>
            <a href="renewal-due-report.php" class="stat-card">
                <div class="stat-icon pink"><i class="fas fa-sync"></i></div>
                <div class="stat-content">
                    <h4 class="stat-title">Cusboonaysiinta Bishaan</h4>
                    <h3 class="stat-value pink-text"><?php echo number_format($monthly_renewal); ?></h3>
                </div>
            </a>
            <a href="renewal-due-report.php" class="stat-card">
                <div class="stat-icon blue"><i class="fas fa-sync"></i></div>
                <div class="stat-content">
                    <h4 class="stat-title">Cusboonaysiinta Dhiman</h4>
                    <h3 class="stat-value blue-text"><?php echo number_format($pending_renewal); ?></h3>
                </div>
            </a>
            <a href="payment.php" class="stat-card">
                <div class="stat-icon green"><i class="fas fa-credit-card"></i></div>
                <div class="stat-content">
                    <h4 class="stat-title">Haraaga Dhiman</h4>
                    <h3 class="stat-value green-text">$<?php echo number_format($pending_balance); ?></h3>
                </div>
            </a>
        <?php endif; ?>

        <!-- ROW 4 (Misc) -->

        <?php if (in_array($_SESSION['designation'], ['Manager', 'Cashier', 'Trainer Assistant'])): ?>
            <a href="attendance-report.php" class="stat-card">
                <div class="stat-icon orange"><i class="fas fa-users-cog"></i></div>
                <div class="stat-content">
                    <h4 class="stat-title">Xubnaha Maanta Jooga</h4>
                    <h3 class="stat-value orange-text"><?php echo number_format($member_present); ?></h3>
                </div>
            </a>
        <?php endif; ?>

        <?php if (in_array($_SESSION['designation'], ['Manager', 'Cashier'])): ?>
            <a href="members.php" class="stat-card">
                <div class="stat-icon yellow"><i class="fas fa-user-plus"></i></div>
                <div class="stat-content">
                    <h4 class="stat-title">Diiwaangelinta Cusub</h4>
                    <h3 class="stat-value yellow-text"><?php echo number_format($new_membership); ?></h3>
                </div>
            </a>
        <?php endif; ?>

        <?php if (in_array($_SESSION['designation'], ['Manager'])): ?>
            <a href="staffs.php" class="stat-card">
                <div class="stat-icon gray"><i class="fas fa-id-badge"></i></div>
                <div class="stat-content">
                    <h4 class="stat-title">Wadarta Shaqaalaha</h4>
                    <h3 class="stat-value"><?php echo number_format($total_staff); ?></h3>
                </div>
            </a>
        <?php endif; ?>
    <?php endif; ?>
</div>