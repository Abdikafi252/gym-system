<?php

/**
 * Auto SMS Expiry Sender
 * This script checks for members whose membership has expired and sends them an SMS.
 * It is designed to be included in the Dashboard (Admin/Staff) so it runs frequently.
 */

// Handle DB connection if not already included
if (!isset($con) && !isset($conn)) {
    // Attempt to include dbcon.php from common locations relative to this file
    if (file_exists(__DIR__ . '/../admin/dbcon.php')) {
        include_once __DIR__ . '/../admin/dbcon.php';
    } elseif (file_exists(__DIR__ . '/../staff/dbcon.php')) {
        include_once __DIR__ . '/../staff/dbcon.php';
    }
}

// Check again
if (!isset($con)) {
    // If still not set, try manual connection (last resort)
    $con = mysqli_connect("localhost", "root", "", "gymnsb");
}

include_once __DIR__ . '/sms_helper.php';

$current_date = date('Y-m-d');

// Ensure reminder columns exist
@mysqli_query($con, "ALTER TABLE members ADD COLUMN IF NOT EXISTS reminder TINYINT(1) DEFAULT 0");
@mysqli_query($con, "ALTER TABLE members ADD COLUMN IF NOT EXISTS reminder_last_sent_at DATETIME NULL");

// 1. Select expired members who haven't received reminder in current cycle
// `reminder` is reset to 0 upon successful renewal in payment flow.
// We limit to 20 per run to avoid timeout/spamming.
$qry = "SELECT user_id, fullname, contact, expiry_date, reminder 
        FROM members 
    WHERE expiry_date < '$current_date' 
    AND (reminder = 0 OR reminder IS NULL)
    LIMIT 20";

$result = mysqli_query($con, $qry);

if ($result) {
    while ($row = mysqli_fetch_array($result)) {
        $user_id = $row['user_id'];
        $name = $row['fullname'];
        $contact = $row['contact'];

        if (empty($contact)) {
            continue;
        }

        // Send SMS + WhatsApp
        $sent_sms = sendExpiryAlert($name, $contact);
        $wa_msg = "Asc $name, Xubinimadaada GYM-ka way dhacday. Fadlan cusbooneysii.\nMahadsanid.";
        $sent_wa = sendWhatsApp($contact, $wa_msg);

        if ($sent_sms || $sent_wa) {
            // Mark as sent for current expired cycle
            mysqli_query($con, "UPDATE members SET reminder = 1, reminder_last_sent_at = NOW() WHERE user_id = '$user_id'");

            $channel = ($sent_sms && $sent_wa) ? 'sms+whatsapp' : ($sent_sms ? 'sms' : 'whatsapp');
            $log = date('Y-m-d H:i:s') . " | EXPIRED REMINDER SENT | user_id=$user_id | name=$name | channel=$channel\n";
            @file_put_contents(__DIR__ . '/auto_reminder_log.txt', $log, FILE_APPEND);
        } else {
            $log = date('Y-m-d H:i:s') . " | EXPIRED REMINDER FAILED | user_id=$user_id | name=$name\n";
            @file_put_contents(__DIR__ . '/auto_reminder_log.txt', $log, FILE_APPEND);
        }
    }
}
