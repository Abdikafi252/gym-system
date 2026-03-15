<?php
session_start();

$is_cli = (php_sapi_name() === 'cli');

if (!$is_cli && !isset($_SESSION['user_id'])) {
    header('location:../index.php');
    exit;
}

include 'dbcon.php';

mysqli_report(MYSQLI_REPORT_OFF);
ini_set('display_errors', 1);
error_reporting(E_ALL);

$created = 0;
$skipped = 0;
$errors = 0;

$branch_filter = '';
if (!$is_cli && isset($_SESSION['branch_id']) && $_SESSION['branch_id'] !== '') {
    $branch_id = mysqli_real_escape_string($con, $_SESSION['branch_id']);
    $branch_filter = " WHERE branch_id = '$branch_id' ";
}

$members_qry = "SELECT user_id, fullname, amount, paid_amount, discount_amount, discount_type, plan, services, paid_date, expiry_date, branch_id FROM members" . $branch_filter;
$members_res = mysqli_query($con, $members_qry);

if (!$members_res) {
    die('Query failed: ' . mysqli_error($con));
}

$recorded_by = (!$is_cli && isset($_SESSION['fullname'])) ? $_SESSION['fullname'] : 'System Backfill';
$recorded_by = mysqli_real_escape_string($con, $recorded_by);

while ($member = mysqli_fetch_assoc($members_res)) {
    $user_id = (int)$member['user_id'];
    $paid_date = $member['paid_date'];

    if (empty($paid_date) || $paid_date === '0000-00-00') {
        $skipped++;
        continue;
    }

    $paid_date_esc = mysqli_real_escape_string($con, $paid_date);

    $exists_qry = "SELECT id FROM payment_history WHERE user_id='$user_id' AND paid_date='$paid_date_esc' LIMIT 1";
    $exists_res = mysqli_query($con, $exists_qry);

    if ($exists_res && mysqli_num_rows($exists_res) > 0) {
        $skipped++;
        continue;
    }

    $fullname = mysqli_real_escape_string($con, $member['fullname']);
    $amount = (float)$member['amount'];
    $paid_amount = (float)$member['paid_amount'];
    $discount_amount = (float)$member['discount_amount'];
    $discount_type = mysqli_real_escape_string($con, $member['discount_type']);
    $plan = (int)$member['plan'];
    $services = mysqli_real_escape_string($con, $member['services']);
    $expiry_date = mysqli_real_escape_string($con, $member['expiry_date']);
    $branch_id_val = mysqli_real_escape_string($con, $member['branch_id']);

    $insert_qry = "INSERT INTO payment_history
        (user_id, fullname, amount, paid_amount, discount_amount, discount_type, plan, services, paid_date, expiry_date, branch_id, recorded_by)
        VALUES
        ('$user_id', '$fullname', '$amount', '$paid_amount', '$discount_amount', '$discount_type', '$plan', '$services', '$paid_date_esc', '$expiry_date', '$branch_id_val', '$recorded_by')";

    if (mysqli_query($con, $insert_qry)) {
        $created++;
    } else {
        $errors++;
    }
}

if ($is_cli) {
    echo "Backfill Payment History - Completed\n";
    echo "Records Created: $created\n";
    echo "Records Skipped: $skipped\n";
    echo "Errors: $errors\n";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backfill Payment History</title>
    <link rel="stylesheet" href="../../css/bootstrap.min.css" />
</head>
<body style="padding:20px;">
    <h3>Backfill Payment History - Completed</h3>
    <p><strong>Records Created:</strong> <?php echo $created; ?></p>
    <p><strong>Records Skipped:</strong> <?php echo $skipped; ?></p>
    <p><strong>Errors:</strong> <?php echo $errors; ?></p>
    <a class="btn btn-primary" href="payment.php">Ku noqo Payment</a>
</body>
</html>
