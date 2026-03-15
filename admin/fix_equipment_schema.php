<?php
mysqli_report(MYSQLI_REPORT_OFF);
$con = mysqli_connect("localhost", "root", "", "gymnsb");

if (!$con) {
    die("Connection Failed: " . mysqli_connect_error());
}

$qry = "ALTER TABLE equipment MODIFY description VARCHAR(255)";
if (mysqli_query($con, $qry)) {
    echo "SUCCESS: Description column resized to VARCHAR(255).\n";
} else {
    echo "ERROR: " . mysqli_error($con) . "\n";
}

// Check other columns just in case
$qry2 = "ALTER TABLE equipment MODIFY name VARCHAR(100)";
mysqli_query($con, $qry2);
$qry3 = "ALTER TABLE equipment MODIFY vendor VARCHAR(100)";
mysqli_query($con, $qry3);
