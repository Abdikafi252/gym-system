<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//the isset function to check username is already loged in and stored on the session
if (!isset($_SESSION['user_id']) || !isset($_SESSION['branch_id']) || !isset($_SESSION['designation'])) {
  header('location:../index.php');
  exit();
}
include "dbcon.php";
$branch_id = $_SESSION['branch_id'];
$qry = "SELECT services, count(*) as number FROM members WHERE branch_id = '$branch_id' GROUP BY services";
$result = mysqli_query($con, $qry);
$result3 = mysqli_query($con, "SELECT gender, count(*) as enumber FROM members WHERE branch_id = '$branch_id' GROUP BY gender");
$qry = "SELECT designation, count(*) as snumber FROM staffs WHERE branch_id = '$branch_id' GROUP BY designation";
$result5 = mysqli_query($con, $qry);

$ann_count_row = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS c FROM announcements WHERE branch_id='$branch_id'"));
$todo_count_row = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS c FROM todo t LEFT JOIN members m ON m.user_id=t.user_id WHERE m.branch_id='$branch_id' AND t.task_status='Pending'"));
$ann_total = (int)($ann_count_row['c'] ?? 0);
$todo_pending_total = (int)($todo_count_row['c'] ?? 0);

// Run Auto-SMS Check
include '../../api/auto_sms_expiry.php';
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
  <link href="../../font-awesome/css/all.css" rel="stylesheet" />
  <link href="../../font-awesome/css/fontawesome.css" rel="stylesheet" />
  <link rel="stylesheet" href="../../css/jquery.gritter.css" />
  <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>

  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">
    google.charts.load('current', {
      'packages': ['corechart']
    });
    google.charts.setOnLoadCallback(drawServicesPie);

    function drawServicesPie() {
      var data = google.visualization.arrayToDataTable([
        ['Services', 'Number'],
        <?php
        while ($row = mysqli_fetch_array($result)) {
          echo "['" . $row["services"] . "', " . $row["number"] . "],";
        }
        ?>
      ]);
      var options = {
        pieHole: 0.4,
      };
      var chart = new google.visualization.PieChart(document.getElementById('piechart'));
      chart.draw(data, options);
    }
  </script>

  <script type="text/javascript">
    google.charts.load('current', {
      'packages': ['bar']
    });
    google.charts.setOnLoadCallback(drawFinanceBar);

    function drawFinanceBar() {
      var data = new google.visualization.arrayToDataTable([
        ['Terms', 'Total Amount', ],
        <?php
        $branch_id = $_SESSION['branch_id'];
        $query1 = "SELECT SUM(paid_amount) as numberone FROM members WHERE branch_id = '$branch_id'";
        $rezz = mysqli_query($con, $query1);
        $data = mysqli_fetch_array($rezz);
        $income = $data['numberone'] ? $data['numberone'] : 0;
        ?>['Income', <?php echo $income; ?>],
        <?php
        $branch_id = $_SESSION['branch_id'];
        $query10 = "SELECT SUM(amount) as numbert FROM equipment WHERE branch_id = '$branch_id'";
        $res1000 = mysqli_query($con, $query10);
        $data = mysqli_fetch_array($res1000);
        $equip_exp = $data['numbert'];

        $query11 = "SELECT SUM(amount) as exp FROM expenses WHERE branch_id = '$branch_id'";
        $res11 = mysqli_query($con, $query11);
        $data11 = mysqli_fetch_array($res11);
        $general_exp = $data11['exp'] ?? 0;

        $total_expenses = $equip_exp + $general_exp;
        ?>['Expenses', <?php echo $total_expenses; ?>],
      ]);

      var options = {
        width: "1050",
        legend: {
          position: 'none'
        },
        bars: 'horizontal',
        axes: {
          x: {
            0: {
              side: 'top',
              label: 'Total'
            }
          }
        },
        bar: {
          groupWidth: "100%"
        }
      };

      var chart = new google.charts.Bar(document.getElementById('top_y_div'));
      chart.draw(data, options);
    };
  </script>

  <script type="text/javascript">
    google.charts.load("current", {
      packages: ["corechart"]
    });
    google.charts.setOnLoadCallback(drawGenderChart);

    function drawGenderChart() {
      var data = google.visualization.arrayToDataTable([
        ['Gender', 'Number'],
        <?php
        while ($row = mysqli_fetch_array($result3)) {
          echo "['" . $row["gender"] . "', " . $row["enumber"] . "],";
        }
        ?>
      ]);
      var options = {
        pieHole: 0.4,
      };
      var chart = new google.visualization.PieChart(document.getElementById('donutchart'));
      chart.draw(data, options);
    }
  </script>

  <script>
    google.charts.load("current", {
      packages: ["corechart"]
    });
    google.charts.setOnLoadCallback(drawDesignationChart);

    function drawDesignationChart() {
      var data = google.visualization.arrayToDataTable([
        ['Designation', 'Number'],
        <?php
        while ($row = mysqli_fetch_array($result5)) {
          echo "['" . $row["designation"] . "', " . $row["snumber"] . "],";
        }
        ?>
      ]);
      var options = {
        pieHole: 0.4,
      };
      var chart = new google.visualization.PieChart(document.getElementById('donutchart2022'));
      chart.draw(data, options);
    }
  </script>

  <script type="text/javascript">
    google.charts.load('current', {
      'packages': ['bar']
    });
    google.charts.setOnLoadCallback(drawGrowthChart);

    function drawGrowthChart() {
      var data = new google.visualization.arrayToDataTable([
        ['Month', 'New Members'],
        <?php
        $branch_id = $_SESSION['branch_id'];
        $query_growth = "SELECT DATE_FORMAT(dor, '%M') as month_name, COUNT(*) as count 
                         FROM members 
                         WHERE branch_id = '$branch_id'
                         GROUP BY MONTH(dor) 
                         ORDER BY dor ASC 
                         LIMIT 6";
        $res_growth = mysqli_query($con, $query_growth);
        while ($data_growth = mysqli_fetch_array($res_growth)) {
          echo "['" . $data_growth['month_name'] . "', " . $data_growth['count'] . "],";
        }
        ?>
      ]);

      var options = {
        width: 710,
        legend: {
          position: 'none'
        },
        bars: 'vertical',
        colors: ['#28b779'],
        axes: {
          x: {
            0: {
              side: 'top',
              label: 'Monthly Growth'
            }
          }
        },
        bar: {
          groupWidth: "70%"
        }
      };

      var chart = new google.charts.Bar(document.getElementById('growth_chart'));
      chart.draw(data, options);
    };
  </script>
  <script type="text/javascript">
    google.charts.load('current', {
      'packages': ['bar']
    });
    google.charts.setOnLoadCallback(drawStuffX);

    function drawStuffX() {
      var data = new google.visualization.arrayToDataTable([
        ['Services', 'Total Numbers'],
        <?php
        $branch_id = $_SESSION['branch_id'];
        $query = "SELECT services, count(*) as number FROM members WHERE branch_id = '$branch_id' GROUP BY services";
        $res = mysqli_query($con, $query);
        while ($data = mysqli_fetch_array($res)) {
          $services = $data['services'];
          $number = $data['number'];
        ?>['<?php echo $services; ?>', <?php echo $number; ?>],
        <?php
        }
        ?>
      ]);

      var options = {
        width: 710,
        legend: {
          position: 'none'
        },
        bars: 'vertical',
        axes: {
          x: {
            0: {
              side: 'top',
              label: 'Total'
            }
          }
        },
        bar: {
          groupWidth: "100%"
        }
      };

      var chart = new google.charts.Bar(document.getElementById('top_x_div'));
      chart.draw(data, options);
    };
    google.charts.setOnLoadCallback(drawStuffX);
  </script>
  <style>
    .dash-pill {
      border-radius: 999px;
      padding: 5px 11px;
      font-size: 12px;
      font-weight: 700;
      border: 1px solid #e5e7eb;
      background: #f8fafc;
      color: #1f2937;
      display: inline-block;
      margin-bottom: 8px;
    }

    .dash-card {
      background: #fff;
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      padding: 10px;
      margin-bottom: 8px;
    }

    .empty-mini {
      padding: 12px;
      text-align: center;
      color: #6b7280;
      font-size: 13px;
    }
  </style>
