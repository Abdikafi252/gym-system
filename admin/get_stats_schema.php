<?php
include "dbcon.php";

$tables = ['members', 'equipment', 'attendance', 'expenses'];

foreach ($tables as $table) {
    echo "TABLE: $table\n";
    $result = mysqli_query($con, "DESCRIBE $table");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "  {$row['Field']} - {$row['Type']}\n";
        }
    } else {
        echo "  Table not found or error.\n";
    }
    echo "\n";
}
