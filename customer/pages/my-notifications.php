<?php
session_start();
include_once "dbcon.php";
include_once "session.php";

if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
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
    <link rel="stylesheet" href="../css/fullcalendar.css" />
    <link rel="stylesheet" href="../css/matrix-style.css" />
    <link rel="stylesheet" href="../css/matrix-media.css" />
    <link rel="stylesheet" href="../css/system-polish.css" />
    <link href="../font-awesome/css/all.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/jquery.gritter.css" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
    <style>
        .notify-shell {
            margin-top: 24px;
        }

        .notify-card {
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            box-shadow: 0 16px 34px rgba(15, 23, 42, 0.08);
        }

        .activity-list {
            list-style: none;
            margin: 0;
            padding: 16px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        }

        .activity-list li {
            margin-bottom: 12px;
        }

        .activity-list li:last-child {
            margin-bottom: 0;
        }

        .activity-item {
            display: flex;
            gap: 14px;
            align-items: flex-start;
            padding: 16px;
            border-radius: 18px;
            background: #fff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.05);
        }

        .activity-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #eff6ff;
            color: #2563eb;
            flex: 0 0 44px;
            font-size: 18px;
        }

        .activity-copy {
            min-width: 0;
            color: #334155;
        }

        .activity-title {
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .activity-date {
            display: inline-block;
            margin-top: 8px;
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            background: #f8fafc;
            border-radius: 999px;
            padding: 6px 10px;
        }

        @media (max-width: 767px) {
            .notify-shell {
                margin-top: 12px;
            }

            .activity-list {
                padding: 12px;
            }

            .activity-item {
                padding: 14px;
                border-radius: 16px;
            }
        }
    </style>
</head>

<body>

    <!--Header-part-->
    <!-- Logo removed per user request -->
    <!--close-Header-part-->

    <!--top-Header-menu-->
    <?php include '../includes/topheader.php' ?>
    <!--close-top-Header-menu-->

    <!--sidebar-menu-->
    <?php $page = 'notification';
    include '../includes/sidebar.php' ?>
    <!--sidebar-menu-->

    <div id="content">
        <div id="content-header">
            <div id="breadcrumb"> <a href="index.php" title="Go to Home" class="tip-bottom"><i class="fas fa-home"></i> Home</a> <a href="#" class="current">Notifications</a> </div>
        </div>
        <div class="container-fluid notify-shell">
            <hr>
            <div class="row-fluid">
                <div class="span12">
                    <div class="widget-box notify-card polish-card">
                        <div class="widget-title"> <span class="icon"> <i class="icon-bullhorn"></i> </span>
                            <h5>My Notifications</h5>
                        </div>
                        <div class="widget-content nopadding">
                            <ul class="activity-list">
                                <?php
                                include "dbcon.php";
                                $user_id = $_SESSION['user_id'];
                                $qry = "SELECT * FROM notifications WHERE member_id='$user_id' ORDER BY sent_date DESC";
                                $result = mysqli_query($con, $qry);

                                if (mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_array($result)) {
                                        echo "<li>";
                                        echo "<div class='activity-item'>";
                                        echo "<div class='activity-icon'><i class='fas fa-comment-dots'></i></div>";
                                        echo "<div class='activity-copy'>";
                                        echo "<div class='activity-title'>New Message</div>";
                                        echo "<div>" . htmlspecialchars($row['message']) . "</div>";
                                        echo "<span class='activity-date'>" . htmlspecialchars($row['sent_date']) . "</span>";
                                        echo "</div>";
                                        echo "</div>";
                                        echo "</li>";
                                    }
                                } else {
                                    echo "<li><div class='alert alert-info'>No notifications found.</div></li>";
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--Footer-part-->
    <div class="row-fluid">
        <div id="footer" class="span12"> <?php echo date("Y"); ?> &copy; M * A GYM System Developed By Abdikafi</div>
    </div>

    <style>
        #footer {
            color: white;
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