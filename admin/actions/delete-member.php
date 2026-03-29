<?php

session_start();
//the isset function to check username is already loged in and stored on the session
if(!isset($_SESSION['user_id'])){
header('location:../index.php');	
exit;
}

if(isset($_GET['id'])){
    $id = (int)$_GET['id'];
    include 'dbcon.php';
    require_once __DIR__ . '/../includes/db_helper.php';
    require_once __DIR__ . '/../includes/accounting_engine.php';

    // 1. SOFT DELETE: Update member status to 'Deleted' instead of deleting the record.
    // This preserves the member's link to their financial history (payment_history, journal_entries).
    $result = safe_query($con, "UPDATE members SET status='Deleted' WHERE user_id=?", "i", [$id]);

    if($result){
        // We no longer delete payment_history or accounting records.
        // They remain in the system for accurate financial reporting.
        echo "ARCHIVED";
        header('Location:../remove-member.php');
    }else{
        echo "ERROR!!";
    }
}
?>
