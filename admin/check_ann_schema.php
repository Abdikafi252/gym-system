<?php
include 'dbcon.php';
$tables = ['announcements', 'todo', 'members', 'staffs'];
foreach ($tables as $t) {
    echo "<h3>Table: $t</h3>";
    $res = mysqli_query($con, "DESCRIBE $t");
    echo "<table border='1'><tr><th>Field</th><th>Type</th></tr>";
    while ($row = mysqli_fetch_assoc($res)) {
        echo "<tr><td>".$row['Field']."</td><td>".$row['Type']."</td></tr>";
    }
    echo "</table>";
}
?>
