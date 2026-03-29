<?php
require 'dbcon.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$tables = ['equipment', 'expenses', 'attendance', 'announcements'];
foreach ($tables as $t) {
    try {
        mysqli_query($con, "ALTER TABLE $t ADD COLUMN branch_id INT(11) NOT NULL DEFAULT 0");
        echo "$t added branch_id.\n";
    } catch (Exception $e) {
        echo "$t error: " . $e->getMessage() . "\n";
    }
}
?>
