<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
    exit;
}

include 'dbcon.php';

if (isset($_POST['update_profile'])) {
    $user_id = $_SESSION['user_id'];
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    // Handle File Upload
    $photo_path = "";
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $upload_dir = '../img/uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_name = time() . '_' . basename($_FILES['profile_pic']['name']);
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_file)) {
            $photo_path = "img/uploads/" . $file_name;
        }
    }

    // Build Query
    $update_query = "UPDATE admin SET username='$username'";

    if (!empty($password)) {
        $password_hash = mysqli_real_escape_string($con, password_hash($password, PASSWORD_DEFAULT));
        $update_query .= ", password='$password_hash'";
    }

    if (!empty($photo_path)) {
        $update_query .= ", photo='$photo_path'";
    }

    $update_query .= " WHERE user_id='$user_id'";

    $result = mysqli_query($con, $update_query);

    if ($result) {
        // Update session variables if username changed
        $_SESSION['username'] = $username; // Although usually login sets this...
        echo "<script>alert('Profile Updated Successfully!'); window.location.href='profile.php';</script>";
    } else {
        echo "<script>alert('Error Updating Profile: " . mysqli_error($con) . "'); window.location.href='profile.php';</script>";
    }
} else {
    header("Location: profile.php");
}
