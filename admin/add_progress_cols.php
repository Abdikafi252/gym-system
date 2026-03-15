<?php
mysqli_report(MYSQLI_REPORT_OFF);
$con = mysqli_connect("localhost", "root", "", "gymnsb");

if (!$con) {
    die("Connection Failed: " . mysqli_connect_error());
}

$columns = [
    'height' => "varchar(10) DEFAULT ''",
    'chest' => "varchar(10) DEFAULT ''",
    'waist' => "varchar(10) DEFAULT ''",
    'thigh' => "varchar(10) DEFAULT ''",
    'arms' => "varchar(10) DEFAULT ''",
    'fat' => "varchar(10) DEFAULT ''"
];

foreach ($columns as $col => $def) {
    $check = mysqli_query($con, "SHOW COLUMNS FROM members LIKE '$col'");
    if (mysqli_num_rows($check) == 0) {
        $result = mysqli_query($con, "ALTER TABLE members ADD COLUMN $col $def");
        if ($result) {
            echo "Added $col successfully.\n";
        } else {
            echo "Failed to add $col: " . mysqli_error($con) . "\n";
        }
    } else {
        echo "Column $col already exists.\n";
    }
}
