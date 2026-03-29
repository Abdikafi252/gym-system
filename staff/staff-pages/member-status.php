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
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../../css/bootstrap.min.css" />
  <link rel="stylesheet" href="../../css/bootstrap-responsive.min.css" />
  <link rel="stylesheet" href="../../css/fullcalendar.css" />
  <link rel="stylesheet" href="../../css/matrix-style.css" />
  <link rel="stylesheet" href="../../css/matrix-media.css" />
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


  <!--top-Header-menu-->
  <?php include '../includes/header.php' ?>
  <!--close-top-Header-menu-->
  <!--start-top-serch-->
  <!-- <div id="search">
  <input type="hidden" placeholder="Search here..."/>
  <button type="submit" class="tip-bottom" title="Search"><i class="icon-search icon-white"></i></button>
</div> -->
  <!--close-top-serch-->

  <?php $page = "membersts";
  include '../includes/sidebar.php' ?>

  <div id="content">
    <div id="content-header">
      <div id="breadcrumb"> <a href="index.php" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <a href="member-status.php" class="current">Status</a> </div>
      <h1 class="text-center">Current Member Status <i class="icon icon-eye-open"></i></h1>
    </div>
    <div class="container-fluid">
      <hr>
      <div class="row-fluid">
        <div class="span12">

          <div class='widget-box'>
            <div class='widget-title'> <span class='icon'> <i class='icon-th'></i> </span>
              <h5>Status Grid</h5>
            </div>
            <div class='widget-content nopadding'>

              <?php

              include "dbcon.php";
              // Automatically update status for members whose plan has expired
              mysqli_query($con, "UPDATE members SET status = 'Expired' WHERE expiry_date < CURDATE() AND status = 'Active'");

              $branch_id = $_SESSION['branch_id'];
              $qry = "SELECT * FROM members WHERE branch_id = '$branch_id' ORDER BY dor DESC";
              $cnt = 1;
              $result = mysqli_query($con, $qry);
              ?>

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
                  padding: 24px;
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
                  margin-bottom: 24px;
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
                  gap: 16px;
                  margin-bottom: 24px;
                  padding: 16px;
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

                .detail-val.expiry {
                  color: #10b981;
                }

                .detail-val.expiry.expired {
                  color: #ef4444;
                }
              </style>

              <div class="members-grid">
                <?php
                while ($row = mysqli_fetch_array($result)) {
                  $gender = strtolower($row['gender'] ?? 'male');
                  $avatar_class = ($gender == 'female') ? 'female' : '';
                  $icon_class = ($gender == 'female') ? 'fas fa-female' : 'fas fa-user';
                  $status = $row['status'];
                  $badge_class = '';
                  $status_text = 'Sugaya';

                   if ($status == 'Active') {
                    $badge_class = 'active';
                    $status_text = 'Active';
                  } else {
                    $badge_class = '';
                    $status_text = 'Expired';
                  }

                  // Handle photo path (prefer img/members, then uploads)
                  $photo_path = '';
                  if (!empty($row['photo'])) {
                    $raw_photo = ltrim($row['photo'], '/');
                    if (strpos($raw_photo, 'img/members/') === 0 || strpos($raw_photo, 'uploads/') === 0) {
                      $photo_path = '../../' . $raw_photo;
                    } else {
                      $photo_path = '../../img/members/' . $raw_photo;
                    }
                  }
                ?>
                  <div class="member-card">
                    <div class="card-badge <?php echo $badge_class; ?>">
                      <?php echo $status_text; ?>
                    </div>

                    <div class="card-header-row">
                      <div class="member-avatar <?php echo $avatar_class; ?>" style="overflow:hidden;">
                        <?php if (!empty($photo_path)): ?>
                          <img src="<?php echo htmlspecialchars($photo_path); ?>"
                            alt="Member Photo"
                            style="width:100%;height:100%;object-fit:cover;border-radius:50%;"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';">
                          <i class="<?php echo $icon_class; ?>" style="display:none;"></i>
                        <?php else: ?>
                          <i class="<?php echo $icon_class; ?>"></i>
                        <?php endif; ?>
                      </div>
                      <div class="member-primary-info">
                        <h4 class="member-name"><?php echo htmlspecialchars($row['fullname']); ?></h4>
                        <p class="member-id">Contact: <?php echo htmlspecialchars($row['contact']); ?></p>
                      </div>
                    </div>

                    <div class="card-details-grid">
                      <div class="detail-box" style="grid-column: span 2;">
                        <span class="detail-label">Service</span>
                        <span class="detail-val" style="color: #3b82f6;"><?php echo htmlspecialchars($row['services']); ?></span>
                      </div>
                      <div class="detail-box">
                        <span class="detail-label">Plan</span>
                        <span class="detail-val"><?php echo htmlspecialchars($row['plan']); ?> Month/s</span>
                      </div>
                      <div class="detail-box">
                        <span class="detail-label">Expiry Date</span>
                        <span class="detail-val expiry <?php echo ($status == 'Expired') ? 'expired' : ''; ?>">
                          <?php echo date('d M, Y', strtotime($row['expiry_date'])); ?>
                        </span>
                      </div>
                    </div>
                  </div>
                <?php
                  $cnt++;
                }
                ?>
              </div>
            </div>
          </div>



        </div>
      </div>
    </div>
  </div>

  <!--end-main-container-part-->

  <!--Footer-part-->

  <div class="row-fluid">
    <div id="footer" class="span12"> <?php echo date("Y"); ?> &copy; M*A GYM System Developed By Abdikafi</a> </div>
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
    // This function is called from the pop-up menus to transfer to
    // a different page. Ignore if the value returned is a null string:
    function goPage(newURL) {

      // if url is empty, skip the menu dividers and reset the menu selection to default
      if (newURL != "") {

        // if url is "-", it is this page -- reset the menu:
        if (newURL == "-") {
          resetMenu();
        }
        // else, send page to designated URL            
        else {
          document.location.href = newURL;
        }
      }
    }

    // resets the menu selection upon entry to this page:
    function resetMenu() {
      document.gomenu.selector.selectedIndex = 2;
    }
  </script>
</body>

</html>