<?php
include 'dbcon.php';
function desc($table) {
    global $con;
    echo "--- Table: $table ---\n";
    $res = mysqli_query($con, "DESCRIBE $table");
    if (!$res) {
        echo "Error: Table $table not found.\n";
        return;
    }
    while($row = mysqli_fetch_assoc($res)) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
}
desc('packages');
desc('rates');
desc('staffs');
desc('members');
desc('attendance');
desc('expenses');
desc('equipment');
desc('payment_history');
desc('announcements');
desc('todo');
?>
