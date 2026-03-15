<?php
include 'dbcon.php';
if (!isset($conn) && isset($con)) $conn = $con;

$sql = "ALTER TABLE members ADD COLUMN photo VARCHAR(255) DEFAULT '' AFTER contact";
if (mysqli_query($conn, $sql)) {
    echo "Column 'photo' added successfully.";
} else {
    echo "Error adding column: " . mysqli_error($conn);
}
