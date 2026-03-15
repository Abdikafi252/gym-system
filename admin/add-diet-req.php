<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'dbcon.php';
    $member_id = $_POST['member_id'];

    // New Columns
    $plan_name = mysqli_real_escape_string($con, $_POST['plan_name']);
    $plan_duration = mysqli_real_escape_string($con, $_POST['plan_duration']);
    $plan_goal = mysqli_real_escape_string($con, $_POST['plan_goal']);

    // The JSON string from Alpine.js
    $custom_data = mysqli_real_escape_string($con, $_POST['instruction']);

    $assigned_by = $_SESSION['user_id'];
    $date_assigned = date('Y-m-d');

    // Store a simple summary in instruction for legacy fallback if needed, or just leave it empty
    $instruction = "Structured Diet Plan Assigned.";

    $qry = "INSERT INTO diet_plans (member_id, plan_name, plan_duration, plan_goal, instruction, custom_data, assigned_by, date_assigned) 
            VALUES ('$member_id', '$plan_name', '$plan_duration', '$plan_goal', '$instruction', '$custom_data', '$assigned_by', '$date_assigned')";
    $result = mysqli_query($con, $qry);

    if ($result) {
        echo "<script>alert('Diet Plan Assigned Successfully!'); window.location='manage-diet.php';</script>";
    } else {
        echo "<script>alert('Error Assigning Diet Plan'); window.location='manage-diet.php';</script>";
    }
}
