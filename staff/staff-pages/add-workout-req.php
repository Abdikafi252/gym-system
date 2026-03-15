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

    // JSON Data
    $custom_data = mysqli_real_escape_string($con, $_POST['instruction']);

    $assigned_by = $_SESSION['user_id'];
    $date_assigned = date('Y-m-d');

    // Legacy fallback
    $instruction = "Structured Workout Plan Assigned.";

    $qry = "INSERT INTO workout_plans (member_id, plan_name, plan_duration, plan_goal, instruction, custom_data, assigned_by, date_assigned) 
            VALUES ('$member_id', '$plan_name', '$plan_duration', '$plan_goal', '$instruction', '$custom_data', '$assigned_by', '$date_assigned')";
    $result = mysqli_query($con, $qry);

    if ($result) {
        echo "<script>alert('Workout Plan Added Successfully'); window.location='manage-workout.php';</script>";
    } else {
        echo "<script>alert('Error Adding Workout Plan'); window.location='manage-workout.php';</script>";
    }
}
