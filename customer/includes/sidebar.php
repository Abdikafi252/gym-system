<div id="sidebar"><a href="#" class="visible-phone"><i class="fas fa-home"></i> Dashboard</a>
  <ul>
    <!-- User Profile Section -->
    <?php
    include_once 'dbcon.php';
    if (!isset($_SESSION['user_id'])) {
      session_start();
    }
    $uid = $_SESSION['user_id'];
    $u_qry = mysqli_query($con, "SELECT fullname, photo FROM members WHERE user_id='$uid'");
    $u_data = mysqli_fetch_array($u_qry);
    $u_name = !empty($u_data['fullname']) ? $u_data['fullname'] : 'Member';
    $u_photo = !empty($u_data['photo']) ? "../../img/members/" . $u_data['photo'] : "../../img/demo/av1.jpg";
    ?>
    <li class="user-profile-sidebar">
      <img src="<?php echo $u_photo; ?>" alt="Member Profile" />
      <span class="user-name"><?php echo htmlspecialchars($u_name); ?></span>
      <span class="user-role-badge member"><i class="fas fa-certificate"></i> Verified Member</span>
    </li>
    <li class="<?php if ($page == 'dashboard') {
                  echo 'active';
                } ?>"><a href="index.php"><i class="fas fa-home"></i> <span>Dashboard</span></a> </li>
    <li class="<?php if ($page == 'profile') {
                  echo 'active';
                } ?>"><a href="profile.php"><i class="fas fa-user-cog"></i> <span>Profile Settings</span></a> </li>
    <li class="<?php if ($page == 'todo') {
                  echo 'active';
                } ?>"> <a href="to-do.php"><i class="fas fa-pencil-alt"></i> <span>To-Do</span></a>

    </li>

    <li class="<?php if ($page == 'reminder') {
                  echo 'active';
                } ?>"><a href="customer-reminder.php"><i class="fas fa-clock"></i> <span>Reminders</span></a></li>

    <li class="<?php if ($page == 'announcement') {
                  echo 'active';
                } ?>"><a href="announcement.php"><i class="fas fa-bullhorn"></i> <span>Announcement</span></a></li>

    <li class="<?php if ($page == 'my-plan') {
                  echo 'active';
                } ?>"><a href="my-plan.php"><i class="fas fa-heartbeat"></i> <span>My Plan</span></a></li>
    <li class="<?php if ($page == 'notification') {
                  echo 'active';
                } ?>"><a href="my-notifications.php"><i class="fas fa-envelope"></i> <span>Notifications</span></a></li>
    <li class="<?php if ($page == 'payments') {
                  echo 'active';
                } ?>"><a href="my-payments.php"><i class="fas fa-money-bill-wave"></i> <span>My Payments</span></a></li>
    <li class="<?php if ($page == 'report') {
                  echo 'active';
                } ?>"><a href="my-report.php"><i class="fas fa-file-alt"></i> <span>Reports</span></a></li>
  </ul>
</div>