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
<html lang="so">

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
</head>

<body>

  <!--Header-part-->
  <?php include '../includes/header-content.php'; ?>
  <!--close-Header-part-->


  <!--top-Header-menu-->
  <?php include '../includes/header.php' ?>
  <!--close-top-Header-menu-->

  <!--sidebar-menu-->
  <?php $page = 'member-remove';
  include '../includes/sidebar.php' ?>
  <!--sidebar-menu-->

  <div id="content">
    <div id="content-header">
      <div id="breadcrumb"> <a href="index.php" title="Go to Home" class="tip-bottom"><i class="fas fa-home"></i> Home</a> <a href="remove-member.php" class="current">Remove Members</a> </div>
      <h1 class="text-center">Remove Members <i class="fas fa-group"></i></h1>
    </div>
    <div class="container-fluid">
      <hr>
      <div class="row-fluid">
        <div class="span12">

          <div class='widget-box'>
            <div class='widget-title'> <span class='icon'> <i class='fas fa-th'></i> </span>
              <h5>Remove Members</h5>
            </div>

            <style>
              .members-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
                gap: 20px;
                padding: 15px;
                background: #f8f9fa;
              }

              .member-card {
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
                padding: 20px;
                border: 1px solid #edf2f7;
                position: relative;
                transition: transform 0.2s ease, box-shadow 0.2s ease;
              }

              .member-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
              }

              .card-badge {
                position: absolute;
                top: -10px;
                left: 20px;
                background: #ef4444;
                color: white;
                font-size: 11px;
                font-weight: bold;
                padding: 4px 10px;
                border-radius: 20px;
                box-shadow: 0 2px 4px rgba(239, 68, 68, 0.4);
              }

              .card-badge.active {
                background: #10b981;
                box-shadow: 0 2px 4px rgba(16, 185, 129, 0.4);
              }

              .card-header-row {
                display: flex;
                align-items: center;
                margin-bottom: 20px;
                border-bottom: 1px solid #f3f4f6;
                padding-bottom: 15px;
              }

              .member-avatar {
                width: 65px;
                height: 65px;
                border-radius: 50%;
                background: #fee2e2;
                color: #dc2626;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 30px;
                margin-right: 15px;
                flex-shrink: 0;
              }

              .member-avatar.female {
                background: #fce7f3;
                color: #be185d;
              }

              .member-primary-info {
                flex-grow: 1;
              }

              .member-name {
                font-size: 16px;
                font-weight: 700;
                color: #111827;
                margin: 0 0 4px 0 !important;
              }

              .member-id {
                font-size: 13px;
                color: #dc2626;
                font-weight: 600;
                margin: 0 !important;
              }

              .card-details-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 15px;
                margin-bottom: 20px;
              }

              .detail-box {
                display: flex;
                flex-direction: column;
              }

              .detail-label {
                font-size: 12px;
                color: #6b7280;
                margin-bottom: 2px;
                font-weight: 600;
              }

              .detail-val {
                font-size: 14px;
                color: #374151;
                font-weight: 500;
              }

              .card-actions {
                display: flex;
                justify-content: center;
                align-items: center;
                padding-top: 15px;
                border-top: 1px dashed #e5e7eb;
              }

              .action-btn.red-btn {
                background: #ef4444;
                color: white;
                text-decoration: none;
                flex-direction: row;
                padding: 10px 20px;
                border-radius: 6px;
                font-size: 14px;
                font-weight: 700;
                transition: background 0.2s;
                display: flex;
                align-items: center;
                justify-content: center;
                width: 100%;
              }

              .action-btn.red-btn i {
                margin-bottom: 0;
                margin-right: 8px;
                font-size: 16px;
              }

              .action-btn.red-btn:hover {
                background: #dc2626;
                color: white;
              }
            </style>

            <div class="members-grid">
              <?php
              include "dbcon.php";
              $today = date('Y-m-d');
              $branch_id = $_SESSION['branch_id'];
              $qry = "SELECT * FROM members WHERE branch_id='$branch_id' ORDER BY dor DESC";
              $result = mysqli_query($conn, $qry);

              while ($row = mysqli_fetch_array($result)) {
                // Basic Details
                $name = htmlspecialchars($row['fullname'], ENT_QUOTES, 'UTF-8');
                $id = htmlspecialchars($row['user_id'], ENT_QUOTES, 'UTF-8');
                $gender = strtolower(htmlspecialchars($row['gender'], ENT_QUOTES, 'UTF-8'));
                $contact = htmlspecialchars($row['contact'], ENT_QUOTES, 'UTF-8');
                $plan_months = htmlspecialchars($row['plan'], ENT_QUOTES, 'UTF-8');
                $expiry = htmlspecialchars($row['expiry_date'], ENT_QUOTES, 'UTF-8');
                $status = htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8');

                // Photo path
                $photo = $row['photo'];
                $photo_path = (!empty($photo) && file_exists("../../img/members/" . $photo)) ? "../../img/members/" . $photo : "";

                // Determine icon and status
                $avatar_class = ($gender == 'female' || $gender == 'dhedig') ? 'female' : '';
                $icon_class = 'fas fa-user-times';
                $is_expired = ($expiry < $today || $status == 'Expired');
              ?>
                <!-- Single Card -->
                <div class="member-card">
                  <?php if ($is_expired): ?>
                    <div class="card-badge">Plan Expired</div>
                  <?php else: ?>
                    <div class="card-badge active">Active</div>
                  <?php endif; ?>

                  <div class="card-header-row">
                    <div class="member-avatar <?php echo $avatar_class; ?>" style="<?php echo !empty($photo_path) ? 'background-image: url(' . $photo_path . '); background-size: cover; background-position: center;' : ''; ?>">
                      <?php if (empty($photo_path)): ?>
                        <i class="<?php echo $icon_class; ?>"></i>
                      <?php endif; ?>
                    </div>
                    <div class="member-primary-info">
                      <h4 class="member-name"><?php echo $name; ?></h4>
                      <p class="member-id">@<?php echo htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                  </div>

                  <div class="card-details-grid">
                    <div class="detail-box">
                      <span class="detail-label">Mobile</span>
                      <span class="detail-val"><?php echo $contact; ?></span>
                    </div>
                    <div class="detail-box">
                      <span class="detail-label">Registration Date (D.O.R)</span>
                      <span class="detail-val"><?php echo htmlspecialchars($row['dor'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="detail-box">
                      <span class="detail-label">Service</span>
                      <span class="detail-val"><?php echo htmlspecialchars($row['services'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="detail-box">
                      <span class="detail-label">Plan</span>
                      <span class="detail-val"><?php echo $plan_months; ?> Months</span>
                    </div>
                  </div>

                  <div class="card-actions">
                    <a href="actions/delete-member.php?id=<?php echo $id; ?>" class="action-btn red-btn" onclick="return confirm('Are you sure you want to remove this member?')">
                      <i class="fas fa-trash-alt"></i>
                      Remove
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

  <!--end-main-container-part-->

  <!--Footer-part-->

  <div class="row-fluid">
    <div id="footer" class="span12"> <?php echo date("Y"); ?> &copy; M*A GYM System Developed By Abdikafi </div>
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
  </script>
</body>

</html>