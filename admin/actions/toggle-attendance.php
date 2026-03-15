<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

include('../dbcon.php');
require_once __DIR__ . '/../../includes/audit_helper.php';
date_default_timezone_set('Africa/Nairobi');

$user_id = $_GET['id'];
$date = $_GET['date'];
$curr_time = date('h:i A');

// Allow attendance only within a valid paid membership period for that date
$safe_user_id = mysqli_real_escape_string($con, $user_id);
$safe_date = mysqli_real_escape_string($con, $date);
$period_qry = "SELECT id FROM payment_history WHERE user_id='$safe_user_id' AND paid_date <= '$safe_date' AND expiry_date >= '$safe_date' LIMIT 1";
$period_res = mysqli_query($con, $period_qry);

if (!$period_res || mysqli_num_rows($period_res) == 0) {
    // Fallback for older records without payment history
    $member_qry = mysqli_query($con, "SELECT dor, paid_date, expiry_date FROM members WHERE user_id='$safe_user_id' LIMIT 1");
    $member = $member_qry ? mysqli_fetch_assoc($member_qry) : null;
    $fallback_start = !empty($member['paid_date']) ? $member['paid_date'] : (!empty($member['dor']) ? $member['dor'] : null);
    $fallback_end = !empty($member['expiry_date']) ? $member['expiry_date'] : null;

    if (empty($fallback_start) || strtotime($date) < strtotime($fallback_start) || (!empty($fallback_end) && strtotime($date) > strtotime($fallback_end))) {
        echo json_encode(["status" => "error", "message" => "Attendance-kan kuma jiro period-ka xubinnimada (renewal period)."]);
        exit;
    }
}

// Prevent future date checkins
if (strtotime($date) > strtotime(date('Y-m-d'))) {
    echo json_encode(["status" => "error", "message" => "Cannot mark attendance for future dates"]);
    exit;
}

$check_qry = "SELECT * FROM attendance WHERE curr_date = '$date' AND user_id = '$user_id'";
$check_res = mysqli_query($con, $check_qry);

if (mysqli_num_rows($check_res) == 0) {
    // 0 -> 1: Check In (Incomplete)
    $sql = "INSERT INTO attendance (user_id, member_id, curr_date, curr_time, present, check_in) 
            VALUES ('$user_id', '$user_id', '$date', '$curr_time', 1, NOW())";

    if ($con->query($sql) === TRUE) {
        $con->query("UPDATE members SET attendance_count = attendance_count + 1 WHERE user_id='$user_id'");
        $actorId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '0';
        audit_log($con, 'admin', $actorId, 'attendance_check_in', 'attendance', $user_id . '|' . $date, 'Attendance checked in');
        echo json_encode([
            "status" => "success",
            "state" => "incomplete",
            "check_in" => $curr_time
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database error"]);
    }
} else {
    $row = mysqli_fetch_array($check_res);
    $check_in_time = $row['curr_time'];

    if (empty($row['check_out']) || strpos($row['check_out'], '0000') !== false) {
        // 1 -> 2: Check Out (Complete)
        $sql = "UPDATE attendance SET check_out = NOW() WHERE user_id='$user_id' AND curr_date = '$date'";
        if ($con->query($sql) === TRUE) {
            $out_time = date('h:i A'); // Approximate since we just used NOW()
            $actorId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '0';
            audit_log($con, 'admin', $actorId, 'attendance_check_out', 'attendance', $user_id . '|' . $date, 'Attendance checked out');
            echo json_encode([
                "status" => "success",
                "state" => "complete",
                "check_in" => $check_in_time,
                "check_out" => $out_time
            ]);
        }
    } else {
        // 2 -> 0: Delete Record (Absent)
        $sql = "DELETE FROM attendance WHERE user_id='$user_id' AND curr_date = '$date'";
        if ($con->query($sql) === TRUE) {
            $con->query("UPDATE members SET attendance_count = GREATEST(0, attendance_count - 1) WHERE user_id='$user_id'");
            $actorId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '0';
            audit_log($con, 'admin', $actorId, 'attendance_mark_absent', 'attendance', $user_id . '|' . $date, 'Attendance removed and marked absent');
            echo json_encode([
                "status" => "success",
                "state" => "absent"
            ]);
        }
    }
}
