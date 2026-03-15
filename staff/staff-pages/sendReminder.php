<?php

session_start();
$app_debug = getenv('APP_DEBUG');
$is_debug = ($app_debug === '1' || strtolower((string)$app_debug) === 'true');
ini_set('display_errors', $is_debug ? 1 : 0);
ini_set('display_startup_errors', $is_debug ? 1 : 0);
error_reporting($is_debug ? E_ALL : 0);

if(!isset($_SESSION['user_id'])){
header('location:../index.php');	
}

if(isset($_GET['id'])){
$id=$_GET['id'];

include 'dbcon.php';
include_once '../../api/sms_helper.php';

@mysqli_query($con, "ALTER TABLE members ADD COLUMN IF NOT EXISTS reminder_last_sent_at DATETIME NULL");

$member_qry = mysqli_query($con, "SELECT fullname, contact FROM members WHERE user_id='$id' LIMIT 1");
$member = mysqli_fetch_assoc($member_qry);

$sent_sms = false;
$sent_wa = false;
if ($member && !empty($member['contact'])) {
    $sent_sms = sendExpiryAlert($member['fullname'], $member['contact']);
    $wa_msg = "Asc " . $member['fullname'] . ", xubinimadaada gym-ka way dhacday. Fadlan cusbooneysii si aad u sii wadato adeegga. Mahadsanid.";
    $sent_wa = sendWhatsApp($member['contact'], $wa_msg);
}

$qry="UPDATE members SET reminder = '1', reminder_last_sent_at = NOW() where user_id=$id";
$result=mysqli_query($con,$qry);

if($result){
    if ($sent_sms || $sent_wa) {
        header('Location: payment.php?type=success&msg=' . urlencode('Notification sent successfully (SMS/WhatsApp)!'));
    } else {
        header('Location: payment.php?type=warning&msg=' . urlencode('Reminder flag updated, but message delivery failed. Check API config/logs.'));
    }
    exit;
    
}else{
    echo"ERROR!!";
}
}
?><!-- Visit codeastro.com for more projects -->
