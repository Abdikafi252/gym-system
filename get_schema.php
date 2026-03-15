<?php
$mysqli = new mysqli("localhost", "root", "", "gymnsb");
$result = $mysqli->query("SHOW TABLES");
while ($row = $result->fetch_row()) {
    $table = $row[0];
    echo "Table: $table\n";
    $res = $mysqli->query("DESCRIBE $table");
    while ($row2 = $res->fetch_assoc()) {
        echo "  " . $row2['Field'] . " " . $row2['Type'] . "\n";
    }
    echo "\n";
}
