<?php
include 'admin/dbcon.php';

function add_column_if_missing($table, $column, $definition) {
    global $con;
    $check = mysqli_query($con, "SHOW COLUMNS FROM `$table` LIKE '$column'");
    if (mysqli_num_rows($check) == 0) {
        $q = "ALTER TABLE `$table` ADD `$column` $definition";
        if (mysqli_query($con, $q)) {
            echo "Added $column to $table.\n";
        } else {
            echo "Error adding $column to $table: " . mysqli_error($con) . "\n";
        }
    } else {
        echo "$column already exists in $table.\n";
    }
}

add_column_if_missing('packages', 'branch_id', "INT(11) NOT NULL DEFAULT 1");
add_column_if_missing('rates', 'branch_id', "INT(11) NOT NULL DEFAULT 1");
?>
