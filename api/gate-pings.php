<?php
header('Content-Type: application/json');
include '../dbcon.php';

// Fetch last 10 logs
$result = $con->query("SELECT * FROM gate_log ORDER BY id DESC LIMIT 10");
$logs = [];
while ($row = $result->fetch_assoc()) {
    $logs[] = $row;
}

echo json_encode($logs);
?>
