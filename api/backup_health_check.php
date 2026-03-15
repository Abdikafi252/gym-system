<?php
$backupDir = __DIR__ . '/../DATABASE FILE/backups';
$logFile = $backupDir . '/backup-health.log';

if (!is_dir($backupDir)) {
    @mkdir($backupDir, 0777, true);
}

$files = glob($backupDir . '/*.sql');
$latestFile = null;
$latestTime = 0;

foreach ($files as $file) {
    $mtime = filemtime($file);
    if ($mtime > $latestTime) {
        $latestTime = $mtime;
        $latestFile = $file;
    }
}

$status = 'OK';
$message = '';

if (!$latestFile) {
    $status = 'FAIL';
    $message = 'No backup file found.';
} else {
    $hoursSince = (time() - $latestTime) / 3600;
    if ($hoursSince > 30) {
        $status = 'FAIL';
        $message = 'Latest backup is older than 30 hours: ' . basename($latestFile);
    } else if (filesize($latestFile) < 1024) {
        $status = 'FAIL';
        $message = 'Latest backup file is too small: ' . basename($latestFile);
    } else {
        $message = 'Latest backup healthy: ' . basename($latestFile);
    }
}

$line = date('Y-m-d H:i:s') . " | $status | $message" . PHP_EOL;
@file_put_contents($logFile, $line, FILE_APPEND);

echo $line;

if ($status === 'FAIL') {
    include_once __DIR__ . '/sms_helper.php';
    $alertPhone = getenv('ADMIN_ALERT_PHONE');
    if ($alertPhone) {
        @sendSMS($alertPhone, 'GYM Backup Alert: ' . $message);
        @sendWhatsApp($alertPhone, 'GYM Backup Alert: ' . $message);
    }
}
