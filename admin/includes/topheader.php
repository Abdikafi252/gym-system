<?php require_once 'lang.php'; ?>
<link rel="stylesheet" href="../css/pro-shell.css" />
<link rel="stylesheet" href="../css/system-polish.css" />
<link rel="stylesheet" href="../css/premium.css" />
<link rel="stylesheet" href="../css/premium-header.css" />
<style>
/* Language Switcher Styling */
.lang-switcher { margin-right: 15px; border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; padding: 2px 10px; display: inline-flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.05); }
.lang-link { color: #94a3b8 !important; font-size: 11px !important; font-weight: 700; text-transform: uppercase; text-decoration: none !important; transition: color 0.2s; }
.lang-link:hover { color: #fff !important; }
.lang-link.active { color: #3b82f6 !important; }
.lang-divider { color: rgba(255,255,255,0.2); font-size: 10px; }

@media (max-width: 991px) {
  #sidebar.open > ul > li > a { display: flex !important; align-items: center !important; gap: 10px !important; }
  #sidebar.open > ul > li > a > span:not(.label) { display: inline-block !important; visibility: visible !important; opacity: 1 !important; width: auto !important; height: auto !important; overflow: visible !important; clip: auto !important; position: static !important; float: none !important; text-indent: 0 !important; font-size: 14px !important; line-height: 1.35 !important; color: inherit !important; flex: 1 1 auto !important; }
  #sidebar.open > ul > li > a > span.label { display: inline-block !important; visibility: visible !important; opacity: 1 !important; margin-left: auto !important; float: none !important; }
  #sidebar .user-profile-sidebar .user-name, #sidebar .user-profile-sidebar .user-role { display: block !important; visibility: visible !important; opacity: 1 !important; }
}
</style>

<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include_once 'dbcon.php';

// Branch filter global logic
$is_staff = (isset($_SESSION['designation']) && in_array($_SESSION['designation'], ['Manager', 'Trainer', 'Trainer Assistant', 'Cashier', 'Cleaner']));
$uid = $_SESSION['user_id'];
$is_manager = (isset($_SESSION['designation']) && $_SESSION['designation'] == 'Manager');

if (!$is_staff) {
    if (isset($_POST['global_branch_id'])) {
        $_SESSION['branch_id'] = $_POST['global_branch_id'];
        session_write_close();
        echo "<script>window.location.href = window.location.href.split('?')[0];</script>";
        exit;
    }
    $branches_res = mysqli_query($con, "SELECT id, branch_name FROM branches ORDER BY branch_name ASC");
    $selected_branch = isset($_SESSION['branch_id']) ? $_SESSION['branch_id'] : '0';
} else {
    $_SESSION['branch_id'] = $_SESSION['branch_id'] ?? ($_SESSION['staff_branch_id'] ?? '');
}

// Fetch User Info
if ($is_manager) {
    $q = "SELECT username, photo FROM staffs WHERE user_id='$uid'";
} else {
    $q = "SELECT username, photo FROM admin WHERE user_id='$uid'";
}
$r = mysqli_query($con, $q);
$user_data = mysqli_fetch_assoc($r);

if ($is_manager) {
    $avatar = !empty($user_data['photo']) ? '../img/staff/' . $user_data['photo'] : '../img/demo/av1.jpg';
} else {
    $avatar = (!empty($user_data['photo']) && strpos($user_data['photo'], '../') === false) ? '../' . $user_data['photo'] : ($user_data['photo'] ?? '../img/demo/av1.jpg');
}
$uname = !empty($user_data['username']) ? $user_data['username'] : ($is_manager ? 'Manager' : 'Admin');
?>

<div id="user-nav">
  <ul class="nav">
    <li style="padding-top: 10px; padding-right: 15px; display: flex; align-items: center;">
      
      <?php if (!$is_staff): ?>
      <!-- Premium Branch Switcher -->
      <div class="premium-branch-switcher">
        <span class="branch-label"><i class="fas fa-building"></i> Branch:</span>
        <form method="post" id="globalBranchForm" style="margin:0;">
          <select name="global_branch_id" id="global_branch_id" onchange="this.form.submit();" class="premium-select">
            <option value="0" <?php if($selected_branch=='0') echo 'selected'; ?>>All Branches</option>
            <?php while ($b = mysqli_fetch_assoc($branches_res)): ?>
              <option value="<?php echo $b['id']; ?>" <?php if($selected_branch==$b['id']) echo 'selected'; ?>><?php echo htmlspecialchars($b['branch_name']); ?></option>
            <?php endwhile; ?>
          </select>
        </form>
      </div>
      <?php endif; ?>

      <!-- Language Switcher -->
      <div class="lang-switcher">
        <a href="?set_lang=so" class="lang-link <?php echo ($_SESSION['lang'] == 'so') ? 'active' : ''; ?>">🇸🇴 SO</a>
        <span class="lang-divider">|</span>
        <a href="?set_lang=en" class="lang-link <?php echo ($_SESSION['lang'] == 'en') ? 'active' : ''; ?>">🇺🇸 EN</a>
      </div>
    </li>

    <li class="dropdown" id="profile-messages">
      <a title="" href="#" data-toggle="dropdown" data-target="#profile-messages" class="dropdown-toggle shell-profile-toggle">
        <img src="<?php echo htmlspecialchars($avatar); ?>" alt="Profile" class="shell-avatar">
        <span class="text shell-user-name">
            <?php 
            echo htmlspecialchars($uname); 
            if ($is_manager) {
                $bid = (int)($_SESSION['branch_id'] ?? 0);
                $bq = mysqli_query($con, "SELECT branch_name FROM branches WHERE id='$bid'");
                $br = mysqli_fetch_assoc($bq);
                echo $br ? ' | ' . htmlspecialchars($br['branch_name']) : '';
            }
            ?>
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