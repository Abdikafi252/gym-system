<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');

//the isset function to check username is already loged in and stored on the session
if (!isset($_SESSION['user_id'])) {
  header('location:../index.php');
}
?>
<!-- Visit codeastro.com for more projects -->
<!DOCTYPE html>
<html lang="so">

<head>
  <title>M * A GYM System</title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../../css/bootstrap.min.css" />
  <link rel="stylesheet" href="../../css/bootstrap-responsive.min.css" />
  <link rel="stylesheet" href="../../css/fullcalendar.css" />
  <link rel="stylesheet" href="../../css/matrix-style.css" />
  <link rel="stylesheet" href="../../css/matrix-media.css" />
  <link rel="stylesheet" href="../../css/status-badges.css" />
  <link href="../../font-awesome/css/fontawesome.css" rel="stylesheet" />
  <link href="../../font-awesome/css/all.css" rel="stylesheet" />
  <link rel="stylesheet" href="../../css/jquery.gritter.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
      font-family: 'Outfit', sans-serif;
    }
  </style>
</head>

<body>

  <!--Header-part-->
  <?php include '../includes/header-content.php'; ?>
  <!--close-Header-part-->

  <!-- Visit codeastro.com for more projects -->
  <!--top-Header-menu-->
  <?php include '../includes/header.php' ?>
  <!--close-top-Header-menu-->

  <!--sidebar-menu-->
  <?php $page = "members";
  include '../includes/sidebar.php' ?>
  <!--sidebar-menu-->

  <div id="content">
    <div id="content-header">
      <div id="breadcrumb"> <a href="index.php" title="Tag Bogga Hore" class="tip-bottom"><i class="fas fa-home"></i> Bogga Hore</a> <a href="#" class="current">Xubnaha Diiwaangashan</a> </div>
      <h1 class="text-center">Liiska Xubnaha Diiwaangashan <i class="fas fa-group"></i></h1>
    </div>
    <div class="container-fluid">
      <hr>
      <div class="row-fluid">
        <div class="span12">

          <div class='widget-box'>
            <div class='widget-title'> <span class='icon'> <i class='fas fa-th'></i> </span>
              <h5>Jadwalka Xubnaha</h5>
            </div>
            <style>
              .members-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
                gap: 25px;
                padding: 25px;
                background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
              }

              .member-card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-radius: 24px;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.02);
                padding: 16px;
                border: 1px solid rgba(255, 255, 255, 0.6);
                position: relative;
                transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                display: flex;
                flex-direction: column;
                overflow: hidden;
              }

              .member-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 4px;
                background: linear-gradient(90deg, #10b981, #3b82f6);
                opacity: 0;
                transition: opacity 0.3s;
              }

              .member-card:hover {
                transform: translateY(-12px) scale(1.02);
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
                border-color: #10b981;
              }

              .member-card:hover::before {
                opacity: 1;
              }

              .card-badge {
                position: absolute;
                top: 0;
                right: 0;
                background: #fee2e2;
                color: #ef4444;
                font-size: 11px;
                font-weight: 700;
                padding: 6px 16px;
                border-bottom-left-radius: 20px;
                text-transform: uppercase;
                letter-spacing: 0.025em;
              }

              .card-badge.active {
                background: #dcfce7;
                color: #10b981;
              }

              .card-header-row {
                display: flex;
                align-items: center;
                margin-bottom: 14px;
              }

              .member-avatar {
                width: 70px;
                height: 70px;
                border-radius: 50%;
                background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
                color: #0369a1;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 32px;
                margin-right: 16px;
                flex-shrink: 0;
                border: 3px solid #fff;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
              }

              .member-avatar.female {
                background: linear-gradient(135deg, #fce7f3 0%, #fbcfe8 100%);
                color: #be185d;
              }

              .member-primary-info {
                flex-grow: 1;
              }

              .member-name {
                font-size: 18px;
                font-weight: 800;
                color: #1e293b;
                margin: 0 0 4px 0 !important;
                line-height: 1.2;
              }

              .member-id {
                font-size: 12px;
                color: #64748b;
                font-weight: 600;
                margin: 0 !important;
                display: inline-block;
                padding: 2px 8px;
                background: #f8fafc;
                border-radius: 6px;
              }

              .card-details-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 10px;
                margin-bottom: 12px;
                padding: 12px;
                background: #f8fafc;
                border-radius: 16px;
              }

              .detail-box {
                display: flex;
                flex-direction: column;
              }

              .detail-label {
                font-size: 10px;
                color: #94a3b8;
                margin-bottom: 4px;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 0.05em;
              }

              .detail-val {
                font-size: 14px;
                color: #334155;
                font-weight: 600;
              }

              .detail-val.amount {
                color: #ef4444;
                font-weight: 800;
              }

              .detail-val.expiry {
                color: #10b981;
                font-weight: 700;
              }

              .detail-val.expiry.expired {
                color: #ef4444;
              }

              .card-actions {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                align-items: stretch;
                gap: 8px;
                margin-top: 10px;
              }

              .action-btn {
                width: 100%;
                min-width: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                height: 40px;
                border-radius: 12px;
                font-size: 12px;
                font-weight: 700;
                text-decoration: none;
                transition: all 0.2s;
                cursor: pointer;
                border: none;
                box-shadow: 0 6px 14px rgba(15, 23, 42, 0.12);
              }

              .action-btn:hover {
                transform: translateY(-1px);
                box-shadow: 0 10px 20px rgba(15, 23, 42, 0.16);
              }

              .view-btn {
                background: #e8f0ff;
                color: #1e3a8a;
              }

              .view-btn:hover {
                background: #dbe7ff;
                color: #1e3a8a;
              }

              .whatsapp-btn {
                background: #e7f8ef;
                color: #166534;
              }

              .whatsapp-btn:hover {
                background: #dcf2e7;
              }

              .call-btn {
                background: #e8f5ff;
                color: #0c4a6e;
              }

              .call-btn:hover {
                background: #d8edff;
              }

              .renew-btn {
                background: #fff2e8;
                color: #9a3412;
              }

              .renew-btn:hover {
                background: #ffe8d6;
              }

              .member-details-modal {
                width: 680px;
                margin-left: 0;
                border-radius: 14px;
                overflow: hidden;
                border: none;
                position: fixed !important;
                left: 50% !important;
                top: 50% !important;
                transform: translate(-50%, -50%);
                max-height: 90vh;
                z-index: 1055;
                max-width: calc(100vw - 40px);
              }

              .member-details-modal.in {
                display: block !important;
                opacity: 1;
              }

              .modal-backdrop.in {
                opacity: 0.55;
              }

              @keyframes memberModalPop {
                from {
                  opacity: 0;
                  transform: translate(-50%, -47%) scale(0.96);
                }
                to {
                  opacity: 1;
                  transform: translate(-50%, -50%) scale(1);
                }
              }

              @media (min-width: 768px) {
                body > * {
                  transition: filter 0.22s ease, opacity 0.22s ease, transform 0.22s ease;
                }

                body.modal-open > *:not(.modal):not(.modal-backdrop):not(script):not(style) {
                  filter: blur(4px);
                  opacity: 0.35;
                  transform: scale(0.992);
                  pointer-events: none;
                  user-select: none;
                }

                .modal-backdrop.in {
                  opacity: 0.78;
                  background: #0f172a;
                }

                #memberDetailsModal > div {
                  background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%) !important;
                  border: 1px solid rgba(226, 232, 240, 0.9);
                  box-shadow: 0 34px 100px rgba(15, 23, 42, 0.46), 0 8px 24px rgba(15, 23, 42, 0.18);
                  min-height: 100vh;
                  max-height: 100vh;
                  height: 100vh;
                  display: flex;
                  flex-direction: column;
                  border-radius: 0 !important;
                  border: none;
                }

                .member-details-modal {
                  width: 100vw !important;
                  max-width: none;
                  height: 100vh;
                  max-height: 100vh;
                  left: 50% !important;
                  top: 50% !important;
                  transform: translate(-50%, -50%) !important;
                  animation: memberModalPop 0.22s ease;
                  box-shadow: none;
                  margin: 0 !important;
                }

                #memberDetailsModal [style*="background:#fff; border-radius:12px; padding:20px"] {
                  box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08) !important;
                  border: 1px solid #eaf0f6;
                  padding: 14px !important;
                  border-radius: 10px !important;
                }

                #memberDetailsModal [style*="max-height:580px"] {
                  max-height: calc(100vh - 110px) !important;
                  padding: 12px !important;
                  flex: 1 1 auto;
                  gap: 8px !important;
                }

                #memberDetailsModal [style*="padding:16px 20px"] {
                  padding: 11px 16px !important;
                }

                #memberDetailsModal [style*="font-size:16px; font-weight:700"] {
                  font-size: 16px !important;
                }

                #memberDetailsModal .close {
                  font-size: 18px !important;
                }

                #modalDefaultIcon,
                #modalMemberPhoto {
                  width: 60px !important;
                  height: 60px !important;
                }

                #modalDefaultIcon {
                  font-size: 26px !important;
                }

                #mdl_name {
                  font-size: 20px !important;
                  line-height: 1.15 !important;
                }

                #mdl_username,
                #mdl_branch,
                #mdl_gender,
                #mdl_contact,
                #mdl_address,
                #mdl_email,
                #mdl_bio_id,
                #mdl_batch_val,
                #mdl_aadhar,
                #mdl_pan,
                #mdl_reg_by,
                #mdl_trainer,
                #mdl_member_id,
                #mdl_paid_date,
                #mdl_extra_aadhar,
                #mdl_extra_pan,
                #mdl_joined_on,
                #mdl_member_status,
                #mdl_service,
                #mdl_plan_badge,
                #mdl_date_range,
                #mdl_total_amt,
                #mdl_disc_amt,
                #mdl_paid_amt,
                #mdl_remaining_amt,
                #mdl_comments {
                  font-size: 14px !important;
                }

                #mdl_days_right {
                  font-size: 20px !important;
                }

                #mdl_status_badge,
                #mdl_fee_badge,
                #mdl_days_badge,
                #mdl_status_right,
                #memberDetailsModal [style*="border-radius:20px"] {
                  font-size: 11px !important;
                  padding: 4px 10px !important;
                }

                #memberDetailsModal [style*="display:flex; gap:16px; align-items:center; flex-wrap:wrap"],
                #memberDetailsModal [style*="display:flex; gap:16px; align-items:center; flex-wrap:wrap;"] {
                  gap: 10px !important;
                }

                #memberDetailsModal [style*="margin-top:14px; padding-top:14px; border-top:1px solid #f1f5f9; display:grid; grid-template-columns:1fr 1fr; gap:10px"],
                #memberDetailsModal [style*="display:grid; grid-template-columns:repeat(4,1fr); gap:8px; background:#f8fafc; border-radius:10px; padding:14px"],
                #memberDetailsModal [style*="background:#f8fafc; border-radius:10px; padding:14px; margin-bottom:12px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px"] {
                  gap: 6px !important;
                }

                #memberDetailsModal [style*="font-size:15px; font-weight:800"] {
                  font-size: 15px !important;
                  margin-bottom: 10px !important;
                  padding-bottom: 7px !important;
                }

                #memberDetailsModal [style*="margin-top:12px; background:#fffbeb; border-radius:8px; padding:10px 14px; display:none"],
                #memberDetailsModal [style*="margin-top:12px; display:none; text-align:center"] {
                  margin-top: 8px !important;
                }
              }

              @media (min-width: 1101px) {
                .member-details-modal {
                  width: 100vw !important;
                  max-width: none;
                }

                #memberDetailsModal [style*="max-height:580px"] {
                  max-height: calc(100vh - 104px) !important;
                  padding: 14px !important;
                }

                #memberDetailsModal [style*="grid-template-columns:1fr 1fr"] {
                  grid-template-columns: 1fr 1fr !important;
                }

                #memberDetailsModal [style*="grid-template-columns:repeat(4,1fr)"] {
                  grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
                }
              }

              @media (min-width: 768px) and (max-width: 1100px) {
                .members-grid {
                  grid-template-columns: repeat(2, minmax(0, 1fr));
                  gap: 16px;
                  padding: 16px;
                }

                .member-card {
                  border-radius: 18px;
                  padding: 18px;
                }

                .card-details-grid {
                  grid-template-columns: 1fr;
                  gap: 10px;
                  padding: 12px;
                }

                .card-actions {
                  grid-template-columns: repeat(3, minmax(0, 1fr));
                  gap: 8px;
                }

                .member-details-modal {
                  width: 100vw !important;
                  max-width: none;
                  height: 100vh;
                  max-height: 100vh;
                  left: 50% !important;
                  top: 50% !important;
                  margin-left: 0 !important;
                  transform: translate(-50%, -50%);
                }

                #memberDetailsModal [style*="max-height:580px"] {
                  max-height: calc(100vh - 112px) !important;
                  padding: 12px !important;
                }

                #memberDetailsModal [style*="grid-template-columns:1fr 1fr"] {
                  grid-template-columns: 1fr !important;
                }

                #memberDetailsModal [style*="grid-template-columns:repeat(4,1fr)"] {
                  grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                }
              }

              @media (max-width: 767px) {
                .members-grid {
                  grid-template-columns: 1fr;
                  gap: 14px;
                  padding: 12px;
                }

                .member-card {
                  border-radius: 18px;
                  padding: 16px;
                }

                .card-header-row {
                  align-items: flex-start;
                }

                .card-details-grid {
                  grid-template-columns: 1fr;
                  gap: 10px;
                  padding: 12px;
                }

                .card-actions {
                  grid-template-columns: repeat(3, minmax(0, 1fr));
                  gap: 8px;
                }

                .action-btn {
                  min-width: 0;
                  height: 38px;
                  font-size: 11px;
                  padding: 8px 4px;
                }

                .member-details-modal {
                  width: 96% !important;
                  left: 50% !important;
                  top: 50% !important;
                  margin-left: 0 !important;
                  transform: translate(-50%, -50%);
                }

                #memberDetailsModal [style*="max-height:580px"] {
                  max-height: calc(100vh - 182px) !important;
                  padding: 10px !important;
                }

                #memberDetailsModal [style*="grid-template-columns:1fr 1fr"] {
                  grid-template-columns: 1fr !important;
                }

                #memberDetailsModal [style*="grid-template-columns:repeat(4,1fr)"] {
                  grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                }
              }

              .edit-btn {
                background: #edf2f7;
                color: #334155;
              }

              .edit-btn:hover {
                background: #e2e8f0;
              }

              .delete-btn {
                background: #fdecec;
                color: #991b1b;
              }

              .delete-btn:hover {
                background: #f9dada;
              }
            </style>

            <div class="members-grid">
              <?php
              include "dbcon.php";
              $today = date('Y-m-d');
              $branch_id = $_SESSION['branch_id'];
              $qry = "SELECT * FROM members WHERE branch_id = '$branch_id' ORDER BY dor ASC, user_id ASC";

              $result = mysqli_query($con, $qry);
              $m_id_counter = 1;
              $memberDetailMap = [];

              // Latest payment history map for fallback calculations
              $paymentHistoryMap = [];
              $hist_q = mysqli_query($con, "
                SELECT ph.user_id, ph.amount, ph.paid_amount, ph.discount_amount, ph.discount_type
                FROM payment_history ph
                INNER JOIN (
                  SELECT user_id, MAX(id) AS max_id
                  FROM payment_history
                  GROUP BY user_id
                ) latest ON latest.max_id = ph.id
              ");
              if ($hist_q) {
                while ($hist_row = mysqli_fetch_assoc($hist_q)) {
                  $paymentHistoryMap[(int)$hist_row['user_id']] = [
                    'amount' => (float)($hist_row['amount'] ?? 0),
                    'paid_amount' => (float)($hist_row['paid_amount'] ?? 0),
                    'discount_amount' => (float)($hist_row['discount_amount'] ?? 0),
                    'discount_type' => (string)($hist_row['discount_type'] ?? 'amount')
                  ];
                }
              }

              while ($row = mysqli_fetch_array($result)) {
                // Basic Details
                $name = htmlspecialchars($row['fullname'] ?? '', ENT_QUOTES, 'UTF-8');
                $id = htmlspecialchars((string)($row['user_id'] ?? ''), ENT_QUOTES, 'UTF-8');
                $gender = strtolower(htmlspecialchars($row['gender'] ?? '', ENT_QUOTES, 'UTF-8'));
                $contact = htmlspecialchars((string)($row['contact'] ?? ''), ENT_QUOTES, 'UTF-8');
                $member_user_id = (int)($row['user_id'] ?? 0);
                $amount = (float)($row['amount'] ?? 0);
                $paid_amount = (float)($row['paid_amount'] ?? 0);
                $discount_amount = (float)($row['discount_amount'] ?? 0);
                $discount_type = (string)($row['discount_type'] ?? 'amount');
                if (isset($paymentHistoryMap[$member_user_id])) {
                  $hist_fin = $paymentHistoryMap[$member_user_id];
                  if ($amount <= 0 && (float)$hist_fin['amount'] > 0) {
                    $amount = (float)$hist_fin['amount'];
                  }
                  if ($paid_amount <= 0 && (float)$hist_fin['paid_amount'] > 0) {
                    $paid_amount = (float)$hist_fin['paid_amount'];
                  }
                  if ($discount_amount <= 0 && (float)$hist_fin['discount_amount'] > 0) {
                    $discount_amount = (float)$hist_fin['discount_amount'];
                  }
                  if ($discount_type === '' && !empty($hist_fin['discount_type'])) {
                    $discount_type = (string)$hist_fin['discount_type'];
                  }
                }
                if ($amount <= 0 && $paid_amount > 0) {
                  $amount = $paid_amount;
                }
                $plan_months = (int)($row['plan'] ?? 0);
                $expiry = htmlspecialchars($row['expiry_date'] ?? '', ENT_QUOTES, 'UTF-8');
                $status = htmlspecialchars($row['status'] ?? '', ENT_QUOTES, 'UTF-8'); // Active/Expired etc

                // Calculation of Base amount for table display
                $base_amount = (float)$amount;
                if ($discount_type == 'percent') {
                  if ($discount_amount > 0 && $discount_amount < 100) {
                    $base_amount = (float)$amount / (1 - ($discount_amount / 100));
                  }
                } else {
                  $base_amount = (float)$amount + $discount_amount;
                }

                // New fields for Modal JSON
                $plan_days      = (int)$plan_months * 30;
                $days_elapsed   = max(0, (int)((strtotime($today) - strtotime((string)$row['dor'])) / 86400));
                $remaining_days = max(0, $plan_days - $days_elapsed);

                // Get branch name
                $branch_name = '';
                $b_qry = mysqli_query($con, "SELECT branch_name FROM branches WHERE id = '" . $row['branch_id'] . "'");
                if ($b_qry && ($b_row = mysqli_fetch_assoc($b_qry))) {
                  $branch_name = $b_row['branch_name'];
                }

                // Fee status
                $fee_paid   = $paid_amount > 0;
                $fee_status = $fee_paid ? 'Lacag La Bixiyay ✓' : 'Lacag Ma Bixin ✗';

                // Registered-by display (Self vs Staff)
                $registered_by_raw = trim((string)($row['registered_by'] ?? ''));
                $registered_by_lc = strtolower($registered_by_raw);
                $is_cashier_or_manager = $registered_by_lc !== '' && (strpos($registered_by_lc, 'cashier') !== false || strpos($registered_by_lc, 'manager') !== false);
                if ($registered_by_raw === '') {
                  $registered_by_display = 'Lama cayimin';
                } else {
                  $registered_by_display = $registered_by_raw;
                }

                $memberData = [
                  'member_id'       => $row['user_id'],
                  'fullname'        => $name,
                  'biometric_id'    => $row['biometric_id'],
                  'batch'           => $row['batch'],
                  'gender'          => $row['gender'],
                  'username'        => $row['username'],
                  'contact'         => $contact,
                  'email'           => $row['email'],
                  'address'         => $row['address'],
                  'aadhar'          => $row['aadhar'],
                  'pan'             => $row['pan'],
                  'plan'            => $plan_months,
                  'services'        => $row['services'],
                  'dor'             => $row['dor'],
                  'paid_date'       => $row['paid_date'],
                  'expiry_date'     => $expiry,
                  'discount_type'   => $discount_type,
                  'discount_amount' => $discount_amount,
                  'paid_amount'     => $paid_amount,
                  'amount'          => $amount,
                  'comments'        => $row['comments'],
                  'trainer_type'    => $row['trainer_type'],
                  'branch_name'     => $branch_name,
                  'registered_by'   => $row['registered_by'],
                  'registered_by_display' => $registered_by_display,
                  'remaining_days'  => $remaining_days,
                  'fee_status'      => $fee_status,
                  'fee_paid'        => $fee_paid,
                  'member_status'   => ($expiry < $today || $status == 'Expired') ? 'Expired' : $status,
                  'id_document'     => $row['id_document']
                ];
                // Determine photo path
                $photo = $row['photo'];
                $photo_path = (!empty($photo) && file_exists("../../img/members/" . $photo)) ? "../../img/members/" . $photo : "";

                $memberData['photo'] = $photo_path;
                $detailKey = (string)$row['user_id'];
                $memberDetailMap[$detailKey] = $memberData;

                // Determine icon and status
                $avatar_class = ($gender == 'female' || $gender == 'dhedig') ? 'female' : '';
                $icon_class = ($gender == 'female' || $gender == 'dhedig') ? 'fas fa-female' : 'fas fa-male';
                $is_expired = ($expiry < $today);
              ?>
                <!-- Single Card -->
                <div class="member-card">
                  <?php if ($is_expired): ?>
                    <div class="card-badge">Qorshihii wuu dhacay</div>
                  <?php else: ?>
                    <div class="card-badge active">Wuu Shaqaynayaa</div>
                  <?php endif; ?>

                  <div class="card-header-row">
                    <div class="member-avatar <?php echo $avatar_class; ?>" style="<?php echo !empty($photo_path) ? 'background-image: url(' . $photo_path . '); background-size: cover; background-position: center;' : ''; ?>">
                      <?php if (empty($photo_path)): ?>
                        <i class="<?php echo $icon_class; ?>"></i>
                      <?php endif; ?>
                    </div>
                    <div class="member-primary-info">
                      <h4 class="member-name"><?php echo $name; ?></h4>
                      <p class="member-id">M ID: <?php echo $m_id_counter++; ?></p>
                    </div>
                  </div>

                  <div class="card-details-grid">
                    <div class="detail-box" style="grid-column: span 2;">
                      <span class="detail-label">Adeegga (Service)</span>
                      <span class="detail-val" style="color: #3b82f6;"><?php echo htmlspecialchars($row['services'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="detail-box">
                      <span class="detail-label">Mobile</span>
                      <span class="detail-val"><?php echo $contact; ?></span>
                    </div>
                    <div class="detail-box">
                      <span class="detail-label">Wadarta Guud</span>
                      <span class="detail-val amount">$<?php echo $is_expired ? '0' : number_format($base_amount, 2); ?></span>
                    </div>
                    <div class="detail-box">
                      <span class="detail-label">La Bixiyay</span>
                      <span class="detail-val" style="color: #10b981;">$<?php echo number_format($paid_amount, 2); ?></span>
                    </div>
                    <div class="detail-box">
                      <span class="detail-label">Haraaga</span>
                      <span class="detail-val" style="color: #ef4444;">$<?php echo number_format(max(0, $amount - $paid_amount), 2); ?></span>
                    </div>
                    <div class="detail-box">
                      <span class="detail-label">Qorshaha</span>
                      <span class="detail-val"><?php echo $plan_months; ?> Bilood</span>
                    </div>
                    <div class="detail-box" style="grid-column: span 2;">
                      <span class="detail-label">Registered By</span>
                      <span class="detail-val"><?php echo htmlspecialchars($registered_by_display, ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="detail-box">
                      <span class="detail-label">Dhicitaanka</span>
                      <span class="detail-val expiry <?php echo $is_expired ? 'expired' : ''; ?>"><?php echo $expiry; ?></span>
                    </div>
                  </div>

                  <?php $contact_digits = preg_replace('/\D+/', '', (string)($row['contact'] ?? '')); ?>
                  <div class="card-actions">
                    <button type="button" class="action-btn view-btn" data-member-id="<?php echo htmlspecialchars($detailKey, ENT_QUOTES, 'UTF-8'); ?>" onclick="viewMemberDetails(this)">
                      <i class="fas fa-eye"></i>
                      Faahfaahin
                    </button>
                    <?php if ($contact_digits !== ''): ?>
                      <a class="action-btn whatsapp-btn" href="https://wa.me/<?php echo $contact_digits; ?>" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-whatsapp"></i>
                        WhatsApp
                      </a>
                      <a class="action-btn call-btn" href="tel:<?php echo $contact_digits; ?>">
                        <i class="fas fa-phone"></i>
                        Wac
                      </a>
                    <?php endif; ?>
                    <a class="action-btn renew-btn" href="user-payment.php?id=<?php echo (int)$row['user_id']; ?>">
                      <i class="fas fa-redo"></i>
                      Cusboonaysii
                    </a>
                    <a class="action-btn edit-btn" href="edit-memberform.php?id=<?php echo (int)$row['user_id']; ?>">
                      <i class="fas fa-edit"></i>
                      Tafatir
                    </a>
                    <a class="action-btn delete-btn" href="remove-member.php?id=<?php echo (int)$row['user_id']; ?>" onclick="return confirm('Ma hubtaa inaad tirtirayso xubintan?');">
                      <i class="fas fa-trash"></i>
                      Tirtir
                    </a>
                  </div>
                </div>
              <?php } ?>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

  <!-- Member Details Modal - Premium Design -->
  <div id="memberDetailsModal" class="modal hide fade member-details-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div style="background:#fff; border-radius:14px; overflow:hidden;">

      <!-- Header Bar -->
      <div style="background:linear-gradient(135deg,#1e293b,#334155); padding:16px 20px; display:flex; justify-content:space-between; align-items:center;">
        <span style="color:#fff; font-size:16px; font-weight:700; display:flex; align-items:center; gap:8px;"><i class="fas fa-id-card"></i> Member Profile</span>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color:#fff; opacity:0.8; font-size:22px; margin:0; padding:0; line-height:1;">×</button>
      </div>

      <div style="max-height:580px; overflow-y:auto; background:#f1f5f9; padding:16px; display:flex; flex-direction:column; gap:12px;">

        <!-- === MEMBER PROFILE CARD === -->
        <div style="background:#fff; border-radius:12px; padding:20px; box-shadow:0 1px 4px rgba(0,0,0,0.07);">
          <div style="display:flex; gap:16px; align-items:center; flex-wrap:wrap;">
            <!-- Photo -->
            <div id="modalDefaultIcon" style="width:80px; height:80px; border-radius:50%; background:#e0f2fe; display:flex; align-items:center; justify-content:center; font-size:36px; color:#0284c7; flex-shrink:0;">
              <i class="fas fa-user"></i>
            </div>
            <img id="modalMemberPhoto" src="" alt="Photo" style="display:none; width:80px; height:80px; border-radius:50%; object-fit:cover; border:3px solid #e2e8f0; flex-shrink:0;">

            <!-- Name + Info -->
            <div style="flex:1; min-width:180px;">
              <div id="mdl_name" style="font-size:20px; font-weight:800; color:#0f172a; margin-bottom:2px;"></div>
              <div id="mdl_username" style="font-size:13px; color:#64748b; margin-bottom:6px;"></div>
              <div style="display:flex; flex-wrap:wrap; gap:6px;">
                <span id="mdl_status_badge" style="padding:3px 12px; border-radius:20px; font-size:11px; font-weight:700;"></span>
                <span id="mdl_fee_badge" style="padding:3px 12px; border-radius:20px; font-size:11px; font-weight:700;"></span>
                <span id="mdl_days_badge" style="padding:3px 12px; border-radius:20px; font-size:11px; font-weight:700; background:#eff6ff; color:#1d4ed8;"></span>
              </div>
            </div>

            <!-- Right side details -->
            <div style="text-align:right; min-width:140px;">
              <div style="font-size:11px; color:#94a3b8; text-transform:uppercase; font-weight:600;">Jinsiga</div>
              <div id="mdl_gender" style="font-size:14px; color:#1e293b; font-weight:600; margin-bottom:8px;"></div>
              <div style="font-size:11px; color:#94a3b8; text-transform:uppercase; font-weight:600;">Telefoon</div>
              <div id="mdl_contact" style="font-size:14px; color:#1e293b; font-weight:600; margin-bottom:8px;"></div>
              <div style="font-size:11px; color:#94a3b8; text-transform:uppercase; font-weight:600;">Laanta</div>
              <div id="mdl_branch" style="font-size:13px; color:#166534; font-weight:700;"></div>
            </div>
          </div>

          <!-- Address / Email row -->
          <div style="margin-top:14px; padding-top:14px; border-top:1px solid #f1f5f9; display:grid; grid-template-columns:1fr 1fr; gap:10px;">
            <div>
              <div style="font-size:11px; color:#94a3b8; font-weight:600; text-transform:uppercase;">Cinwaanka</div>
              <div id="mdl_address" style="font-size:13px; color:#334155; font-weight:500;"></div>
            </div>
            <div>
              <div style="font-size:11px; color:#94a3b8; font-weight:600; text-transform:uppercase;">Email</div>
              <div id="mdl_email" style="font-size:13px; color:#334155; font-weight:500;"></div>
            </div>
            <div>
              <div style="font-size:11px; color:#94a3b8; font-weight:600; text-transform:uppercase;">Biometric ID</div>
              <div id="mdl_bio_id" style="font-size:13px; color:#334155; font-weight:600;"></div>
            </div>
            <div>
              <div style="font-size:11px; color:#94a3b8; font-weight:600; text-transform:uppercase;">Fasalka (Batch)</div>
              <div id="mdl_batch_val" style="font-size:13px; color:#334155; font-weight:600;"></div>
            </div>
            <div>
              <div style="font-size:11px; color:#94a3b8; font-weight:600; text-transform:uppercase;">Aadhar</div>
              <div id="mdl_aadhar" style="font-size:13px; color:#334155; font-weight:600;"></div>
            </div>
            <div>
              <div style="font-size:11px; color:#94a3b8; font-weight:600; text-transform:uppercase;">PAN</div>
              <div id="mdl_pan" style="font-size:13px; color:#334155; font-weight:600;"></div>
            </div>
            <div>
              <div style="font-size:11px; color:#94a3b8; font-weight:600; text-transform:uppercase;">Diiwaangeliyey</div>
              <div id="mdl_reg_by" style="font-size:13px; color:#6d28d9; font-weight:700;"></div>
            </div>
            <div>
              <div style="font-size:11px; color:#94a3b8; font-weight:600; text-transform:uppercase;">Nooca Tababarka</div>
              <div id="mdl_trainer" style="font-size:13px; color:#334155; font-weight:600;"></div>
            </div>
          </div>
        </div>

        <!-- === MEMBERSHIP DETAILS CARD === -->
        <div style="background:#fff; border-radius:12px; padding:20px; box-shadow:0 1px 4px rgba(0,0,0,0.07);">
          <div style="font-size:15px; font-weight:800; color:#0f172a; margin-bottom:14px; display:flex; align-items:center; gap:8px; border-bottom:1px solid #f1f5f9; padding-bottom:10px;">
            <i class="fas fa-layer-group" style="color:#0284c7;"></i> Qorshaha Xubinnimada
          </div>

          <!-- Plan name + Days + Status -->
          <div style="background:#f8fafc; border-radius:10px; padding:14px; margin-bottom:12px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
            <div>
              <div id="mdl_service" style="font-size:16px; font-weight:800; color:#1e293b;"></div>
              <div style="margin-top:4px;">
                <span style="background:#eff6ff; color:#1d4ed8; font-size:12px; font-weight:700; padding:3px 10px; border-radius:20px; display:inline-block;">
                  <i class="fas fa-calendar-alt"></i> <span id="mdl_plan_badge"></span>
                </span>
              </div>
            </div>
            <div style="text-align:right;">
              <div id="mdl_days_right" style="font-size:20px; font-weight:800; color:#0284c7;"></div>
              <div style="font-size:11px; color:#64748b;">Maalmood Haray</div>
              <span id="mdl_status_right" style="padding:3px 12px; border-radius:20px; font-size:11px; font-weight:700; display:inline-block; margin-top:4px;"></span>
            </div>
          </div>

          <!-- Date Range -->
          <div style="background:#f8fafc; border-radius:8px; padding:10px 14px; margin-bottom:12px; font-size:13px; color:#475569; text-align:center;">
            <i class="fas fa-calendar-check" style="color:#0284c7;"></i>
            <span id="mdl_date_range" style="font-weight:600; margin:0 6px;"></span>
          </div>

          <!-- Financial Row -->
          <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:8px; background:#f8fafc; border-radius:10px; padding:14px;">
            <div style="text-align:center;">
              <div style="font-size:11px; color:#64748b; font-weight:600; margin-bottom:4px;">Lacagta Guud</div>
              <div id="mdl_total_amt" style="font-size:15px; font-weight:800; color:#1e293b;"></div>
            </div>
            <div style="text-align:center; border-left:1px solid #e2e8f0;">
              <div style="font-size:11px; color:#64748b; font-weight:600; margin-bottom:4px;">Sicir-dhimis</div>
              <div id="mdl_disc_amt" style="font-size:15px; font-weight:800; color:#ea580c;"></div>
            </div>
            <div style="text-align:center; border-left:1px solid #e2e8f0;">
              <div style="font-size:11px; color:#64748b; font-weight:600; margin-bottom:4px;">La Bixiyay</div>
              <div id="mdl_paid_amt" style="font-size:15px; font-weight:800; color:#059669;"></div>
            </div>
            <div style="text-align:center; border-left:1px solid #e2e8f0;">
              <div style="font-size:11px; color:#64748b; font-weight:600; margin-bottom:4px;">Haraaga</div>
              <div id="mdl_remaining_amt" style="font-size:15px; font-weight:800; color:#dc2626;"></div>
            </div>
          </div>

          <div id="mdl_comments_wrap" style="margin-top:12px; background:#fffbeb; border-radius:8px; padding:10px 14px; display:none;">
            <div style="font-size:11px; color:#94a3b8; font-weight:600; text-transform:uppercase; margin-bottom:3px;">Faahfaahin</div>
            <div id="mdl_comments" style="font-size:13px; color:#475569;"></div>
          </div>
          <div id="mdl_document_wrap" style="margin-top:12px; display:none; text-align:center;">
            <a id="mdl_document_link" href="#" target="_blank" class="btn btn-info" style="border-radius:20px; font-weight:600; font-size:13px; padding:8px 20px;">
              <i class="fas fa-file-pdf"></i> Cadeenta Documentiga (View Document)
            </a>
          </div>
        </div>

        <div style="background:#fff; border-radius:12px; padding:20px; box-shadow:0 1px 4px rgba(0,0,0,0.07);">
          <div style="font-size:15px; font-weight:800; color:#0f172a; margin-bottom:14px; display:flex; align-items:center; gap:8px; border-bottom:1px solid #f1f5f9; padding-bottom:10px;">
            <i class="fas fa-info-circle" style="color:#0f766e;"></i> Xog Dheeraad Ah
          </div>
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
            <div>
              <div style="font-size:11px; color:#94a3b8; font-weight:600; text-transform:uppercase;">Member ID</div>
              <div id="mdl_member_id" style="font-size:13px; color:#334155; font-weight:700;"></div>
            </div>
            <div>
              <div style="font-size:11px; color:#94a3b8; font-weight:600; text-transform:uppercase;">Paid Date</div>
              <div id="mdl_paid_date" style="font-size:13px; color:#334155; font-weight:700;"></div>
            </div>
            <div>
              <div style="font-size:11px; color:#94a3b8; font-weight:600; text-transform:uppercase;">Aadhar</div>
              <div id="mdl_extra_aadhar" style="font-size:13px; color:#334155; font-weight:600;"></div>
            </div>
            <div>
              <div style="font-size:11px; color:#94a3b8; font-weight:600; text-transform:uppercase;">PAN</div>
              <div id="mdl_extra_pan" style="font-size:13px; color:#334155; font-weight:600;"></div>
            </div>
            <div>
              <div style="font-size:11px; color:#94a3b8; font-weight:600; text-transform:uppercase;">Taariikhda Diiwaangelinta</div>
              <div id="mdl_joined_on" style="font-size:13px; color:#334155; font-weight:600;"></div>
            </div>
            <div>
              <div style="font-size:11px; color:#94a3b8; font-weight:600; text-transform:uppercase;">Status</div>
              <div id="mdl_member_status" style="font-size:13px; color:#0f766e; font-weight:700;"></div>
            </div>
          </div>
        </div>

      </div><!-- end scroll -->

      <!-- Footer -->
      <div style="background:#f8fafc; border-top:1px solid #e2e8f0; padding:12px 20px; display:flex; justify-content:flex-end;">
        <button class="btn" data-dismiss="modal" aria-hidden="true" style="background:#e2e8f0; color:#475569; border:none; padding:8px 22px; border-radius:6px; font-weight:700; font-size:13px;">Xir ✕</button>
      </div>

    </div>
  </div>

  <!--end-main-container-part-->

  <!--Footer-part-->

  <div class="row-fluid">
    <div id="footer" class="span12"> <?php echo date("Y"); ?> &copy; M * A GYM System Developed By Abdikafi </div>
  </div>

  <style>
    #footer {
      color: white;
    }
  </style>

  <!--end-Footer-part-->

  <script src="../../js/excanvas.min.js"></script>
  <script src="../../js/jquery.min.js"></script>
  <script src="../../js/jquery.ui.custom.js"></script>
  <script src="../../js/bootstrap.min.js"></script>
  <script src="../../js/jquery.flot.min.js"></script>
  <script src="../../js/jquery.flot.resize.min.js"></script>
  <script src="../../js/jquery.peity.min.js"></script>
  <script src="../../js/fullcalendar.min.js"></script>
  <script src="../../js/matrix.js"></script>
  <script src="../../js/matrix.dashboard.js"></script>
  <script src="../../js/jquery.gritter.min.js"></script>
  <script src="../../js/matrix.interface.js"></script>
  <script src="../../js/matrix.chat.js"></script>
  <script src="../../js/jquery.validate.js"></script>
  <script src="../../js/matrix.form_validation.js"></script>
  <script src="../../js/jquery.wizard.js"></script>
  <script src="../../js/jquery.uniform.js"></script>
  <script src="../../js/select2.min.js"></script>
  <script src="../../js/matrix.popover.js"></script>
  <script src="../../js/jquery.dataTables.min.js"></script>
  <script src="../../js/matrix.tables.js"></script>

  <script>
    window.memberDetailMap = <?php echo json_encode($memberDetailMap, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
  </script>

  <script type="text/javascript">
    function goPage(newURL) {
      if (newURL != "") {
        if (newURL == "-") {
          resetMenu();
        } else {
          document.location.href = newURL;
        }
      }
    }

    function resetMenu() {
      document.gomenu.selector.selectedIndex = 2;
    }

    function decodeBase64Json(base64Data) {
      var binary = atob(base64Data);

      try {
        return JSON.parse(binary);
      } catch (plainJsonError) {}

      var bytes = [];

      for (var index = 0; index < binary.length; index++) {
        bytes.push(binary.charCodeAt(index));
      }

      if (window.TextDecoder) {
        return JSON.parse(new TextDecoder('utf-8').decode(new Uint8Array(bytes)));
      }

      var encoded = '';
      for (var byteIndex = 0; byteIndex < bytes.length; byteIndex++) {
        encoded += '%' + ('00' + bytes[byteIndex].toString(16)).slice(-2);
      }

      return JSON.parse(decodeURIComponent(encoded));
    }

    function setTextSafe(id, value, fallback) {
      var element = document.getElementById(id);
      if (!element) {
        return null;
      }

      var finalValue = value;
      if (finalValue === undefined || finalValue === null || finalValue === '') {
        finalValue = fallback !== undefined ? fallback : 'N/A';
      }

      element.textContent = finalValue;
      return element;
    }

    function showModalSafe(modalId) {
      if (window.jQuery) {
        var modalInstance = window.jQuery('#' + modalId);
        if (modalInstance.length && typeof modalInstance.modal === 'function') {
          modalInstance.modal('show');
          return;
        }
      }

      var modalElement = document.getElementById(modalId);
      if (!modalElement) {
        throw new Error('Modal not found: ' + modalId);
      }

      modalElement.style.display = 'block';
      modalElement.className += ' in';
      modalElement.setAttribute('aria-hidden', 'false');
      document.body.classList.add('modal-open');
    }

    function viewMemberDetails(source) {
      try {
        var detailKey = typeof source === 'string' ? source : (source && source.getAttribute ? source.getAttribute('data-member-id') : '');
        var data = window.memberDetailMap && detailKey ? window.memberDetailMap[detailKey] : null;

        if (!data) {
          var base64Data = typeof source === 'string' ? source : (source && source.getAttribute ? source.getAttribute('data-member') : '');
          if (!base64Data) {
            throw new Error('Member payload missing');
          }
          data = decodeBase64Json(base64Data);
        }
        var isExpired = (data.member_status === 'Expired');

        // --- PROFILE CARD ---
        setTextSafe('mdl_name', data.fullname);
        setTextSafe('mdl_username', '@' + (data.username || ''), '@');
        setTextSafe('mdl_gender', data.gender);
        setTextSafe('mdl_contact', data.contact);
        setTextSafe('mdl_branch', data.branch_name);
        setTextSafe('mdl_address', data.address);
        setTextSafe('mdl_email', data.email);
        setTextSafe('mdl_bio_id', data.biometric_id);
        setTextSafe('mdl_batch_val', data.batch);
        setTextSafe('mdl_aadhar', data.aadhar);
        setTextSafe('mdl_pan', data.pan);
        setTextSafe('mdl_member_id', data.member_id);
        setTextSafe('mdl_paid_date', data.paid_date);
        setTextSafe('mdl_extra_aadhar', data.aadhar);
        setTextSafe('mdl_extra_pan', data.pan);
        setTextSafe('mdl_joined_on', data.dor);
        setTextSafe('mdl_member_status', data.member_status);
        var registeredByText = (data.registered_by_display || '').trim();
        if (registeredByText === '') {
          var rawRegisteredBy = (data.registered_by || '').trim();
          var rbLower = rawRegisteredBy.toLowerCase();
          var isCashierOrManager = rbLower !== '' && (rbLower.indexOf('cashier') !== -1 || rbLower.indexOf('manager') !== -1);
          if (rawRegisteredBy === '') {
            registeredByText = 'Lama cayimin';
          } else {
            registeredByText = rawRegisteredBy;
          }
        }
        setTextSafe('mdl_reg_by', registeredByText, 'Lama cayimin');
        setTextSafe('mdl_trainer', data.trainer_type);

        // Status badge
        var sb = document.getElementById('mdl_status_badge');
        sb.innerText = isExpired ? '✗ Dhacay' : '✓ Firfircoon';
        sb.style.background = isExpired ? '#fee2e2' : '#dcfce7';
        sb.style.color = isExpired ? '#dc2626' : '#16a34a';

        // Fee status badge
        var fb = document.getElementById('mdl_fee_badge');
        fb.innerText = data.fee_status;
        fb.style.background = data.fee_paid ? '#dcfce7' : '#fee2e2';
        fb.style.color = data.fee_paid ? '#16a34a' : '#dc2626';

        // Days badge
        setTextSafe('mdl_days_badge', String(data.remaining_days || 0) + ' Maalmood Haray');

        // --- MEMBERSHIP CARD ---
        setTextSafe('mdl_service', data.services);
        setTextSafe('mdl_plan_badge', (data.plan ? data.plan + ' Bilood' : 'N/A'));
        setTextSafe('mdl_days_right', data.remaining_days, '0');

        var expiryEl = document.getElementById('mdl_expiry');
        if (expiryEl) {
          expiryEl.textContent = data.expiry_date || 'N/A';
          expiryEl.style.color = isExpired ? '#dc2626' : '#059669';
        }

        var start = data.paid_date || data.dor;
        var end = data.expiry_date;
        setTextSafe('mdl_date_range', (start || '?') + ' -> ' + (end || '?'));

        var sr = document.getElementById('mdl_status_right');
        sr.innerText = isExpired ? '✗ Expired' : '✓ Active';
        sr.style.background = isExpired ? '#fee2e2' : '#dcfce7';
        sr.style.color = isExpired ? '#dc2626' : '#16a34a';

        // Financial
        var totalCostAfterDiscount = parseFloat(data.amount || 0);
        var disc = parseFloat(data.discount_amount || 0);
        var paid = parseFloat(data.paid_amount || 0);

        var baseTotal = totalCostAfterDiscount;
        if (data.discount_type === 'percent') {
          if (disc > 0 && disc < 100) {
            baseTotal = totalCostAfterDiscount / (1 - (disc / 100));
          }
        } else {
          baseTotal = totalCostAfterDiscount + disc;
        }

        var discountInDollars = baseTotal - totalCostAfterDiscount;
        var remain = Math.max(0, totalCostAfterDiscount - paid);

        setTextSafe('mdl_total_amt', '$' + baseTotal.toFixed(2));
        setTextSafe('mdl_disc_amt', '$' + discountInDollars.toFixed(2));
        setTextSafe('mdl_paid_amt', '$' + paid.toFixed(2));
        setTextSafe('mdl_remaining_amt', '$' + remain.toFixed(2));

        // Comments
        var cw = document.getElementById('mdl_comments_wrap');
        var cc = document.getElementById('mdl_comments');
        var commentsText = typeof data.comments === 'string' ? data.comments.trim() : '';
        if (commentsText !== '') {
          cc.textContent = commentsText;
          cw.style.display = 'block';
        } else {
          cw.style.display = 'none';
        }

        var docWrap = document.getElementById('mdl_document_wrap');
        var docLink = document.getElementById('mdl_document_link');
        var idDocument = typeof data.id_document === 'string' ? data.id_document.trim() : '';
        if (idDocument !== '') {
          docLink.href = '../../img/members/' + idDocument;
          docWrap.style.display = 'block';
        } else {
          docWrap.style.display = 'none';
        }

        // Photo
        const modalPhoto = document.getElementById('modalMemberPhoto');
        const modalIcon = document.getElementById('modalDefaultIcon');
        if (data.photo) {
          modalPhoto.src = data.photo;
          modalPhoto.style.display = 'block';
          modalIcon.style.display = 'none';
        } else {
          modalPhoto.style.display = 'none';
          modalIcon.style.display = 'flex';
        }

        showModalSafe('memberDetailsModal');
      } catch (e) {
        console.error("Error parsing member data:", e);
        alert("Waan ka xunnahay, xogta xubinta lama soo tusi karo hadda.");
      }
    }
  </script>
</body>

</html>