<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
}

if (isset($_GET['id'])) {
    include 'dbcon.php';
    $user_id = $_GET['id'];

    // Fetch member's contact
    $mem_qry = "SELECT fullname, contact FROM members WHERE user_id='$user_id'";
    $mem_res = mysqli_query($con, $mem_qry);
    $mem_row = mysqli_fetch_array($mem_res);
    $contact = $mem_row['contact'];
    $fullname = $mem_row['fullname'];

    // English Reward Message
    $message = "Dear Member,

We are writing to inform you that your membership program is nearing its expiration. Please ensure that you make all necessary payments before the final due date.

It is very important to make your payments on time to avoid any interruptions or suspensions of the services you enjoy.

We greatly value you as a customer and look forward to continuing to serve you in the future.

If you have any questions, please feel free to contact us.

Thank you,

Abdikafi Abdikadir Ali
GYM Management";

    // Escape message for DB
    $msg_db = mysqli_real_escape_string($con, $message);

    $qry = "INSERT INTO notifications (member_id, message, status, sent_date) VALUES ('$user_id', '$msg_db', 'Unread', NOW())";

    // 2. Update members table 'reminder' flag (legacy support)
    $qry2 = "UPDATE members SET reminder='1' WHERE user_id='$user_id'";

    $result1 = mysqli_query($con, $qry);
    $result2 = mysqli_query($con, $qry2);

    if ($result1 && $result2) {
        // Redirect to WhatsApp
        $wa_number = preg_replace('/[^0-9]/', '', $contact);
        if (substr($wa_number, 0, 3) != '252') {
            if (strlen($wa_number) == 9) { // e.g. 615xxxxxx
                $wa_number = '252' . $wa_number;
            }
        }

        $wa_message = urlencode($message);

        // Auto-redirect to WhatsApp immediately
        echo "<script>
            window.location.href = 'https://wa.me/$wa_number?text=$wa_message';
        </script>";
    } else {
        echo "<script>alert('Error Sending Reminder'); window.location='reminders.php';</script>";
    }
}
?>
