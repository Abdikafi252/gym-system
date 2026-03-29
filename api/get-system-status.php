<?php
header('Content-Type: application/json');
include '../dbcon.php';

// 1. Get last 5 gate logs
$gate_logs = [];
$res1 = mysqli_query($con, "SELECT fullname, result, log_time FROM gate_log ORDER BY id DESC LIMIT 5");
while ($row = mysqli_fetch_assoc($res1)) {
    $gate_logs[] = $row;
}

// 2. Get last 5 attendance logs
$attendance_logs = [];
$res2 = mysqli_query($con, "SELECT m.fullname, a.check_in, a.access_status FROM attendance a JOIN members m ON a.user_id = m.user_id ORDER BY a.id DESC LIMIT 5");
while ($row = mysqli_fetch_assoc($res2)) {
    $attendance_logs[] = $row;
}

// 3. Get Terminal Config
require_once __DIR__ . '/../includes/FaceTerminal.php';
$terminal = new FaceTerminal($con);
$config = $terminal->getConfig();

echo json_encode([
    'gate_logs' => $gate_logs,
    'attendance_logs' => $attendance_logs,
    'terminal_config' => [
        'ip' => $config['ip'],
        'enabled' => $config['enabled']
    ]
]);
?>
