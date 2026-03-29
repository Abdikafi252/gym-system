
<?php
// DEBUG: Show all errors for troubleshooting blank screen
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
if (!isset($_SESSION['user_id'])) {
  header('location:../index.php');
}
include "dbcon.php";
require_once "includes/lang.php";
require_once "includes/db_helper.php";
require_once "includes/accounting_engine.php";


// Use global branch filter from session (match Member List logic)
$selected_branch = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : 0;
$branch_where = $selected_branch > 0 ? " WHERE branch_id = $selected_branch " : "";

function admin_table_exists($con, $table)
{
  static $cache = [];
  if (isset($cache[$table])) {
    return $cache[$table];
  }

  $safeTable = mysqli_real_escape_string($con, $table);
  $result = mysqli_query($con, "SHOW TABLES LIKE '$safeTable'");
  $cache[$table] = $result && mysqli_num_rows($result) > 0;
  return $cache[$table];
}

function admin_column_exists($con, $table, $column)
{
  static $cache = [];
  $key = $table . '.' . $column;
  if (isset($cache[$key])) {
    return $cache[$key];
  }

  if (!admin_table_exists($con, $table)) {
    $cache[$key] = false;
    return false;
  }

  $safeTable = mysqli_real_escape_string($con, $table);
  $safeColumn = mysqli_real_escape_string($con, $column);
  $result = mysqli_query($con, "SHOW COLUMNS FROM `$safeTable` LIKE '$safeColumn'");
  $cache[$key] = $result && mysqli_num_rows($result) > 0;
  return $cache[$key];
}

function admin_scalar($con, $sql, $default = 0)
{
  $result = mysqli_query($con, $sql);
  if (!$result) {
    return $default;
  }

  $row = mysqli_fetch_row($result);
  return $row ? $row[0] : $default;
}




$qry = "SELECT services, count(*) as number FROM members $branch_where GROUP BY services";
$result = mysqli_query($con, $qry);

$result3 = mysqli_query($con, "SELECT gender, count(*) as enumber FROM members $branch_where GROUP BY gender");
$qry = "SELECT designation, count(*) as snumber FROM staffs" . ($selected_branch > 0 ? " WHERE branch_id = $selected_branch" : "") . " GROUP BY designation";
$result5 = mysqli_query($con, $qry);




$income_total = 0.0;
if (admin_table_exists($con, 'payment_history') && admin_column_exists($con, 'payment_history', 'paid_amount')) {
  $income_total = (float)admin_scalar($con, "SELECT COALESCE(SUM(paid_amount),0) FROM payment_history" . $branch_where, 0);
} elseif (admin_column_exists($con, 'members', 'paid_amount')) {
  $income_total = (float)admin_scalar($con, "SELECT COALESCE(SUM(paid_amount),0) FROM members" . $branch_where, 0);
} elseif (admin_column_exists($con, 'members', 'amount')) {
  $income_total = (float)admin_scalar($con, "SELECT COALESCE(SUM(amount),0) FROM members" . $branch_where, 0);
}

$equip_total = admin_table_exists($con, 'equipment') ? (float)admin_scalar($con, "SELECT COALESCE(SUM(amount),0) FROM equipment" . $branch_where, 0) : 0.0;
$general_expense_total = admin_table_exists($con, 'expenses') ? (float)admin_scalar($con, "SELECT COALESCE(SUM(amount),0) FROM expenses" . $branch_where, 0) : 0.0;
$expense_total = $equip_total + $general_expense_total;
$net_total = $income_total - $expense_total;


$ann_total = admin_table_exists($con, 'announcements') ? (int)admin_scalar($con, "SELECT COUNT(*) FROM announcements", 0) : 0;
$todo_pending_total = admin_table_exists($con, 'todo') ? (int)admin_scalar($con, "SELECT COUNT(*) FROM todo WHERE task_status='Pending'", 0) : 0;

