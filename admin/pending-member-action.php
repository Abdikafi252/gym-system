<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header('location:../index.php');
  exit();
}

require_once __DIR__ . '/../includes/security_core.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('location:pending-members.php?err=invalid');
  exit();
}

if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
  header('location:pending-members.php?err=csrf');
  exit();
}

$member_id = isset($_POST['member_id']) ? (int)$_POST['member_id'] : 0;
$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($member_id <= 0 || !in_array($action, ['approve', 'reject'])) {
  header('location:pending-members.php?err=invalid');
  exit();
}

include 'dbcon.php';
if (!isset($con) && isset($conn)) {
  $con = $conn;
}

if ($action === 'approve') {
  $qry = "UPDATE members
    SET status='Active',
        paid_date=CURDATE(),
        amount=CASE
          WHEN COALESCE(amount, 0) > 0 THEN amount
          ELSE COALESCE((SELECT ph.amount FROM payment_history ph WHERE ph.user_id=members.user_id ORDER BY ph.id DESC LIMIT 1), amount)
        END,
        paid_amount=CASE
          WHEN COALESCE(amount, 0) > 0 THEN amount
          ELSE COALESCE((SELECT ph.amount FROM payment_history ph WHERE ph.user_id=members.user_id ORDER BY ph.id DESC LIMIT 1), paid_amount)
        END,
        expiry_date=CASE
          WHEN COALESCE(plan, 0) > 0 THEN DATE_ADD(CURDATE(), INTERVAL plan MONTH)
          ELSE expiry_date
        END,
        updated_by='Admin',
        updated_at=NOW()
    WHERE user_id='$member_id' AND status='Pending'";
} else {
  $qry = "DELETE FROM members
    WHERE user_id='$member_id' AND status='Pending'";
}
mysqli_query($con, $qry);

if (mysqli_affected_rows($con) <= 0) {
  header('location:pending-members.php?err=notfound');
  exit();
}

if ($action === 'approve') {
  header('location:pending-members.php?msg=approved');
} else {
  header('location:pending-members.php?msg=deleted');
}
exit();
