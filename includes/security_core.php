<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function ensure_security_tables($con)
{
    mysqli_query($con, "CREATE TABLE IF NOT EXISTS login_attempts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        attempt_key VARCHAR(255) NOT NULL,
        attempted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        success TINYINT(1) NOT NULL DEFAULT 0,
        INDEX idx_attempt_key_time (attempt_key, attempted_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

function get_client_ip()
{
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($parts[0]);
    }
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token)
{
    return !empty($_SESSION['csrf_token']) && !empty($token) && hash_equals($_SESSION['csrf_token'], $token);
}

function is_rate_limited($con, $attemptKey, $maxAttempts = 5, $windowMinutes = 15)
{
    $safeKey = mysqli_real_escape_string($con, $attemptKey);
    $qry = "SELECT COUNT(*) AS total FROM login_attempts
            WHERE attempt_key='$safeKey' AND success=0
            AND attempted_at >= (NOW() - INTERVAL " . (int)$windowMinutes . " MINUTE)";
    $res = mysqli_query($con, $qry);
    $row = $res ? mysqli_fetch_assoc($res) : ['total' => 0];
    return ((int)$row['total']) >= $maxAttempts;
}

function record_login_attempt($con, $attemptKey, $success = 0)
{
    $safeKey = mysqli_real_escape_string($con, $attemptKey);
    $successVal = $success ? 1 : 0;
    mysqli_query($con, "INSERT INTO login_attempts(attempt_key, success) VALUES('$safeKey', '$successVal')");
}

function clear_failed_attempts($con, $attemptKey)
{
    $safeKey = mysqli_real_escape_string($con, $attemptKey);
    mysqli_query($con, "DELETE FROM login_attempts WHERE attempt_key='$safeKey' AND success=0");
}

function normalize_designation($designation)
{
    $designation = trim((string)$designation);

    if ($designation === 'GYM Assistant') {
        return 'Trainer Assistant';
    }

    if ($designation === 'Security') {
        return 'Cleaner';
    }

    return $designation;
}

function current_designation()
{
    $current = $_SESSION['designation'] ?? '';
    $normalized = normalize_designation($current);

    if ($normalized !== $current) {
        $_SESSION['designation'] = $normalized;
    }

    return $normalized;
}
