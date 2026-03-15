<?php

session_start();
//the isset function to check username is already loged in and stored on the session
if (!isset($_SESSION['user_id'])) {
     header('location:../index.php');
}

include('../dbcon.php');
date_default_timezone_set('Africa/Nairobi');
$todays_date = date('Y-m-d');

$user_id = $_GET['id'];

$sql = "UPDATE attendance SET check_out = NOW() WHERE user_id='$user_id' AND curr_date = '$todays_date' AND check_out IS NULL";
$res = $con->query($sql);
?>
<script>
     // alert("Delete Successfully");
     window.location = "../attendance.php";
</script>