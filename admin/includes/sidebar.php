<div id="sidebar"><a href="#" class="visible-phone"><i class="fas fa-home"></i> Dashboard</a>
  <ul>
    <!-- User Profile Section -->
    <?php
    $admin_id = $_SESSION['user_id'];
    $prof_query = "SELECT * FROM admin WHERE user_id='$admin_id'";
    $prof_res = mysqli_query($con, $prof_query);
    $prof_row = $prof_res ? mysqli_fetch_assoc($prof_res) : [];
    $admin_photo_raw = trim((string)($prof_row['photo'] ?? ''));
    if ($admin_photo_raw !== '' && !preg_match('/^(?:[a-z]+:)?\\/\\//i', $admin_photo_raw) && strpos($admin_photo_raw, '../') !== 0) {
      $admin_avatar = '../' . ltrim($admin_photo_raw, '/');
    } else {
      $admin_avatar = $admin_photo_raw !== '' ? $admin_photo_raw : '../img/user.png';
    }
    $admin_name = trim((string)($prof_row['fullname'] ?? ''));
    if ($admin_name === '') {
      $admin_name = trim((string)($prof_row['username'] ?? ($_SESSION['username'] ?? 'System Admin')));
    }
    $admin_role = trim((string)($prof_row['designation'] ?? ''));
    if ($admin_role === '') {
      $admin_role = 'Administrator';
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
                } ?>"><a href="index.php"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a> </li>
    <li class="<?php if ($page == 'profile') {
                  echo 'active';
                } ?>"><a href="profile.php"><i class="fas fa-user-cog"></i> <span>Profile Settings</span></a> </li>
    <li class="<?php if ($page == 'members') {
                  echo 'active';
                } ?>"><a href="members.php"><i class="fas fa-users"></i> <span>Manage Members</span> <span class="label label-important"><?php include 'dashboard-usercount.php'; ?> </span></a></li>
    <li class="<?php if ($page == 'pending-members') {
                  echo 'active';
                } ?>"><a href="pending-members.php"><i class="fas fa-user-clock"></i> <span>Pending Members</span></a></li>

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
    <li class="<?php if ($page == 'attendance') {
                  echo 'submenu active';
                } else {
                  echo 'submenu';
                } ?>"> <a href="#"><i class="fas fa-calendar-alt"></i> <span>Attendance</span></a>
      <ul>
        <li class="<?php if ($page == 'attendance') {
                      echo 'active';
                    } ?>"><a href="attendance.php"><i class="fas fa-arrow-right"></i> Check In/Out</a></li>
        <li class="<?php if ($page == 'view-attendance') {
                      echo 'active';
                    } ?>"><a href="view-attendance.php"><i class="fas fa-arrow-right"></i> View</a></li>
      </ul>
    </li>



    <li class="<?php if ($page == 'member-status') {
                  echo 'active';
                } ?>"><a href="member-status.php"><i class="fas fa-eye"></i> <span>Member's Status</span></a></li>
    <li class="<?php if ($page == 'payment') {
                  echo 'active';
                } ?>"><a href="payment.php"><i class="fas fa-hand-holding-usd"></i> <span>Payments</span></a></li>
    <li class="<?php if ($page == 'announcement') {
                  echo 'active';
                } ?>"><a href="announcement.php"><i class="fas fa-bullhorn"></i> <span>Announcements</span></a></li>

    <li class="<?php if ($page == 'expenses') {
                  echo 'active';
                } ?>"><a href="expenses.php"><i class="fas fa-money-bill-wave"></i> <span>Expenses</span></a></li>



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
                } ?>"><a href="staffs.php"><i class="fas fa-briefcase"></i> <span>Staff Management</span></a></li>
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
    <li class="<?php if ($page == 'chart') {
                  echo 'active';
                } ?>"><a href="analytics.php"><i class="fas fa-chart-line"></i> Analytics</a></li>
  </ul>
  </li>



  <!-- Visit codeastro.com for more projects -->
  </ul>
</div>