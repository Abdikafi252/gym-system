<?php
session_start();
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
//the isset function to check username is already loged in and stored on the session
if (!isset($_SESSION['user_id'])) {
  header('location:../index.php');
}
include "dbcon.php";
$qry = "SELECT services, count(*) as number FROM members GROUP BY services";
$result = mysqli_query($con, $qry);
$result3 = mysqli_query($con, "SELECT gender, count(*) as enumber FROM members GROUP BY gender");
$qry = "SELECT designation, count(*) as snumber FROM staffs GROUP BY designation";
$result5 = mysqli_query($con, $qry);

$income_row = mysqli_fetch_assoc(mysqli_query($con, "SELECT COALESCE(SUM(paid_amount),0) AS income_total FROM payment_history"));
$equip_row = mysqli_fetch_assoc(mysqli_query($con, "SELECT COALESCE(SUM(amount),0) AS equip_total FROM equipment"));
$exp_row = mysqli_fetch_assoc(mysqli_query($con, "SELECT COALESCE(SUM(amount),0) AS exp_total FROM expenses"));
$income_total = (float)($income_row['income_total'] ?? 0);
$expense_total = (float)($equip_row['equip_total'] ?? 0) + (float)($exp_row['exp_total'] ?? 0);
$net_total = $income_total - $expense_total;

$ann_count_row = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS c FROM announcements"));
$todo_count_row = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS c FROM todo WHERE task_status='Pending'"));
$ann_total = (int)($ann_count_row['c'] ?? 0);
$todo_pending_total = (int)($todo_count_row['c'] ?? 0);

// Run Auto-SMS Check
include '../api/auto_sms_expiry.php';
?>
<!-- Visit codeastro.com for more projects -->
<!DOCTYPE html>

<html lang="en">

