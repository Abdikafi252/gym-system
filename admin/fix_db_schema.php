<?php
mysqli_report(MYSQLI_REPORT_OFF);
$con = mysqli_connect("localhost", "root", "", "gymnsb");

if (!$con) {
    die("Connection Failed: " . mysqli_connect_error());
}

$qry = "ALTER TABLE members MODIFY fullname VARCHAR(100)";
if (mysqli_query($con, $qry)) {
    echo "SUCCESS: Fullname column resized to VARCHAR(100).\n";
} else {
    echo "ERROR: " . mysqli_error($con) . "\n";
}

// While we are at it, let's resize username too just in case
$qry2 = "ALTER TABLE members MODIFY username VARCHAR(100)";
mysqli_query($con, $qry2);