// Live Counter: Members currently in the gym (Checked in today but not out)
$today_check_val = date('Y-m-d');
$live_members = (int)admin_scalar($con, "SELECT COUNT(*) FROM attendance 
                 WHERE curr_date = '$today_check_val' 
                 AND (check_out IS NULL OR check_out = '0000-00-00 00:00:00')" . ($selected_branch > 0 ? " AND branch_id = $selected_branch" : ""));

// Run Auto-SMS Check
include '../api/auto_sms_expiry.php';
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
  <link rel="stylesheet" href="../css/system-polish.css" />
  <link rel="stylesheet" href="../css/premium.css" />
  <link href="../font-awesome/css/all.css" rel="stylesheet" />
  <link href="../font-awesome/css/fontawesome.css" rel="stylesheet" />
  <link rel="stylesheet" href="../css/jquery.gritter.css" />
  <link href='https://fonts.googleapis.com/css?family=Outfit:400,700,800' rel='stylesheet' type='text/css'>



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
        colors: ['#3b82f6', '#22c55e', '#ef4444', '#f59e0b', '#8b5cf6'],
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
        $query = "SELECT services, count(*) as number FROM members $branch_where GROUP BY services";
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

  <style>
    /* Google Chart Text Coloring */
    text { fill: #475569 !important; font-family: 'Outfit', sans-serif !important; }
  </style>

  <script type="text/javascript">
    google.charts.load('current', {
      'packages': ['corechart', 'bar']
    });
    google.charts.setOnLoadCallback(drawRevenueChart);

    function drawRevenueChart() {
      var data = google.visualization.arrayToDataTable([
        ['Month', 'Revenue ($)'],
        <?php
        // Try to get data from journal (Membership Revenue Account 4000)
        $rev_sql = "SELECT DATE_FORMAT(je.entry_date, '%b %Y') as month_label, SUM(jl.credit) as total 
                    FROM journal_lines jl 
                    JOIN journal_entries je ON jl.journal_entry_id = je.id 
                    WHERE jl.account_id = (SELECT id FROM chart_of_accounts WHERE code='4000') 
                    GROUP BY DATE_FORMAT(je.entry_date, '%Y-%m') 
                    ORDER BY je.entry_date ASC LIMIT 6";
        $rev_res = mysqli_query($con, $rev_sql);
        if ($rev_res && mysqli_num_rows($rev_res) > 0) {
            while ($row = mysqli_fetch_assoc($rev_res)) {
                echo "['" . $row['month_label'] . "', " . (float)$row['total'] . "],";
            }
        } else {
            // Fallback to payment_history if accounting is empty
            $rev_sql = "SELECT DATE_FORMAT(paid_date, '%b %Y') as month_label, SUM(paid_amount) as total 
                        FROM payment_history 
                        WHERE paid_date IS NOT NULL 
                        GROUP BY DATE_FORMAT(paid_date, '%Y-%m') 
                        ORDER BY paid_date ASC LIMIT 6";
             $rev_res = mysqli_query($con, $rev_sql);
             while ($row = mysqli_fetch_assoc($rev_res)) {
                echo "['" . $row['month_label'] . "', " . (float)$row['total'] . "],";
             }
        }
        ?>
      ]);

      var options = {
        title: 'Monthly Revenue Growth',
        curveType: 'function',
        legend: { position: 'bottom' },
        hAxis: { title: 'Month' },
        vAxis: { title: 'Amount ($)' },
        colors: ['#3b82f6'],
        pointSize: 5,
        chartArea: { width: '85%', height: '70' }
      };

      var chart = new google.visualization.LineChart(document.getElementById('revenue_line_chart'));
      chart.draw(data, options);
    }
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

  
  <!--sidebar-menu-->
  <?php $page = 'dashboard';
  include 'includes/sidebar.php' ?>
  <!--sidebar-menu-->

  <!--main-container-part-->
  <div id="content">
    <!--breadcrumbs-->
    <div id="content-header">
      <div id="breadcrumb"> <a href="index.php" title="Go to Home Page" class="tip-bottom"><i class="fa fa-home"></i> Home</a></div>
    </div>
    <!--End-breadcrumbs-->

    <!--Action boxes-->
    <div class="container-fluid">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; flex-wrap:wrap; gap:10px;">
        <h3 style="margin:0; font-weight:800; color:#1e293b;"><?php echo __('dashboard'); ?> Overview</h3>
        <div style="display:flex; gap:10px; align-items:center;">
          <!-- Hardware Status Widget -->
          <div id="hw_widget" style="background: #1e293b; color: white; padding: 10px 15px; border-radius: 12px; display: flex; align-items: center; gap: 10px; border: 1px solid #334155;">
             <i class="fas fa-microchip" style="color: #3b82f6; font-size: 20px;"></i>
             <div style="line-height: 1.2;">
                <div id="hw_status" style="font-size: 11px; font-weight: 700;">Checking Terminal...</div>
                <div id="hw_ip" style="font-size: 10px; opacity: 0.6;">IP: -</div>
             </div>
             <a href="face-terminal-settings.php" style="margin-left: 10px; color: #94a3b8;"><i class="fas fa-cog"></i></a>
          </div>
          <div class="live-counter-pill">
            <span class="live-dot"></span>
            <span><?php echo $live_members; ?> Members Live in Gym</span>
          </div>
        </div>
      </div>


      <!-- Branch filter is now global in the header -->

      <!-- Extended Dashboard Stats Cards -->
      <?php include 'dashboard-extended-stats.php'; ?>

      <!--End-Action boxes-->

      <!--Chart-box-->

      <div class="row-fluid dashboard-stack">
        <div class="widget-box chart-widget polish-card">
          <div class="widget-title bg_lg"><span class="icon"><i class="fas fa-chart-line"></i></span>
            <h5><?php echo __('revenue'); ?> & <?php echo __('expenses'); ?></h5>
          </div>
          <div class="widget-content">
            <div class="dash-summary">
              <span class="dash-pill" style="border-left: 4px solid #3b82f6;"><i class="fas fa-hand-holding-usd"></i> <?php echo __('revenue'); ?>: $<?php echo number_format($income_total, 2); ?></span>
              <span class="dash-pill" style="border-left: 4px solid #ef4444;"><i class="fas fa-file-invoice-dollar"></i> <?php echo __('expenses'); ?>: $<?php echo number_format($expense_total, 2); ?></span>
              <span class="dash-pill" style="border-left: 4px solid #22c55e;"><i class="fas fa-wallet"></i> <?php echo __('net_income'); ?>: $<?php echo number_format($net_total, 2); ?></span>
            </div>
            <div class="row-fluid">
              <div class="span12">
                <div id="revenue_line_chart" class="chart-canvas" style="height: 300px;"></div>
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
              <h5>Registered Members by Gender</h5>
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
              <h5>Staff by Designation</h5>
            </div>
            <div class="widget-content nopadding collapse in" id="collapseG2">
              <ul class="recent-posts">

                <div id="donutchart2022" class="chart-canvas" style="height: 320px;"></div>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <!--End-Chart-box--> 
      <!-- <hr/> -->
      <div class="row-fluid">
        <div class="span6 dashboard-col">
          <div class="widget-box polish-card">
            <div class="widget-title bg_ly" data-toggle="collapse" href="#collapseG2"><span class="icon"><i class="fas fa-chevron-down"></i></span>
              <h5>Gym Announcements</h5>
            </div>
            <div class="widget-content nopadding collapse in" id="collapseG2">
              <ul class="recent-posts">
                <li>

                  <?php

                  include "dbcon.php";
                  $qry = "SELECT * FROM announcements" . ($selected_branch > 0 ? " WHERE branch_id = $selected_branch " : "") . " ORDER BY id DESC LIMIT 5";
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

                  <div class="dash-summary" style="margin-top:6px;"><span class="dash-pill">Total Announcements: <?php echo $ann_total; ?></span></div>
                  <div class="widget-action-link"><a href="manage-announcement.php"><button class="btn btn-warning btn-mini polish-btn">View All</button></a></div>
                </li>
              </ul>
            </div>
          </div>


        </div>
        <div class="row-fluid">
          <div class="span6 dashboard-col">
            <div class="widget-box polish-card">
              <div class="widget-title"> <span class="icon"><i class="fas fa-history"></i></span>
                <h5>Recent Activities</h5>
              </div>
              <div class="widget-content nopadding">
                <ul class="recent-posts">
                  <?php
                  // Recent Registrations and Payments combined
                  $activityQueries = [];
                  if (admin_table_exists($con, 'members') && admin_column_exists($con, 'members', 'fullname') && admin_column_exists($con, 'members', 'dor')) {
                    $activityQueries[] = "SELECT fullname as member_name, dor as activity_date, 'New Member' as activity_type FROM members" . $branch_where;
                  }
                  if (admin_table_exists($con, 'payment_history') && admin_column_exists($con, 'payment_history', 'fullname') && admin_column_exists($con, 'payment_history', 'paid_date')) {
                    $activityQueries[] = "SELECT fullname as member_name, paid_date as activity_date, 'Payment' as activity_type FROM payment_history " . ($selected_branch > 0 ? " WHERE branch_id = $selected_branch AND " : " WHERE ") . " paid_date IS NOT NULL";
                  }
                  if (admin_table_exists($con, 'attendance') && admin_column_exists($con, 'attendance', 'curr_date')) {
                    $activityQueries[] = "SELECT m.fullname as member_name, a.curr_date as activity_date, 'Attendance' as activity_type FROM attendance a JOIN members m ON m.user_id=a.user_id" . ($selected_branch > 0 ? " WHERE m.branch_id = $selected_branch" : "");
                  }
                  $res_activity = false;
                  if (!empty($activityQueries)) {
                    $query_activity = implode(" UNION ALL ", $activityQueries) . " ORDER BY activity_date DESC LIMIT 6";
                    $res_activity = mysqli_query($con, $query_activity);
                  }
                  if ($res_activity && mysqli_num_rows($res_activity) > 0) {
                  while ($activity = mysqli_fetch_array($res_activity)) { ?>
                    <li>
                      <div class="user-thumb"> <i class="fas <?php echo ($activity['activity_type'] == 'New Member') ? 'fa-user-plus' : (($activity['activity_type'] == 'Attendance') ? 'fa-calendar-check' : 'fa-money-bill-wave'); ?>" style="font-size: 1.5em; color: <?php echo ($activity['activity_type'] == 'New Member') ? '#28b779' : (($activity['activity_type'] == 'Attendance') ? '#7c3aed' : '#0284c7'); ?>; margin-top: 10px;"></i> </div>
                      <div class="article-post">
                        <span class="user-info"> <?php echo $activity['activity_type']; ?> / Date: <?php echo $activity['activity_date']; ?> </span>
                        <p><strong><?php echo $activity['member_name']; ?></strong> performed <?php echo strtolower($activity['activity_type']); ?>. </p>
                      </div>
                    </li>
                  <?php }
                  } else {
                    echo "<li><div class='empty-mini'>No recent activity found.</div></li>";
                  }
                  ?>
                </ul>
              </div>
            </div>
          </div>

          <div class="span6 dashboard-col">
            <div class="widget-box polish-card">
              <div class="widget-title"> <span class="icon"><i class="fas fa-tasks"></i></span>
                <h5>Customer Task List</h5>
              </div>
              <div class="widget-content">
                <div class="todo">
                  <div class="dash-summary"><span class="dash-pill">Pending Tasks: <?php echo $todo_pending_total; ?></span></div>
                  <ul>
                    <?php
                    $result = false;
                    if (admin_table_exists($con, 'todo')) {
                      $qry = "SELECT t.task_desc, t.task_status, m.fullname FROM todo t LEFT JOIN members m ON m.user_id=t.user_id" . ($selected_branch > 0 ? " WHERE m.branch_id = $selected_branch " : "") . " ORDER BY (t.task_status='Pending') DESC, t.id DESC LIMIT 8";
                      $result = mysqli_query($con, $qry);
                    }
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
      // Hardware Status Checker
      function checkHardware() {
          $.ajax({
              url: 'actions/test-terminal-connection.php',
              type: 'GET',
              success: function(response){
                  try {
                      var res = JSON.parse(response);
                      if(res.status === 'success') {
                          $('#hw_status').html('<span style="color:#10b981;">● Terminal: ONLINE</span>');
                          $('#hw_ip').text('IP: ' + res.ip);
                      } else {
                          $('#hw_status').html('<span style="color:#ef4444;">● Terminal: OFFLINE</span>');
                      }
                  } catch(e) {}
              }
          });
      }
      checkHardware();
      setInterval(checkHardware, 30000); // Check every 30 seconds

      var resizeTimer;
      window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
          if (window.google && google.visualization) {
            drawServicesPie();
            drawServicesBar();
            drawRevenueChart();
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