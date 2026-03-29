<?php
session_start();
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
    $branch_id = $_POST["branch_id"];
    $salary = (float)($_POST['salary'] ?? 0);
    // 
    include 'dbcon.php';
    require_once 'includes/db_helper.php';

    // Handle Photo Update
    $current_photo_row = safe_fetch_assoc($con, "SELECT photo FROM staffs WHERE user_id=?", "i", [$id]);
    $photo = $current_photo_row['photo'] ?? null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../img/staff/";
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
    $sql = "UPDATE staffs SET fullname=?, username=?, gender=?, contact=?, address=?, designation=?, photo=?, branch_id=?, salary=?, updated_at=NOW() WHERE user_id=?";
    $params = [$fullname, $username, $gender, $contact, $address, $designation, $photo, $branch_id, $salary, $id];
    $result = safe_query($con, $sql, "ssssssssdi", $params);
    if (!$result) {
        // Debug output for DB error
        if (isset($con->error)) {
            echo "<div style='color:red;font-weight:bold;'>DB ERROR: " . htmlspecialchars($con->error) . "</div>";
        } else {
            echo "ERROR!! (No DB error message)";
        }
    } else {
        header('Location:staffs.php');
    }
} else {
    echo "<h3>YOU ARE NOT AUTHORIZED TO REDIRECT THIS PAGE. GO BACK to <a href='index.php'> DASHBOARD </a></h3>";
}
?>