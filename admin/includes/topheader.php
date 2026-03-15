<link rel="stylesheet" href="../css/pro-shell.css" />
<link rel="stylesheet" href="../css/system-polish.css" />
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
</style>

<div id="user-nav">
  <ul class="nav">
    <li class="dropdown" id="profile-messages">
      <a title="" href="#" data-toggle="dropdown" data-target="#profile-messages" class="dropdown-toggle shell-profile-toggle">
        <?php
        // Fetch Admin Info
        include 'dbcon.php';
        $uid = $_SESSION['user_id'];
        $q = "SELECT username, photo FROM admin WHERE user_id='$uid'";
        $r = mysqli_query($con, $q);
        $user_data = mysqli_fetch_assoc($r);
        $avatar = !empty($user_data['photo']) ? '../' . $user_data['photo'] : '../img/demo/av1.jpg';
        $uname = !empty($user_data['username']) ? $user_data['username'] : 'Admin';
        ?>
        <img src="<?php echo htmlspecialchars($avatar); ?>" alt="Profile" class="shell-avatar">
        <span class="text shell-user-name"><?php echo htmlspecialchars($uname); ?></span>
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
</div><!-- Visit codeastro.com for more projects -->