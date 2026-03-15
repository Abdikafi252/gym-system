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

  #sidebar.open > ul > li > a > span.label {
    display: inline-block !important;
    visibility: visible !important;
    opacity: 1 !important;
    margin-left: auto !important;
    float: none !important;
  }

  #sidebar .user-profile-sidebar .user-name,
  #sidebar .user-profile-sidebar .user-role {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
  }
}

.shell-profile-meta {
  display: inline-flex;
  flex-direction: column;
  align-items: flex-start;
  line-height: 1.15;
}

.shell-user-role {
  font-size: 11px;
  color: rgba(255, 255, 255, 0.78);
  font-weight: 600;
}
</style>

<div id="user-nav">
  <ul class="nav">
    <li class="dropdown" id="profile-messages">
      <a title="" href="#" data-toggle="dropdown" data-target="#profile-messages" class="dropdown-toggle shell-profile-toggle">
        <?php
        require_once __DIR__ . '/../../includes/security_core.php';
        include 'dbcon.php';
        $uid = $_SESSION['user_id'];
        $q = mysqli_query($con, "SELECT fullname, username, photo, designation FROM staffs WHERE user_id='$uid'");
        $u_data = $q ? mysqli_fetch_array($q) : [];
        $avatar = !empty($u_data['photo']) ? '../../img/staff/' . $u_data['photo'] : '../../img/demo/av1.jpg';
        $fallback_name = $_SESSION['fullname'] ?? ($_SESSION['username'] ?? 'Staff User');
        $uname = !empty($u_data['fullname']) ? $u_data['fullname'] : (!empty($u_data['username']) ? $u_data['username'] : $fallback_name);
        $urole = !empty($u_data['designation']) ? normalize_designation($u_data['designation']) : ($_SESSION['designation'] ?? 'Staff Member');
        $_SESSION['fullname'] = $uname;
        ?>
        <img src="<?php echo htmlspecialchars($avatar); ?>" alt="Profile" class="shell-avatar">
        <span class="shell-profile-meta">
          <span class="text shell-user-name"><?php echo htmlspecialchars($uname); ?></span>
          <span class="shell-user-role"><?php echo htmlspecialchars($urole); ?></span>
        </span>
        <b class="caret shell-caret"></b>
      </a>
      <ul class="dropdown-menu">
        <li><a href="profile.php"><i class="fas fa-user-cog"></i> Profile Settings</a></li>
        <li class="divider"></li>
        <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a></li>
      </ul>
    </li>
    <li class="logout-now">
      <a href="../logout.php" title="Log Out">
        <i class="fas fa-sign-out-alt shell-logout-icon"></i>
        Log Out
      </a>
    </li>
  </ul>
</div>