<head>
  <title>M * A GYM System</title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../css/bootstrap.min.css" />
  <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
  <link rel="stylesheet" href="../css/fullcalendar.css" />
  <link rel="stylesheet" href="../css/matrix-style.css" />
  <link rel="stylesheet" href="../css/matrix-media.css" />
  <link rel="stylesheet" href="../css/system-polish.css" />
  <link href="../font-awesome/css/all.css" rel="stylesheet" />
  <link href="../font-awesome/css/fontawesome.css" rel="stylesheet" />
  <link rel="stylesheet" href="../css/jquery.gritter.css" />
  <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>



  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">
    function dashboardChartWidth(maxWidth) {
      var viewportWidth = window.innerWidth || document.documentElement.clientWidth || maxWidth;
      var gutter = viewportWidth < 768 ? 36 : 90;
      return Math.min(maxWidth, Math.max(260, viewportWidth - gutter));
    }

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
        width: dashboardChartWidth(760),
        height: window.innerWidth < 768 ? 260 : 320,
        chartArea: {
          width: '90%',
          height: '80%'
        },
        legend: {
          position: window.innerWidth < 768 ? 'bottom' : 'right'
        }
      };
      var chart = new google.visualization.PieChart(document.getElementById('piechart'));
      chart.draw(data, options);
    }
  </script>
  <!-- Visit codeastro.com for more projects -->
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">
    google.charts.load('current', {
      'packages': ['bar']
    });
    google.charts.setOnLoadCallback(drawServicesBar);

    function drawServicesBar() {
      var data = new google.visualization.arrayToDataTable([
        ['Services', 'Total Numbers'],
        // ["King's pawn (e4)", 44],
        // ["Queen's pawn (d4)", 31],
        // ["Knight to King 3 (Nf3)", 12],
        // ["Queen's bishop pawn (c4)", 10],
        // ['Other', 3]

        <?php
        $query = "SELECT services, count(*) as number FROM members GROUP BY services";
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
        width: dashboardChartWidth(710),
        legend: {
          position: 'none'
        },
        bars: 'vertical',
        axes: {
          x: {
            0: {
              side: 'top',
              label: 'Total'
            } // Top x-axis.
          }
        },
        bar: {
          groupWidth: "100%"
        }
      };

      var chart = new google.charts.Bar(document.getElementById('top_x_div'));
      chart.draw(data, options);
    };
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
        // Income
        $query1 = "SELECT SUM(amount) as numberone FROM members";
        $rezz = mysqli_query($con, $query1);
        $data = mysqli_fetch_array($rezz);
        $income = $data['numberone'];
        ?>['Income', <?php echo $income; ?>],

        <?php
        // Expenses (Equipment + General)
        $query10 = "SELECT SUM(amount) as numbert FROM equipment";
        $res1000 = mysqli_query($con, $query10);
        $data = mysqli_fetch_array($res1000);
        $equip_exp = $data['numbert'];

        $query11 = "SELECT SUM(amount) as exp FROM expenses";
        $res11 = mysqli_query($con, $query11);
        $data11 = mysqli_fetch_array($res11);
        $general_exp = $data11['exp'];

        $total_expenses = $equip_exp + $general_exp;
        ?>['Expenses', <?php echo $total_expenses; ?>],


      ]);

      var options = {
        width: dashboardChartWidth(1050),
        legend: {
          position: 'none'
        },

        bars: 'horizontal',
        axes: {
          x: {
            0: {
              side: 'top',
              label: 'Total'
            } // Top x-axis.
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
        width: dashboardChartWidth(600),
        height: window.innerWidth < 768 ? 260 : 300,
        chartArea: {
          width: '88%',
          height: '80%'
        },
        legend: {
          position: window.innerWidth < 768 ? 'bottom' : 'right'
        }
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
        width: dashboardChartWidth(600),
        height: window.innerWidth < 768 ? 260 : 300,
        chartArea: {
          width: '88%',
          height: '80%'
        },
        legend: {
          position: window.innerWidth < 768 ? 'bottom' : 'right'
        }
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
        $query_growth = "SELECT DATE_FORMAT(dor, '%M') as month_name, COUNT(*) as count 
                         FROM members 
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
        width: dashboardChartWidth(710),
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
  <style>
    .dash-card {
      background: #fff;
      border-radius: 18px;
      border: 1px solid #e5e7eb;
      box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
      padding: 14px 16px;
      margin-bottom: 12px;
    }

    .dash-summary {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      margin-bottom: 14px;
    }

    .dash-pill {
      border-radius: 999px;
      padding: 7px 12px;
      font-size: 12px;
      font-weight: 700;
      border: 1px solid #e5e7eb;
      background: #f8fafc;
      color: #1f2937;
    }

    .dashboard-stack {
      display: flex;
      flex-direction: column;
      gap: 18px;
    }

    .chart-widget .widget-content {
      padding: 18px;
    }

    .chart-canvas {
      width: 100%;
      min-height: 220px;
    }

    .dashboard-col {
      margin-bottom: 18px;
    }

    .recent-posts > li {
      overflow: hidden;
    }

    .article-post {
      overflow: hidden;
    }

    .article-post p,
    .article-post strong,
    .dash-card p {
      word-wrap: break-word;
      overflow-wrap: anywhere;
    }

    .widget-action-link {
      margin-top: 10px;
    }

    .widget-action-link .btn {
      border-radius: 999px;
      padding-left: 14px;
      padding-right: 14px;
    }

    .empty-mini {
      padding: 12px;
      text-align: center;
      color: #6b7280;
      font-size: 13px;
    }

    @media (max-width: 767px) {
      .chart-widget .widget-content {
        padding: 14px;
      }

      .dash-summary {
        gap: 8px;
      }

      .dash-pill {
        width: 100%;
        text-align: center;
      }

      .dash-card {
        border-radius: 16px;
      }

      .dashboard-col {
        margin-bottom: 16px;
      }

      .widget-box {
        border-radius: 18px;
        overflow: hidden;
      }

      .user-thumb {
        width: auto;
        margin-right: 10px;
      }
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

  <!-- Visit codeastro.com for more projects -->
  <!--sidebar-menu-->
  <?php $page = 'dashboard';
  include 'includes/sidebar.php' ?>
  <!--sidebar-menu-->

  <!--main-container-part-->
  <div id="content">
    <!--breadcrumbs-->
    <div id="content-header">
      <div id="breadcrumb"> <a href="index.php" title="Tag Bogga Hore" class="tip-bottom"><i class="fa fa-home"></i> Bogga Hore</a></div>
    </div>
    <!--End-breadcrumbs-->

    <!--Action boxes-->
    <div class="container-fluid">

      <!-- Extended Dashboard Stats Cards -->
      <?php include 'dashboard-extended-stats.php'; ?>

      <!--End-Action boxes-->

      <!--Chart-box-->

      <div class="row-fluid dashboard-stack">
        <div class="widget-box chart-widget polish-card">
          <div class="widget-title bg_lg"><span class="icon"><i class="fas fa-file"></i></span>
            <h5>Warbixinta Dakhliga iyo Kharashyada</h5>
          </div>
          <div class="widget-content">
            <div class="dash-summary">
              <span class="dash-pill">Income: $<?php echo number_format($income_total, 2); ?></span>
              <span class="dash-pill">Expenses: $<?php echo number_format($expense_total, 2); ?></span>
              <span class="dash-pill">Net: $<?php echo number_format($net_total, 2); ?></span>
            </div>
            <div class="row-fluid">
              <div class="span12">
                <div id="top_y_div" class="chart-canvas" style="height: 220px;"></div>
              </div>

            </div>
            <hr>
            <div class="row-fluid">
              <div class="span12">
                <div id="growth_chart" class="chart-canvas" style="height: 280px;"></div>
              </div>
            </div>
          </div>
        </div>
      </div><!-- End of row-fluid -->

      <div class="row-fluid">
        <div class="span6 dashboard-col">
          <div class="widget-box chart-widget polish-card">
            <div class="widget-title bg_ly" data-toggle="collapse" href="#collapseG2"><span class="icon"><i class="fas fa-chevron-down"></i></span>
              <h5>Xubnaha Diiwaangashan marka loo eego Jinsiga</h5>
            </div>
            <div class="widget-content nopadding collapse in" id="collapseG2">
              <ul class="recent-posts">

                <div id="donutchart" class="chart-canvas" style="height: 320px;"></div>

              </ul>
            </div>
          </div>
        </div>

        <div class="span6 dashboard-col">
          <div class="widget-box chart-widget polish-card">
            <div class="widget-title bg_ly" data-toggle="collapse" href="#collapseG2"><span class="icon"><i class="fas fa-chevron-down"></i></span>
              <h5>Shaqaalaha marka loo eego Xilka</h5>
            </div>
            <div class="widget-content nopadding collapse in" id="collapseG2">
              <ul class="recent-posts">

                <div id="donutchart2022" class="chart-canvas" style="height: 320px;"></div>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <!--End-Chart-box--> <!-- Visit codeastro.com for more projects -->
      <!-- <hr/> -->
      <div class="row-fluid">
        <div class="span6 dashboard-col">
          <div class="widget-box polish-card">
            <div class="widget-title bg_ly" data-toggle="collapse" href="#collapseG2"><span class="icon"><i class="fas fa-chevron-down"></i></span>
              <h5>Ogeysiisyada Jimicsiga</h5>
            </div>
            <div class="widget-content nopadding collapse in" id="collapseG2">
              <ul class="recent-posts">
                <li>

                  <?php

                  include "dbcon.php";
                  $qry = "SELECT * FROM announcements ORDER BY id DESC LIMIT 5";
                  $result = mysqli_query($con, $qry); // Fixed $conn to $con
                  if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_array($result)) {
                      echo "<div class='dash-card'>";
                      echo "<span class='user-info'>W/Q: Admin / Taariikhda: " . htmlspecialchars($row['date']) . "</span>";
                      echo "<p style='margin:6px 0 0;'><a href='#'>" . htmlspecialchars($row['message']) . "</a></p>";
                      echo "</div>";
                    }
                  } else {
                    echo "<div class='empty-mini'>Ogeysiis lama helin.</div>";
                  }
                  ?>

                  <div class="dash-summary" style="margin-top:6px;"><span class="dash-pill">Total Announcements: <?php echo $ann_total; ?></span></div>
                  <div class="widget-action-link"><a href="manage-announcement.php"><button class="btn btn-warning btn-mini polish-btn">Eeg Dhammaan</button></a></div>
                </li>
              </ul>
            </div>
          </div><!-- Visit codeastro.com for more projects -->


        </div>
        <div class="row-fluid">
          <div class="span6 dashboard-col">
            <div class="widget-box polish-card">
              <div class="widget-title"> <span class="icon"><i class="fas fa-history"></i></span>
                <h5>Dhaqdhaqaaqyada U Dambeeyay</h5>
              </div>
              <div class="widget-content nopadding">
                <ul class="recent-posts">
                  <?php
                  // Recent Registrations and Payments combined
                  $query_activity = "
                  (SELECT fullname as member_name, dor as activity_date, 'Xubin Cusub' as activity_type FROM members)
                  UNION ALL
                  (SELECT fullname as member_name, paid_date as activity_date, 'Lacag-bixin' as activity_type FROM payment_history WHERE paid_date IS NOT NULL)
                  UNION ALL
                  (SELECT m.fullname as member_name, a.curr_date as activity_date, 'Imaansho' as activity_type FROM attendance a JOIN members m ON m.user_id=a.user_id)
                  ORDER BY activity_date DESC LIMIT 6";
                  $res_activity = mysqli_query($con, $query_activity);
                  if ($res_activity && mysqli_num_rows($res_activity) > 0) {
                  while ($activity = mysqli_fetch_array($res_activity)) { ?>
                    <li>
                      <div class="user-thumb"> <i class="fas <?php echo ($activity['activity_type'] == 'Xubin Cusub') ? 'fa-user-plus' : (($activity['activity_type'] == 'Imaansho') ? 'fa-calendar-check' : 'fa-money-bill-wave'); ?>" style="font-size: 1.5em; color: <?php echo ($activity['activity_type'] == 'Xubin Cusub') ? '#28b779' : (($activity['activity_type'] == 'Imaansho') ? '#7c3aed' : '#0284c7'); ?>; margin-top: 10px;"></i> </div>
                      <div class="article-post">
                        <span class="user-info"> <?php echo $activity['activity_type']; ?> / Taariikhda: <?php echo $activity['activity_date']; ?> </span>
                        <p><strong><?php echo $activity['member_name']; ?></strong> ayaa sameeyay <?php echo strtolower($activity['activity_type']); ?>. </p>
                      </div>
                    </li>
                  <?php }
                  } else {
                    echo "<li><div class='empty-mini'>Dhaqdhaqaaq dhaw lama helin.</div></li>";
                  }
                  ?>
                </ul>
              </div>
            </div>
          </div>

          <div class="span6 dashboard-col">
            <div class="widget-box polish-card">
              <div class="widget-title"> <span class="icon"><i class="fas fa-tasks"></i></span>
                <h5>Liiska Shaqooyinka Macamiisha</h5>
              </div>
              <div class="widget-content">
                <div class="todo">
                  <div class="dash-summary"><span class="dash-pill">Pending Tasks: <?php echo $todo_pending_total; ?></span></div>
                  <ul>
                    <?php
                    $qry = "SELECT t.task_desc, t.task_status, m.fullname FROM todo t LEFT JOIN members m ON m.user_id=t.user_id ORDER BY (t.task_status='Pending') DESC, t.id DESC LIMIT 8";
                    $result = mysqli_query($con, $qry);
                    if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_array($result)) { ?>
                      <li class='clearfix'>
                        <div class='txt'> <?php echo htmlspecialchars($row["task_desc"]) ?> <?php if ($row["task_status"] == "Pending") {
                                                                            echo '<span class="by label label-info">Sugaya</span>';
                                                                          } else {
                                                                            echo '<span class="by label label-success">Waa Socda</span>';
                                                                          } ?>
                          <?php if (!empty($row['fullname'])) { ?><small style="display:block;color:#6b7280;">Xubin: <?php echo htmlspecialchars($row['fullname']); ?></small><?php } ?>
                        </div>
                      </li>
                    <?php }
                    } else {
                      echo "<li><div class='empty-mini'>Shaqooyin lama helin.</div></li>";
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
    <div id="footer" class="span12"> <?php echo date("Y"); ?> &copy; M * A GYM System Developed By Abdikafi </div>
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

  <script src="../js/excanvas.min.js"></script> <!-- Visit codeastro.com for more projects -->
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
  <!-- <script src="../js/matrix.interface.js"></script>  -->
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

    (function() {
      var resizeTimer;
      window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
          if (window.google && google.visualization) {
            drawServicesPie();
            drawServicesBar();
            drawFinanceBar();
            drawGenderChart();
            drawDesignationChart();
            drawGrowthChart();
          }
        }, 150);
      });
    })();
  </script>
</body>

</html>