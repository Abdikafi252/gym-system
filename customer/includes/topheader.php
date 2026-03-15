<link rel="stylesheet" href="../../css/pro-shell.css" />
<link rel="stylesheet" href="../../css/system-polish.css" />
<style>
@media (max-width: 991px) {
  #sidebar.open > ul > li > a {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
  }

  #sidebar.open > ul > li > a > span:not(.label) {
    display: inline-block !important;
    visibility: visible !important;
    opacity: 1 !important;
    width: auto !important;
    height: auto !important;
    overflow: visible !important;
    clip: auto !important;
    position: static !important;
    float: none !important;
    text-indent: 0 !important;
    font-size: 14px !important;
    line-height: 1.35 !important;
    color: inherit !important;
    flex: 1 1 auto !important;
  }

  #sidebar .user-profile-sidebar .user-name,
  #sidebar .user-profile-sidebar .user-role,
  #sidebar .user-profile-sidebar .user-role-badge {
    display: inline-flex !important;
    visibility: visible !important;
    opacity: 1 !important;
  }

  #sidebar .user-profile-sidebar .user-name {
    display: block !important;
  }
}
</style>

<div id="user-nav">
  <ul class="nav">
    <li class="dropdown" id="profile-messages">
      <a title="" href="#" data-toggle="dropdown" data-target="#profile-messages" class="dropdown-toggle shell-profile-toggle">
        <?php
        include_once 'dbcon.php';
        if (!isset($_SESSION['user_id'])) {
          session_start();
        }
        $uid = $_SESSION['user_id'];
        $q = mysqli_query($con, "SELECT fullname, photo FROM members WHERE user_id='$uid'");
        $u_data = mysqli_fetch_array($q);
        $avatar = !empty($u_data['photo']) ? '../../img/members/' . $u_data['photo'] : '../../img/demo/av1.jpg';
        $uname = !empty($u_data['fullname']) ? $u_data['fullname'] : 'Customer';
        ?>
        <img src="<?php echo htmlspecialchars($avatar); ?>" alt="Profile" class="shell-avatar">
        <span class="text shell-user-name"><?php echo htmlspecialchars($uname); ?></span>
        <b class="caret shell-caret"></b>
      </a>
      <ul class="dropdown-menu">
        <li><a href="profile.php"><i class="fas fa-user-cog"></i> Profile Settings</a></li>
        <li class="divider"></li>
        <li><a href="../pages/my-report.php"><i class="fas fa-file-alt"></i> My Report</a></li>
        <li class="divider"></li>
        <li><a href="to-do.php"><i class="fas fa-check-circle"></i> My Tasks</a></li>
        <li class="divider "></li>
        <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a></li>
      </ul>
    </li>

    <li class="logout-now"><a title="" href="../logout.php"><i class="fas fa-sign-out-alt shell-logout-icon"></i> <span class="text">Log Out</span></a></li>
  </ul>
</div>