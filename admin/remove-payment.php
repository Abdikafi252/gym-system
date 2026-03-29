<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
    exit;
}
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    include 'dbcon.php';
    require_once __DIR__ . '/includes/accounting_engine.php';
    // Delete accounting records for this payment
    acc_delete_source_postings($con, 'payment_history', $id);
    $qry = "DELETE FROM payment_history WHERE id='$id'";
    $result = mysqli_query($con, $qry);
    if ($result) {
        header('Location: payment.php?msg=Payment+deleted+successfully');
    } else {
        echo "ERROR!!";
    }
}
?>
