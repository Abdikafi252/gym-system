<?php
// CLI/HTTP runner for automatic expired-member reminders
include_once __DIR__ . '/auto_sms_expiry.php';

echo "Auto reminder run completed at " . date('Y-m-d H:i:s') . PHP_EOL;
