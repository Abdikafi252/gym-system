<div id="sidebar"><a href="#" class="visible-phone"><i class="fas fa-home"></i> Dashboard</a>
  <ul>
    <!-- User Profile Section -->
    <?php
    $admin_id = $_SESSION['user_id'];
    $is_manager = (isset($_SESSION['designation']) && $_SESSION['designation'] == 'Manager');

    if ($is_manager) {
      $prof_query = "SELECT fullname, username, photo, designation FROM staffs WHERE user_id='$admin_id'";
    } else {
      $prof_query = "SELECT * FROM admin WHERE user_id='$admin_id'";
    }

    $prof_res = mysqli_query($con, $prof_query);
    $prof_row = $prof_res ? mysqli_fetch_assoc($prof_res) : [];

    if ($is_manager) {
      $photo_val = trim((string)($prof_row['photo'] ?? ''));
      $admin_avatar = ($photo_val !== '') ? '../img/staff/' . $photo_val : '../img/demo/av1.jpg';
    } else {
      $admin_photo_raw = trim((string)($prof_row['photo'] ?? ''));
      if ($admin_photo_raw !== '' && !preg_match('/^(?:[a-z]+:)?\\/\\//i', $admin_photo_raw) && strpos($admin_photo_raw, '../') !== 0) {
        $admin_avatar = '../' . ltrim($admin_photo_raw, '/');
      } else {
        $admin_avatar = $admin_photo_raw !== '' ? $admin_photo_raw : '../img/user.png';
      }
    }

    $admin_name = trim((string)($prof_row['fullname'] ?? ''));
    if ($admin_name === '') {
      $admin_name = trim((string)($prof_row['username'] ?? ($_SESSION['username'] ?? ($is_manager ? 'Manager' : 'System Admin'))));
    }
    $admin_role = trim((string)($prof_row['designation'] ?? ''));
    if ($admin_role === '') {
      $admin_role = $is_manager ? 'Manager' : 'Administrator';
    }
    $_SESSION['fullname'] = $admin_name;
    ?>
    <li class="user-profile-sidebar">
      <img src="<?php echo htmlspecialchars($admin_avatar); ?>" alt="Admin" />
      <span class="user-name"><?php echo htmlspecialchars($admin_name); ?></span>
      <span class="user-role"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($admin_role); ?></span>
    </li>

    <li class="<?php if ($page == 'dashboard') {
                  echo 'active';
                } ?>"><a href="index.php"><i class="fas fa-tachometer-alt"></i> <span><?php echo __('dashboard'); ?></span></a> </li>
    <li class="<?php if ($page == 'profile') {
                  echo 'active';
                } ?>"><a href="profile.php"><i class="fas fa-user-cog"></i> <span>Profile Settings</span></a> </li>
    <li class="<?php if ($page == 'members') {
                  echo 'active';
                } ?>"><a href="members.php"><i class="fas fa-users"></i> <span><?php echo __('members'); ?></span> <span class="label label-important"><?php include 'dashboard-usercount.php'; ?> </span></a></li>

    <li class="submenu"> <a href="#"><i class="fas fa-dumbbell"></i> <span>Gym Equipment</span> <span class="label label-important"><?php include 'dashboard-equipcount.php'; ?> </span></a>
      <ul>
        <li class="<?php if ($page == 'list-equip') {
                      echo 'active';
                    } ?>"><a href="equipment.php"><i class="fas fa-arrow-right"></i> List Equipment List</a></li>
        <li class="<?php if ($page == 'add-equip') {
                      echo 'active';
                    } ?>"><a href="equipment-entry.php"><i class="fas fa-arrow-right"></i> Add Equipment Form</a></li>
      </ul>
    </li>

    <li class="submenu"> <a href="#"><i class="fas fa-box-open"></i> <span>Plans & Services</span></a>
      <ul>
        <li class="<?php if ($page == 'packages') {
                      echo 'active';
                    } ?>"><a href="packages.php"><i class="fas fa-arrow-right"></i> Manage Packages</a></li>
        <li class="<?php if ($page == 'services') {
                      echo 'active';
                    } ?>"><a href="services.php"><i class="fas fa-arrow-right"></i> Manage Services</a></li>
      </ul>
    </li>
    <li class="<?php if ($page == 'attendance' || $page == 'gate-monitor') {
                  echo 'submenu active';
                } else {
                  echo 'submenu';
                } ?>"> <a href="#"><i class="fas fa-calendar-alt"></i> <span><?php echo __('attendance'); ?></span></a>
      <ul>
        <li class="<?php if ($page == 'attendance') { echo 'active'; } ?>"><a href="attendance.php"><i class="fas fa-arrow-right"></i> Check In/Out</a></li>
        <li class="<?php if ($page == 'view-attendance') { echo 'active'; } ?>"><a href="view-attendance.php"><i class="fas fa-arrow-right"></i> View History</a></li>
      </ul>
    </li>



    <li class="<?php if ($page == 'member-status') {
                  echo 'active';
                } ?>"><a href="member-status.php"><i class="fas fa-eye"></i> <span><?php echo __('member_status'); ?></span></a></li>
    <li class="<?php if ($page == 'announcement') {
                  echo 'active';
                } ?>"><a href="announcement.php"><i class="fas fa-bullhorn"></i> <span><?php echo __('announcements'); ?></span></a></li>

    <li class="<?php if (in_array($page, ['payment', 'expenses', 'accounting', 'list-equip', 'add-equip'])) {
                  echo 'submenu active';
                } else {
                  echo 'submenu';
                } ?>">
      <a href="#"><i class="fas fa-calculator"></i> <span>Finance</span></a>
      <ul>
        <li class="<?php if ($page == 'payment') {
                      echo 'active';
                    } ?>"><a href="payment.php"><i class="fas fa-arrow-right"></i> <?php echo __('members'); ?> (Payments)</a></li>
        <li class="<?php if ($page == 'expenses') {
                      echo 'active';
                    } ?>"><a href="expenses.php"><i class="fas fa-arrow-right"></i> <?php echo __('expenses'); ?></a></li>
           <li><a href="accounting-owner-capital.php"><i class="fas fa-arrow-right"></i> Owner Capital</a></li>
           <li><a href="accounting-owner-draw.php"><i class="fas fa-hand-holding-usd"></i> Owner Draw</a></li>
        <li><a href="accounting-cycle.php"><i class="fas fa-arrow-right"></i> Accounting Cycle</a></li>
        <li><a href="accounting-sync.php"><i class="fas fa-arrow-right"></i> Historical Sync</a></li>
        <li><a href="accounting-transactions.php"><i class="fas fa-arrow-right"></i> Analyze Transactions</a></li>
        <li><a href="accounting-journal.php"><i class="fas fa-arrow-right"></i> Journal</a></li>
        <li><a href="accounting-ledger.php"><i class="fas fa-arrow-right"></i> Ledger</a></li>
        <li><a href="accounting-trial-balance.php"><i class="fas fa-arrow-right"></i> Trial Balance</a></li>
        <li><a href="accounting-statements.php"><i class="fas fa-file-invoice-dollar"></i> Financial Statements</a></li>
        <li><a href="accounting-closing.php"><i class="fas fa-door-closed"></i> <?php echo __('fiscal_periods'); ?></a></li>
        <li><a href="accounting-history.php"><i class="fas fa-history"></i> <?php echo __('yearly_history'); ?></a></li>
        <li><a href="liabilities.php"><i class="fas fa-arrow-right"></i> Liabilities</a></li>
      </ul>
    </li>



    <li class="<?php if ($page == 'reminders') {
                  echo 'active';
                } ?>"><a href="reminders.php"><i class="fas fa-bell"></i> <span>Reminders</span></a></li>
    <li class="<?php if ($page == 'notifications') {
                  echo 'active';
                } ?>"><a href="notifications.php"><i class="fas fa-bell"></i> <span>Notification Center</span></a></li>
    <li class="<?php if ($page == 'renewal-due') {
                  echo 'active';
                } ?>"><a href="renewal-due-report.php"><i class="fas fa-calendar-alt"></i> <span>Renewal Due Report</span></a></li>

    <li class="<?php if ($page == 'staff-management') {
                  echo 'active';
                } ?>"><a href="staffs.php"><i class="fas fa-briefcase"></i> <span><?php echo __('staff_management'); ?></span></a></li>
    <li class="<?php if ($page == 'payroll') {
                  echo 'active';
                } ?>"><a href="payroll.php"><i class="fas fa-money-check-alt"></i> <span><?php echo __('payroll'); ?></span></a></li>
    <li class="<?php if ($page == 'manage-branches') {
                  echo 'active';
                } ?>"><a href="manage-branches.php"><i class="fas fa-building"></i> <span>Manage Branches</span></a></li>
    <li class="submenu"> <a href="#"><i class="fas fa-file"></i> <span>Reports</span></a>
      <ul>
        <li class="<?php if ($page == 'chart') {
                      echo 'active';
                    } ?>"><a href="reports.php"><i class="fas fa-arrow-right"></i> Charts</a></li>
        <li class="<?php if ($page == 'member-repo') {
                      echo 'active';
                    } ?>"><a href="members-report.php"><i class="fas fa-arrow-right"></i> Members Report</a></li>
    </li>

    <li class="<?php if ($page == 'attendance-repo') {
                  echo 'active';
                } ?>"><a href="attendance-report.php"><i class="fas fa-arrow-right"></i> Attendance Report</a></li>
    <li class="<?php if ($page == 'ai-assistant') { echo 'active'; } ?>"><a href="ai-assistant.php"><i class="fas fa-robot"></i> <span>AI Assistant</span></a></li>
    <li class="<?php if ($page == 'chart') {
                  echo 'active';
                } ?>"><a href="analytics.php"><i class="fas fa-chart-line"></i> Analytics</a></li>
  </ul>
  </li>



  
  </ul>
</div>