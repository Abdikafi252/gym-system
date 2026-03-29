<?php
/**
 * Gate Access Control - Biometric / RFID / Face Verification Core
 * Shared by: face-access.php, zk-push.php, and biometric_access.php
 * Returns Array: [status, action, name, message, days_left, photo]
 */

function validate_biometric_access($con, $biometric_id, $device_type = 'Biometric') {
    $biometric_id = trim($biometric_id);
    $today        = date('Y-m-d');
    $now_time     = date('H:i:s');
    $display_time = date('h:i A');
    $now_datetime = date('Y-m-d H:i:s');

    try {
        // ── 1. Find member by biometric_id OR user_id (Face terminals often use user_id) ────────────────
        $stmt = $con->prepare(
            "SELECT user_id, fullname, expiry_date, status, contact, branch_id, photo
             FROM members
             WHERE biometric_id = ? OR user_id = ?
             LIMIT 1"
        );
        $stmt->bind_param("ss", $biometric_id, $biometric_id);
        $stmt->execute();
        $member = $stmt->get_result()->fetch_assoc();

        if (!$member) {
            log_gate($con, null, 'UNKNOWN', "DENY: Not Found ($device_type)", $today, $now_time, null);
            return ['status' => 'DENY', 'message' => 'Member Not Registered', 'code' => 'NOT_FOUND'];
        }

        $user_id     = $member['user_id'];
        $fullname    = $member['fullname'];
        $expiry_date = $member['expiry_date'];
        $status      = $member['status'];
        $branch_id   = $member['branch_id'];
        $photo       = $member['photo'];

        // ── 2. Membership Status & Expiry Check ─────────────────────────────────
        $days_left = $expiry_date ? (int) floor((strtotime($expiry_date) - strtotime($today)) / 86400) : 0;

        if ($status !== 'Active') {
            log_gate($con, $user_id, $fullname, 'DENY: Inactive', $today, $now_time, $photo);
            return ['status' => 'DENY', 'code' => 'INACTIVE', 'name' => $fullname, 'message' => 'Account Inactive'];
        }

        if ($expiry_date && $expiry_date < $today) {
            log_gate($con, $user_id, $fullname, 'DENY: Expired', $today, $now_time, $photo);
            @include 'sms_helper.php';
            if (function_exists('sendExpiryAlert')) { sendExpiryAlert($fullname, $member['contact'] ?? ''); }
            return ['status' => 'DENY', 'code' => 'EXPIRED', 'name' => $fullname, 'message' => 'Expired', 'days_left' => $days_left];
        }

        // ── 3. Attendance Logic (24-Hour Rule Enforcement) ────────────────────
        // We only count 1 attendance per day. Multiple entries/exits just update the last check-out.
        $stmt = $con->prepare("SELECT id, check_in FROM attendance WHERE user_id = ? AND curr_date = ? LIMIT 1");
        $stmt->bind_param("is", $user_id, $today);
        $stmt->execute();
        $existing_record = $stmt->get_result()->fetch_assoc();

        if ($existing_record) {
            // Already checked in today. Just update the latest exit time.
            $stmt = $con->prepare("UPDATE attendance SET check_out = ? WHERE id = ?");
            $stmt->bind_param("si", $now_datetime, $existing_record['id']);
            $stmt->execute();
            $action = 'RE-ENTRY/LOGGED';
        } else {
            // First time today (Check-In)
            $stmt = $con->prepare("INSERT INTO attendance (user_id, member_id, curr_date, curr_time, present, check_in, access_status, branch_id) VALUES (?, ?, ?, ?, 1, ?, 'OPEN', ?)");
            $stmt->bind_param("iisssi", $user_id, $user_id, $today, $display_time, $now_datetime, $branch_id);
            $stmt->execute();
            
            // Only increment count once per 24 hours
            $con->query("UPDATE members SET attendance_count = attendance_count + 1 WHERE user_id = $user_id");
            $action = 'CHECK-IN';
        }

        log_gate($con, $user_id, $fullname, 'OPEN', $today, $now_time, $photo);
        
        return [
            'status' => 'OPEN',
            'action' => $action,
            'name' => $fullname,
            'message' => 'Welcome, ' . $fullname,
            'days_left' => $days_left,
            'photo' => $photo
        ];

    } catch (Exception $e) {
        return ['status' => 'ERROR', 'message' => $e->getMessage()];
    }
}

function log_gate($con, $user_id, $fullname, $result, $date, $time, $photo) {
    if (!$con) return;
    $stmt = $con->prepare("INSERT INTO gate_log (user_id, fullname, result, log_date, log_time, photo) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("isssss", $user_id, $fullname, $result, $date, $time, $photo);
        $stmt->execute();
    }
}

// Support direct execution (Legacy/Standalone calls)
if (basename($_SERVER['PHP_SELF']) == 'biometric_access.php') {
    include '../dbcon.php';
    $biometric_id = $_REQUEST['biometric_id'] ?? null;
    if (!$biometric_id) {
        $body = file_get_contents('php://input');
        if ($body) {
            parse_str($body, $parsed);
            $biometric_id = $parsed['PIN'] ?? $parsed['biometric_id'] ?? null;
        }
    }
    if ($biometric_id) {
        echo json_encode(validate_biometric_access($con, $biometric_id, 'Finger/Legacy'));
    } else {
        echo json_encode(['status' => 'DENY', 'message' => 'Missing ID']);
    }
}
?>
