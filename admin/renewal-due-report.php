<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
    exit;
}
include 'dbcon.php';

$days = isset($_GET['days']) ? (int)$_GET['days'] : 7;
if ($days < 1 || $days > 90) $days = 7;

$qry = "SELECT user_id, fullname, contact, services, paid_date, expiry_date, status
        FROM members
        WHERE status IN ('Active','Expired')
          AND expiry_date <= DATE_ADD(CURDATE(), INTERVAL $days DAY)
        ORDER BY expiry_date ASC";
$res = mysqli_query($con, $qry);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Renewal Due Report</title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../css/bootstrap.min.css" />
  <link rel="stylesheet" href="../css/matrix-style.css" />
  <link href="../font-awesome/css/all.css" rel="stylesheet" />
</head>
<body>
<?php include 'includes/header-content.php'; ?>
<?php include 'includes/topheader.php'; ?>
<?php $page = 'renewal-due'; include 'includes/sidebar.php'; ?>
<div id="content">
  <div id="content-header">
    <div id="breadcrumb"><a href="index.php" class="tip-bottom"><i class="fas fa-home"></i> Home</a> <a href="#" class="current">Renewal Due Report</a></div>
  </div>
  <div class="container-fluid">
    <div class="widget-box">
      <div class="widget-title"><span class="icon"><i class="fas fa-calendar-alt"></i></span><h5>Renewals Due</h5></div>
      <div class="widget-content">
        <form method="GET" class="form-inline" style="margin-bottom:10px;">
          <label>Due in next&nbsp;</label>
          <select name="days" class="span2">
            <option value="7" <?php if($days==7) echo 'selected'; ?>>7 days</option>
            <option value="15" <?php if($days==15) echo 'selected'; ?>>15 days</option>
            <option value="30" <?php if($days==30) echo 'selected'; ?>>30 days</option>
          </select>
          <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Filter</button>
        </form>
        <table class="table table-bordered table-striped">
          <thead><tr><th>ID</th><th>Name</th><th>Contact</th><th>Service</th><th>Paid Date</th><th>Expiry Date</th><th>Status</th></tr></thead>
          <tbody>
          <?php if ($res && mysqli_num_rows($res) > 0): while($row=mysqli_fetch_assoc($res)): ?>
            <tr>
              <td><?php echo $row['user_id']; ?></td>
              <td><?php echo htmlspecialchars($row['fullname']); ?></td>
              <td><?php echo htmlspecialchars($row['contact']); ?></td>
              <td><?php echo htmlspecialchars($row['services']); ?></td>
              <td><?php echo htmlspecialchars($row['paid_date']); ?></td>
              <td><?php echo htmlspecialchars($row['expiry_date']); ?></td>
              <td><span class="label <?php echo ($row['status']=='Active') ? 'label-success' : 'label-important'; ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
            </tr>
          <?php endwhile; else: ?>
            <tr><td colspan="7" style="text-align:center;">No records found.</td></tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</body>
</html>
