<?php
session_start();
//the isset function to check username is already loged in and stored on the session
if (!isset($_SESSION['user_id'])) {
  header('location:../index.php');
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
  <style>
    .equip-page-hero {
      margin: 0 20px 18px;
      padding: 22px 24px;
      border-radius: 24px;
      background: linear-gradient(135deg, #0f172a 0%, #1d4ed8 55%, #60a5fa 100%);
      color: #fff;
      box-shadow: 0 20px 45px rgba(29, 78, 216, 0.24);
    }

    .equip-page-hero h2 {
      margin: 0 0 8px;
      font-size: 28px;
      line-height: 1.15;
    }

    .equip-page-hero p {
      margin: 0;
      color: rgba(255,255,255,.88);
    }

    .equip-summary-grid {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 14px;
      margin: 18px 20px 0;
    }

    .equip-summary-card {
      background: #fff;
      border-radius: 20px;
      padding: 16px 18px;
      border: 1px solid #dbe4f0;
      box-shadow: 0 14px 30px rgba(15, 23, 42, 0.07);
    }

    .equip-summary-label {
      font-size: 12px;
      color: #64748b;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .5px;
    }

    .equip-summary-value {
      margin-top: 6px;
      font-size: 28px;
      color: #0f172a;
      font-weight: 800;
      line-height: 1;
    }

    .equip-toolbar {
      display: flex;
      gap: 12px;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      padding: 18px 20px 0;
    }

    .equip-search-wrap {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      flex: 1;
    }

    .equip-search,
    .equip-filter {
      min-height: 44px;
      border-radius: 14px;
      border: 1px solid #cbd5e1;
      padding: 10px 14px;
      background: #fff;
      color: #0f172a;
      box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.04);
    }

    .equip-search {
      min-width: 260px;
      flex: 1;
    }

    .equip-toolbar-actions {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    .equip-toolbar-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 11px 16px;
      border-radius: 999px;
      text-decoration: none !important;
      font-weight: 700;
      font-size: 13px;
    }

    .equip-toolbar-btn.primary {
      background: #2563eb;
      color: #fff;
      box-shadow: 0 12px 22px rgba(37, 99, 235, 0.24);
    }

    .equip-toolbar-btn.light {
      background: #e2e8f0;
      color: #0f172a;
    }

    .equip-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 22px;
      padding: 20px;
      background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);
    }

    .equip-card {
      position: relative;
      background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
      border: 1px solid #dbe4f0;
      border-radius: 28px;
      padding: 20px;
      box-shadow: 0 18px 40px rgba(15, 23, 42, 0.09);
      transition: all .35s cubic-bezier(.175, .885, .32, 1.275);
      overflow: hidden;
      animation: equipIn .45s ease both;
    }

    .equip-card:hover {
      transform: translateY(-8px) scale(1.02);
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.18);
    }

    .equip-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 120px;
      background: linear-gradient(135deg, #eff6ff 0%, #e0f2fe 45%, #ede9fe 100%);
      opacity: 1;
    }

    .equip-title {
      font-size: 17px;
      font-weight: 800;
      color: #0f172a;
      margin-bottom: 6px;
      line-height: 1.2;
    }

    .equip-subtitle {
      font-size: 12px;
      color: #64748b;
      font-weight: 700;
    }

    .equip-meta {
      font-size: 13px;
      color: #4b5563;
      margin: 6px 0;
      line-height: 1.55;
    }

    .equip-thumb {
      width: 96px;
      height: 96px;
      border-radius: 22px;
      object-fit: cover;
      border: 4px solid #fff;
      box-shadow: 0 14px 28px rgba(15, 23, 42, 0.12);
      background: #e5e7eb;
      margin-right: 12px;
      flex-shrink: 0;
    }

    .equip-head {
      display: flex;
      align-items: center;
      margin-bottom: 16px;
      gap: 14px;
      position: relative;
      z-index: 1;
    }

    .equip-body {
      padding: 14px 0 4px;
      border-top: 1px solid #edf2f7;
      position: relative;
      z-index: 1;
    }

    .equip-badges {
      margin-top: 10px;
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
    }

    .equip-actions {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 10px;
      margin-top: 18px;
    }

    .equip-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      border-radius: 16px;
      padding: 11px 12px;
      font-size: 12px;
      font-weight: 700;
      text-decoration: none !important;
      transition: all .2s ease;
      border: 1px solid transparent;
      box-shadow: 0 10px 18px rgba(15, 23, 42, 0.06);
    }

    .equip-btn.view { background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #1d4ed8; border-color: #bfdbfe; }
    .equip-btn.edit { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #b45309; border-color: #fde68a; }
    .equip-btn.delete { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #b91c1c; border-color: #fecaca; }
    .equip-btn:hover { transform: translateY(-2px); opacity: .96; }

    .equip-badge {
      display: inline-block;
      border-radius: 999px;
      padding: 6px 11px;
      font-size: 12px;
      font-weight: 700;
      border: 1px solid #e5e7eb;
      background: #eef2ff;
      color: #3730a3;
      box-shadow: inset 0 1px 0 rgba(255,255,255,.6);
    }

    .equip-badge.status-new {
      background: #dcfce7;
      color: #166534;
      border-color: #bbf7d0;
    }

    .equip-badge.status-used {
      background: #fef3c7;
      color: #92400e;
      border-color: #fde68a;
    }

    .equip-badge.status-maintenance {
      background: #fee2e2;
      color: #b91c1c;
      border-color: #fecaca;
    }

    .empty-equip-link {
      display: inline-flex;
      margin-top: 12px;
      align-items: center;
      gap: 8px;
      padding: 10px 14px;
      border-radius: 999px;
      background: #dbeafe;
      color: #1d4ed8;
      font-weight: 700;
      text-decoration: none !important;
    }

    @media (max-width: 991px) {
      .equip-summary-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }

    @media (max-width: 767px) {
      .equip-page-hero,
      .equip-summary-grid,
      .equip-toolbar {
        margin-left: 10px;
        margin-right: 10px;
      }

      .equip-page-hero {
        padding: 18px;
        border-radius: 18px;
      }

      .equip-page-hero h2 {
        font-size: 22px;
      }

      .equip-summary-grid {
        grid-template-columns: 1fr;
      }

      .equip-grid {
        grid-template-columns: 1fr;
        padding: 12px;
      }

      .equip-search-wrap,
      .equip-toolbar-actions {
        width: 100%;
      }

      .equip-search,
      .equip-filter,
      .equip-toolbar-btn {
        width: 100%;
      }

      .equip-head {
        align-items: flex-start;
      }

      .equip-actions {
        grid-template-columns: 1fr;
      }

      .equip-btn {
        width: 100%;
      }
    }

    @keyframes equipIn {
      from {
        opacity: 0;
        transform: translateY(14px) scale(.98);
      }

      to {
        opacity: 1;
        transform: translateY(0) scale(1);
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
  <!--start-top-serch-->
  <!-- <div id="search">
  <input type="hidden" placeholder="Search here..."/>
  <button type="submit" class="tip-bottom" title="Search"><i class="icon-search icon-white"></i></button>
</div> -->
  <!--close-top-serch-->

  <!--sidebar-menu-->
  <?php $page = 'list-equip';
  include 'includes/sidebar.php' ?>
  <!--sidebar-menu-->

  <div id="content">
    <div id="content-header">
      <div id="breadcrumb"> <a href="index.php" title="Go to Home" class="tip-bottom"><i class="fas fa-home"></i> Home</a> <a href="equipment.php" class="current">Equipment</a> </div>
      <h1 class="text-center">GYM Equipment List <i class="fas fa-cogs"></i></h1>
    </div>
    <div class="container-fluid">
      <hr>
      <div class="equip-page-hero">
        <h2><i class="fas fa-dumbbell"></i> Gym Equipment Center</h2>
        <p>Manage gym equipment with a modern list view, full action buttons, and responsive design for both mobile and desktop.</p>
      </div>
      <div class="row-fluid">
        <div class="span12">

          <div class='widget-box'>
            <div class='widget-title'> <span class='icon'> <i class='fas fa-cogs'></i> </span>
              <h5>Equipment Table</h5>
            </div>
            <div class='widget-content nopadding'>

              <?php

              include "dbcon.php";
              mysqli_query($con, "ALTER TABLE equipment ADD COLUMN IF NOT EXISTS photo VARCHAR(255) NULL");
              mysqli_query($con, "ALTER TABLE equipment ADD COLUMN IF NOT EXISTS status VARCHAR(100) NOT NULL DEFAULT 'Brand New'");
              $branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : 0;
              $branch_where = $branch_id > 0 ? " WHERE branch_id = " . $branch_id : "";
              $qry = "select * from equipment" . $branch_where . " ORDER BY date DESC, id DESC";
              $cnt = 1;
              $result = mysqli_query($con, $qry);
              $equipment_rows = array();
              $total_equipment = 0;
              $brand_new_count = 0;
              $used_count = 0;
              $maintenance_count = 0;

              if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                  $equipment_rows[] = $row;
                  $total_equipment++;
                  $row_status = !empty($row['status']) ? $row['status'] : 'Brand New';
                  if ($row_status === 'Used') {
                    $used_count++;
                  } elseif ($row_status === 'Maintenance Required') {
                    $maintenance_count++;
                  } else {
                    $brand_new_count++;
                  }
                }
              }

              echo "<div class='equip-summary-grid'>";
              echo "<div class='equip-summary-card'><div class='equip-summary-label'>Total Equipment</div><div class='equip-summary-value'>" . $total_equipment . "</div></div>";
              echo "<div class='equip-summary-card'><div class='equip-summary-label'>Brand New</div><div class='equip-summary-value'>" . $brand_new_count . "</div></div>";
              echo "<div class='equip-summary-card'><div class='equip-summary-label'>Used</div><div class='equip-summary-value'>" . $used_count . "</div></div>";
              echo "<div class='equip-summary-card'><div class='equip-summary-label'>Maintenance</div><div class='equip-summary-value'>" . $maintenance_count . "</div></div>";
              echo "</div>";

              echo "<div class='equip-toolbar'>";
              echo "<div class='equip-search-wrap'>";
              echo "<input type='text' id='equipment-search' class='equip-search' placeholder='Search equipment by name, vendor, address or contact'>";
              echo "<select id='equipment-status-filter' class='equip-filter'>";
              echo "<option value='all'>All Status</option>";
              echo "<option value='Brand New'>Brand New</option>";
              echo "<option value='Used'>Used</option>";
              echo "<option value='Maintenance Required'>Maintenance Required</option>";
              echo "</select>";
              echo "</div>";
              echo "<div class='equip-toolbar-actions'>";
              echo "<a class='equip-toolbar-btn primary' href='equipment-entry.php'><i class='fas fa-plus-circle'></i> Add Equipment</a>";
              echo "<a class='equip-toolbar-btn light' href='equipment.php'><i class='fas fa-rotate-right'></i> Reset</a>";
              echo "</div>";
              echo "</div>";

              echo "<div class='equip-grid'>";
              if (!empty($equipment_rows)) {
                foreach ($equipment_rows as $row) {
                  $photo = !empty($row['photo']) ? '../img/equipment/' . $row['photo'] : '../img/dumbbell.png';
                  $status_text = !empty($row['status']) ? $row['status'] : 'Brand New';
                  $status_class = 'status-new';
                  if ($status_text === 'Used') {
                    $status_class = 'status-used';
                  } elseif ($status_text === 'Maintenance Required') {
                    $status_class = 'status-maintenance';
                  }
                  echo "<div class='equip-card equipment-item' data-status='" . htmlspecialchars($status_text, ENT_QUOTES) . "' data-search='" . htmlspecialchars(strtolower(($row['name'] ?? '') . ' ' . ($row['vendor'] ?? '') . ' ' . ($row['address'] ?? '') . ' ' . ($row['contact'] ?? '')), ENT_QUOTES) . "'>";
                  echo "<div class='equip-head'>";
                  echo "<img class='equip-thumb' src='" . htmlspecialchars($photo) . "' alt='Equipment' onerror=\"this.src='../img/dumbbell.png'\">";
                  echo "<div><div class='equip-title'>#" . $cnt . " - " . htmlspecialchars($row['name']) . "</div><div class='equip-subtitle'>" . htmlspecialchars($row['vendor']) . "</div></div>";
                  echo "</div>";
                  echo "<div class='equip-body'>";
                  echo "<div class='equip-meta'><strong>Description:</strong> " . htmlspecialchars($row['description']) . "</div>";
                  echo "<div class='equip-meta'><strong>Vendor:</strong> " . htmlspecialchars($row['vendor']) . "</div>";
                  echo "<div class='equip-meta'><strong>Address:</strong> " . htmlspecialchars($row['address']) . "</div>";
                  echo "<div class='equip-meta'><strong>Contact:</strong> " . htmlspecialchars($row['contact']) . "</div>";
                  echo "<div class='equip-meta'><strong>Date of Purchase:</strong> " . htmlspecialchars($row['date']) . "</div>";
                  echo "<div class='equip-badges'>";
                  echo "<span class='equip-badge " . $status_class . "'>Status: " . htmlspecialchars($status_text) . "</span>";
                  echo "<span class='equip-badge'>Qty: " . (int)$row['quantity'] . "</span>";
                  echo "<span class='equip-badge'>Cost: $" . number_format((float)$row['amount'], 2) . "</span>";
                  if (isset($row['branch_id']) && $row['branch_id'] > 0) {
                    $bid = $row['branch_id'];
                    $br_res = mysqli_query($con, "SELECT branch_name FROM branches WHERE id='$bid'");
                    $br_row = mysqli_fetch_assoc($br_res);
                    $bname = $br_row ? $br_row['branch_name'] : 'Unknown';
                    echo "<span class='equip-badge' style='background:#fef2f2; color:#991b1b; border-color:#fee2e2;'><i class='fas fa-building'></i> " . htmlspecialchars($bname) . "</span>";
                  }
                  echo "</div>";
                  echo "<div class='equip-actions'>";
                  echo "<a class='equip-btn view' href='view-equipment.php?id=" . (int)$row['id'] . "'><i class='fas fa-eye'></i> View</a>";
                  echo "<a class='equip-btn edit' href='edit-equipmentform.php?id=" . (int)$row['id'] . "'><i class='fas fa-pen'></i> Update</a>";
                  echo "<a class='equip-btn delete' href='actions/delete-equipment.php?id=" . (int)$row['id'] . "' onclick=\"return confirm('Are you sure you want to delete this equipment?')\"><i class='fas fa-trash'></i> Delete</a>";
                  echo "</div>";
                  echo "</div>";
                  echo "</div>";
                  $cnt++;
                }
              } else {
                echo "<div class='equip-card'><div class='equip-title'>No Equipment Found</div><div class='equip-meta'>Please add new equipment.</div><a class='empty-equip-link' href='equipment-entry.php'><i class='fas fa-plus-circle'></i> Add Equipment</a></div>";
              }
              echo "</div>";
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
    <div id="footer" class="span12"> <?php echo date("Y"); ?> &copy; M*A GYM System Developed By Abdikafi </div>
  </div>

  <style>
    #footer {
      color: white;
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

    (function() {
      var searchInput = document.getElementById('equipment-search');
      var statusFilter = document.getElementById('equipment-status-filter');
      var cards = document.querySelectorAll('.equipment-item');

      function filterEquipment() {
        var term = searchInput ? searchInput.value.toLowerCase().trim() : '';
        var status = statusFilter ? statusFilter.value : 'all';

        Array.prototype.forEach.call(cards, function(card) {
          var haystack = card.getAttribute('data-search') || '';
          var cardStatus = card.getAttribute('data-status') || '';
          var matchesText = term === '' || haystack.indexOf(term) !== -1;
          var matchesStatus = status === 'all' || cardStatus === status;
          card.style.display = matchesText && matchesStatus ? '' : 'none';
        });
      }

      if (searchInput) {
        searchInput.addEventListener('input', filterEquipment);
      }

      if (statusFilter) {
        statusFilter.addEventListener('change', filterEquipment);
      }
    })();
  </script>
</body>

</html>
