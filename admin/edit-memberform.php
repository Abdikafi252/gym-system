<?php
session_start();
//the isset function to check username is already loged in and stored on the session
if (!isset($_SESSION['user_id'])) {
  header('location:../index.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>M*A GYM System</title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../css/bootstrap.min.css" />
  <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
  <link rel="stylesheet" href="../css/fullcalendar.css" />
  <link rel="stylesheet" href="../css/matrix-style.css" />
  <link rel="stylesheet" href="../css/matrix-media.css" />
  <link href="../font-awesome/css/fontawesome.css" rel="stylesheet" />
  <link href="../font-awesome/css/all.css" rel="stylesheet" />
  <link rel="stylesheet" href="../css/jquery.gritter.css" />
  <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
  <style>
    /* Modern Form Styling */
    body {
      background: #f4f6f9;
      font-family: 'Open Sans', sans-serif;
    }

    .form-card-container {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
      padding: 30px;
      margin: 20px auto;
      max-width: 1000px;
    }

    .form-section {
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      padding: 20px;
      margin-bottom: 25px;
      background: #fafbfc;
    }

    .form-section-title {
      font-size: 16px;
      color: #718096;
      font-weight: 600;
      margin-bottom: 20px;
      border-bottom: 2px solid #edf2f7;
      padding-bottom: 10px;
    }

    .avatar-upload {
      text-align: center;
      margin-bottom: 30px;
    }

    .avatar-circle {
      width: 100px;
      height: 100px;
      background: #e2e8f0;
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 40px;
      color: #a0aec0;
      position: relative;
    }

    .avatar-icon {
      position: absolute;
      bottom: 0;
      right: 0;
      background: #3182ce;
      color: #fff;
      width: 30px;
      height: 30px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 14px;
      border: 3px solid #fff;
    }

    .form-group-row {
      display: flex;
      flex-wrap: wrap;
      margin-right: -10px;
      margin-left: -10px;
    }

    .form-col {
      padding-right: 10px;
      padding-left: 10px;
      flex: 1;
      min-width: 250px;
      margin-bottom: 15px;
    }

    .form-col-full {
      flex: 0 0 100%;
      max-width: 100%;
    }

    .custom-label {
      display: block;
      font-size: 13px;
      font-weight: 600;
      color: #4a5568;
      margin-bottom: 8px;
    }

    .input-wrapper {
      position: relative;
    }

    .input-icon {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      color: #a0aec0;
      font-size: 14px;
    }

    .custom-input {
      width: 100%;
      padding: 10px 10px 10px 35px !important;
      border: 1px solid #cbd5e0 !important;
      border-radius: 6px !important;
      font-size: 14px !important;
      color: #2d3748 !important;
      height: 42px !important;
      box-shadow: none !important;
      box-sizing: border-box !important;
      background: #fff;
      transition: all 0.2s;
    }

    .custom-input:focus {
      border-color: #3182ce !important;
      box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1) !important;
      outline: none;
    }

    .custom-input[readonly],
    .custom-input[disabled] {
      background: #edf2f7;
      cursor: not-allowed;
    }

    .radio-group {
      display: flex;
      align-items: center;
      height: 42px;
      gap: 15px;
    }

    .radio-label {
      display: flex;
      align-items: center;
      cursor: pointer;
      font-size: 14px;
      color: #4a5568;
    }

    .radio-label input {
      margin: 0 8px 0 0 !important;
    }

    .action-buttons {
      text-align: right;
      padding-top: 20px;
      border-top: 1px solid #edf2f7;
    }

    .btn-save {
      background: #3182ce !important;
      color: white !important;
      border: none !important;
      padding: 10px 25px !important;
      border-radius: 6px !important;
      font-size: 15px !important;
      font-weight: 600 !important;
      transition: background 0.2s !important;
    }

    .btn-save:hover {
      background: #2b6cb0 !important;
    }

    /* Override widget box style just for content-header alignment */
    #content-header {
      background: #fff;
      padding: 15px 20px;
      border-bottom: 1px solid #e2e8f0;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    .page-title {
      font-size: 20px;
      font-weight: 700;
      color: #2d3748;
      margin: 0;
      display: flex;
      align-items: center;
    }

    .page-title i {
      margin-right: 10px;
      color: #4a5568;
    }

    .btn-back {
      background: #f8fafc;
      color: #475569;
      border: 1px solid #e2e8f0;
      padding: 8px 18px;
      border-radius: 50px;
      font-weight: 600;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      text-decoration: none;
      margin-bottom: 10px;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .btn-back:hover {
      background: #f1f5f9;
      color: #1e293b;
      transform: translateX(-3px);
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
      text-decoration: none;
    }

    .btn-back i {
      font-size: 14px;
    }

    .page-title {
      display: inline-block;
      margin-right: 20px !important;
    }

    /* Webcam Modal Styles */
    .webcam-modal {
      display: none;
      position: fixed;
      z-index: 9999;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.8);
      backdrop-filter: blur(5px);
      align-items: center;
      justify-content: center;
    }

    .webcam-container {
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      width: 90%;
      max-width: 500px;
      text-align: center;
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    #webcam-video {
      width: 100%;
      border-radius: 8px;
      background: #000;
      margin-bottom: 15px;
      transform: scaleX(-1); /* Mirror view */
    }

    .webcam-actions {
      display: flex;
      gap: 10px;
      justify-content: center;
    }

    .btn-capture {
      background: #e53e3e;
      color: #fff;
      border: none;
      padding: 10px 20px;
      border-radius: 6px;
      font-weight: 700;
      cursor: pointer;
    }

    .btn-cancel-webcam {
      background: #edf2f7;
      color: #4a5568;
      border: none;
      padding: 10px 20px;
      border-radius: 6px;
      font-weight: 700;
      cursor: pointer;
    }
  </style>
