<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
}
include "dbcon.php";

date_default_timezone_set('Africa/Nairobi');
$todays_date = date('Y-m-d');

// Default to current month and year if not selected
$selected_year = isset($_GET['year']) ? $_GET['year'] : date('Y');
$selected_month = isset($_GET['month']) ? str_pad($_GET['month'], 2, '0', STR_PAD_LEFT) : date('m');

// Get number of days in the selected month
$num_of_days = cal_days_in_month(CAL_GREGORIAN, $selected_month, $selected_year);

// View type: cycle (30-day) or calendar
$view_type = isset($_GET['view_type']) ? $_GET['view_type'] : 'calendar';

// Fetch all members who were registered on or before the end of the selected month
$end_of_selected_month = "$selected_year-$selected_month-$num_of_days";
$members_qry = "SELECT user_id, fullname, dor, paid_date, expiry_date FROM members WHERE status = 'Active'";
if ($view_type == 'calendar') {
    $members_qry .= " AND dor <= '$end_of_selected_month'";
}
$members_qry .= " ORDER BY fullname ASC";
$members_res = mysqli_query($con, $members_qry);

// Pre-fetch attendance data
$attendance_data = [];
$att_qry = "SELECT a.user_id, a.curr_time, a.check_out, a.curr_date, m.dor 
            FROM attendance a 
            JOIN members m ON a.user_id = m.user_id 
            WHERE m.status = 'Active' AND a.present = 1";

if ($view_type == 'calendar') {
    $att_qry .= " AND MONTH(a.curr_date) = '$selected_month' AND YEAR(a.curr_date) = '$selected_year'";
}
$att_res = mysqli_query($con, $att_qry);

while ($att_row = mysqli_fetch_array($att_res)) {
    $uid = $att_row['user_id'];
    $att_date = $att_row['curr_date'];
    $start_date = $att_row['dor'];

    if ($view_type == 'calendar') {
        $day = (int)date('d', strtotime($att_date));
        $attendance_data[$uid][$day] = [
            'check_in' => $att_row['curr_time'],
            'check_out' => $att_row['check_out']
        ];
    } else {
        $diff = strtotime($att_date) - strtotime($start_date);
        $day_number = floor($diff / 86400) + 1;
        if ($day_number >= 1 && $day_number <= 30) {
            $attendance_data[$uid][$day_number] = [
                'check_in' => $att_row['curr_time'],
                'check_out' => $att_row['check_out']
            ];
        }
    }
}

// Build period map per member for selected month (latest payment period overlapping this month)
$month_start = "$selected_year-$selected_month-01";
$period_map = [];
$period_qry = "SELECT user_id, paid_date, expiry_date FROM payment_history
               WHERE paid_date <= '$end_of_selected_month' AND expiry_date >= '$month_start'
               ORDER BY user_id ASC, paid_date DESC";
