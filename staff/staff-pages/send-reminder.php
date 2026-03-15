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

    // 1. Insert into notifications table (System Notification)
    $message = "Dear Member,

Waxaan halkaan kugu ogeysiinaynaa in barnaamijkaaga xubinimo (Membership Program) uu dhawaan dhamaanayo. Fadlan hubi inaad bixiso dhammaan lacagaha kugu waajiba ka hor taariikhda kama dambaysta ah.

Waa arrin aad muhiim u ah inaad waqtigeeda ku bixiso lacagta si looga fogaado hakad ama joojin ku timaada adeegyada aad ka faa’iideysato.

Waxaan si weyn kuu qadarinaynaa macaamiil ahaan, waxaana rajeyneynaa inaan sii wadno ku adeegidda mustaqbalka.

Haddii aad qabto wax su’aalo ah, fadlan nala soo xiriir.

Mahadsanid,

Abdikafi Abdikadir Ali
Maamulka GYM";

    // Escape message for DB
    $msg_db = mysqli_real_escape_string($con, $message);

    $qry = "INSERT INTO notifications (member_id, message, status, sent_date) VALUES ('$user_id', '$msg_db', 'Unread', NOW())";

    // 2. Update members table 'reminder' flag (legacy support)
    $qry2 = "UPDATE members SET reminder='1' WHERE user_id='$user_id'";

    $result1 = mysqli_query($con, $qry);
    $result2 = mysqli_query($con, $qry2);

    if ($result1 && $result2) {
        // Redirect to WhatsApp
        // Format number: remove leading 0 if present, add country code if missing. Assuming Somalia 252.
        // Implementation note: Ideally, we should validate the number format.
        // For this snippet, we'll try a basic clean up.
        $wa_number = preg_replace('/[^0-9]/', '', $contact);
        if (substr($wa_number, 0, 3) != '252') {
            if (strlen($wa_number) == 9) { // e.g. 615xxxxxx
                $wa_number = '252' . $wa_number;
            }
        }

        $wa_message = urlencode($message);
        $wa_message = urlencode($message);

        // Auto-redirect to WhatsApp immediately
        echo "<script>
            window.location.href = 'https://wa.me/$wa_number?text=$wa_message';
        </script>";
    } else {
        echo "<script>alert('Error Sending Reminder'); window.location='reminders.php';</script>";
    }
}
