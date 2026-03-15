<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
    exit;
}
include 'dbcon.php';

$expiring_today = 0;
$expired_total = 0;
$due_7days = 0;
$sent_today = 0;

$r = mysqli_query($con, "SELECT COUNT(*) c FROM members WHERE expiry_date = CURDATE() AND status='Active'");
if ($r) $expiring_today = (int)mysqli_fetch_assoc($r)['c'];
$r = mysqli_query($con, "SELECT COUNT(*) c FROM members WHERE expiry_date < CURDATE()");
if ($r) $expired_total = (int)mysqli_fetch_assoc($r)['c'];
$r = mysqli_query($con, "SELECT COUNT(*) c FROM members WHERE expiry_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)");
if ($r) $due_7days = (int)mysqli_fetch_assoc($r)['c'];
$r = mysqli_query($con, "SELECT COUNT(*) c FROM members WHERE DATE(reminder_last_sent_at)=CURDATE()");
if ($r) $sent_today = (int)mysqli_fetch_assoc($r)['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Notification Center</title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../css/bootstrap.min.css" />
  <link rel="stylesheet" href="../css/matrix-style.css" />
  <link href="../font-awesome/css/all.css" rel="stylesheet" />
  <style>.notif-card{background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:16px;margin-bottom:12px;}</style>
</head>
<body>
<?php include 'includes/header-content.php'; ?>
<?php include 'includes/topheader.php'; ?>
<?php $page='notifications'; include 'includes/sidebar.php'; ?>
<div id="content">
  <div id="content-header"><div id="breadcrumb"><a href="index.php"><i class="fas fa-home"></i> Home</a> <a href="#" class="current">Notifications</a></div></div>
  <div class="container-fluid">
    <div class="notif-card"><h4><i class="fas fa-bell"></i> Expiring Today: <?php echo $expiring_today; ?></h4></div>
    <div class="notif-card"><h4><i class="fas fa-user-clock"></i> Due in 7 Days: <?php echo $due_7days; ?></h4></div>
    <div class="notif-card"><h4><i class="fas fa-user-times"></i> Expired Members: <?php echo $expired_total; ?></h4></div>
    <div class="notif-card"><h4><i class="fas fa-paper-plane"></i> Reminders Sent Today: <?php echo $sent_today; ?></h4></div>
  </div>
</div>
</body>
</html>