</head>

<body>

  <!--Header-part-->
  <?php include '../includes/header-content.php'; ?>
  <!--close-Header-part-->


  <!--top-Header-menu-->
  <?php $page = "dashboard";
  include '../includes/header.php' ?>
  <!--close-top-Header-menu-->


  <!--sidebar-menu-->
  <?php $page = "dashboard";
  include '../includes/sidebar.php' ?>
  <!--sidebar-menu-->

  <!--main-container-part-->
  <div id="content">
    <!--breadcrumbs-->
    <div id="content-header">
      <div id="breadcrumb"> <a href="index.php" title="Go to Home Page" class="tip-bottom"><i class="fas fa-home"></i> Home</a></div>
    </div>
    <!--End-breadcrumbs-->

    <!--Action boxes-->
    <div class="container-fluid">

      <!-- Extended Dashboard Stats Cards -->
      <?php include 'dashboard-extended-stats.php'; ?>

      <!-- <div class="quick-actions_homepage">
      <ul class="quick-actions">
        <li class="bg_lb span"> <a href="index.php"> <i class="icon-dashboard"></i> System Dashboard </a> </li>

        <li class="bg_ls span2"> <a href="announcement.php"> <i class="icon-bullhorn"></i>Announcements </a> </li> -->


      <!-- <li class="bg_ls span2"> <a href="buttons.html"> <i class="icon-tint"></i> Buttons</a> </li>
        <li class="bg_ly span3"> <a href="form-common.html"> <i class="icon-th-list"></i> Forms</a> </li>
        <li class="bg_lb span2"> <a href="interface.html"> <i class="icon-pencil"></i>Elements</a> </li> -->
      <!-- <li class="bg_lg"> <a href="calendar.html"> <i class="icon-calendar"></i> Calendar</a> </li>
        <li class="bg_lr"> <a href="error404.html"> <i class="icon-info-sign"></i> Error</a> </li> -->

      <!-- </ul>
    </div> -->
      <!--End-Action boxes-->

      <!--Chart-box-->
      <?php if (in_array($_SESSION['designation'], ['Manager', 'Cashier'])): ?>
        <div class="row-fluid">
          <div class="widget-box">
            <div class="widget-title bg_lg"><span class="icon"><i class="fas fa-file"></i></span>
              <h5>Income and Expenses Report</h5>
            </div>
            <div class="widget-content">
              <div class="row-fluid">
                <div class="span12">
                  <div id="top_y_div" style="width: 710px; height: 180px;"></div>
                </div>
              </div>
              <hr>
              <div class="row-fluid">
                <div class="span12">
                  <div id="growth_chart" style="width: 710px; height: 250px;"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row-fluid">
          <div class="span6">
            <div class="widget-box">
              <div class="widget-title bg_ly" data-toggle="collapse" href="#collapseGender"><span class="icon"><i class="fas fa-chevron-down"></i></span>
                <h5>Registered Members by Gender</h5>
              </div>
              <div class="widget-content nopadding collapse in" id="collapseGender">
                <ul class="recent-posts">
                  <div id="donutchart" style="width: 600px; height: 300px;"></div>
                </ul>
              </div>
            </div>
          </div>

          <div class="span6">
            <div class="widget-box">
              <div class="widget-title bg_ly" data-toggle="collapse" href="#collapseStaff"><span class="icon"><i class="fas fa-chevron-down"></i></span>
                <h5>Staff by Designation</h5>
              </div>
              <div class="widget-content nopadding collapse in" id="collapseStaff">
                <ul class="recent-posts">
                  <div id="donutchart2022" style="width: 600px; height: 300px;"></div>
                </ul>
              </div>
            </div>
          </div>
        </div>
      <?php else: ?>
        <div class="row-fluid">
          <div class="span12">
            <div class="widget-box">
              <div class="widget-title bg_lg"><span class="icon"><i class="fas fa-info-circle"></i></span>
                <h5>Dashboard Overview</h5>
              </div>
              <div class="widget-content" style="text-align: center; padding: 40px;">
                <h3>Welcome to the Dashboard!</h3>
                <p>Please use the left menu to start your work.</p>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!--End-Chart-box-->
      <hr />
      <div class="row-fluid">
        <div class="span6">
          <div class="widget-box">
            <div class="widget-title bg_ly" data-toggle="collapse" href="#collapseG2"><span class="icon"><i class="icon-chevron-down"></i></span>
              <h5>Gym Announcements</h5>
            </div>
            <div class="widget-content nopadding collapse in" id="collapseG2">
              <ul class="recent-posts">
                <li>
                  <?php
                  $branch_id = $_SESSION['branch_id'];
                  $qry = "SELECT * FROM announcements WHERE branch_id='$branch_id' ORDER BY id DESC LIMIT 5";
                  $result = mysqli_query($con, $qry);
                  if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_array($result)) {
                      echo "<div class='dash-card'>";
                      echo "<span class='user-info'>By: Admin / Date: " . htmlspecialchars($row['date']) . "</span>";
                      echo "<p style='margin:6px 0 0;'><a href='#'>" . htmlspecialchars($row['message']) . "</a></p>";
                      echo "</div>";
                    }
                  } else {
                    echo "<div class='empty-mini'>No announcements found.</div>";
                  }
                  ?>
                  <span class="dash-pill">Total Announcements: <?php echo $ann_total; ?></span><br>
                  <a href="announcement.php"><button class="btn btn-warning btn-mini">View All</button></a>
                </li>
              </ul>
            </div>
          </div>


        </div>
        <div class="row-fluid">
          <div class="span6">
            <div class="widget-box">
              <div class="widget-title"> <span class="icon"><i class="fas fa-history"></i></span>
                <h5>Recent Activities</h5>
              </div>
              <div class="widget-content nopadding">
                <ul class="recent-posts">
                  <?php
                  // Recent Registrations and Payments combined
                  $branch_id = $_SESSION['branch_id'];
                  $query_activity = "
                  (SELECT fullname as member_name, dor as activity_date, 'New Member' as activity_type FROM members WHERE branch_id = '$branch_id')
                  UNION ALL
                  (SELECT fullname as member_name, paid_date as activity_date, 'Payment' as activity_type FROM payment_history WHERE branch_id = '$branch_id' AND paid_date IS NOT NULL)
                  UNION ALL
                  (SELECT m.fullname as member_name, a.curr_date as activity_date, 'Attendance' as activity_type FROM attendance a JOIN members m ON m.user_id=a.user_id WHERE m.branch_id='$branch_id')
                  ORDER BY activity_date DESC LIMIT 6";
                  $res_activity = mysqli_query($con, $query_activity);
                  if ($res_activity && mysqli_num_rows($res_activity) > 0) {
                  while ($activity = mysqli_fetch_array($res_activity)) { ?>
                    <li>
                      <div class="user-thumb"> <i class="fas <?php echo ($activity['activity_type'] == 'New Member') ? 'fa-user-plus' : (($activity['activity_type'] == 'Attendance') ? 'fa-calendar-check' : 'fa-money-bill-wave'); ?>" style="font-size: 1.5em; color: <?php echo ($activity['activity_type'] == 'New Member') ? '#28b779' : (($activity['activity_type'] == 'Attendance') ? '#7c3aed' : '#0284c7'); ?>; margin-top: 10px;"></i> </div>
                      <div class="article-post">
                        <span class="user-info"> <?php echo $activity['activity_type']; ?> / Date: <?php echo $activity['activity_date']; ?> </span>
                        <p><strong><?php echo $activity['member_name']; ?></strong> has performed <?php echo strtolower($activity['activity_type']); ?>. </p>
                      </div>
                    </li>
                  <?php }
                  } else {
                    echo "<li><div class='empty-mini'>No recent activities found.</div></li>";
                  }
                  ?>
                </ul>
              </div>
            </div>
          </div>

          <div class="span6">
            <div class="widget-box">
              <div class="widget-title"> <span class="icon"><i class="fas fa-tasks"></i></span>
                <h5>Customer To-Do List</h5>
              </div>
              <div class="widget-content">
                <div class="todo">
                  <span class="dash-pill">Pending Tasks: <?php echo $todo_pending_total; ?></span>
                  <ul>
                    <?php
                    $qry = "SELECT t.task_desc, t.task_status, m.fullname FROM todo t LEFT JOIN members m ON m.user_id=t.user_id WHERE m.branch_id='$branch_id' ORDER BY (t.task_status='Pending') DESC, t.id DESC LIMIT 8";
                    $result = mysqli_query($con, $qry);
                    if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_array($result)) { ?>
                      <li class='clearfix'>
                        <div class='txt'> <?php echo htmlspecialchars($row["task_desc"]) ?> <?php if ($row["task_status"] == "Pending") {
                                                                            echo '<span class="by label label-info">Pending</span>';
                                                                          } else {
                                                                            echo '<span class="by label label-success">In Progress</span>';
                                                                          } ?>
                          <?php if (!empty($row['fullname'])) { ?><small style="display:block;color:#6b7280;">Member: <?php echo htmlspecialchars($row['fullname']); ?></small><?php } ?>
                        </div>
                      </li>
                    <?php }
                    } else {
                      echo "<li><div class='empty-mini'>No tasks found.</div></li>";
                    }
                    ?>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div> <!-- End of Row for Activity and ToDo -->
      </div><!-- End of Announcement Bar -->
    </div><!-- End of container-fluid -->
  </div><!-- End of content-ID -->

  <!--end-main-container-part-->

  <!--Footer-part-->

  <div class="row-fluid">
    <div id="footer" class="span12"> <?php echo date("Y"); ?> &copy; M*A GYM System Developed By Abdikafi </div>
  </div>

  <style>
    #footer {
      color: white;
    }

    #piechart {
      width: 800px;
      height: 280px;
      margin-left: auto;
      margin-right: auto;
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
  <!-- <script src="../../js/matrix.interface.js"></script>  -->
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