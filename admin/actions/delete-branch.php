<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    include '../dbcon.php';

    $qry = "DELETE FROM branches WHERE id=$id";
    $result = mysqli_query($con, $qry);

    if ($result) {
        header('location:../manage-branches.php?success=2');
    } else {
        header('location:../manage-branches.php?error=2');
    }
} else {
    header('location:../manage-branches.php');
}
