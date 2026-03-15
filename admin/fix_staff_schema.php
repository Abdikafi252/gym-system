<?php
include 'dbcon.php';
// Check if photo column exists
$result = mysqli_query($con, "SHOW COLUMNS FROM staffs LIKE 'photo'");
if (mysqli_num_rows($result) == 0) {
    if (mysqli_query($con, "ALTER TABLE staffs ADD COLUMN photo VARCHAR(255) DEFAULT NULL AFTER contact")) {
        echo "SUCCESS: photo column added.\n";
    } else {
        echo "ERROR: " . mysqli_error($con) . "\n";
    }
} else {
    echo "EXISTS: photo column already present.\n";
}
