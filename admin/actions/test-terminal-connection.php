<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

include '../dbcon.php';
require_once '../../includes/FaceTerminal.php';

$ft = new FaceTerminal($con);

// A simple command to check if device is alive
// For Uni-Ubi, /getSystemConfig is valid. 
// For others, /config/get or just checking the port might work.
$res = $ft->syncPerson('test_ping', 'Ping Test', '../../img/user.png');

if (isset($res['status']) && $res['status'] === 'error' && strpos($res['message'], 'Connection Error') !== false) {
    echo json_encode(['status' => 'error', 'message' => $res['message']]);
} else if (isset($res['success']) || (isset($res['status']) && $res['status'] === 'success') || isset($res['data'])) {
    echo json_encode(['status' => 'success', 'message' => 'Terminal responded!']);
} else {
    // If we get any JSON back, the device is there.
    if (!empty($res)) {
         echo json_encode(['status' => 'success', 'message' => 'Device reached!']);
    } else {
         echo json_encode(['status' => 'error', 'message' => 'No response from device. Check IP and Port.']);
    }
}
?>