</head>

<body>

  <!--Header-part-->
  <?php include 'includes/header-content.php'; ?>
  <!--close-Header-part-->


  <!--top-Header-menu-->
  <?php include 'includes/topheader.php' ?>
  <!--close-top-Header-menu-->

  <!--sidebar-menu-->
  <?php $page = 'members-update';
  include 'includes/sidebar.php' ?>
  <!--sidebar-menu-->

  <?php
  include 'dbcon.php';
  require_once 'includes/db_helper.php';
  $id = $_GET['id'];
  $row = safe_fetch_assoc($conn, "SELECT * FROM members WHERE user_id=?", "i", [$id]);
  if ($row) {
  ?>
    <div id="content">
      <div id="content-header" style="position: relative;">
        <h1 class="page-title"><i class="fas fa-edit"></i> Edit Member Details</h1>
      </div>

      <div class="container-fluid">
        <div class="avatar-upload">
          <?php
          $photo = $row['photo'];
          $photo_path = (!empty($photo) && file_exists("../img/members/" . $photo)) ? "../img/members/" . $photo : "";
          ?>
            <label for="member_photo" style="cursor: pointer;">
              <div class="avatar-circle" id="imagePreviewContainer">
                <i class="fas fa-user-edit" id="defaultAvatarIcon" style="<?php echo !empty($photo_path) ? 'display:none;' : ''; ?>"></i>
                <img id="imagePreview" src="<?php echo $photo_path; ?>" alt="Preview" style="<?php echo !empty($photo_path) ? 'display:block;' : 'display:none;'; ?> width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                <div class="avatar-icon" style="background: #38a169;">
                  <i class="fas fa-camera"></i>
                </div>
              </div>
            </label>
            <div style="margin-top: 15px; display: flex; flex-direction: column; gap: 8px; align-items: center;">
              <button type="button" class="btn" onclick="openWebcam()" style="background:#e53e3e; color:#fff; font-size:12px; font-weight:600; padding:6px 20px; border-radius:20px; border:none; width: fit-content; box-shadow: 0 4px 6px rgba(229, 62, 62, 0.2);">
                <i class="fas fa-camera"></i> Take New Photo
              </button>
              <label for="member_photo" style="background:#f1f5f9; color:#475569; font-size:11px; font-weight:600; padding:4px 15px; border-radius:20px; display:inline-block; cursor: pointer; border: 1px solid #e2e8f0;">
                <i class="fas fa-upload"></i> Upload File Instead
              </label>
            </div>
          </div> <!-- End of avatar-upload -->

        <form action="edit-member-req.php" method="POST" enctype="multipart/form-data">
          <input type="file" name="photo" id="member_photo" style="display: none;" accept="image/*" onchange="previewFile()">
          <input type="hidden" name="webcam_image" id="webcam_image">
          <!-- Hidden ID -->
          <input type="hidden" name="id" value="<?php echo $row['user_id']; ?>">

          <!-- Registration Information -->
          <div class="form-section">
            <div class="form-section-title">Registration Information :</div>
            <div class="form-group-row">
              <div class="form-col">
                <label class="custom-label">Face ID (Wajiga)</label>
                <div class="input-wrapper">
                  <i class="fas fa-user-check input-icon" style="color:#3182ce;"></i>
                  <input type="text" class="custom-input" name="biometric_id" value="<?php echo $row['biometric_id']; ?>" required readonly />
                </div>
              </div>
              <div class="form-col">
                <label class="custom-label">Batch (Class)</label>
                <div class="input-wrapper">
                  <i class="fas fa-database input-icon" style="color:#3182ce;"></i>
                  <select name="batch" class="custom-input" style="padding-left:35px !important;">
                    <option value="" <?php if ($row['batch'] == '') echo 'selected'; ?>>Select Batch</option>
                    <option value="Subax" <?php if ($row['batch'] == 'Subax') echo 'selected'; ?>>Morning (Subax)</option>
                    <option value="Duhur" <?php if ($row['batch'] == 'Duhur') echo 'selected'; ?>>Noon (Duhur)</option>
                    <option value="Galab" <?php if ($row['batch'] == 'Galab') echo 'selected'; ?>>Afternoon (Galab)</option>
                    <option value="Habeen" <?php if ($row['batch'] == 'Habeen') echo 'selected'; ?>>Evening (Habeen)</option>
                  </select>
                </div>
              </div>
              <div class="form-col">
                <label class="custom-label">Select Branch</label>
                <div class="input-wrapper">
                  <i class="fas fa-building input-icon" style="color:#3182ce;"></i>
                  <select name="branch_id" class="custom-input" style="padding-left:35px !important;" required>
                    <?php
                    $branch_res = safe_query($conn, "SELECT * FROM branches");
                    while ($branch_row = mysqli_fetch_assoc($branch_res)) {
                      $sel = ($row['branch_id'] == $branch_row['id']) ? 'selected' : '';
                      echo "<option value='" . $branch_row['id'] . "' $sel>" . htmlspecialchars($branch_row['branch_name']) . "</option>";
                    }
                    ?>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <!-- Personal Information -->
          <div class="form-section">
            <div class="form-section-title">Personal Information :</div>
            <div class="form-group-row">
              <div class="form-col">
                <label class="custom-label">Full Name</label>
                <div class="input-wrapper">
                  <i class="fas fa-user input-icon" style="color:#3182ce;"></i>
                  <input type="text" class="custom-input" name="fullname" value="<?php echo htmlspecialchars($row['fullname'], ENT_QUOTES); ?>" required />
                </div>
              </div>
              <div class="form-col">
                <label class="custom-label">Gender</label>
                <div class="input-wrapper">
                  <i class="fas fa-venus-mars input-icon" style="color:#3182ce;"></i>
                  <select name="gender" class="custom-input" style="padding-left:35px !important;" required>
                    <option value="Male" <?php if ($row['gender'] == 'Male' || $row['gender'] == 'Lab') echo 'selected'; ?>>Male</option>
                    <option value="Female" <?php if ($row['gender'] == 'Female' || $row['gender'] == 'Dhedig') echo 'selected'; ?>>Female</option>
                    <option value="Other" <?php if ($row['gender'] == 'Other') echo 'selected'; ?>>Other</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="form-group-row">
              <div class="form-col">
                <label class="custom-label">Username</label>
                <div class="input-wrapper">
                  <i class="fas fa-user-circle input-icon" style="color:#3182ce;"></i>
                  <input type="text" class="custom-input" name="username" value="<?php echo htmlspecialchars($row['username'], ENT_QUOTES); ?>" required />
                </div>
              </div>
              <div class="form-col">
                <label class="custom-label">Password</label>
                <div class="input-wrapper">
                  <i class="fas fa-lock input-icon" style="color:#3182ce;"></i>
                  <input type="password" class="custom-input" name="password" disabled placeholder="********** Leave as is" />
                </div>
              </div>
            </div>

            <div class="form-group-row">
              <div class="form-col">
                <label class="custom-label">Mobile Phone</label>
                <div class="input-wrapper">
                  <i class="fas fa-phone input-icon" style="color:#3182ce;"></i>
                  <input type="number" class="custom-input" name="contact" value="<?php echo htmlspecialchars($row['contact'], ENT_QUOTES); ?>" required />
                </div>
              </div>
              <div class="form-col">
                <label class="custom-label">Email</label>
                <div class="input-wrapper">
                  <i class="fas fa-envelope input-icon" style="color:#3182ce;"></i>
                  <input type="email" class="custom-input" name="email" value="<?php echo htmlspecialchars($row['email'], ENT_QUOTES); ?>" placeholder="Email" />
                </div>
              </div>
            </div>

            <div class="form-group-row">
              <div class="form-col form-col-full">
                <label class="custom-label">Address</label>
                <div class="input-wrapper">
                  <textarea name="address" class="custom-input" style="padding-left:15px !important; height:auto !important;" rows="2" required><?php echo htmlspecialchars($row['address'], ENT_QUOTES); ?></textarea>
                </div>
              </div>
            </div>
          </div>

          <!-- Document -->
          <div class="form-section">
            <div class="form-section-title">Identification Documents :</div>
            <div class="form-group-row">
              <div class="form-col">
                <label class="custom-label">ID Number / Aadhar</label>
                <div class="input-wrapper">
                  <i class="fas fa-id-badge input-icon" style="color:#3182ce;"></i>
                  <input type="text" class="custom-input" name="aadhar" value="<?php echo htmlspecialchars($row['aadhar'], ENT_QUOTES); ?>" />
                </div>
              </div>
              <div class="form-col">
                <label class="custom-label">PAN Number</label>
                <div class="input-wrapper">
                  <i class="fas fa-id-card-alt input-icon" style="color:#3182ce;"></i>
                  <input type="text" class="custom-input" name="pan" value="<?php echo htmlspecialchars($row['pan'], ENT_QUOTES); ?>" />
                </div>
              </div>
            </div>

            <div class="form-group-row">
              <div class="form-col">
                <label class="custom-label">ID Document Type</label>
                <div class="input-wrapper">
                  <i class="fas fa-file-alt input-icon" style="color:#3182ce;"></i>
                  <select name="id_doc_type" class="custom-input" style="padding-left:35px !important;" <?php echo isset($row['id_document']) && $row['id_document'] != '' ? 'disabled' : ''; ?>>
                    <?php
                    $current_type = isset($row['id_doc_type']) ? $row['id_doc_type'] : '';
                    ?>
                    <option value="" <?php echo $current_type == '' ? 'selected' : ''; ?>>Select Type</option>
                    <option value="National ID" <?php echo $current_type == 'National ID' ? 'selected' : ''; ?>>National ID</option>
                    <option value="Passport" <?php echo $current_type == 'Passport' ? 'selected' : ''; ?>>Passport</option>
                    <option value="Driving License" <?php echo $current_type == 'Driving License' ? 'selected' : ''; ?>>Driving License</option>
                    <option value="Other" <?php echo $current_type == 'Other' ? 'selected' : ''; ?>>Other</option>
                  </select>
                </div>
              </div>
              <div class="form-col">
                <label class="custom-label">ID Document (Upload)</label>
                <div class="input-wrapper" style="align-items:center;">
                  <i class="fas fa-upload input-icon" style="color:#3182ce;"></i>
                  <?php if (isset($row['id_document']) && $row['id_document'] != ''): ?>
                    <div id="existing_doc_container" style="padding-left: 35px; width: 100%; display: flex; align-items: center; justify-content: space-between;">
                      <span style="font-size: 13px; color: #16a34a; font-weight: 600;"><i class="fas fa-check-circle"></i> Document Uploaded</span>
                      <div>
                        <a href="../img/members/<?php echo htmlspecialchars($row['id_document']); ?>" target="_blank" class="btn btn-mini btn-info" style="margin-left: 10px;">View</a>
                        <button type="button" class="btn btn-mini btn-danger" style="margin-left: 5px; border-radius: 50%; padding: 2px 6px;" onclick="removeDocument()" title="Remove Document"><i class="fas fa-times"></i></button>
                      </div>
                      <input type="hidden" name="existing_id_document" id="existing_id_document" value="<?php echo htmlspecialchars($row['id_document']); ?>">
                      <input type="hidden" name="remove_id_document" id="remove_id_document" value="0">
                    </div>
                    <div id="new_doc_upload" style="display: none; width: 100%;">
                      <input type="file" name="id_document" class="custom-input" style="padding-left:35px !important; padding-top:8px;" accept=".jpg,.jpeg,.png,.pdf" />
                    </div>
                  <?php else: ?>
                    <input type="file" name="id_document" class="custom-input" style="padding-left:35px !important; padding-top:8px;" accept=".jpg,.jpeg,.png,.pdf" />
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>

          <!-- Membership Plan Details -->
          <div class="form-section">
            <div class="form-section-title">Membership Plan Details :</div>

            <div class="form-group-row">
              <div class="form-col">
                <label class="custom-label">Plan</label>
                <div class="input-wrapper">
                  <i class="far fa-calendar input-icon" style="color:#3182ce;"></i>
                  <select name="plan" id="plan" class="custom-input" style="padding-left:35px !important;" onchange="calculateExpiry()" required>
                    <option value="" disabled>Select Plan</option>
                    <?php
                    $resPkgs = safe_query($conn, "SELECT * FROM packages");
                    while ($pkg = mysqli_fetch_array($resPkgs)) {
                      $sel = ($row['plan'] == $pkg['duration']) ? 'selected' : '';
                      echo "<option value='" . $pkg['duration'] . "' $sel>" . $pkg['packagename'] . " (" . $pkg['duration'] . " Months) </option>";
                    }
                    ?>
                  </select>
                </div>
              </div>
              <div class="form-col">
                <label class="custom-label">Service</label>
                <div class="input-wrapper">
                  <select name="services" id="services" class="custom-input" style="padding-left:15px !important;" onchange="updateAmount()" required>
                    <option value="" disabled>Select Service</option>
                    <?php
                    $resRates = safe_query($conn, "SELECT * FROM rates");
                    while ($rate = mysqli_fetch_array($resRates)) {
                      $sel = ($row['services'] == $rate['name']) ? 'selected' : '';
                      echo "<option value='" . $rate['name'] . "' data-charge='" . $rate['charge'] . "' $sel>" . $rate['name'] . " - $" . $rate['charge'] . " per month </option>";
                    }
                    ?>
                  </select>
                </div>
              </div>
            </div>

            <div class="form-group-row">
              <div class="form-col">
                <label class="custom-label">Joining Date</label>
                <div class="input-wrapper">
                  <i class="far fa-calendar-check input-icon" style="color:#3182ce;"></i>
                  <input type="date" name="dor" id="dor" class="custom-input" value="<?php echo $row['dor']; ?>" onchange="calculateExpiry()" required />
                </div>
              </div>
              <div class="form-col">
                <label class="custom-label">Expiry Date</label>
                <div class="input-wrapper">
                  <i class="far fa-calendar-times input-icon" style="color:#3182ce;"></i>
                  <input type="date" name="expiry_date" id="expiry_date" class="custom-input" value="<?php echo $row['expiry_date']; ?>" required readonly />
                </div>
              </div>
            </div>

            <div class="form-group-row">
              <div class="form-col">
                <label class="custom-label">Discount Type</label>
                <div class="radio-group" style="padding-left:15px;">
                  <label class="radio-label">
                    <input type="radio" name="discount_type" value="percent" <?php echo ($row['discount_type'] == 'percent') ? 'checked' : ''; ?> onchange="calculateTotal()"> Percentage (%)
                  </label>
                  <label class="radio-label">
                    <input type="radio" name="discount_type" value="amount" <?php echo ($row['discount_type'] == 'amount') ? 'checked' : ''; ?> onchange="calculateTotal()"> Amount ($)
                  </label>
                </div>
              </div>
              <div class="form-col">
                <label class="custom-label">Discount Amount</label>
                <div class="input-wrapper">
                  <i class="fas fa-percent input-icon" style="color:#3182ce;" id="discount-icon"></i>
                  <input type="number" name="discount_amount" id="discount_amount" class="custom-input" value="<?php echo $row['discount_amount']; ?>" min="0" oninput="calculateTotal()" />
                </div>
              </div>
              <div class="form-col">
                <label class="custom-label">Paid Amount</label>
                <div class="input-wrapper">
                  <i class="fas fa-money-bill-wave input-icon" style="color:#3182ce;"></i>
                  <input type="number" name="paid_amount" id="paid_amount" class="custom-input" value="<?php echo $row['paid_amount']; ?>" min="0" required />
                </div>
              </div>
            </div>

            <div class="form-group-row">
              <div class="form-col form-col-full">
                <label class="custom-label">Comments</label>
                <div class="input-wrapper">
                  <i class="far fa-comment-alt input-icon" style="color:#3182ce; top:15px; transform:none;"></i>
                  <textarea name="comments" class="custom-input" style="height:auto !important; padding-left:35px !important;" rows="2"><?php echo htmlspecialchars($row['comments'], ENT_QUOTES); ?></textarea>
                </div>
              </div>
            </div>

            <div class="form-group-row">
              <?php
              $per_month = ($row['plan'] > 0) ? ($row['amount'] / $row['plan']) : 0;
              $balance = $row['amount'] - $row['paid_amount'];
              if ($balance < 0) $balance = 0;
              ?>
              <div style="background:#eff6ff; padding:15px; border-radius:8px; border:1px solid #bfdbfe; margin-top:20px; width:100%;">
                <h5 style="margin-top:0; color:#1d4ed8; font-size:14px; font-weight:600;">Payment Summary</h5>
                <div style="display: flex; gap: 15px; margin-top: 15px; flex-wrap: wrap;">
                  <div style="flex: 1; min-width: 120px; background: #fff; padding: 10px; border-radius: 6px; border: 1px solid #e2e8f0; text-align: center;">
                    <span style="display: block; font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Subtotal</span>
                    <span id="display_subtotal" style="font-size: 16px; font-weight: 700; color: #475569;">$<?php echo number_format($row['amount'] + $row['discount_amount'], 2); ?></span>
                  </div>
                  <div style="flex: 1; min-width: 120px; background: #fff; padding: 10px; border-radius: 6px; border: 1px solid #fecaca; text-align: center;">
                    <span style="display: block; font-size: 11px; color: #dc2626; font-weight: 600; text-transform: uppercase;">Discount</span>
                    <span id="display_discount_amount" style="font-size: 16px; font-weight: 700; color: #dc2626;">$<?php echo number_format($row['discount_amount'], 2); ?></span>
                  </div>
                  <div style="flex: 1; min-width: 120px; background: #fff; padding: 10px; border-radius: 6px; border: 1px solid #bfdbfe; text-align: center;">
                    <span style="display: block; font-size: 11px; color: #1d4ed8; font-weight: 600; text-transform: uppercase;">Net Total</span>
                    <span id="display_total_amount" style="font-size: 16px; font-weight: 700; color: #1e3a8a;">$<?php echo number_format($row['amount'], 2); ?></span>
                  </div>
                  <div style="flex: 1; min-width: 120px; background: #fff; padding: 10px; border-radius: 6px; border: 1px solid #bbf7d0; text-align: center;">
                    <span style="display: block; font-size: 11px; color: #166534; font-weight: 600; text-transform: uppercase;">Paid</span>
                    <span id="display_paid_amount" style="font-size: 16px; font-weight: 700; color: #059669;">$<?php echo number_format($row['paid_amount'], 2); ?></span>
                  </div>
                  <div style="flex: 1; min-width: 120px; background: #fff; padding: 10px; border-radius: 6px; border: 1px solid #fecaca; text-align: center;">
                    <span style="display: block; font-size: 11px; color: #991b1b; font-weight: 600; text-transform: uppercase;">Remaining</span>
                    <span id="display_remaining_amount" style="font-size: 16px; font-weight: 700; color: #dc2626;">$<?php echo number_format($balance, 2); ?></span>
                  </div>
                </div>
                <input type="hidden" name="amount" id="amount" value="<?php echo $per_month; ?>">
                <input type="hidden" name="total_amount" id="total_amount" value="<?php echo $row['amount']; ?>">
              </div>
            </div>
          </div>

          <!-- Trainer Details -->
          <div class="form-section">
            <div class="form-section-title">Trainer Details :</div>
            <div class="form-group-row">
              <div class="form-col">
                <label class="custom-label">Trainer Type</label>
                <div class="radio-group">
                  <i class="fas fa-id-card input-icon" style="color:#3182ce; position:relative; left:0; margin-right:10px;"></i>
                  <label class="radio-label">
                    <input type="radio" name="trainer_type" value="General Training" <?php echo ($row['trainer_type'] == 'General Training' || $row['trainer_type'] == '') ? 'checked' : ''; ?>> General Training
                  </label>
                  <label class="radio-label">
                    <input type="radio" name="trainer_type" value="Personal" <?php echo ($row['trainer_type'] == 'Personal') ? 'checked' : ''; ?>> Personal Trainer
                  </label>
                </div>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="action-buttons" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <a href="members.php" class="btn" style="background: #ef4444; color: white; padding: 10px 20px; border-radius: 6px; font-weight: 600; text-decoration: none; border: none; font-size: 15px;"><i class="fas fa-times"></i> CANCEL / GO BACK</a>
            <button type="submit" class="btn btn-save" name="submit">UPDATE MEMBER</button>
          </div>

        </form>
      </div>
    </div>
    </div>
  </div>
  <?php } ?>

  <!-- Webcam Modal -->
  <div id="webcamModal" class="webcam-modal">
    <div class="webcam-container">
      <h3 style="margin-top:0; margin-bottom:15px; font-size:18px;">Capture Member Photo</h3>
      <video id="webcam-video" autoplay playsinline></video>
      <canvas id="webcam-canvas" style="display:none;"></canvas>
      <div class="webcam-actions">
        <button type="button" class="btn-cancel-webcam" onclick="closeWebcam()">Cancel</button>
        <button type="button" class="btn-capture" onclick="capturePhoto()">Snap Photo</button>
      </div>
    </div>
  </div>

  <!-- Webcam Modal -->
  <div id="webcamModal" class="webcam-modal">
    <div class="webcam-container">
      <h3 style="margin-top:0; margin-bottom:15px; font-size:18px;">Capture Member Photo</h3>
      <video id="webcam-video" autoplay playsinline></video>
      <canvas id="webcam-canvas" style="display:none;"></canvas>
      <div class="webcam-actions">
        <button type="button" class="btn-cancel-webcam" onclick="closeWebcam()">Cancel</button>
        <button type="button" class="btn-capture" onclick="capturePhoto()">Snap Photo</button>
      </div>
    </div>
  </div>

  <div class="row-fluid">
    <div id="footer" class="span12" style="color:white;"> <?php echo date("Y"); ?> &copy; M*A GYM System Developed By Abdikafi </div>
  </div>

  <script src="../js/jquery.min.js"></script>
  <script src="../js/bootstrap.min.js"></script>

  <script type="text/javascript">
    function calculateExpiry() {
      var dor = document.getElementById('dor').value;
      var plan = document.getElementById('plan').value;
      if (dor && plan) {
        var date = new Date(dor);
        date.setMonth(date.getMonth() + parseInt(plan));
        var yyyy = date.getFullYear();
        var mm = String(date.getMonth() + 1).padStart(2, '0');
        var dd = String(date.getDate()).padStart(2, '0');
        document.getElementById('expiry_date').value = yyyy + '-' + mm + '-' + dd;
      }
      calculateTotal();
    }

    function updateAmount() {
      var select = document.getElementById('services');
      var option = select.options[select.selectedIndex];
      if (option && option.dataset.charge) {
        var hiddenAmount = document.getElementById('amount');
        if (hiddenAmount) {
          hiddenAmount.value = option.dataset.charge;
        }
      }
      calculateTotal();
    }

    function calculateTotal() {
      var select = document.getElementById('services');
      var option = select.options[select.selectedIndex];
      var amountPerMonth = parseFloat(option && option.dataset.charge ? option.dataset.charge : 0) || 0;
      var planMonths = parseFloat(document.getElementById('plan').value) || 0;
      var subTotal = amountPerMonth * planMonths;

      var discountTypeRadios = document.querySelectorAll('input[name="discount_type"]:checked');
      var discountType = discountTypeRadios.length > 0 ? discountTypeRadios[0].value : 'amount';
      var discountAmount = parseFloat(document.getElementById('discount_amount').value) || 0;

      // Update Discount Icon
      var discountIcon = document.getElementById('discount-icon');
      if (discountIcon) {
        discountIcon.className = discountType === 'percent' ? 'fas fa-percent input-icon' : 'fas fa-dollar-sign input-icon';
      }

      var finalTotal = subTotal;
      if (discountType === 'percent') {
        finalTotal = subTotal - (subTotal * (discountAmount / 100));
      } else {
        finalTotal = subTotal - discountAmount;
      }

      if (finalTotal < 0) finalTotal = 0;

      // Update Subtotal UI
      var displaySub = document.getElementById('display_subtotal');
      if (displaySub) displaySub.innerText = '$' + subTotal.toFixed(2);

      // Update Total UI
      var displayTotal = document.getElementById('display_total_amount');
      if (displayTotal) displayTotal.innerText = '$' + finalTotal.toFixed(2);
      document.getElementById('total_amount').value = finalTotal.toFixed(2);

      // Update Remaining
      var paidAmount = parseFloat(document.getElementById('paid_amount').value) || 0;
      var remaining = finalTotal - paidAmount;
      if (remaining < 0) remaining = 0;

      var displayPaid = document.getElementById('display_paid_amount');
      if (displayPaid) displayPaid.innerText = '$' + paidAmount.toFixed(2);

      var displayRemain = document.getElementById('display_remaining_amount');
      if (displayRemain) displayRemain.innerText = '$' + remaining.toFixed(2);
    }

    // Listen for paid amount changes
    document.getElementById('paid_amount').addEventListener('input', calculateTotal);

    // Call total calculation once on load to ensure sync if initial values were weird
    window.onload = function() {
      // Calculate initial subtotal based on plan and service
      var select = document.getElementById('services');
      var option = select.options[select.selectedIndex];
      var plan = document.getElementById('plan').value;
      if (option && option.dataset.charge && plan) {
        var subtotal = parseFloat(option.dataset.charge) * parseInt(plan);
        document.getElementById('display_subtotal').innerText = '$' + subtotal.toFixed(2);
      }
      calculateTotal();
    };

    function previewFile() {
      var preview = document.getElementById('imagePreview');
      var icon = document.getElementById('defaultAvatarIcon');
      var file = document.getElementById('member_photo').files[0];
      var reader = new FileReader();

      // Clear webcam image if a file is manually selected
      document.getElementById('webcam_image').value = "";

      reader.onloadend = function() {
        preview.src = reader.result;
        preview.style.display = 'block';
        icon.style.display = 'none';
      }

      if (file) {
        reader.readAsDataURL(file);
      }
    }

    // --- Webcam Logic ---
    let stream = null;

    async function openWebcam() {
      const modal = document.getElementById('webcamModal');
      const video = document.getElementById('webcam-video');

      try {
        stream = await navigator.mediaDevices.getUserMedia({
          video: {
            width: { ideal: 640 },
            height: { ideal: 480 }
          }
        });
        video.srcObject = stream;
        modal.style.display = 'flex';
      } catch (err) {
        console.error("Webcam error:", err);
        alert("Camaradu ma furnayso ama ogolaansho ayaad u dhiiday.");
      }
    }

    function closeWebcam() {
      const modal = document.getElementById('webcamModal');
      const video = document.getElementById('webcam-video');

      if (stream) {
        stream.getTracks().forEach(track => track.stop());
        stream = null;
      }
      video.srcObject = null;
      modal.style.display = 'none';
    }

    function capturePhoto() {
      const video = document.getElementById('webcam-video');
      const canvas = document.getElementById('webcam-canvas');
      const preview = document.getElementById('imagePreview');
      const icon = document.getElementById('defaultAvatarIcon');
      const hiddenInput = document.getElementById('webcam_image');

      canvas.width = video.videoWidth;
      canvas.height = video.videoHeight;
      const context = canvas.getContext('2d');

      // Mirror capture to match video feed
      context.translate(canvas.width, 0);
      context.scale(-1, 1);
      context.drawImage(video, 0, 0, canvas.width, canvas.height);

      const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
      hiddenInput.value = dataUrl;
      preview.src = dataUrl;
      preview.style.display = 'block';
      icon.style.display = 'none';

      // Clear file input if webcam is used
      document.getElementById('member_photo').value = "";

      closeWebcam();
    }

    function removeDocument() {
      if (confirm('Are you sure you want to remove this document?')) {
        document.getElementById('existing_doc_container').style.display = 'none';
        document.getElementById('new_doc_upload').style.display = 'block';
        document.getElementById('remove_id_document').value = '1';

        // Remove disabled visually and logically from the dropdown type
        var typeSelect = document.querySelector('select[name="id_doc_type"]');
        typeSelect.disabled = false;

        // Ensure a hidden input isn't shadowing the select value now
        var hiddenSelect = document.querySelector('input[name="id_doc_type"][type="hidden"]');
        if (hiddenSelect) hiddenSelect.remove();
      }
    }
  </script>
</body>

</html>