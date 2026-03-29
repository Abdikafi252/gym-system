<?php
/**
 * DA-Catcher: HTTP Callback Receiver for DA-T12-RV1109 Face Recognition Terminal
 * Points to: http://[your-server]/Gym-System/api/da-catcher.php
 */

header('Content-Type: application/json');
include '../dbcon.php';
require_once 'biometric_access.php';

// 1. Log Raw Request for Debugging (Optional but Recommended during Setup)
$raw_body = file_get_contents('php://input');
$log_entry = date('Y-m-d H:i:s') . " - RAW: " . $raw_body . "\n";
file_put_contents('da_catcher_debug.txt', $log_entry, FILE_APPEND);

// 2. Parse Terminal Payload
// Standard RV1109 terminals often send JSON with 'employeeNo' or 'userId'
$data = json_decode($raw_body, true);

// If not JSON, check $_POST
if (!$data) {
    $data = $_POST;
}

// 3. Extract Identifier (Terminal usually sends UserID as 'employeeNo' or 'personId')
$identifier = $data['employeeNo'] ?? $data['personId'] ?? $data['userId'] ?? $data['PIN'] ?? null;

if (!$identifier) {
    echo json_encode([
        'result' => 0,
        'success' => false,
        'message' => 'No identifier found in payload'
    ]);
    exit;
}

// 4. Process Attendance
$result = validate_biometric_access($con, $identifier, 'Face-Terminal');

// 5. Response to Terminal
// Most terminals expect a specific success format (usually { "result": 1 } or { "success": true })
if ($result['status'] === 'OPEN') {
    echo json_encode([
        'result' => 1,
        'success' => true,
        'message' => $result['message'],
        'action' => $result['action']
    ]);
} else {
    echo json_encode([
        'result' => 0,
        'success' => false,
        'message' => $result['message']
    ]);
}
?>
