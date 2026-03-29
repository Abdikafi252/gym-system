<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    include 'dbcon.php';
    require_once __DIR__ . '/includes/accounting_engine.php';
    // Delete accounting records for this expense
    acc_delete_source_postings($con, 'expense', $id);
    $qry = "DELETE FROM expenses WHERE id='$id'";
    $result = mysqli_query($con, $qry);

    if ($result) {
        header('Location: expenses.php');
    } else {
        echo "ERROR!!";
    }
}
