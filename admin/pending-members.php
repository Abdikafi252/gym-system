<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header('location:../index.php');
  exit();
}

require_once __DIR__ . '/../includes/security_core.php';

include 'dbcon.php';
if (!isset($con) && isset($conn)) {
  $con = $conn;
}

$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
$err = isset($_GET['err']) ? $_GET['err'] : '';

$qry = "SELECT user_id, fullname, username, contact, services, plan, dor, status, branch_id
        FROM members
        WHERE status='Pending'
        ORDER BY dor DESC, user_id DESC";
$result = mysqli_query($con, $qry);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>M * A GYM System</title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../css/bootstrap.min.css" />
  <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
  <link rel="stylesheet" href="../css/matrix-style.css" />
  <link rel="stylesheet" href="../css/matrix-media.css" />
  <link rel="stylesheet" href="../css/system-polish.css" />
  <link href="../font-awesome/css/all.css" rel="stylesheet" />
</head>
<body>
  <?php include 'includes/header-content.php'; ?>
  <?php include 'includes/topheader.php'; ?>
  <?php $page = 'pending-members'; include 'includes/sidebar.php'; ?>

  <div id="content">
    <div id="content-header">
      <div id="breadcrumb">
        <a href="index.php" class="tip-bottom"><i class="fas fa-home"></i> Bogga Hore</a>
        <a href="#" class="current">Pending Members</a>
      </div>
      <h1 class="text-center">Pending Members <i class="fas fa-user-clock"></i></h1>
    </div>

    <div class="container-fluid">
      <hr>
      <?php if ($msg === 'approved'): ?>
        <div class="alert alert-success">Xubinta waa la aqbalay (Active).</div>
      <?php elseif ($msg === 'deleted'): ?>
        <div class="alert alert-warning">Xubinta waa la diiday oo si toos ah ayaa loo tirtiray.</div>
      <?php elseif ($err === 'csrf'): ?>
        <div class="alert alert-error">Codsiga waa la diiday. Fadlan dib isku day.</div>
      <?php elseif ($err === 'notfound'): ?>
        <div class="alert alert-error">Xubin pending ah lama helin.</div>
      <?php elseif ($err === 'invalid'): ?>
        <div class="alert alert-error">Codsi aan sax ahayn.</div>
      <?php endif; ?>

      <div class="row-fluid">
        <div class="span12">
          <div class="widget-box">
            <div class="widget-title">
              <span class="icon"><i class="fas fa-list"></i></span>
              <h5>Liiska Xubnaha Sugaya Aqbalid</h5>
            </div>
            <div class="widget-content nopadding">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Magaca</th>
                    <th>Username</th>
                    <th>Contact</th>
                    <th>Service</th>
                    <th>Plan</th>
                    <th>Taariikh Diiwaan</th>
                    <th>Branch</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i = 1;
                  if ($result && mysqli_num_rows($result) > 0):
                    while ($row = mysqli_fetch_assoc($result)):
                  ?>
                  <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['contact']); ?></td>
                    <td><?php echo htmlspecialchars($row['services']); ?></td>
                    <td><?php echo (int)$row['plan']; ?> Bilood</td>
                    <td><?php echo htmlspecialchars($row['dor']); ?></td>
                    <td><?php echo (int)$row['branch_id']; ?></td>
                    <td><span class="label label-warning">Pending</span></td>
                    <td>
                      <form action="pending-member-action.php" method="POST" style="display:inline-block; margin:0;">
                        <input type="hidden" name="member_id" value="<?php echo (int)$row['user_id']; ?>">
                        <input type="hidden" name="action" value="approve">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="submit" class="btn btn-success btn-mini"><i class="fas fa-check"></i> Aqbal</button>
                      </form>
                      <form action="pending-member-action.php" method="POST" style="display:inline-block; margin:0 0 0 6px;">
                        <input type="hidden" name="member_id" value="<?php echo (int)$row['user_id']; ?>">
                        <input type="hidden" name="action" value="reject">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="submit" class="btn btn-danger btn-mini"><i class="fas fa-times"></i> Diid</button>
                      </form>
                    </td>
                  </tr>
                  <?php
                    endwhile;
                  else:
                  ?>
                  <tr>
                    <td colspan="10" class="text-center" style="padding:20px;">Pending members ma jiraan.</td>
                  </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="../js/jquery.min.js"></script>
  <script src="../js/bootstrap.min.js"></script>
  <script src="../js/matrix.js"></script>
</body>
</html>
