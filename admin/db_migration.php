<?php
include "dbcon.php";

echo "<h2>Gym System - Database Migration for Diet & Workouts</h2>";

$queries = [
    // Diet Plans Table
    "ALTER TABLE diet_plans ADD COLUMN plan_name VARCHAR(255) NULL AFTER member_id",
    "ALTER TABLE diet_plans ADD COLUMN plan_duration VARCHAR(50) NULL AFTER plan_name",
    "ALTER TABLE diet_plans ADD COLUMN plan_goal VARCHAR(100) NULL AFTER plan_duration",
    "ALTER TABLE diet_plans ADD COLUMN custom_data TEXT NULL AFTER instruction",

    // Workout Plans Table
    "ALTER TABLE workout_plans ADD COLUMN plan_name VARCHAR(255) NULL AFTER member_id",
    "ALTER TABLE workout_plans ADD COLUMN plan_duration VARCHAR(50) NULL AFTER plan_name",
    "ALTER TABLE workout_plans ADD COLUMN plan_goal VARCHAR(100) NULL AFTER plan_duration",
    "ALTER TABLE workout_plans ADD COLUMN custom_data TEXT NULL AFTER instruction"
];

foreach ($queries as $q) {
    if (mysqli_query($con, $q)) {
        echo "<p style='color:green;'>Success: " . $q . "</p>";
    } else {
        echo "<p style='color:red;'>Error or already exists: " . mysqli_error($con) . "</p>";
    }
}

echo "<h3>Migration Complete.</h3>";
