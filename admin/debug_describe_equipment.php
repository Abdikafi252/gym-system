<?php
mysqli_report(MYSQLI_REPORT_OFF);
$con = mysqli_connect("localhost", "root", "", "gymnsb");

if (!$con) {
    die("Connection Failed: " . mysqli_connect_error());
}

$result = mysqli_query($con, "DESCRIBE equipment");
if (!$result) {
    die("Query Failed: " . mysqli_error($con));
}

echo "Column | Type | Null | Key | Default | Extra\n";
echo "--------------------------------------------------\n";
while ($row = mysqli_fetch_assoc($result)) {
    echo "{$row['Field']} | {$row['Type']} | {$row['Null']} | {$row['Key']} | {$row['Default']} | {$row['Extra']}\n";
}
