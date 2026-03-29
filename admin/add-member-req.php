<?php
session_start();
// Check session
if (!isset($_SESSION['user_id'])) {
  header('location:../index.php');
}
require_once __DIR__ . '/../includes/security_core.php';
$_SESSION['designation'] = current_designation();

// Enable Error Reporting
mysqli_report(MYSQLI_REPORT_OFF);
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title>Gym System - Processing</title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../css/bootstrap.min.css" />
  <link rel="stylesheet" href="../css/matrix-style.css" />
</head>

<body>

  <?php include 'includes/header-content.php'; ?>
  <?php include 'includes/topheader.php' ?>
  <?php $page = 'members-entry';
  include 'includes/sidebar.php' ?>

  <div id="content">
    <div id="content-header" style="padding: 20px;">
      <h1>Registration Status</h1>
    </div>
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span12">
          <div class="widget-box">
            <div class="widget-content">
              <?php

              if (isset($_POST['fullname'])) {
                $fullname = $_POST["fullname"];
                $username = $_POST["username"];
                $password = $_POST["password"];
                $dor = $_POST["dor"];
                $gender = $_POST["gender"];
                $services = $_POST["services"];
                $amount = $_POST["amount"];
                $plan = $_POST["plan"];
                $address = $_POST["address"];
                $contact = $_POST["contact"];
                $biometric_id = $_POST["biometric_id"];
                $expiry_date = $_POST["expiry_date"];

                // Calculate Dates
                $paid_date = isset($_POST['paid_date']) ? trim($_POST['paid_date']) : $dor;
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $paid_date)) {
                  $paid_date = $dor;
                }
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $paid_date)) {
                  $paid_date = date("Y-m-d");
                }
                $p_year = date('Y', strtotime($paid_date));

                // Capture Registrar as explicit role + name (Cashier/Manager)
                $registrar_name = trim((string)($_SESSION['fullname'] ?? $_SESSION['username'] ?? ''));
                $registrar_role = trim((string)($_SESSION['designation'] ?? current_designation()));

                if ($registrar_name === '') {
                  $id = (int)($_SESSION['user_id'] ?? 0);
                  include 'dbcon.php';
                  $q = mysqli_query($con, "SELECT fullname, username, designation FROM admin WHERE user_id='$id' LIMIT 1");
                  if ($q && ($row = mysqli_fetch_assoc($q))) {
                    $registrar_name = trim((string)($row['fullname'] ?? ''));
                    if ($registrar_name === '') {
                      $registrar_name = trim((string)($row['username'] ?? ''));
                    }
                    if ($registrar_role === '') {
                      $registrar_role = trim((string)($row['designation'] ?? ''));
                    }
                  }
                }

                $registrar_role = trim((string)$registrar_role);
                if ($registrar_role === '') {
                  $registrar_role = 'Staff';
                }

                if ($registrar_name === '') {
                  $registrar_name = 'Unknown';
                }

                $registered_by = $registrar_role . ': ' . $registrar_name;

                $batch = isset($_POST["batch"]) ? $_POST["batch"] : '';
                $email = isset($_POST["email"]) ? $_POST["email"] : '';
                $aadhar = isset($_POST["aadhar"]) ? $_POST["aadhar"] : '';
                $pan = isset($_POST["pan"]) ? $_POST["pan"] : '';
                $discount_type = isset($_POST["discount_type"]) ? $_POST["discount_type"] : 'amount';
                $discount_amount = isset($_POST["discount_amount"]) ? $_POST["discount_amount"] : 0;
                $paid_amount = isset($_POST["paid_amount"]) ? $_POST["paid_amount"] : 0;
                $comments = isset($_POST["comments"]) ? $_POST["comments"] : '';
                $branch_id = isset($_POST["branch_id"]) ? $_POST["branch_id"] : null;

                include 'dbcon.php';
                require_once __DIR__ . '/../includes/audit_helper.php';
                require_once __DIR__ . '/includes/accounting_engine.php';
                acc_bootstrap_tables($con);

                if (!$con) {
                  die("<h3 style='color:red'>Database Connection Failed: " . mysqli_connect_error() . "</h3>");
                }

                $trainer_type = isset($_POST['trainer_type']) ? $_POST['trainer_type'] : 'General Training';
                $totalamount  = isset($_POST['total_amount']) ? $_POST['total_amount'] : ($amount * $plan);

                // Handle Photo Upload (File or Webcam)
                $photo_name = '';
                $target_dir = "../img/members/";

                if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
                  $file_ext   = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                  $photo_name = "member_" . time() . "_" . $biometric_id . "." . $file_ext;
                  if (!move_uploaded_file($_FILES['photo']['tmp_name'], $target_dir . $photo_name)) {
                    $photo_name = '';
                  }
                } elseif (!empty($_POST['webcam_image'])) {
                  // Handle Webcam Base64 Image
                  $base64_img = $_POST['webcam_image'];
                  if (strpos($base64_img, 'data:image/jpeg;base64,') === 0) {
                    $base64_img = str_replace('data:image/jpeg;base64,', '', $base64_img);
                    $base64_img = str_replace(' ', '+', $base64_img);
                    $data = base64_decode($base64_img);
                    $photo_name = "member_cam_" . time() . "_" . $biometric_id . ".jpg";
                    if (!file_put_contents($target_dir . $photo_name, $data)) {
                      $photo_name = "";
                    }
                  }
                }

                // Ensure id_document columns exist
                mysqli_query($con, "ALTER TABLE members ADD COLUMN IF NOT EXISTS id_doc_type VARCHAR(50) DEFAULT ''");
                mysqli_query($con, "ALTER TABLE members ADD COLUMN IF NOT EXISTS id_document VARCHAR(255) DEFAULT ''");
                mysqli_query($con, "ALTER TABLE members ADD COLUMN IF NOT EXISTS created_by VARCHAR(100) NULL");
                mysqli_query($con, "ALTER TABLE members ADD COLUMN IF NOT EXISTS updated_by VARCHAR(100) NULL");
                mysqli_query($con, "ALTER TABLE members ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP NULL DEFAULT NULL");
                mysqli_query($con, "CREATE TABLE IF NOT EXISTS payment_history (
                  id INT AUTO_INCREMENT PRIMARY KEY,
                  invoice_no VARCHAR(50) NULL,
                  user_id INT NOT NULL,
                  fullname VARCHAR(255) NOT NULL,
                  amount DECIMAL(10,2) DEFAULT 0,
                  paid_amount DECIMAL(10,2) DEFAULT 0,
                  discount_amount DECIMAL(10,2) DEFAULT 0,
                  discount_type VARCHAR(20) DEFAULT 'amount',
                  plan INT DEFAULT 1,
                  services VARCHAR(255) DEFAULT '',
                  paid_date DATE,
                  expiry_date DATE,
                  branch_id INT DEFAULT 0,
                  recorded_by VARCHAR(100) DEFAULT 'Admin',
                  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )");
                mysqli_query($con, "ALTER TABLE payment_history ADD COLUMN IF NOT EXISTS invoice_no VARCHAR(50) NULL");
                mysqli_query($con, "ALTER TABLE payment_history ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");

                // Handle ID Document Upload
                $id_doc_type = isset($_POST['id_doc_type']) ? $_POST['id_doc_type'] : '';
                $id_doc_name = '';
                if (isset($_FILES['id_document']) && $_FILES['id_document']['error'] == 0) {
                  $id_target_dir = "../img/members/";
                  $id_ext = pathinfo($_FILES['id_document']['name'], PATHINFO_EXTENSION);
                  $id_doc_name = "iddoc_" . time() . "_" . $biometric_id . "." . $id_ext;
                  if (!move_uploaded_file($_FILES['id_document']['tmp_name'], $id_target_dir . $id_doc_name)) {
                    $id_doc_name = '';
                  }
                }

                $password = password_hash($password, PASSWORD_DEFAULT);

                $registered_by_esc = mysqli_real_escape_string($con, $registered_by);
                $qry = "INSERT INTO members(fullname,username,password,dor,gender,services,amount,p_year,paid_date,plan,address,contact,biometric_id,expiry_date,status,registered_by,attendance_count,ini_bodytype,curr_bodytype,progress_date,batch,email,aadhar,pan,discount_type,discount_amount,paid_amount,comments,trainer_type,photo,branch_id,id_doc_type,id_document,created_by,updated_by,updated_at)
                  VALUES ('$fullname','$username','$password','$dor','$gender','$services','$totalamount','$p_year','$paid_date','$plan','$address','$contact','$biometric_id','$expiry_date','Active','$registered_by','0','','','$dor','$batch','$email','$aadhar','$pan','$discount_type','$discount_amount','$paid_amount','$comments','$trainer_type','$photo_name','$branch_id','$id_doc_type','$id_doc_name','$registered_by_esc','$registered_by_esc',NOW())";

                echo "<p>Attempting Insert...</p>";

                $result = mysqli_query($con, $qry);

                if (!$result) {
                  echo "<div class='alert alert-error'>";
                  echo "<h1>Error Occured!</h1>";
                  echo "<h3>SQL Error Detail:</h3>";
                  echo "<pre>" . mysqli_error($con) . "</pre>";
                  echo "<p>Please take a screenshot of this error.</p>";
                  echo "</div>";
                  echo "<a class='btn btn-warning' href='member-entry.php'>Go Back</a>";
                } else {
                  $new_user_id = mysqli_insert_id($con);
                  $fullname_esc = mysqli_real_escape_string($con, $fullname);
                  $services_esc = mysqli_real_escape_string($con, $services);
                  $discount_type_esc = mysqli_real_escape_string($con, $discount_type);
                  $recorded_by_esc = mysqli_real_escape_string($con, $registered_by);
                  $invoice_no = 'GMS' . date('YmdHis') . rand(100, 999) . $new_user_id;

                  $history_qry = "INSERT INTO payment_history (invoice_no, user_id, fullname, amount, paid_amount, discount_amount, discount_type, plan, services, paid_date, expiry_date, branch_id, recorded_by)
                                  VALUES ('$invoice_no', '$new_user_id', '$fullname_esc', '$totalamount', '$paid_amount', '$discount_amount', '$discount_type_esc', '$plan', '$services_esc', '$paid_date', '$expiry_date', '$branch_id', '$recorded_by_esc')";
                  mysqli_query($con, $history_qry);
                  $payment_history_id = mysqli_insert_id($con);

                  if ((float)$paid_amount > 0) {
                    $accMemo = 'New member payment Invoice ' . $invoice_no;
                    acc_create_entry_once(
                      $con,
                      $paid_date,
                      $accMemo,
                      'payment_history',
                      (string)$payment_history_id,
                      [
                        ['account_code' => '1000', 'debit' => (float)$paid_amount, 'credit' => 0, 'line_memo' => $accMemo],
                        ['account_code' => '4000', 'debit' => 0, 'credit' => (float)$paid_amount, 'line_memo' => $accMemo]
                      ],
                      0,
                      0,
                      $recorded_by_esc
                    );
                  }

                  $actorId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '0';
                  audit_log($con, 'admin', $actorId, 'member_register', 'member', $new_user_id, 'New member registered: ' . $fullname);

                  // Success
                  include_once '../api/sms_helper.php';
                  $welcome_msg = "Asc $fullname, Ku soo dhowaw GYM System. Xubinnimadaada waa diyaar. Face ID: $biometric_id. Dhicitaanka: $expiry_date.";
                  sendSMS($contact, $welcome_msg);

                  // Face Terminal Sync (DA-T12 / WL-P72)
                  require_once __DIR__ . '/../includes/FaceTerminal.php';
                  $ft = new FaceTerminal($con);
                  if ($ft->isEnabled()) {
                      $sync_res = $ft->syncPerson($biometric_id, $fullname, $target_dir . $photo_name);
                      if (isset($sync_res['status']) && $sync_res['status'] === 'success') {
                          echo "<div class='alert alert-info'><strong>Terminal Sync:</strong> Member pushed to Face Terminal successfully.</div>";
                      } elseif (isset($sync_res['status']) && $sync_res['status'] === 'error') {
                          echo "<div class='alert alert-warning'><strong>Terminal Sync Warning:</strong> " . $sync_res['message'] . "</div>";
                      }
                  }

                   echo "<div class='alert alert-success'>";
                  echo "<h1>Success!</h1>";
                  echo "<h3>Member details added successfully.</h3>";
                  echo "<p>Status: <strong>Active</strong>. Member is now active.</p>";
                  echo "</div>";
                  echo "<a class='btn btn-success' href='members.php'>Confirm & Go to List</a>";

                  // Temporarily disable redirect to ensure user sees success message
                  // echo "<script>setTimeout(function(){ window.location.href = 'members.php'; }, 2000);</script>";
                }
              } else {
                echo "<div class='alert alert-warning'>";
                echo "<h3>NO POST DATA RECEIVED</h3>";
                echo "<p>The form did not submit any data to this page.</p>";
                echo "<a class='btn btn-primary' href='member-entry.php'>Go Back to Form</a>";
                echo "</div>";
              }
              ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!--Footer-part-->
  <div class="row-fluid">
    <div id="footer" class="span12"> <?php echo date("Y"); ?> &copy; Gym System </div>
  </div>

  <script src="../js/jquery.min.js"></script>
  <script src="../js/jquery.ui.custom.js"></script>
  <script src="../js/bootstrap.min.js"></script>
  <script src="../js/matrix.js"></script>
</body>

</html>