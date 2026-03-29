<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
    exit;
}
include 'dbcon.php';
require_once __DIR__ . '/includes/accounting_engine.php';

$page = 'repair_owner_capital_postings';
$msg = '';
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

if (isset($_POST['repair'])) {
    $fixed = 0;
    $failed = 0;
    $res = mysqli_query($con, "SELECT id, contribution_date, amount, reference_no, notes, funded_by FROM owner_capital_contributions WHERE YEAR(contribution_date) = $year");
    if ($res && mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_assoc($res)) {
            $sourceId = $row['id'];
            $check = acc_source_already_posted($con, 'owner_capital', $sourceId);
            if (!$check) {
                $result = acc_record_owner_capital(
                    $con,
                    $row['contribution_date'],
                    $row['amount'],
                    $row['reference_no'],
                    $row['notes'],
                    $row['funded_by'],
                    isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin'
                );
                if ($result['ok']) {
                    $fixed++;
                } else {
                    $failed++;
                }
            }
        }
    }
    $msg = "Repair completed. Fixed: $fixed, Failed: $failed.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Repair Owner Capital Postings</title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../css/bootstrap.min.css" />
  <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
  <link rel="stylesheet" href="../css/matrix-style.css" />
  <link rel="stylesheet" href="../css/matrix-media.css" />
  <link href="../font-awesome/css/all.css" rel="stylesheet" />
</head>
<body>
<?php include 'includes/header-content.php'; ?>
<?php include 'includes/topheader.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<div id="content">
  <div class="container-fluid">
    <h1>Repair Owner Capital Postings</h1>
    <?php if ($msg !== '') { echo '<div class="alert alert-info">' . htmlspecialchars($msg) . '</div>'; } ?>
    <form method="post">
      <button type="submit" name="repair" class="btn btn-primary">Repair Missing Owner Capital Journal Entries</button>
    </form>
    <p>Press the button to re-post any missing owner capital journal entries for this year.</p>
  </div>
</div>
<script src="../js/jquery.min.js"></script><script src="../js/bootstrap.min.js"></script><script src="../js/matrix.js"></script>
</body>
</html>
