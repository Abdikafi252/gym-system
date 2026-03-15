<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    include 'dbcon.php';
    $qry = "DELETE FROM diet_plans WHERE id='$id'";
    $result = mysqli_query($con, $qry);

    if ($result) {
        header('Location: manage-diet.php');
    } else {
        echo "ERROR!!";
    }
}
