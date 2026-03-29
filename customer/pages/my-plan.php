<?php
session_start();
include_once "dbcon.php";
include_once "session.php";
include_once "../includes/emoji-helper.php";

if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
    exit();
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
    <link rel="stylesheet" href="../css/system-polish.css" />
    <link href="../font-awesome/css/fontawesome.css" rel="stylesheet" />
    <link href="../font-awesome/css/all.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/jquery.gritter.css" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
</head>

<body>

    <!--Header-part-->
    <!-- Logo removed per user request -->
    <!--close-Header-part-->

    <!--top-Header-menu-->
    <?php include '../includes/topheader.php' ?>
    <!--close-top-Header-menu-->

    <!--sidebar-menu-->
    <?php $page = 'my-plan';
    include '../includes/sidebar.php' ?>
    <!--sidebar-menu-->

    <div id="content">
        <div id="content-header">
            <div id="breadcrumb"> <a href="index.php" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <a href="#" class="current">My Plan</a> </div>
        </div>
        <div class="container-fluid" style="margin-top: 50px;">
            <hr>
            <div class="row-fluid">
                <div class="span6">
                    <div class="widget-box plan-main-card">
                        <div class="widget-title" style="background: #f0f9ff; border-bottom: 2px solid #0284c7;">
                            <span class="icon"> <i class="fas fa-utensils" style="color:#0284c7;"></i> </span>
                            <h5 style="color: #0c4a6e; font-weight: 800;">
                                <?php echo $row['plan_name'] ? htmlspecialchars($row['plan_name']) : 'Diet Plan'; ?>
                            </h5>
                        </div>
                        <div class="widget-content" style="padding: 24px; background: #fff;">
                            <?php
                            $member_id = $_SESSION['user_id'];
                            $qry = "SELECT * FROM diet_plans WHERE member_id='$member_id'";
                            $result = mysqli_query($con, $qry);
                            $row = mysqli_fetch_array($result);

                            if ($row) {
                                $plan_json = $row['custom_data'];
                                $decoded = json_decode($plan_json, true);
                                $is_structured = ($plan_json && json_last_error() === JSON_ERROR_NONE && is_array($decoded) && !empty($decoded));

                                if ($is_structured) {
                                    // Header with Goal & Duration
                                    echo '<div style="background:#f8fafc; padding:15px; border-radius:12px; margin-bottom:20px; border:1px solid #e2e8f0; display:flex; justify-content:space-around; align-items:center;">';
                                    echo '<div style="text-align:center;"><span style="display:block; font-size:10px; color:#94a3b8; font-weight:800; text-transform:uppercase;">Goal</span><span style="font-weight:700; color:#0c4a6e;">' . ($row['plan_goal'] ?: 'Muscle Building') . '</span></div>';
                                    echo '<div style="width:1px; height:30px; background:#e2e8f0;"></div>';
                                    echo '<div style="text-align:center;"><span style="display:block; font-size:10px; color:#94a3b8; font-weight:800; text-transform:uppercase;">Duration</span><span style="font-weight:700; color:#0c4a6e;">' . ($row['plan_duration'] ?: '7 Days') . '</span></div>';
                                    echo '</div>';

                                    echo '<div class="plan-cards">';
                                    foreach ($decoded as $day) {
                                        if (empty($day['meals'])) continue;

                                        $dayProtein = 0;
                                        $dayCarbs = 0;
                                        $dayFat = 0;
                                        $dayKcal = 0;

                                        echo '<div class="day-card card-blue">';
                                        echo '<div class="day-header" style="background: linear-gradient(135deg, #0284c7, #0369a1);"><i class="fas fa-calendar-day"></i> ' . htmlspecialchars($day['name']) . '</div>';
                                        foreach ($day['meals'] as $meal) {
                                            echo '<div class="plan-section-row" style="padding: 15px 15px; border-bottom: 1px solid #f1f5f9; background: #fafafa;">';
                                            echo '<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">';
                                            echo '<span style="font-weight: 800; color: #0c4a6e; text-transform: uppercase; font-size: 11px; letter-spacing:0.5px;"><i class="fas fa-utensils"></i> ' . htmlspecialchars($meal['name']) . '</span>';
                                            echo '<span style="font-size: 10px; color: #64748b; font-weight: 700; background: #fff; padding: 2px 8px; border-radius: 10px; border: 1px solid #e2e8f0;"><i class="far fa-clock"></i> ' . htmlspecialchars($meal['time']) . '</span>';
                                            echo '</div>';
                                            echo '<ul class="item-list" style="margin-left:0;">';
                                            foreach ($meal['foods'] as $food) {
                                                $dayProtein += (float)$food['protein'];
                                                $dayCarbs += (float)$food['carbs'];
                                                $dayFat += (float)$food['fat'];
                                                $dayKcal += (float)$food['calories'];

                                                echo '<li style="padding: 10px; display:flex; align-items:center; gap:12px; border-bottom:1px dashed #e2e8f0; border-radius:8px; margin-bottom:5px;">';
                                                echo '<div style="flex-shrink:0;">' . getEmojiForText($food['name'], 'diet') . '</div>';
                                                echo '<div style="flex:1;"><div style="font-weight:700; color:#1e293b; font-size:14px;">' . htmlspecialchars($food['name']) . '</div> <div style="color:#64748b; font-size:12px;">' . htmlspecialchars($food['unit']) . '</div></div>';
                                                echo '<div style="display:grid; grid-template-columns: repeat(2, 1fr); gap:4px; font-size:10px; font-weight:800; min-width:120px;">';
                                                if (isset($food['protein'])) echo '<span title="Protein" style="background:#eff6ff; padding:2px 6px; border-radius:4px; color:#2563eb;">🥩 ' . $food['protein'] . 'g</span>';
                                                if (isset($food['carbs'])) echo '<span title="Carbs" style="background:#f0fdf4; padding:2px 6px; border-radius:4px; color:#16a34a;">🥖 ' . $food['carbs'] . 'g</span>';
                                                if (isset($food['fat'])) echo '<span title="Fat" style="background:#fffbeb; padding:2px 6px; border-radius:4px; color:#d97706;">🧈 ' . $food['fat'] . 'g</span>';
                                                if (isset($food['calories'])) echo '<span title="Calories" style="background:#fff1f2; padding:2px 6px; border-radius:4px; color:#e11d48;">🔥 ' . $food['calories'] . '</span>';
                                                echo '</div>';
                                                echo '</li>';
                                            }
                                            echo '</ul>';
                                            echo '</div>';
                                        }
                                        // Day Nutrition Summary
                                        echo '<div style="background:#f1f5f9; padding:12px 18px; display:flex; flex-direction:column; gap:8px; font-size:11px; font-weight:800; color:#475569; border-top:1px solid #e2e8f0;">';
                                        echo '<div>TOTAL DAY NUTRITION :</div>';
                                        echo '<div style="display:flex; gap:15px; flex-wrap:wrap;">';
                                        echo '<span style="color:#2563eb;">Protein ' . $dayProtein . 'g</span>';
                                        echo '<span style="color:#16a34a;">Carbs ' . $dayCarbs . 'g</span>';
                                        echo '<span style="color:#d97706;">Fat ' . $dayFat . 'g</span>';
                                        echo '<span style="color:#e11d48;">Calories ' . $dayKcal . '</span>';
                                        echo '</div>';
                                        echo '</div>';
                                        echo '</div>';
                                    }
                                    echo '</div>';
                                } else {
                                    $instruction_data = $row['instruction'];
                                    echo "<h5 style='color: #475569; font-weight: 700; margin-bottom: 12px;'><i class='fas fa-clipboard-list'></i> Trainer Instructions:</h5>";
                                    echo "<div class='legacy-text' style='border-left: 4px solid #0284c7;'>" . nl2br(htmlspecialchars($instruction_data)) . "</div>";
                                }
                                echo "<div class='text-right mt-3' style='margin-top: 20px; border-top: 1px solid #f1f5f9; padding-top: 10px;'><small style='color: #94a3b8; font-weight: 600;'><i class='fas fa-clock'></i> Date Assigned: " . date('M j, Y', strtotime($row['date_assigned'])) . "</small></div>";
                            } else {
                                echo "<div class='alert alert-info' style='border-radius: 10px; border: none; background: #f0f9ff; color: #0369a1; padding: 20px;'><i class='fas fa-info-circle'></i> No diet plan has been assigned to you yet.</div>";
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <div class="span6">
                    <div class="widget-box plan-main-card">
                        <div class="widget-title" style="background: #fff7ed; border-bottom: 2px solid #ea580c;">
                            <span class="icon"> <i class="fas fa-running" style="color:#ea580c;"></i> </span>
                            <h5 style="color: #7c2d12; font-weight: 800;">
                                <?php echo $row['plan_name'] ? htmlspecialchars($row['plan_name']) : 'Workout Plan'; ?>
                            </h5>
                        </div>
                        <div class="widget-content" style="padding: 24px; background: #fff;">
                            <?php
                            $qry = "SELECT * FROM workout_plans WHERE member_id='$member_id'";
                            $result = mysqli_query($con, $qry);
                            $row = mysqli_fetch_array($result);

                            if ($row) {
                                $plan_json = $row['custom_data'];
                                $decoded = json_decode($plan_json, true);
                                $is_structured = ($plan_json && json_last_error() === JSON_ERROR_NONE && is_array($decoded) && !empty($decoded));

                                if ($is_structured) {
                                    // Header with Goal & Duration
                                    echo '<div style="background:#fffaf5; padding:15px; border-radius:12px; margin-bottom:20px; border:1px solid #fed7aa; display:flex; justify-content:space-around; align-items:center;">';
                                    echo '<div style="text-align:center;"><span style="display:block; font-size:10px; color:#9a3412; font-weight:800; text-transform:uppercase;">Goal</span><span style="font-weight:700; color:#7c2d12;">' . ($row['plan_goal'] ?: 'Muscle Gain') . '</span></div>';
                                    echo '<div style="width:1px; height:30px; background:#fed7aa;"></div>';
                                    echo '<div style="text-align:center;"><span style="display:block; font-size:10px; color:#9a3412; font-weight:800; text-transform:uppercase;">Duration</span><span style="font-weight:700; color:#7c2d12;">' . ($row['plan_duration'] ?: '30 Days') . '</span></div>';
                                    echo '</div>';

                                    echo '<div class="plan-cards">';
                                    foreach ($decoded as $day) {
                                        if (empty($day['categories'])) continue;
                                        echo '<div class="day-card card-orange">';
                                        echo '<div class="day-header" style="background: linear-gradient(135deg, #ea580c, #c2410c);"><i class="fas fa-calendar-day"></i> ' . htmlspecialchars($day['name']) . '</div>';
                                        foreach ($day['categories'] as $cat) {
                                            echo '<div class="plan-section-row" style="padding: 15px 15px; border-bottom: 1px solid #fff7ed; background: #fffaf5;">';
                                            echo '<div style="margin-bottom:10px; display:flex; align-items:center; gap:8px;">';
                                            echo getEmojiForText($cat['name'], 'workout');
                                            echo '<span style="font-weight: 800; color: #7c2d12; text-transform: uppercase; font-size: 11px; letter-spacing:0.5px;">' . htmlspecialchars($cat['name']) . '</span>';
                                            echo '</div>';
                                            echo '<ul class="item-list" style="margin-left:0;">';
                                            foreach ($cat['exercises'] as $ex) {
                                                echo '<li style="padding: 10px; display:flex; align-items:center; gap:12px; border-bottom:1px dashed #fed7aa; border-radius:8px; margin-bottom:5px;">';
                                                echo '<div style="flex-shrink:0;">' . getEmojiForText($ex['name'], 'workout') . '</div>';
                                                echo '<div style="flex:1;"><span style="font-weight:700; color:#1e293b; font-size:14px;">' . htmlspecialchars($ex['name']) . '</span></div>';
                                                echo '<div style="display:flex; gap:10px; font-size:10px; font-weight:800; color:#475569;">';
                                                echo '<span title="Sets" style="background:#fff7ed; padding:2px 6px; border-radius:4px; color:#ea580c;">' . $ex['sets'] . ' Sets</span>';
                                                echo '<span title="Reps" style="background:#fff7ed; padding:2px 6px; border-radius:4px; color:#ea580c;">' . $ex['reps'] . ' Reps</span>';
                                                if ($ex['rest']) echo '<span title="Rest" style="background:#f8fafc; padding:2px 6px; border-radius:4px; color:#64748b;">⏳ ' . $ex['rest'] . '</span>';
                                                echo '</div>';
                                                echo '</li>';
                                            }
                                            echo '</ul>';
                                            echo '</div>';
                                        }
                                        echo '</div>';
                                    }
                                    echo '</div>';
                                } else {
                                    $instruction_data = $row['instruction'];
                                    echo "<h5 style='color: #475569; font-weight: 700; margin-bottom: 12px;'><i class='fas fa-clipboard-list'></i> Trainer Instructions:</h5>";
                                    echo "<div class='legacy-text' style='border-left: 4px solid #ea580c;'>" . nl2br(htmlspecialchars($instruction_data)) . "</div>";
                                }
                                echo "<div class='text-right mt-3' style='margin-top: 20px; border-top: 1px solid #f1f5f9; padding-top: 10px;'><small style='color: #94a3b8; font-weight: 600;'><i class='fas fa-clock'></i> Date Assigned: " . date('M j, Y', strtotime($row['date_assigned'])) . "</small></div>";
                            } else {
                                echo "<div class='alert alert-info' style='border-radius: 10px; border: none; background: #fff7ed; color: #c2410c; padding: 20px;'><i class='fas fa-info-circle'></i> No workout plan has been assigned to you yet.</div>";
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
        <div id="footer" class="span12"> <?php echo date("Y"); ?> &copy; M*A GYM System Developed By Abdikafi</div>
    </div>

    <style>
        #footer {
            color: white;
            padding: 20px 0;
            text-align: center;
        }

        .plan-main-card {
            border: none !important;
            border-radius: 16px !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05) !important;
            overflow: hidden !important;
        }

        .plan-cards {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: 10px;
        }

        .day-card {
            background: #fff;
            border: 1px solid #f1f5f9;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .day-card:hover {
            transform: translateX(5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .day-header {
            color: white;
            padding: 12px 18px;
            font-weight: 800;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            letter-spacing: 0.5px;
        }

        .item-list {
            list-style: none;
            margin: 0;
            padding: 5px 0;
        }

        .item-list li {
            padding: 12px 18px;
            border-bottom: 1px solid #f8fafc;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 14px;
            color: #334155;
            line-height: 1.6;
            font-weight: 500;
        }

        .item-list li:last-child {
            border-bottom: none;
        }

        .item-list li i {
            margin-top: 4px;
            font-size: 15px;
            opacity: 0.9;
        }

        .legacy-text {
            background: #f8fafc;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            color: #475569;
            font-size: 15px;
            line-height: 1.7;
            font-weight: 500;
        }

        @media (max-width: 767px) {
            .container-fluid {
                margin-top: 18px !important;
            }

            .plan-main-card .widget-content {
                padding: 16px !important;
            }

            .plan-main-card .widget-title h5 {
                font-size: 14px;
                line-height: 1.4;
            }

            .plan-cards {
                gap: 14px;
            }

            .day-card:hover {
                transform: none;
            }

            .day-header {
                font-size: 14px;
                padding: 12px 14px;
            }

            .plan-section-row {
                padding: 12px !important;
            }

            .item-list li {
                padding: 10px 12px;
                flex-direction: column;
                align-items: flex-start;
            }

            .item-list li > div:last-child {
                width: 100%;
                min-width: 0 !important;
                display: flex !important;
                flex-wrap: wrap;
                gap: 6px !important;
            }

            .legacy-text {
                padding: 16px;
                font-size: 14px;
            }
        }
    </style>

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