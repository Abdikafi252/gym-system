<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

include '../../dbcon.php';
require_once __DIR__ . '/../../includes/FaceTerminal.php';

$terminal = new FaceTerminal($con);
if (!$terminal->isEnabled()) {
    die(json_encode(['status' => 'error', 'message' => 'Terminal is disabled or IP not configured.']));
}

// Fetch all members who have a photo
$query = "SELECT user_id, fullname, biometric_id, photo FROM members WHERE photo != '' AND photo IS NOT NULL";
$result = mysqli_query($con, $query);

$success_count = 0;
$fail_count = 0;
$errors = [];

while ($row = mysqli_fetch_assoc($result)) {
    $photoPath = __DIR__ . '/../../img/members/' . $row['photo'];
    $syncResult = $terminal->syncPerson($row['biometric_id'], $row['fullname'], $photoPath);
    
    if (isset($syncResult['status']) && $syncResult['status'] === 'success' || (isset($syncResult['code']) && $syncResult['code'] == 0)) {
        $success_count++;
    } else {
        $fail_count++;
        $errors[] = "ID {$row['biometric_id']}: " . ($syncResult['message'] ?? 'Unknown Error');
    }
    
    // Slow down slightly to avoid overwhelming the device
    usleep(100000); // 0.1 seconds
}

echo json_encode([
    'status' => 'done',
    'success' => $success_count,
    'failed' => $fail_count,
    'errors' => array_slice($errors, 0, 10) // Show first 10 errors
]);
?>
