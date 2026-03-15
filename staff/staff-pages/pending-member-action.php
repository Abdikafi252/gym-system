<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header('location:../index.php');
  exit();
}

require_once __DIR__ . '/../../includes/security_core.php';
$_SESSION['designation'] = current_designation();
if (!in_array($_SESSION['designation'], ['Manager', 'Cashier'])) {
  header('location:index.php');
  exit();
}

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

$branch_id = (int)($_SESSION['branch_id'] ?? 0);
$updated_by = mysqli_real_escape_string($con, $_SESSION['designation']);

$can_access_condition = "user_id='$member_id' AND status='Pending' AND (branch_id='$branch_id' OR branch_id IS NULL OR branch_id='0')";

if ($action === 'approve') {
  $branch_set = '';
  if ($branch_id > 0) {
    $branch_set = ", branch_id=CASE WHEN branch_id IS NULL OR branch_id='0' THEN '$branch_id' ELSE branch_id END";
  }
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
              updated_by='$updated_by',
              updated_at=NOW()$branch_set
          WHERE $can_access_condition";
} else {
  $qry = "DELETE FROM members WHERE $can_access_condition";
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