$period_res = mysqli_query($con, $period_qry);
if ($period_res) {
    while ($period_row = mysqli_fetch_assoc($period_res)) {
        $uid = $period_row['user_id'];
        if (!isset($period_map[$uid])) {
            $period_map[$uid] = [
                'start' => $period_row['paid_date'],
                'end' => $period_row['expiry_date']
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>M * A GYM System - Attendance Report</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="../css/matrix-style.css" />
    <link rel="stylesheet" href="../css/matrix-media.css" />
    <link href="../font-awesome/css/all.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>

    <style>
        .report-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            padding: 25px;
            margin-bottom: 30px;
        }

        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .report-title {
            font-size: 20px;
            font-weight: 700;
            color: #2d3748;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .report-filters {
            display: flex;
            gap: 15px;
        }

        .report-filters select {
            padding: 8px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #fff;
            color: #4a5568;
            font-weight: 600;
            outline: none;
            cursor: pointer;
        }

        .legend-container {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #4a5568;
        }

        .status-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
        }

        .status-present {
            background-color: #10b981;
        }

        .status-absent {
            background-color: #ef4444;
        }

        .status-incomplete {
            background-color: #f59e0b;
        }

        .attendance-table-wrapper {
            overflow-x: auto;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }

        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            white-space: nowrap;
        }

        .attendance-table th,
        .attendance-table td {
            padding: 12px 6px;
            text-align: center;
            border-bottom: 1px solid #edf2f7;
            font-size: 13px;
        }

        .attendance-table th {
            background-color: #6b46c1;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
        }

        .attendance-table .col-name {
            text-align: left;
            min-width: 150px;
            padding-left: 15px;
            background-color: #fff;
            position: sticky;
            left: 0;
            z-index: 10;
            border-right: 1px solid #e2e8f0;
        }

        .attendance-table th.col-name {
            background-color: #6b46c1;
            padding-left: 15px;
            border-right: none;
        }

        .attendance-table tbody tr:hover {
            background-color: #f7fafc;
        }

        .attendance-table tbody tr:hover .col-name {
            background-color: #f7fafc;
        }

        .attendance-table td {
            color: #4a5568;
            font-weight: 500;
        }

        .icon-cell {
            display: flex;
            justify-content: center;
        }

        /* Tabs styling */
        .page-tabs {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }

        .page-tab {
            padding: 8px 20px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            color: #4a5568;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }

        .page-tab.active {
            background: #6b46c1;
            color: #fff;
            border-color: #6b46c1;
        }

        .page-tab:hover:not(.active) {
            background: #f7fafc;
        }

        /* Interactive Cell Styles */
        .toggle-attendance {
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
        }

        .toggle-attendance:hover {
            transform: scale(1.15);
            filter: brightness(1.1);
        }

        .future-day {
            opacity: 0.3;
            cursor: not-allowed;
            filter: grayscale(1);
        }

        .today-col {
            background-color: rgba(107, 70, 193, 0.05);
        }

        /* Tooltip styling */
        .icon-cell .tooltip-info {
            visibility: hidden;
            width: 140px;
            background-color: #2d3748;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 8px;
            position: absolute;
            z-index: 100;
            bottom: 125%;
            left: 50%;
            margin-left: -70px;
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 11px;
            line-height: 1.4;
            pointer-events: none;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .icon-cell .tooltip-info::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #2d3748 transparent transparent transparent;
        }

        .icon-cell:hover .tooltip-info {
            visibility: visible;
            opacity: 1;
        }

        .cell-loading {
            font-size: 12px;
            color: #6b46c1;
        }

        /* Responsive adjustments for columns */
        .attendance-table th,
        .attendance-table td {
            min-width: 40px;
        }
    </style>
</head>

<body>

    <?php include 'includes/header-content.php'; ?>
    <?php include 'includes/topheader.php' ?>
    <?php $page = 'attendance-repo';
    include 'includes/sidebar.php' ?>

    <div id="content">
        <div id="content-header">
            <div id="breadcrumb"> <a href="index.php" title="Tag Bogga Hore" class="tip-bottom"><i class="fas fa-home"></i> Bogga Hore</a> <a href="#" class="current">Warbixinta Imaatinka</a> </div>
        </div>
        <div class="container-fluid">

            <div class="page-tabs" style="margin-top:20px;">
                <a href="#" class="page-tab active">Attendance Management Report</a>
            </div>

            <div class="report-card">
                <div class="report-header">
                    <h2 class="report-title"><i class="fas fa-users" style="color:#6b46c1;"></i> Jadwalka Imaanshaha</h2>
                    <form method="GET" class="report-filters" id="filterForm">
                        <input type="hidden" name="view_type" value="calendar">
                        <select name="month">
                            <?php
                            $monthsText = ["01" => "January", "02" => "February", "03" => "March", "04" => "April", "05" => "May", "06" => "June", "07" => "July", "08" => "August", "09" => "September", "10" => "October", "11" => "November", "12" => "December"];
                            foreach ($monthsText as $m_num => $m_name) {
                                $sel = ($m_num == $selected_month) ? 'selected' : '';
                                echo "<option value='$m_num' $sel>$m_name</option>";
                            }
                            ?>
                        </select>
                        <select name="year">
                            <?php
                            $curr_y = date('Y');
                            for ($y = $curr_y - 2; $y <= $curr_y + 1; $y++) {
                                $sel = ($y == $selected_year) ? 'selected' : '';
                                echo "<option value='$y' $sel>$y</option>";
                            }
                            ?>
                        </select>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
                    </form>
                </div>

                <div class="legend-container">
                    <div class="legend-item">
                        <div class="status-icon status-present"><i class="fas fa-check"></i></div> Soo Galay (Present/Checked Out)
                    </div>
                    <div class="legend-item">
                        <div class="status-icon status-absent"><i class="fas fa-times"></i></div> Maqane (Absent)
                    </div>
                    <div class="legend-item">
                        <div class="status-icon status-incomplete"><i class="fas fa-clock"></i></div> Wuu Joogaa Hadda (Incomplete)
                    </div>
                </div>

                <div class="attendance-table-wrapper">
                    <table class="attendance-table">
                        <thead>
                            <tr>
                                <th class="col-name">MAGACA XUBINTA</th>
                                <?php
                                $cols = ($view_type == 'calendar') ? $num_of_days : 30;
                                for ($d = 1; $d <= $cols; $d++): ?>
                                    <th><?php echo ($view_type == 'calendar') ? str_pad($d, 2, '0', STR_PAD_LEFT) : "Day $d"; ?></th>
                                <?php endfor; ?>
                                <th>TOTAL PRE.</th>
                                <th>TOTAL ABS.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $today = date('Y-m-d');
                            $cols = ($view_type == 'calendar') ? $num_of_days : 30;

                            if (mysqli_num_rows($members_res) > 0) {
                                while ($member = mysqli_fetch_assoc($members_res)):
                                    $uid = $member['user_id'];
                                    $start_date = $member['dor'];
                                    $period_start = isset($period_map[$uid]) ? $period_map[$uid]['start'] : (!empty($member['paid_date']) ? $member['paid_date'] : $member['dor']);
                                    $period_end = isset($period_map[$uid]) ? $period_map[$uid]['end'] : (!empty($member['expiry_date']) ? $member['expiry_date'] : $today);
                                    $present_count = 0;
                                    $absent_count = 0;
                            ?>
                                    <tr>
                                        <td class="col-name">
                                            <strong><?php echo htmlspecialchars($member['fullname']); ?></strong><br>
                                            <span style="font-size:11px; color:#718096;">Diiwaangelin: <?php echo date('d M Y', strtotime($start_date)); ?></span><br>
                                            <?php if (!empty($period_start) && $period_start !== $start_date): ?>
                                                <span style="font-size:11px; color:#4c51bf;">Period Start (Renewal): <?php echo date('d M Y', strtotime($period_start)); ?></span>
                                            <?php else: ?>
                                                <span style="font-size:11px; color:#718096;">Period Start: <?php echo date('d M Y', strtotime($period_start)); ?></span>
                                            <?php endif; ?>
                                        </td>

                                        <?php for ($d = 1; $d <= $cols; $d++):
                                            $cell_date = ($view_type == 'calendar') ? "$selected_year-$selected_month-" . str_pad($d, 2, '0', STR_PAD_LEFT) : date('Y-m-d', strtotime($member['dor'] . " + " . ($d - 1) . " days"));
                                            $is_future = (strtotime($cell_date) > strtotime($today));
                                            $is_before_period_start = (strtotime($cell_date) < strtotime($period_start));
                                            $is_after_period_end = (!empty($period_end) && strtotime($cell_date) > strtotime($period_end));
                                            $is_today = ($cell_date == $today);
                                        ?>
                                            <td class="<?php echo $is_today ? 'today-col' : ''; ?>">
                                                <div class="icon-cell <?php echo ($is_future || $is_before_period_start || $is_after_period_end) ? 'future-day' : 'toggle-attendance'; ?>"
                                                    data-uid="<?php echo $uid; ?>"
                                                    data-date="<?php echo $cell_date; ?>">
                                                    <?php
                                                    $is_valid_day = false;
                                                    $day_info = null;

                                                    if ($view_type == 'calendar') {
                                                        $is_valid_day = (!$is_future && !$is_before_period_start && !$is_after_period_end);
                                                        if (isset($attendance_data[$uid][$d])) $day_info = $attendance_data[$uid][$d];
                                                    } else {
                                                        $is_valid_day = (!$is_future && !$is_before_period_start && !$is_after_period_end);
                                                        if (isset($attendance_data[$uid][$d])) $day_info = $attendance_data[$uid][$d];
                                                    }

                                                    if ($is_future || $is_before_period_start || $is_after_period_end) {
                                                        echo "<div style='width:24px; height:24px;'></div>";
                                                        if ($is_future) echo "<span class='tooltip-info'>Date: {$cell_date}<br>(Mustaqbal)</span>";
                                                    } else {
                                                        if ($day_info) {
                                                            $is_incomplete = empty($day_info['check_out']) || strpos($day_info['check_out'], '0000') !== false;
                                                            if ($is_incomplete) {
                                                                echo '<div class="status-icon status-incomplete"><i class="fas fa-clock"></i></div>';
                                                                echo "<span class='tooltip-info'>Date: {$cell_date}<br>In: {$day_info['check_in']}</span>";
                                                            } else {
                                                                echo '<div class="status-icon status-present"><i class="fas fa-check"></i></div>';
                                                                echo "<span class='tooltip-info'>Date: {$cell_date}<br>In: {$day_info['check_in']}<br>Out: " . date('h:i A', strtotime($day_info['check_out'])) . "</span>";
                                                            }
                                                            $present_count++;
                                                        } else {
                                                            echo '<div class="status-icon status-absent"><i class="fas fa-times"></i></div>';
                                                            echo "<span class='tooltip-info'>Date: {$cell_date}<br>Absent (Maqane)</span>";
                                                            $absent_count++;
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </td>
                                        <?php endfor; ?>

                                        <td><strong style="color:#10b981;" class="total-pre"><?php echo $present_count; ?></strong></td>
                                        <td><strong style="color:#ef4444;" class="total-abs"><?php echo $absent_count; ?></strong></td>
                                    </tr>
                            <?php
                                endwhile;
                            } else {
                                echo "<tr><td colspan='" . ($cols + 3) . "' style='text-align:center; padding:20px;'>Lama helin xubno (No members found)</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <div class="row-fluid">
        <div id="footer" class="span12"> <?php echo date("Y"); ?> &copy; M * A GYM System Developed By Abdikafi </div>
    </div>

    <style>
        #footer {
            color: white;
            text-align: center;
            padding: 10px;
        }
    </style>

    <script src="../js/jquery.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/matrix.js"></script>
</body>

</html>