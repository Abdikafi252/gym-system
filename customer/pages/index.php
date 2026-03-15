<?php
session_start();
include "dbcon.php";
include "session.php";

if (!isset($_SESSION['user_id'])) {
  header("location:../index.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title>M * A GYM System</title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../css/bootstrap.min.css" />
  <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
  <link rel="stylesheet" href="../css/matrix-style.css" />
  <link rel="stylesheet" href="../css/matrix-media.css" />
  <link rel="stylesheet" href="../../css/system-polish.css" />
  <link href="../font-awesome/css/all.css" rel="stylesheet" />
  <link rel="stylesheet" href="../css/jquery.gritter.css" />
  <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
</head>

<body>

  <!--Header-part-->
  <!-- Logo removed per user request -->
  <!--close-Header-part-->

  <!--top-Header-menu-->
  <?php include '../includes/topheader.php' ?>
  <!--close-top-Header-menu-->

  <!--sidebar-menu-->
  <?php $page = "dashboard";
  include '../includes/sidebar.php' ?>
  <!--sidebar-menu-->

  <?php
  $uid = $_SESSION['user_id'];
  $qry_stats = "SELECT * FROM members WHERE user_id='$uid'";
  $res_stats = mysqli_query($con, $qry_stats);
  $row_stats = mysqli_fetch_array($res_stats);

  $total_bill = $row_stats['amount'];
  $paid_amount = $row_stats['paid_amount'];
  $discount = $row_stats['discount_amount'];
  $balance = $total_bill - $paid_amount;

  $attendance_qry = mysqli_query($con, "SELECT COUNT(*) as total FROM attendance WHERE user_id='$uid'");
  $attendance_data = mysqli_fetch_array($attendance_qry);
  $total_attendance = $attendance_data['total'];

  // Monthly Attendance (Current Month)
  $cur_month = date('Y-m');
  $monthly_att_qry = mysqli_query($con, "SELECT COUNT(*) as monthly FROM attendance WHERE user_id='$uid' AND curr_date LIKE '$cur_month-%'");
  $monthly_att_data = mysqli_fetch_array($monthly_att_qry);
  $monthly_attendance = $monthly_att_data['monthly'];

  // Plan Type Logic
  $plan_months = $row_stats['plan'];
  $plan_type = "Standard";
  if ($plan_months == 1) $plan_type = "Monthly Plan";
  else if ($plan_months == 3) $plan_type = "Quarterly Plan";
  else if ($plan_months == 6) $plan_type = "Half-Year Plan";
  else if ($plan_months == 12) $plan_type = "VIP Yearly";

  // Days Left Logic
  $expiry_date = new DateTime($row_stats['expiry_date']);
  $today = new DateTime();
  $days_left = $today->diff($expiry_date)->format("%r%a"); // %r gets the sign
  $days_left_text = ($days_left > 0) ? $days_left : 0;

  // Workout Streak Logic (Current Month)
  $current_month = date('Y-m');
  $streak_qry = mysqli_query($con, "SELECT curr_date FROM attendance WHERE user_id='$uid' AND curr_date LIKE '$current_month-%' ORDER BY curr_date DESC");
  $streak_days = 0;
  $last_date = date('Y-m-d');
  while ($streak_row = mysqli_fetch_array($streak_qry)) {
    if ($streak_row['curr_date'] == $last_date || $streak_row['curr_date'] == date('Y-m-d', strtotime($last_date . ' - 1 days'))) {
      $streak_days++;
      $last_date = $streak_row['curr_date'];
    } else {
      break;
    }
  }

  // Profile Image
  $u_photo = !empty($row_stats['photo']) ? "../img/members/" . $row_stats['photo'] : "../img/demo/av1.jpg";
  ?>

  <!--main-container-part-->
  <div id="content">
    <!--breadcrumbs removed per latest UI request-->

    <!--Action boxes-->
    <div class="container-fluid">
      <div class="row-fluid" style="margin-top: 50px;">
        <div class="span12">
          <style>
            /* Ultra Modern Customer Dashboard CSS */
            .dashboard-wrapper {
              font-family: 'Open Sans', sans-serif;
              color: #1e293b;
            }

            .welcome-hero {
              background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
              border-radius: 16px;
              padding: 22px 30px;
              color: white;
              display: flex;
              align-items: center;
              justify-content: space-between;
              gap: 18px;
              box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12);
              margin-bottom: 20px;
              position: relative;
              overflow: hidden;
            }

            .welcome-hero::after {
              content: '';
              position: absolute;
              top: 0;
              right: 0;
              width: 50%;
              height: 100%;
              background: radial-gradient(circle at top right, rgba(56, 189, 248, 0.2), transparent 70%);
              pointer-events: none;
            }

            .hero-content h1 {
              font-weight: 800;
              font-size: 24px;
              margin: 0 0 6px 0;
              letter-spacing: -0.5px;
            }

            .hero-content p {
              color: #cbd5e1;
              font-size: 16px;
              margin: 0;
              max-width: 500px;
              line-height: 1.5;
            }

            .hero-date {
              font-size: 13px;
              color: #94a3b8;
              margin-top: 4px;
            }


            .hero-profile {
              display: flex;
              align-items: center;
              gap: 20px;
              z-index: 1;
            }

            .profile-img-wrap {
              width: 80px;
              height: 80px;
              border-radius: 50%;
              border: 4px solid rgba(255, 255, 255, 0.2);
              overflow: hidden;
              background: #fff;
              box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            }

            .profile-img-wrap img {
              width: 100%;
              height: 100%;
              object-fit: cover;
            }

            .profile-info h3 {
              margin: 0 0 5px 0;
              font-size: 20px;
              font-weight: 700;
            }

            .status-badge {
              display: inline-block;
              padding: 6px 14px;
              border-radius: 20px;
              font-size: 12px;
              font-weight: 700;
              text-transform: uppercase;
              letter-spacing: 1px;
            }

            .status-active {
              background: rgba(22, 163, 74, 0.2);
              color: #4ade80;
              border: 1px solid rgba(74, 222, 128, 0.3);
            }

            .status-expired {
              background: rgba(220, 38, 38, 0.2);
              color: #f87171;
              border: 1px solid rgba(248, 113, 113, 0.3);
            }

            .modern-grid {
              display: grid;
              grid-template-columns: repeat(4, 1fr);
              gap: 20px;
              margin-bottom: 30px;
            }

            .m-card {
              background: #fff;
              border-radius: 16px;
              padding: 24px;
              box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
              border: 1px solid rgba(226, 232, 240, 0.8);
              transition: all 0.3s ease;
              text-decoration: none !important;
              display: flex;
              flex-direction: column;
              justify-content: center;
              position: relative;
              overflow: hidden;
            }

            .m-card:hover {
              transform: translateY(-5px);
              box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
              border-color: #cbd5e1;
            }

            a.m-card * {
              text-decoration: none !important;
            }

            .m-card-icon {
              width: 54px;
              height: 54px;
              border-radius: 14px;
              display: flex;
              align-items: center;
              justify-content: center;
              font-size: 26px;
              margin-bottom: 16px;
              flex-shrink: 0;
            }

            .icon-blue {
              background: #eff6ff;
              color: #3b82f6;
            }

            .icon-purple {
              background: #faf5ff;
              color: #a855f7;
            }

            .icon-green {
              background: #f0fdf4;
              color: #22c55e;
            }

            .icon-orange {
              background: #fff7ed;
              color: #f97316;
            }

            .icon-pink {
              background: #fdf2f8;
              color: #ec4899;
            }

            .icon-yellow {
              background: #fefce8;
              color: #eab308;
            }

            .m-card-title {
              font-size: 14px;
              color: #64748b;
              font-weight: 600;
              margin: 0 0 8px 0 !important;
              text-transform: uppercase;
              letter-spacing: 0.5px;
            }

            .m-card-value {
              font-size: 28px;
              font-weight: 800;
              color: #0f172a;
              margin: 0 !important;
              line-height: 1.1;
            }

            .m-card-sub {
              font-size: 13px;
              color: #94a3b8;
              margin-top: 8px;
              font-weight: 500;
            }

            /* Special Large Card */
            .m-card.large {
              grid-column: span 2;
              background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
              color: white;
              border: none;
            }

            .m-card.large .m-card-title {
              color: #bfdbfe;
            }

            .m-card.large .m-card-value {
              color: white;
            }

            .m-card.large .m-card-sub {
              color: #93c5fd;
            }

            .m-card.large .m-card-icon {
              background: rgba(255, 255, 255, 0.2);
              color: white;
            }

            @media (max-width: 1200px) {
              .modern-grid {
                grid-template-columns: repeat(2, 1fr);
              }

              .m-card.large {
                grid-column: span 2;
              }
            }

            @media (max-width: 768px) {
              .welcome-hero {
                text-align: center;
                justify-content: center;
                align-items: center;
                padding: 24px 18px;
              }

              .modern-grid {
                grid-template-columns: 1fr;
              }

              .m-card.large {
                grid-column: span 1;
              }
            }
          </style>

          <div class="dashboard-wrapper">

            <!-- Welcome Hero -->
            <div class="welcome-hero">
              <div class="hero-content">
                <h1>👋 Kusoo Dhawow, <?php echo strtok($row_stats['fullname'], " "); ?>!</h1>
                <div class="hero-date"><?php echo date('l, d M Y'); ?></div>
              </div>
            </div>

            <!-- Stats Grid -->
            <div class="modern-grid">
              <!-- Card 1: Membership Info (Large) -->
              <a href="my-plan.php" class="m-card large" style="position:relative; overflow:hidden;">
                <div style="position:absolute;top:-15px;right:-15px;font-size:110px;opacity:0.08;line-height:1;pointer-events:none;">🏅</div>
                <div class="m-card-icon"><i class="fas fa-medal"></i></div>
                <h4 class="m-card-title">👑 Qorshaha Xubinnimada (Plan)</h4>
                <h3 class="m-card-value"><?php echo $row_stats['services']; ?> — <?php echo strtoupper($plan_type); ?></h3>
                <div class="m-card-sub">
                  ⏰ Waqtiga dhicitaanka: <strong style="color:white;"><?php echo date('d M Y', strtotime($row_stats['expiry_date'])); ?></strong>
                  (<?php echo $days_left_text; ?> maalmood baa harsan)
                </div>
              </a>

              <!-- Card 2: Streak -->
              <a href="my-report.php" class="m-card" style="position:relative; overflow:hidden;">
                <div style="position:absolute;top:-15px;right:-15px;font-size:100px;opacity:0.07;line-height:1;pointer-events:none;">🔥</div>
                <div class="m-card-icon icon-orange"><i class="fas fa-fire"></i></div>
                <h4 class="m-card-title">🔥 Imaanshaha Bishaan</h4>
                <h3 class="m-card-value"><?php echo $monthly_attendance; ?> <span style="font-size:16px; color:#94a3b8;">Maalin</span></h3>
                <div class="m-card-sub">Horumarka bisha hadda socota</div>
              </a>

              <!-- Card 3: Total Attendance -->
              <a href="my-report.php" class="m-card" style="position:relative; overflow:hidden;">
                <div style="position:absolute;top:-15px;right:-15px;font-size:100px;opacity:0.07;line-height:1;pointer-events:none;">📅</div>
                <div class="m-card-icon icon-purple"><i class="fas fa-calendar-check"></i></div>
                <h4 class="m-card-title">📅 Wadarta Imaanshaha</h4>
                <h3 class="m-card-value"><?php echo $total_attendance; ?> <span style="font-size:16px; color:#94a3b8;">Jeer</span></h3>
                <div class="m-card-sub">Imaanshaha guud abid</div>
              </a>

              <!-- Card 4: Payments Link -->
              <a href="my-payments.php" class="m-card" style="position:relative; overflow:hidden;">
                <div style="position:absolute;top:-15px;right:-15px;font-size:100px;opacity:0.07;line-height:1;pointer-events:none;">🧾</div>
                <div class="m-card-icon icon-green"><i class="fas fa-file-invoice-dollar"></i></div>
                <h4 class="m-card-title">🧾 Rasiidka & Lacagta</h4>
                <h3 class="m-card-value" style="color: #22c55e;">Eeg Taariikhda</h3>
                <div class="m-card-sub">💳 Lacag bixinta ($<?php echo $row_stats['paid_amount']; ?> Total) & Rasiidka</div>
              </a>

              <!-- Card 5: Finance Details -->
              <a href="my-payments.php" class="m-card" style="position:relative; overflow:hidden;">
                <div style="position:absolute;top:-15px;right:-15px;font-size:100px;opacity:0.07;line-height:1;pointer-events:none;"><?php echo $balance > 0 ? '⚠️' : '💰'; ?></div>
                <div class="m-card-icon <?php echo $balance > 0 ? 'icon-pink' : 'icon-blue'; ?>"><i class="fas fa-wallet"></i></div>
                <h4 class="m-card-title"><?php echo $balance > 0 ? '⚠️' : '✅'; ?> Baaqiga / Haraaga</h4>
                <h3 class="m-card-value" style="color: <?php echo $balance > 0 ? '#e11d48' : '#0f172a'; ?>;">
                  <?php
                  if ($balance < 0) {
                    echo '<span style="font-size:16px; color:#10b981;">(Credit)</span> $' . number_format(abs($balance), 2);
                  } else {
                    echo '$' . number_format($balance, 2);
                  }
                  ?>
                </h3>
                <div class="m-card-sub">Lacagta kugu dhiman</div>
              </a>

              <!-- Card 6: Report Link -->
              <a href="my-report.php" class="m-card" style="position:relative; overflow:hidden;">
                <div style="position:absolute;top:-15px;right:-15px;font-size:100px;opacity:0.07;line-height:1;pointer-events:none;">📊</div>
                <div class="m-card-icon icon-yellow"><i class="fas fa-chart-line"></i></div>
                <h4 class="m-card-title">📊 Warbixintaada</h4>
                <h3 class="m-card-value" style="color: #eab308;">My Report</h3>
                <div class="m-card-sub">Faahfaahinta horumarkaaga</div>
              </a>

            </div> <!-- End modern-grid -->
          </div> <!-- End dashboard-wrapper -->
        </div> <!-- End span12 -->
      </div> <!-- End row-fluid -->

      <div class="row-fluid">

        <div class="span6">

          <div class="widget-box todo-card">
            <div class="widget-title todo-header"> <span class="icon"><i class="icon-list" style="color: #28b779;"></i></span>
              <h5>Liiska Shaqooyinka (To-Do List)</h5>
            </div>
            <div class="widget-content nopadding">

              <?php
              $qry = "SELECT * FROM todo WHERE user_id='" . $_SESSION['user_id'] . "'";
              $result = mysqli_query($con, $qry);

              echo "<table class='table table-striped table-bordered todo-table'>
              <thead>
                <tr>
                  <th>FAAHFAAHINTA</th>
                  <th>XALADA</th>
                  <th style='width: 80px;'>OPTS</th>
                </tr>
              </thead>";
              while ($row = mysqli_fetch_array($result)) {
                $status_color = ($row['task_status'] == 'In Progress') ? '#0284c7' : '#16a34a';
                echo "<tbody>
                <tr>
                  <td class='taskDesc' style='font-weight: 500; color: #4a5568;'><a href='to-do.php' style='margin-right: 10px; color: #cbd5e0;'><i class='fas fa-thumbtack'></i></a>" . $row['task_desc'] . "</td>
                  <td class='taskStatus'><span style='display:inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; background: rgba(2, 132, 199, 0.1); color: $status_color;'>" . $row['task_status'] . "</span></td>
                  <td class='taskOptions' style='text-align: center;'>
                    <a href='update-todo.php?id=" . $row['id'] . "' class='tip-top' data-original-title='Update' style='color: #4a5568; margin-right:8px;'><i class='icon-edit'></i></a> 
                    <a href='actions/remove-todo.php?id=" . $row['id'] . "' class='tip-top' data-original-title='Done' style='color: #16a34a;'><i class='icon-ok'></i></a>
                  </td>
                </tr>
              </tbody>";
              }
              ?>

              </table>
            </div>
          </div>



        </div> <!-- End of ToDo List Bar -->

        <div class="span6">
          <div class="widget-box todo-card">
            <div class="widget-title todo-header" data-toggle="collapse" href="#collapseG2">
              <span class="icon"><i class="icon-bullhorn" style="color: #e67e22;"></i></span>
              <h5>Ogeysiisyada Gym-ka (Announcements)</h5>
            </div>
            <div class="widget-content nopadding collapse in" id="collapseG2" style="max-height: 400px; overflow-y: auto;">
              <ul class="recent-posts">

                <?php
                $qry = "select * from announcements ORDER BY date DESC";
                $result = mysqli_query($con, $qry);

                if (mysqli_num_rows($result) > 0) {
                  while ($row = mysqli_fetch_array($result)) {
                    echo "<li style='border-bottom: 1px solid #f1f5f9; padding: 20px;'>";
                    echo "<div class='user-thumb' style='border-radius: 10px; overflow: hidden; border: 2px solid #edf2f7;'> <img width='70' height='40' alt='User' src='../img/demo/av1.jpg'> </div>";
                    echo "<div class='article-post'>";
                    echo "<span class='user-info' style='color: #64748b; font-size: 12px; font-weight: 600;'><i class='fas fa-user-shield'></i> Admin | <i class='fas fa-calendar-alt'></i> " . date('M j, Y', strtotime($row['date'])) . " </span>";
                    echo "<p style='margin-top: 8px;'><a href='announcement.php' style='color: #2d3748; font-weight: 600; font-size: 15px; line-height: 1.5; text-decoration: none;'>" . $row['message'] . "</a> </p>";
                    echo "</div>";
                    echo "</li>";
                  }
                } else {
                  echo "<li style='padding: 20px; text-align: center; color: #94a3b8;'>Ma jiraan ogeysiisyo hadda.</li>";
                }
                ?>

              </ul>
              <div style="padding: 15px; text-align: right; background: #f8fafc; border-top: 1px solid #edf2f7;">
                <a href="announcement.php" class="btn btn-warning btn-mini" style="border-radius: 4px; font-weight: 700; text-transform: uppercase; padding: 5px 15px;">View All Announcements</a>
              </div>
            </div>
          </div>
        </div> <!-- end of announcement -->

      </div><!-- End of row-fluid -->
    </div><!-- End of container-fluid -->
  </div><!-- End of content-ID -->

  <!--end-main-container-part-->

  <!--Footer-part-->

  <div class="row-fluid">
    <div id="footer" class="span12"> <?php echo date("Y"); ?> &copy; M * A GYM System Developed By Abdikafi</a> </div>
  </div>



  <style>
    #footer {
      color: white;
    }

    .card {
      box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
      max-width: 460px;
      margin: auto;
      text-align: center;
      font-family: arial;
    }

    .title {
      color: grey;
      font-size: 18px;
    }
  </style>



  <!--end-Footer-part-->

  <script src="../js/excanvas.min.js"></script>
  <script src="../js/jquery.min.js"></script>
  <script src="../js/jquery.ui.custom.js"></script>
  <script src="../js/bootstrap.min.js"></script>
  <script src="../js/jquery.flot.min.js"></script>
  <script src="../js/jquery.flot.resize.min.js"></script>
  <script src="../js/jquery.peity.min.js"></script>
  <script src="../js/fullcalendar.min.js"></script>
  <script src="../js/matrix.js"></script>
  <script src="../js/matrix.dashboard.js"></script>
  <script src="../js/jquery.gritter.min.js"></script>
  <script src="../js/matrix.interface.js"></script>
  <script src="../js/matrix.chat.js"></script>
  <script src="../js/jquery.validate.js"></script>
  <script src="../js/matrix.form_validation.js"></script>
  <script src="../js/jquery.wizard.js"></script>
  <script src="../js/jquery.uniform.js"></script>
  <script src="../js/select2.min.js"></script>
  <script src="../js/matrix.popover.js"></script>
  <script src="../js/jquery.dataTables.min.js"></script>
  <script src="../js/matrix.tables.js"></script>

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