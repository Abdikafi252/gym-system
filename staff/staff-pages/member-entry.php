<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
  <link rel="stylesheet" href="../../css/bootstrap.min.css" />
  <link rel="stylesheet" href="../../css/bootstrap-responsive.min.css" />
  <link rel="stylesheet" href="../../css/fullcalendar.css" />
  <link rel="stylesheet" href="../../css/matrix-style.css" />
  <link rel="stylesheet" href="../../css/matrix-media.css" />
  <link href="../../font-awesome/css/fontawesome.css" rel="stylesheet" />
  <link href="../../font-awesome/css/all.css" rel="stylesheet" />
  <link rel="stylesheet" href="../../css/jquery.gritter.css" />
  <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
  <style>
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
      position: relative;
    }

    .btn-close-card {
      position: absolute;
      top: 15px;
      right: 15px;
      background: #fdf2f2;
      color: #e53e3e;
      border: none;
      border-radius: 50%;
      width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 16px;
      cursor: pointer;
      transition: all 0.2s;
      text-decoration: none;
    }

    .btn-close-card:hover {
      background: #e53e3e;
      color: #fff;
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
      display: flex;
      justify-content: center;
      margin-bottom: 25px;
    }

    .avatar-upload-card {
      background: #fff;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      padding: 20px 30px;
      display: flex;
      align-items: center;
      gap: 20px;
      box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
    }

    .avatar-circle {
      width: 110px;
      height: 110px;
      background: #edf2f7;
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 48px;
      color: #a0aec0;
      position: relative;
      border: 3px solid #e2e8f0;
      cursor: pointer;
      transition: border-color 0.2s;
    }

    .avatar-circle:hover {
      border-color: #e53e3e;
    }

    .avatar-icon {
      position: absolute;
      bottom: 2px;
      right: 2px;
      background: #e53e3e;
      color: #fff;
      width: 32px;
      height: 32px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 14px;
      border: 3px solid #fff;
      box-shadow: 0 2px 6px rgba(229, 62, 62, 0.4);
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
      margin-bottom: 8px;
      color: #4a5568;
      font-weight: 600;
      font-size: 13px;
    }

    .input-wrapper {
      position: relative;
      display: flex;
      align-items: center;
    }

    .input-icon {
      position: absolute;
      left: 12px;
      color: #cbd5e0;
      font-size: 14px;
      z-index: 2;
    }

    .custom-input {
      width: 100%;
      height: 42px;
      padding: 8px 12px 8px 35px !important;
      font-size: 14px;
      color: #2d3748;
      background-color: #fff;
      border: 1px solid #e2e8f0;
      border-radius: 6px;
      box-sizing: border-box !important;
      transition: all 0.2s;
    }

    .custom-input:focus {
      outline: none;
      border-color: #3182ce;
      box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1);
    }

    .custom-input::placeholder {
      color: #a0aec0;
    }

    /* Read-only styles */
    .custom-input[readonly] {
      background: #f7fafc;
      color: #718096 !important;
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
      background: #e53e3e !important;
      color: #fff !important;
      border: none !important;
      padding: 12px 30px !important;
      font-size: 15px !important;
      border-radius: 6px !important;
      font-weight: 600 !important;
      cursor: pointer !important;
      width: auto !important;
      display: inline-flex !important;
      align-items: center !important;
      justify-content: center !important;
      transition: background 0.2s !important;
    }

    .btn-save:hover {
      background: #c53030 !important;
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
  <?php include '../includes/header-content.php'; ?>
  <!--close-Header-part-->


  <!--top-Header-menu-->
  <?php include '../includes/header.php' ?>
  <!--close-top-Header-menu-->

  <!--sidebar-menu-->
  <?php $page = 'members-entry';
  include '../includes/sidebar.php' ?>
  <!--sidebar-menu-->

  <div id="content">
    <div id="content-header">
      <h1 class="page-title"><i class="fas fa-user-plus"></i> Register New Member</h1>
    </div>

    <div class="container-fluid">
      <?php
      include 'dbcon.php';
      if (!isset($conn) && isset($con)) $conn = $con;

      $res = mysqli_query($conn, "SELECT biometric_id FROM members");
      $max_bio = 0;
      while ($row = mysqli_fetch_array($res)) {
        $val = (int)$row['biometric_id'];
        if ($val > $max_bio && $val < 1000000) {
          $max_bio = $val;
        }
      }
      $next_bio = str_pad($max_bio + 1, 10, "0", STR_PAD_LEFT);
      ?>
      <div class="form-card-container">
        <a href="members.php" class="btn-close-card" title="Go Back">
          <i class="fas fa-times"></i>
        </a>
        <form action="add-member-req.php" method="POST" enctype="multipart/form-data">
          <input type="file" name="photo" id="member_photo" style="display: none;" accept="image/*" onchange="previewFile()">
          <input type="hidden" name="webcam_image" id="webcam_image">

          <div class="form-section">
            <div class="form-section-title">Personal Information :</div>

            <div style="display: flex; gap: 30px; margin-bottom: 20px; flex-wrap: wrap;">
              <!-- Photo Column -->
              <div style="flex: 0 0 250px;">
                <div class="avatar-upload-card" style="flex-direction: column; text-align: center; justify-content: center; height: 100%;">
                  <label for="member_photo" style="cursor:pointer; margin:0;">
                    <div class="avatar-circle" id="imagePreviewContainer" style="margin:0 auto 15px auto;">
                      <i class="fas fa-user" id="defaultAvatarIcon"></i>
                      <img id="imagePreview" src="#" alt="Preview" style="display:none; width:100%; height:100%; border-radius:50%; object-fit:cover; position:absolute; top:0; left:0;">
                      <div class="avatar-icon"><i class="fas fa-camera"></i></div>
                    </div>
                  </label>
                  <div>
                    <div style="font-size:15px; font-weight:700; color:#2d3748; margin-bottom:4px;">Profile Picture</div>
                    <div style="font-size:13px; color:#718096; margin-bottom:12px;">Take a photo or upload</div>
                    <div style="display: flex; flex-direction: column; gap: 8px; align-items: center;">
                      <button type="button" class="btn" onclick="openWebcam()" style="background:#e53e3e; color:#fff; font-size:12px; font-weight:600; padding:6px 16px; border-radius:20px; border:none; width: fit-content;">
                        <i class="fas fa-camera"></i> Take Photo
                      </button>
                      <label for="member_photo" style="background:#f1f5f9; color:#475569; font-size:11px; font-weight:600; padding:4px 12px; border-radius:20px; display:inline-block; cursor: pointer; border: 1px solid #e2e8f0;">
                        <i class="fas fa-upload"></i> Upload File
                      </label>
                      <input type="hidden" name="webcam_image" id="webcam_image" value="" />
                    </div>
                  </div>
                </div>
              </div>

              <!-- Fields Column -->
              <div style="flex: 1; min-width: 300px;">
                <div class="form-group-row">
                  <div class="form-col">
                    <label class="custom-label">Full Name</label>
                    <div class="input-wrapper">
                      <i class="fas fa-user input-icon" style="color:#e53e3e;"></i>
                      <input type="text" class="custom-input" name="fullname" placeholder="Enter Full Name" required />
                    </div>
                  </div>
                  <div class="form-col">
                    <label class="custom-label">Face ID (Wajiga)</label>
                    <div class="input-wrapper" style="gap: 10px;">
                      <i class="fas fa-user-check input-icon" style="color:#e53e3e;"></i>
                      <input type="text" class="custom-input" name="biometric_id" id="biometric_id" value="<?php echo $next_bio; ?>" required readonly />
                    </div>
                  </div>
                </div>

                <div class="form-group-row">
                  <div class="form-col">
                    <label class="custom-label">Username</label>
                    <div class="input-wrapper">
                      <i class="fas fa-user-circle input-icon" style="color:#e53e3e;"></i>
                      <input type="text" class="custom-input" name="username" placeholder="Username" required />
                    </div>
                  </div>
                  <div class="form-col">
                    <label class="custom-label">Password</label>
                    <div class="input-wrapper">
                      <i class="fas fa-lock input-icon" style="color:#e53e3e;"></i>
                      <input type="password" class="custom-input" name="password" placeholder="**********" required />
                    </div>
                  </div>
                </div>

                <div class="form-group-row">
                  <div class="form-col">
                    <label class="custom-label">Email</label>
                    <div class="input-wrapper">
                      <i class="fas fa-envelope input-icon" style="color:#e53e3e;"></i>
                      <input type="email" class="custom-input" name="email" placeholder="Email" />
                    </div>
                  </div>
                  <div class="form-col">
                    <label class="custom-label">Phone (Mobile)</label>
                    <div class="input-wrapper">
                      <i class="fas fa-phone input-icon" style="color:#e53e3e;"></i>
                      <input type="number" class="custom-input" name="contact" placeholder="Example: 61xxxxxxx" required />
                    </div>
                  </div>
                </div>

                <div class="form-group-row">
                  <div class="form-col">
                    <label class="custom-label">Gender</label>
                    <div class="input-wrapper">
                      <i class="fas fa-venus-mars input-icon" style="color:#e53e3e;"></i>
                      <select name="gender" class="custom-input" style="padding-left:35px !important;" required>
                        <option value="Male" selected>Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-col">
                    <label class="custom-label">Batch</label>
                    <div class="input-wrapper">
                      <i class="fas fa-database input-icon" style="color:#e53e3e;"></i>
                      <select name="batch" class="custom-input" style="padding-left:35px !important;">
                        <option value="" selected>Select Batch</option>
                        <option value="Morning">Morning</option>
                        <option value="Noon">Noon</option>
                        <option value="Afternoon">Afternoon</option>
                        <option value="Evening">Evening</option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="form-group-row">
                  <div class="form-col form-col-full">
                    <label class="custom-label">Address</label>
                    <div class="input-wrapper">
                      <textarea name="address" class="custom-input" style="padding-left:15px !important; height:auto !important;" rows="2" placeholder="Enter the person's address..." required></textarea>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Document -->
          <div class="form-section">
            <div class="form-section-title">Identification Document :</div>

            <!-- Identification Document -->
            <div class="form-group-row">
              <div class="form-col">
                <label class="custom-label">ID Document Type</label>
                <div class="input-wrapper">
                  <i class="fas fa-file-alt input-icon" style="color:#e53e3e;"></i>
                  <select name="id_doc_type" class="custom-input" style="padding-left:35px !important;">
                    <option value="">Select Type</option>
                    <option value="National ID">National ID</option>
                    <option value="Passport">Passport</option>
                    <option value="Driving License">Driving License</option>
                    <option value="Birth Certificate">Birth Certificate</option>
                    <option value="Other">Other</option>
                  </select>
                </div>
              </div>
              <div class="form-col">
                <label class="custom-label">Identification Document (Upload)</label>
                <div class="input-wrapper" style="align-items:center;">
                  <i class="fas fa-upload input-icon" style="color:#e53e3e;"></i>
                  <input type="file" name="id_document" class="custom-input" style="padding-left:35px !important; padding-top:8px;" accept=".jpg,.jpeg,.png,.pdf" />
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
                  <i class="far fa-calendar input-icon" style="color:#e53e3e;"></i>
                  <select name="plan" id="plan" class="custom-input" style="padding-left:35px !important;" onchange="calculateExpiry()" required>
                    <option value="" disabled selected>Select Plan</option>
                    <?php
                    $branch_id = $_SESSION['branch_id'];
                    $qryPkgs = "SELECT * FROM packages WHERE branch_id = '$branch_id'";
                    $resPkgs = mysqli_query($con, $qryPkgs);
                    while ($pkg = mysqli_fetch_array($resPkgs)) {
                      echo "<option value='" . $pkg['duration'] . "'>" . $pkg['packagename'] . " (" . $pkg['duration'] . " Months)</option>";
                    }
                    ?>
                  </select>
                </div>
              </div>
              <div class="form-col">
                <label class="custom-label">Service</label>
                <div class="input-wrapper">
                  <i class="fas fa-dumbbell input-icon" style="color:#e53e3e;"></i>
                  <input type="hidden" id="amount" value="0">
                  <select name="services" id="services" class="custom-input" style="padding-left:35px !important;" onchange="updateAmount()" required>
                    <option value="" disabled selected>Select Service</option>
                    <?php
                    $branch_id = $_SESSION['branch_id'];
                    $qryRates = "SELECT * FROM rates WHERE branch_id = '$branch_id'";
                    $resRates = mysqli_query($con, $qryRates);
                    while ($rate = mysqli_fetch_array($resRates)) {
                      echo "<option value='" . $rate['name'] . "' data-charge='" . $rate['charge'] . "'>" . $rate['name'] . " - $" . $rate['charge'] . " per month</option>";
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
                  <i class="far fa-calendar-check input-icon" style="color:#e53e3e;"></i>
                  <input type="date" name="dor" id="dor" class="custom-input" style="padding-left:35px !important;" value="<?php echo date('Y-m-d'); ?>" onchange="calculateExpiry()" required />
                </div>
              </div>
              <div class="form-col">
                <label class="custom-label">Expiry Date</label>
                <div class="input-wrapper">
                  <i class="far fa-calendar-times input-icon" style="color:#e53e3e;"></i>
                  <input type="date" name="expiry_date" id="expiry_date" class="custom-input" style="padding-left:35px !important;" required readonly />
                </div>
              </div>
            </div>

            <div class="form-group-row">
              <div class="form-col">
                <label class="custom-label">Payment Start Date</label>
                <div class="input-wrapper">
                  <i class="fas fa-receipt input-icon" style="color:#e53e3e;"></i>
                  <input type="date" name="paid_date" class="custom-input" style="padding-left:35px !important;" value="<?php echo date('Y-m-d'); ?>" required />
                </div>
              </div>
              <div class="form-col"></div>
            </div>

            <div class="form-group-row">
              <div class="form-col">
                <label class="custom-label">Discount Type</label>
                <div class="radio-group" style="padding-left:15px; height: 42px; display: flex; align-items: center; gap: 20px; background: #fff; border: 1px solid #e2e8f0; border-radius: 6px;">
                  <label class="radio-label" style="margin:0; font-size:14px; font-weight:600; color:#4a5568; cursor:pointer;">
                    <input type="radio" name="discount_type" value="percent" checked onchange="calculateTotal()"> Percentage (%)
                  </label>
                  <label class="radio-label" style="margin:0; font-size:14px; font-weight:600; color:#4a5568; cursor:pointer;">
                    <input type="radio" name="discount_type" value="amount" onchange="calculateTotal()"> Amount ($)
                  </label>
                </div>
              </div>
              <div class="form-col">
                <label class="custom-label">Discount Amount</label>
                <div class="input-wrapper">
                  <i class="fas fa-percent input-icon" style="color:#e53e3e;" id="discount-icon"></i>
                  <input type="number" name="discount_amount" id="discount_amount" class="custom-input" style="padding-left:35px !important;" value="0" min="0" oninput="calculateTotal()" />
                </div>
              </div>
            </div>

            <div class="form-group-row">
              <div class="form-col">
                <label class="custom-label">Paid Amount</label>
                <div class="input-wrapper">
                  <i class="fas fa-money-bill-wave input-icon" style="color:#e53e3e;"></i>
                  <input type="number" name="paid_amount" id="paid_amount" class="custom-input" style="padding-left:35px !important;" value="0" min="0" required />
                </div>
              </div>
              <div class="form-col">
                <label class="custom-label" style="color: #c53030;">Total Amount</label>
                <div class="input-wrapper" style="background:#fef2f2; border-radius:6px; border:1px solid #fecaca; height:42px; display:flex; align-items:center; padding:0 15px;">
                  <i class="fas fa-dollar-sign" style="color:#b91c1c; margin-right:8px; font-size:16px;"></i>
                  <span id="display_total_amount" style="font-size:18px; font-weight:700; color:#991b1b;">0.00</span>
                </div>
              </div>
            </div>

            <div class="form-group-row">
              <div class="form-col form-col-full">
                <label class="custom-label">Comments</label>
                <div class="input-wrapper">
                  <i class="far fa-comment-alt input-icon" style="color:#3182ce; top:15px; transform:none;"></i>
                  <textarea name="comments" class="custom-input" style="height:auto !important; padding-left:35px !important;" rows="2" placeholder="Enter additional details..."></textarea>
                </div>
              </div>
            </div>

            <input type="hidden" name="total_amount" id="total_amount" value="0">
            <input type="hidden" name="trainer_type" value="None">

            <div style="text-align: right; margin-top: 15px;">
              <button type="submit" class="btn-save"><i class="fas fa-save" style="margin-right:8px;"></i> Save</button>
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
                    <input type="radio" name="trainer_type" value="General Training" checked> General Training
                  </label>
                  <label class="radio-label">
                    <input type="radio" name="trainer_type" value="Personal"> Personal Trainer
                  </label>
                </div>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="action-buttons">
            <button type="submit" class="btn btn-save" name="submit">SAVE</button>
          </div>

        </form>
      </div>
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

  <script src="../../js/jquery.min.js"></script>
  <script src="../../js/bootstrap.min.js"></script>

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
        document.getElementById('amount').value = option.dataset.charge;
      }
      calculateTotal();
    }

    function calculateTotal() {
      var amountPerMonth = parseFloat(document.getElementById('amount').value) || 0;
      var planMonths = parseFloat(document.getElementById('plan').value) || 0;
      var subTotal = amountPerMonth * planMonths;

      var discountType = document.querySelector('input[name="discount_type"]:checked').value;
      var discountAmount = parseFloat(document.getElementById('discount_amount').value) || 0;

      // Update Discount Icon
      document.getElementById('discount-icon').className = discountType === 'percent' ? 'fas fa-percent input-icon' : 'fas fa-dollar-sign input-icon';

      var finalTotal = subTotal;
      if (discountType === 'percent') {
        finalTotal = subTotal - (subTotal * (discountAmount / 100));
      } else {
        finalTotal = subTotal - discountAmount;
      }

      if (finalTotal < 0) finalTotal = 0;

      // Set values
      document.getElementById('display_total_amount').innerText = finalTotal.toFixed(2);
      document.getElementById('total_amount').value = finalTotal.toFixed(2);

      // Auto-fill paid amount for convenience
      document.getElementById('paid_amount').value = finalTotal.toFixed(2);
    }

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
      } else {
        preview.src = "";
        preview.style.display = 'none';
        icon.style.display = 'block';
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
        alert("Camaradu ma furnayso ama ogolaansho ayaad u diiday.");
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

    // Face registration is handled via hardware terminal.
  </script>
</body>

</html>