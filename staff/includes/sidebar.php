<!--sidebar-menu-->
<div id="sidebar"><a href="#" class="visible-phone"><i class="fas fa-home"></i> Dashboard</a>
  <ul>
    <!-- User Profile Section -->
    <?php
    require_once __DIR__ . '/../../includes/security_core.php';
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['designation']) || !isset($_SESSION['branch_id'])) {
      echo "<script>window.location.href='../index.php';</script>";
      exit();
    }
    $_SESSION['designation'] = current_designation();
    include 'dbcon.php'; // Ensure connection is available for $con
    $uid = $_SESSION['user_id'];
    $u_qry = mysqli_query($con, "SELECT fullname, username, photo, designation FROM staffs WHERE user_id='$uid'");
    $u_data = $u_qry ? mysqli_fetch_array($u_qry) : [];
    $fallback_name = $_SESSION['fullname'] ?? ($_SESSION['username'] ?? 'Staff User');
    $u_name = !empty($u_data['fullname']) ? $u_data['fullname'] : (!empty($u_data['username']) ? $u_data['username'] : $fallback_name);
    $u_photo = !empty($u_data['photo']) ? "../../img/staff/" . $u_data['photo'] : "../../img/demo/av1.jpg";
    $u_role = !empty($u_data['designation']) ? normalize_designation($u_data['designation']) : ($_SESSION['designation'] ?? 'Staff Member');
    $_SESSION['fullname'] = $u_name;
    ?>
    <li class="user-profile-sidebar">
      <img src="<?php echo $u_photo; ?>" alt="Staff Profile" />
      <span class="user-name"><?php echo htmlspecialchars($u_name); ?></span>
      <span class="user-role"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($u_role); ?></span>
    </li>

    <li class="<?php if ($page == 'dashboard') {
                  echo 'active';
                } ?>"><a href="index.php"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a> </li>
    <?php if ($_SESSION['designation'] == 'Manager'): ?>
      <li class="<?php if ($page == 'staff-management') {
                    echo 'active';
                  } ?>"><a href="staffs.php"><i class="fas fa-briefcase"></i> <span>Staff Member</span></a> </li>
    <?php endif; ?>
    <li class="<?php if ($page == 'profile') {
                  echo 'active';
                } ?>"><a href="profile.php"><i class="fas fa-user-cog"></i> <span>Profile Settings</span></a> </li>

    <?php if (in_array($_SESSION['designation'], ['Manager', 'Cashier', 'Trainer', 'Trainer Assistant'])): ?>
      <li class="submenu"> <a href="#"><i class="fas fa-users"></i> <span>Manage Members</span> <span class="label label-important"><?php include 'dashboard-usercount.php'; ?> </span></a>
        <ul>
          <li class="<?php if ($page == 'members') {
                        echo 'active';
                      } ?>"><a href="members.php"><i class="fas fa-arrow-right"></i> List Members</a></li>
          <?php if (in_array($_SESSION['designation'], ['Manager', 'Cashier'])): ?>
            <li class="<?php if ($page == 'member-entry') {
                          echo 'active';
                        } ?>"><a href="member-entry.php"><i class="fas fa-arrow-right"></i> Member Entry Form</a></li>
            <li class="<?php if ($page == 'pending-members') {
                          echo 'active';
                        } ?>"><a href="pending-members.php"><i class="fas fa-arrow-right"></i> Pending Members</a></li>
          <?php endif; ?>
        </ul>
      </li>
    <?php endif; ?>

    <?php if ($_SESSION['designation'] == 'Manager'): ?>
      <li class="submenu"> <a href="#"><i class="fas fa-dumbbell"></i> <span>Gym Equipment</span> <span class="label label-important"><?php include 'dashboard-equipcount.php'; ?> </span></a>
        <ul>
          <li class="<?php if ($page == 'equipment') {
                        echo 'active';
                      } ?>"><a href="equipment.php"><i class="fas fa-arrow-right"></i> List Equipment</a></li>
          <?php if ($_SESSION['designation'] == 'Manager'): ?>
            <li class="<?php if ($page == 'equipment-entry') {
                          echo 'active';
                        } ?>"><a href="equipment-entry.php"><i class="fas fa-arrow-right"></i> Add Equipment</a></li>
          <?php endif; ?>
        </ul>
      </li>
    <?php endif; ?>

    <?php if (in_array($_SESSION['designation'], ['Manager', 'Cashier'])): ?>
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
    <?php endif; ?>

    <?php if (in_array($_SESSION['designation'], ['Manager', 'Cashier', 'Trainer Assistant'])): ?>
      <li class="submenu"> <a href="#"><i class="fas fa-calendar-alt"></i> <span>Attendance</span></a>
        <ul>
          <li class="<?php if ($page == 'attendance') {
                        echo 'active';
                      } ?>"><a href="attendance.php"><i class="fas fa-arrow-right"></i> Check In/Out</a></li>
          <li class="<?php if ($page == 'view-attendance') {
                        echo 'active';
                      } ?>"><a href="view-attendance.php"><i class="fas fa-arrow-right"></i> View</a></li>
        </ul>
      </li>
    <?php endif; ?>

    <?php if (in_array($_SESSION['designation'], ['Manager', 'Cashier', 'Trainer'])): ?>
      <li class="<?php if ($page == 'customer-progress') {
                    echo 'active';
                  } ?>"><a href="customer-progress.php"><i class="fas fa-tasks"></i> <span>Member's Progress</span></a></li>
    <?php endif; ?>

    <?php if (in_array($_SESSION['designation'], ['Manager', 'Cashier', 'Trainer'])): ?>
      <li class="<?php if ($page == 'member-status') {
                    echo 'active';
                  } ?>"><a href="member-status.php"><i class="fas fa-eye"></i> <span>Member's Status</span></a></li>
    <?php endif; ?>

    <?php if (in_array($_SESSION['designation'], ['Manager', 'Cashier'])): ?>
      <li class="<?php if ($page == 'payment') {
                    echo 'active';
                  } ?>"><a href="payment.php"><i class="fas fa-hand-holding-usd"></i> <span>Payments</span></a></li>
    <?php endif; ?>

    <?php if (in_array($_SESSION['designation'], ['Manager'])): ?>
      <li class="<?php if ($page == 'expenses') {
                    echo 'active';
                  } ?>"><a href="expenses.php"><i class="fas fa-money-bill-wave"></i> <span>Expenses</span></a></li>
    <?php endif; ?>

    <?php if (in_array($_SESSION['designation'], ['Manager', 'Trainer'])): ?>
      <li class="submenu"> <a href="#"><i class="fas fa-calendar-check"></i> <span>Diet & Workout</span></a>
        <ul>
          <li class="<?php if ($page == 'diet') {
                        echo 'active';
                      } ?>"><a href="manage-diet.php"><i class="fas fa-arrow-right"></i> Diet Plans</a></li>
          <li class="<?php if ($page == 'workout') {
                        echo 'active';
                      } ?>"><a href="manage-workout.php"><i class="fas fa-arrow-right"></i> Workout Plans</a></li>
        </ul>
      </li>
    <?php endif; ?>

    <?php if (in_array($_SESSION['designation'], ['Manager', 'Cashier', 'Trainer'])): ?>
      <li class="<?php if ($page == 'reminders') {
                    echo 'active';
                  } ?>"><a href="reminders.php"><i class="fas fa-bell"></i> <span>Reminders</span></a></li>
      <li class="<?php if ($page == 'notifications') {
                    echo 'active';
                  } ?>"><a href="notifications.php"><i class="fas fa-bell"></i> <span>Notification Center</span></a></li>
      <li class="<?php if ($page == 'renewal-due') {
                    echo 'active';
                  } ?>"><a href="renewal-due-report.php"><i class="fas fa-calendar-alt"></i> <span>Renewal Due Report</span></a></li>
    <?php endif; ?>

    <?php if (in_array($_SESSION['designation'], ['Manager', 'Cashier', 'Trainer', 'Trainer Assistant'])): ?>
      <li class="submenu <?php if ($page == 'announcement') {
                            echo 'active';
                          } ?>"> <a href="#"><i class="fas fa-bullhorn"></i> <span>Announcements</span></a>
        <ul>
          <li class="<?php if ($page == 'announcement') {
                        echo 'active';
                      } ?>"><a href="announcement.php"><i class="fas fa-arrow-right"></i> View Announcements</a></li>
          <?php if (in_array($_SESSION['designation'], ['Manager', 'Cashier', 'Trainer', 'Trainer Assistant'])): ?>
            <li class="<?php if ($page == 'manage-announcement') {
                          echo 'active';
                        } ?>"><a href="post-announcement.php"><i class="fas fa-arrow-right"></i> Post Announcement</a></li>
          <?php endif; ?>
        </ul>
      </li>
    <?php endif; ?>

    <?php if (in_array($_SESSION['designation'], ['Manager', 'Cashier'])): ?>
      <li class="submenu"> <a href="#"><i class="fas fa-file"></i> <span>Reports</span></a>
        <ul>
          <li class="<?php if ($page == 'reports') {
                        echo 'active';
                      } ?>"><a href="reports.php"><i class="fas fa-arrow-right"></i> Charts</a></li>
          <li class="<?php if ($page == 'members-report') {
                        echo 'active';
                      } ?>"><a href="members-report.php"><i class="fas fa-arrow-right"></i> Members Report</a></li>
          <li class="<?php if ($page == 'progress-report') {
                        echo 'active';
                      } ?>"><a href="progress-report.php"><i class="fas fa-arrow-right"></i> Progress Report</a></li>
          <li class="<?php if ($page == 'attendance-report') {
                        echo 'active';
                      } ?>"><a href="attendance-report.php"><i class="fas fa-arrow-right"></i> Attendance Report</a></li>
          <li class="<?php if ($page == 'analytics') {
                        echo 'active';
                      } ?>"><a href="analytics.php"><i class="fas fa-chart-line"></i> Analytics</a></li>
        </ul>
      </li>
    <?php endif; ?>

  </ul>
</div>
<!--sidebar-menu-->