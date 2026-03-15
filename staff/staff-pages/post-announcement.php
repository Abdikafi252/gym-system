<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
    exit();
}

if (!in_array($_SESSION['designation'], ['Manager', 'Cashier', 'Trainer', 'Trainer Assistant'])) {
    header('location:index.php');
    exit();
}

if (isset($_POST['message'])) {
    $message = $_POST["message"];
    $date = $_POST["date"];
    $branch_id = $_SESSION['branch_id'];

    include 'dbcon.php';
    $qry = "INSERT INTO announcements(message, date, branch_id) VALUES ('$message', '$date', '$branch_id')";
    $result = mysqli_query($conn, $qry);

    if ($result) {
        header('location:announcement.php');
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    header('location:announcement.php');
}
