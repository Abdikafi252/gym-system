<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('location:../../index.php');
}

if (isset($_POST['branch_name'])) {
    include '../dbcon.php';
    $branch_name = mysqli_real_escape_string($con, $_POST['branch_name']);
    $address = mysqli_real_escape_string($con, $_POST['address']);
    $contact = mysqli_real_escape_string($con, $_POST['contact']);

    $qry = "INSERT INTO branches (branch_name, address, contact) VALUES ('$branch_name', '$address', '$contact')";
    $result = mysqli_query($con, $qry);

    if ($result) {
        header('location:../manage-branches.php?success=1');
    } else {
        header('location:../manage-branches.php?error=1');
    }
} else {
    header('location:../manage-branches.php');
}
