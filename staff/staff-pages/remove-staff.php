<?php
session_start();
//the isset function to check username is already loged in and stored on the session
if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $branch_id = $_SESSION['branch_id'];

    include 'dbcon.php';

    // Restrict deletion to own branch
    $qry = "DELETE FROM staffs WHERE user_id='$id' AND branch_id='$branch_id'";
    $result = mysqli_query($con, $qry);

    if ($result && mysqli_affected_rows($con) > 0) {
        header('Location:staffs.php?msg=deleted');
    } else {
        header('Location:staffs.php?msg=error');
    }
}
?>
