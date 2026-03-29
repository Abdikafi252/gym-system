<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//the isset function to check username is already loged in and stored on the session
if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
}
?>

<?php

if (isset($_POST['fullname'])) {
    $fullname = $_POST["fullname"];
    $username = $_POST["username"];
    $gender = $_POST["gender"];
    $contact = $_POST["contact"];
    $address = $_POST["address"];
    $designation = $_POST["designation"];
    $id = $_POST["id"];
    // 
    include 'dbcon.php';

    // Handle Photo Update
    $current_photo_qry = "SELECT photo FROM staffs WHERE user_id='$id'";
    $current_photo_res = mysqli_query($con, $current_photo_qry);
    $current_photo_row = mysqli_fetch_assoc($current_photo_res);
    $photo = $current_photo_row['photo'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../../img/staff/";
        $file_name = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Delete old photo if it exists and is not default
            if (!empty($photo) && file_exists($target_dir . $photo)) {
                unlink($target_dir . $photo);
            }
            $photo = $file_name;
        }
    }

    //code after connection is successfull
    //update query
    $branch_id = $_SESSION['branch_id'];
    $qry = "update staffs set fullname='$fullname', username='$username', gender='$gender', contact='$contact', address='$address', designation='$designation', photo='$photo', updated_at=NOW() where user_id='$id' AND branch_id='$branch_id'";
    $result = mysqli_query($con, $qry); //query executes

    if (!$result) {
        echo "ERROR!!";
    } else {

        header('Location:staffs.php');
    }
} else {
    echo "<h3>YOU ARE NOT AUTHORIZED TO REDIRECT THIS PAGE. GO BACK to <a href='index.php'> DASHBOARD </a></h3>";
}
?>