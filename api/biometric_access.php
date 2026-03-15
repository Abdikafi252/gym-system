<?php
header('Content-Type: application/json');
include '../dbcon.php';

// biometric_id from request (GET or POST)
$biometric_id = $_REQUEST['biometric_id'] ?? null;

if (!$biometric_id) {
    echo json_encode(['status' => 'DENY', 'message' => 'Missing ID']);
    exit;
}

try {
    // 1. Fetch member by biometric_id - Using Prepared Statements for Security
    $stmt = $con->prepare("SELECT user_id, fullname, expiry_date, status, contact FROM members WHERE biometric_id = ?");
    $stmt->bind_param("s", $biometric_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $member = $result->fetch_assoc();

    if (!$member) {
        echo json_encode(['status' => 'DENY', 'message' => 'Member Not Found']);
        exit;
    }

    $user_id = $member['user_id'];
    $fullname = $member['fullname'];
    $expiry_date = $member['expiry_date'];
    $status = $member['status'];

    // 2. Validate Membership Status and Expiry
    $today = date('Y-m-d');
    if ($status !== 'Active' || ($expiry_date && $expiry_date < $today)) {
        // Trigger SMS Alert if not sent today
        include 'sms_helper.php';
        sendExpiryAlert($fullname, $member['contact'] ?? '');

        echo json_encode(['status' => 'DENY', 'message' => 'Membership Expired', 'name' => $fullname]);
        exit;
    }

    // 3. Attendance Logic (Check-in/Check-out)
    // Check if there's an active session (checked in today but not checked out)
    $stmt = $con->prepare("SELECT id, check_in FROM attendance WHERE member_id = ? AND DATE(check_in) = ? AND check_out IS NULL LIMIT 1");
    $stmt->bind_param("is", $user_id, $today);
    $stmt->execute();
    $active_session = $stmt->get_result()->fetch_assoc();

    if ($active_session) {
        // Already checked in today -> Process Check-out
        $attendance_id = $active_session['id'];
        $checkout_time = date('Y-m-d H:i:s');
        $stmt = $con->prepare("UPDATE attendance SET check_out = ? WHERE id = ?");
        $stmt->bind_param("si", $checkout_time, $attendance_id);
        $stmt->execute();

        echo json_encode([
            'status' => 'OPEN',
            'action' => 'CHECK-OUT',
            'message' => 'Goodbye ' . $fullname,
            'name' => $fullname
        ]);
    } else {
        // Not checked in today -> Process Check-in
        $checkin_time = date('Y-m-d H:i:s');
        $stmt = $con->prepare("INSERT INTO attendance (member_id, check_in, access_status) VALUES (?, ?, 'OPEN')");
        $stmt->bind_param("is", $user_id, $checkin_time);
        $stmt->execute();

        echo json_encode([
            'status' => 'OPEN',
            'action' => 'CHECK-IN',
            'message' => 'Welcome ' . $fullname,
            'name' => $fullname
        ]);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'ERROR', 'message' => $e->getMessage()]);
}
