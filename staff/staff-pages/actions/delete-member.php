<?php

session_start();
//the isset function to check username is already loged in and stored on the session
if(!isset($_SESSION['user_id'])){
header('location:../index.php');	
}

if(isset($_GET['id'])){
    $id=$_GET['id'];

    include 'dbcon.php';
    $branch_id = $_SESSION['branch_id'];

    // Delete payment_history for this member (branch-checked)
    mysqli_query($con, "DELETE FROM payment_history WHERE user_id=$id AND branch_id='$branch_id'");

    // Delete member (branch-checked)
    $qry = "DELETE FROM members WHERE user_id=$id AND branch_id='$branch_id'";
    $result = mysqli_query($con, $qry);

    if ($result && mysqli_affected_rows($con) > 0) {
        echo "DELETED";
        header('Location:../remove-member.php?msg=success');
    } else {
        header('Location:../remove-member.php?msg=error');
    }
}
?>
