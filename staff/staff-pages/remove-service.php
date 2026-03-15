<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    include 'dbcon.php';
    $qry = "DELETE FROM rates WHERE id='$id'";
    $result = mysqli_query($con, $qry);

    if ($result) {
        header('Location: services.php');
    } else {
        echo "ERROR!!";
    }
}
