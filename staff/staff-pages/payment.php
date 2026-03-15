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
  <title>M * A GYM System</title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../../css/bootstrap.min.css" />
  <link rel="stylesheet" href="../../css/bootstrap-responsive.min.css" />
  <link rel="stylesheet" href="../../css/fullcalendar.css" />
  <link rel="stylesheet" href="../../css/matrix-style.css" />
  <link rel="stylesheet" href="../../css/matrix-media.css" />
  <link rel="stylesheet" href="../../css/system-polish.css" />
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

  <!--start-top-serch-->
  <!-- <div id="search">
  <input type="hidden" placeholder="Search here..."/>
  <button type="submit" class="tip-bottom" title="Search"><i class="icon-search icon-white"></i></button>
</div> -->
  <!--close-top-serch-->
  <!--sidebar-menu-->
  <?php $page = "payment";
  include '../includes/sidebar.php' ?>
  <!--sidebar-menu-->

  <div id="content">
    <div id="content-header">
      <div id="breadcrumb"> <a href="index.php" title="Tag Bogga Hore" class="tip-bottom"><i class="fas fa-home"></i> Bogga Hore</a> <a href="payment.php" class="current">Lacag Bixinta</a> </div>
      <h1 class="text-center">Lacag Bixinta Xubnaha Diiwaangashan <i class="fas fa-group"></i></h1>
    </div>
    <div class="container-fluid payment-shell">
      <hr>
      <div class="row-fluid">
        <div class="span12">

          <div class='widget-box'>
            <div class='widget-title'> <span class='icon'> <i class='fas fa-th'></i> </span>
              <h5>Jadwalka Lacag Bixinta Xubnaha</h5>
              <div class="payment-mode-switch pull-right">
                <button type="button" id="modeReadable" class="btn btn-mini btn-primary active">Akhris Fudud</button>
                <button type="button" id="modeCompact" class="btn btn-mini">11 Column</button>
              </div>
              <a href="export-payment-history.php" class="btn btn-info btn-mini pull-right" style="margin:7px 10px 0 0;"><i class="icon-download-alt"></i> Export CSV</a>
              <form id="custom-search-form" role="search" method="POST" action="search-result.php" class="form-search form-horizontal pull-right" onsubmit="showLoadingOverlay()">
                <div class="input-append span12">
                  <input type="text" class="search-query" placeholder="Raadi" name="search" required>
                  <button type="submit" class="btn"><i class="icon-search"></i></button>
                </div>
              </form>
            </div>
            <div class='widget-content nopadding'>

              <?php

              include "dbcon.php";
              $branch_id = $_SESSION['branch_id'];
              $qry = "SELECT * FROM members WHERE branch_id = '$branch_id'";
              $cnt = 1;
              $result = mysqli_query($con, $qry);


              echo "<div id='paymentTableWrap' class='payment-fit-wrap mode-readable'>";
              echo "<table class='table table-bordered table-hover payment-fullview'>
        <thead>
          <tr>
            <th><span class='th-long'>#</span><span class='th-short'>#</span></th>
            <th><span class='th-long'>Xubinta</span><span class='th-short'>Magac</span></th>
            <th><span class='th-long'>Taariikhda Lacagta u dambaysay</span><span class='th-short'>Date</span></th>
            <th><span class='th-long'>Lacagta Wadarta</span><span class='th-short'>Total</span></th>
            <th><span class='th-long'>Dhimista</span><span class='th-short'>Disc</span></th>
            <th><span class='th-long'>La Bixiyay</span><span class='th-short'>Paid</span></th>
            <th><span class='th-long'>Haraaga</span><span class='th-short'>Bal</span></th>
            <th><span class='th-long'>Adeegga La Doortay</span><span class='th-short'>Srv</span></th>
            <th><span class='th-long'>Qorshaha</span><span class='th-short'>Plan</span></th>
            <th><span class='th-long'>Falka</span><span class='th-short'>Pay</span></th>
            <th><span class='th-long'>Xusuusin</span><span class='th-short'>Rem</span></th>
          </tr>
        </thead><tbody>";

              while ($row = mysqli_fetch_array($result)) {
                $discount = $row['discount_amount'];
                $paid = $row['paid_amount'];
                $amount = $row['amount']; // Net amount (what they owe)
                $total = $amount + $discount; // Gross amount (before discount)
                $balance = $amount - $paid;
              ?>
                  <tr>
                    <td>
                      <div class='text-center'><?php echo $cnt; ?></div>
                    </td>
                    <td>
                      <div class='text-center'><?php echo $row['fullname'] ?></div>
                    </td>
                    <td>
                      <div class='text-center'><?php echo ($row['paid_date'] == 0 ? "Xubin Cusub" : $row['paid_date']) ?></div>
                    </td>
                    <td>
                      <div class='text-center'><strong><?php echo '$' . number_format($total, 2) ?></strong></div>
                    </td>
                    <td>
                      <div class='text-center' style="color: #be185d;"><?php echo '$' . number_format($discount, 2) ?></div>
                    </td>
                    <td>
                      <div class='text-center' style="color: #16a34a;"><strong><?php echo '$' . number_format($paid, 2) ?></strong></div>
                    </td>
                    <td>
                      <div class='text-center' style="color: #dc2626;"><strong><?php echo '$' . number_format($balance, 2) ?></strong></div>
                    </td>
                    <td>
                      <div class='text-center'><?php echo $row['services'] ?></div>
                    </td>
                    <td>
                      <div class='text-center'><?php echo $row['plan'] . " Bilood" ?></div>
                    </td>
                    <td>
                      <div class='text-center'><a href='user-payment.php?id=<?php echo $row['user_id'] ?>'><button class='btn btn-success btn'><i class='fas fa-dollar-sign'></i><span class='btn-label'> Bixi Lacagta</span></button></a></div>
                    </td>
                    <td>
                      <div class='text-center'><a href='sendReminder.php?id=<?php echo $row['user_id'] ?>'><button class='btn btn-danger btn' <?php echo ($row['reminder'] == 1 ? "disabled" : "") ?>><i class='fas fa-bell'></i><span class='btn-label'> Digniin</span></button></a></div>
                    </td>
                  </tr>
              <?php $cnt++;
              }

              if ($cnt === 1) {
                echo "<tr><td colspan='11'><div class='text-center' style='padding:18px;color:#64748b;'>Weli xog lacageed lama helin.</div></td></tr>";
              }

              echo "</tbody></table></div>";

              ?>
            </div>
          </div>



        </div>
      </div>
    </div>
  </div>

  <!--end-main-container-part-->

  <!--Footer-part-->

  <div class="row-fluid">
    <div id="footer" class="span12"> <?php echo date("Y"); ?> &copy; M * A GYM System Developed By Abdikafi</a> </div>
  </div>

  <style>
    #footer {
      color: white;
    }
  </style>

  <!--end-Footer-part-->

  <style>
    .payment-shell .payment-mode-switch {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      margin: 7px 10px 0 0;
    }

    .payment-shell .payment-mode-switch .btn {
      border-radius: 999px;
      padding: 5px 10px;
      min-height: 28px;
      font-size: 11px;
      line-height: 1;
    }

    .payment-shell .payment-mode-switch .btn.active {
      box-shadow: 0 6px 18px rgba(37, 99, 235, 0.25);
    }

    .payment-shell .widget-box {
      border-radius: 18px;
      overflow: hidden;
      box-shadow: 0 14px 30px rgba(15, 23, 42, 0.08);
      border: 1px solid #e2e8f0;
    }

    .payment-shell .widget-title {
      background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    }

    .payment-shell .widget-title h5 {
      font-weight: 800;
      color: #0f172a;
    }

    .payment-shell .table td,
    .payment-shell .table th {
      vertical-align: middle;
      white-space: nowrap;
    }

    .payment-shell .table th {
      font-size: 11px;
      letter-spacing: 0.01em;
    }

    .payment-shell .table th .th-short {
      display: none;
    }

    .payment-shell .table td {
      font-size: 12px;
    }

    .payment-shell .btn-success,
    .payment-shell .btn-danger,
    .payment-shell .btn-info {
      border-radius: 999px;
      font-weight: 700;
    }

    #custom-search-form {
      margin: 0;
      margin-top: 5px;
      padding: 0;
    }

    #custom-search-form .search-query {
      padding-right: 3px;
      padding-right: 4px \9;
      padding-left: 3px;
      padding-left: 4px \9;
      /* IE7-8 doesn't have border-radius, so don't indent the padding */

      margin-bottom: 0;
      -webkit-border-radius: 3px;
      -moz-border-radius: 3px;
      border-radius: 3px;
    }

    #custom-search-form button {
      border: 0;
      background: none;
      /** belows styles are working good */
      padding: 2px 5px;
      margin-top: 2px;
      position: relative;
      left: -28px;
      /* IE7-8 doesn't have border-radius, so don't indent the padding */
      margin-bottom: 0;
      -webkit-border-radius: 3px;
      -moz-border-radius: 3px;
      border-radius: 3px;
    }

    .search-query:focus+button {
      z-index: 3;
    }

    @media (max-width: 767px) {
      .payment-shell {
        padding-bottom: 6px;
      }

      .payment-shell .payment-mode-switch {
        float: none;
        margin: 0 12px 8px;
        width: calc(100% - 24px);
        justify-content: flex-start;
      }

      .widget-title {
        min-height: 0;
        padding-bottom: 12px;
      }

      .widget-title h5 {
        float: none;
        display: block;
        margin-right: 0;
        margin-bottom: 10px;
      }

      .widget-title .pull-right {
        float: none;
        display: block;
        margin: 10px 12px 0 !important;
      }

      .payment-shell .widget-content.nopadding {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
      }

      .payment-shell .payment-fit-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
      }

      .payment-shell .payment-fit-wrap.mode-readable table.payment-fullview {
        min-width: 1320px !important;
        width: 1320px !important;
        table-layout: auto;
      }

      .payment-shell .payment-fit-wrap.mode-readable table.payment-fullview th,
      .payment-shell .payment-fit-wrap.mode-readable table.payment-fullview td {
        padding: 6px 7px;
        font-size: 11px;
        line-height: 1.25;
        white-space: nowrap;
      }

      .payment-shell .payment-fit-wrap.mode-compact {
        overflow-x: hidden;
      }

      .payment-shell .payment-fit-wrap.mode-compact table.payment-fullview {
        min-width: 100% !important;
        width: 100% !important;
        table-layout: fixed;
      }

      .payment-shell .payment-fit-wrap.mode-compact table.payment-fullview th,
      .payment-shell .payment-fit-wrap.mode-compact table.payment-fullview td {
        padding: 2px 1px;
        font-size: 7px;
        line-height: 1.05;
        white-space: normal;
        word-break: break-word;
        overflow-wrap: anywhere;
      }

      .payment-shell .payment-fit-wrap.mode-compact table.payment-fullview th:nth-child(1),
      .payment-shell .payment-fit-wrap.mode-compact table.payment-fullview td:nth-child(1) { width: 4%; }
      .payment-shell .payment-fit-wrap.mode-compact table.payment-fullview th:nth-child(2),
      .payment-shell .payment-fit-wrap.mode-compact table.payment-fullview td:nth-child(2) { width: 6%; }
      .payment-shell .payment-fit-wrap.mode-compact table.payment-fullview th:nth-child(3),
      .payment-shell .payment-fit-wrap.mode-compact table.payment-fullview td:nth-child(3) { width: 13%; }
      .payment-shell .payment-fit-wrap.mode-compact table.payment-fullview th:nth-child(4),
      .payment-shell .payment-fit-wrap.mode-compact table.payment-fullview td:nth-child(4) { width: 10%; }
      .payment-shell .payment-fit-wrap.mode-compact table.payment-fullview th:nth-child(5),
      .payment-shell .payment-fit-wrap.mode-compact table.payment-fullview td:nth-child(5) { width: 8%; }
      .payment-shell .payment-fit-wrap.mode-compact table.payment-fullview th:nth-child(6),
      .payment-shell .payment-fit-wrap.mode-compact table.payment-fullview td:nth-child(6) { width: 7%; }
      .payment-shell .payment-fit-wrap.mode-compact table.payment-fullview th:nth-child(7),
      .payment-shell .payment-fit-wrap.mode-compact table.payment-fullview td:nth-child(7) { width: 8%; }
      .payment-shell .payment-fit-wrap.mode-compact table.payment-fullview th:nth-child(8),
      .payment-shell .payment-fit-wrap.mode-compact table.payment-fullview td:nth-child(8) { width: 7%; }
      .payment-shell .payment-fit-wrap.mode-compact table.payment-fullview th:nth-child(9),
      .payment-shell .payment-fit-wrap.mode-compact table.payment-fullview td:nth-child(9) { width: 7%; }
      .payment-shell .payment-fit-wrap.mode-compact table.payment-fullview th:nth-child(10),
      .payment-shell .payment-fit-wrap.mode-compact table.payment-fullview td:nth-child(10) { width: 7%; }
      .payment-shell .payment-fit-wrap.mode-compact table.payment-fullview th:nth-child(11),
      .payment-shell .payment-fit-wrap.mode-compact table.payment-fullview td:nth-child(11) { width: 7%; }
      .payment-shell .payment-fit-wrap.mode-compact table.payment-fullview th:nth-child(12),
      .payment-shell .payment-fit-wrap.mode-compact table.payment-fullview td:nth-child(12) { width: 8%; }
      .payment-shell .payment-fit-wrap.mode-compact table.payment-fullview th:nth-child(13),
      .payment-shell .payment-fit-wrap.mode-compact table.payment-fullview td:nth-child(13) { width: 8%; }

      .payment-shell .payment-fit-wrap.mode-compact table.payment-fullview .text-center {
        text-align: center;
      }

      .payment-shell .payment-fit-wrap.mode-compact table.payment-fullview img {
        width: 22px !important;
        height: 22px !important;
      }

      .payment-shell .payment-fit-wrap.mode-compact table.payment-fullview .btn {
        min-height: 18px;
        padding: 1px 2px;
        font-size: 7px;
        border-radius: 6px;
        width: 100%;
        white-space: nowrap;
      }

      .payment-shell .payment-fit-wrap.mode-compact table.payment-fullview .btn .btn-label {
        display: none;
      }

      .payment-shell .payment-fit-wrap.mode-compact table.payment-fullview .btn i {
        font-size: 7px;
        margin: 0;
      }

      .payment-shell .payment-fit-wrap.mode-compact table.payment-fullview th .th-long {
        display: none;
      }

      .payment-shell .payment-fit-wrap.mode-compact table.payment-fullview th .th-short {
        display: inline;
      }

      .payment-shell .btn {
        min-height: 32px;
        padding: 4px 8px;
        font-size: 11px;
        line-height: 1.2;
      }

      #custom-search-form {
        width: auto;
      }

      #custom-search-form .input-append {
        display: flex;
        width: 100%;
      }

      #custom-search-form .search-query {
        flex: 1 1 auto;
        width: auto;
        min-height: 36px;
      }

      #custom-search-form button {
        left: auto;
        margin-top: 0;
        padding: 0 12px;
      }
    }
  </style>

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
  <script src="../../js/toast-helper.js"></script>

  <div id="loadingOverlay" class="loading-overlay"><div class="loading-box">Fadlan sug... Raadin socota</div></div>

  <script type="text/javascript">
    function showLoadingOverlay() {
      var overlay = document.getElementById('loadingOverlay');
      if (overlay) overlay.style.display = 'flex';
    }

    (function() {
      var params = new URLSearchParams(window.location.search);
      var msg = params.get('msg');
      var type = params.get('type') || 'success';
      if (msg && typeof showToast === 'function') {
        showToast(decodeURIComponent(msg), type);
      }
    })();

    (function() {
      var wrap = document.getElementById('paymentTableWrap');
      var readableBtn = document.getElementById('modeReadable');
      var compactBtn = document.getElementById('modeCompact');
      if (!wrap || !readableBtn || !compactBtn) {
        return;
      }

      function setMode(mode) {
        var isCompact = mode === 'compact';
        wrap.classList.toggle('mode-compact', isCompact);
        wrap.classList.toggle('mode-readable', !isCompact);
        readableBtn.classList.toggle('active', !isCompact);
        readableBtn.classList.toggle('btn-primary', !isCompact);
        compactBtn.classList.toggle('active', isCompact);
        compactBtn.classList.toggle('btn-primary', isCompact);
        try {
          localStorage.setItem('staffPaymentTableMode', isCompact ? 'compact' : 'readable');
        } catch (e) {}
      }

      readableBtn.addEventListener('click', function() {
        setMode('readable');
      });

      compactBtn.addEventListener('click', function() {
        setMode('compact');
      });

      var saved = null;
      try {
        saved = localStorage.getItem('staffPaymentTableMode');
      } catch (e) {}
      setMode(saved === 'compact' ? 'compact' : 'readable');
    })();

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