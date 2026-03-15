<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header('location:../../index.php');
    exit;
}

include 'dbcon.php';

if (isset($_POST['update_profile'])) {
    $user_id = $_SESSION['user_id'];
    $fullname = mysqli_real_escape_string($con, $_POST['fullname']);
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    // Photo update removed as per requirements - Users use registration photo

    // Build Query
    $update_query = "UPDATE staffs SET fullname='$fullname', username='$username'";

    if (!empty($password)) {
        $password_hash = mysqli_real_escape_string($con, password_hash($password, PASSWORD_DEFAULT));
        $update_query .= ", password='$password_hash'";
    }

    $update_query .= " WHERE user_id='$user_id'";

    $result = mysqli_query($con, $update_query);

    if ($result) {
        $_SESSION['username'] = $username;
        echo "<script>alert('Profile Updated Successfully!'); window.location.href='profile.php';</script>";
    } else {
        echo "<script>alert('Error Updating Profile: " . mysqli_error($con) . "'); window.location.href='profile.php';</script>";
    }
} else {
    header("Location: profile.php");
}
