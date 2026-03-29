<?php
session_start();
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
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
  <link rel="stylesheet" href="../css/uniform.css" />
  <link rel="stylesheet" href="../css/select2.css" />
  <link rel="stylesheet" href="../css/matrix-style.css" />
  <link rel="stylesheet" href="../css/matrix-media.css" />
  <link rel="stylesheet" href="../css/system-polish.css" />
  <link href="../font-awesome/css/fontawesome.css" rel="stylesheet" />
  <link href="../font-awesome/css/all.css" rel="stylesheet" />
  <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
</head>

<body>

  <!--Header-part-->
  <?php include 'includes/header-content.php'; ?>
  <!--close-Header-part-->


  <!--top-Header-menu-->
  <?php include 'includes/topheader.php' ?>
  <!--close-top-Header-menu-->
  
  <!--sidebar-menu-->
  <?php $page = 'staff-management';
  include 'includes/sidebar.php' ?>
  <!--sidebar-menu-->

  <div id="content">
    <div id="content-header">
      <div id="breadcrumb"> <a href="index.php" title="Go to Home Page" class="tip-bottom"><i class="fas fa-home"></i> Home</a> <a href="staffs.php" class="current">Staff Members</a> </div>
      <h1 class="text-center">GYM Staff List <i class="fas fa-briefcase"></i></h1>
    </div>
    <div class="container-fluid">
      <hr>
      <div class="row-fluid">
        <div class="span12">
          <a href="staffs-entry.php"><button class="btn btn-primary">Add Staff Member</button></a>
          <!-- Search/Filter Bar for Staff -->
          <div class="row-fluid">
            <div class="span6 offset3">
              <form id="staffSearchForm" class="form-inline" style="margin-bottom: 18px; display: flex; gap: 10px; align-items: center;">
                <input type="text" id="staffSearchInput" class="form-control" placeholder="Search by name, phone, or designation..." style="flex: 1; min-width: 180px;" />
                <button type="button" class="btn btn-info" onclick="filterStaff()"><i class="fas fa-search"></i> Search</button>
                <button type="button" class="btn btn-secondary" onclick="resetStaffFilter()"><i class="fas fa-undo"></i> Reset</button>
              </form>
            </div>
          </div>
          <div class='widget-box'>
            <div class='widget-title'> <span class='icon'> <i class='fas fa-th'></i> </span>
              <h5>Staff Schedule</h5>
            </div>
            <style>
              .staff-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
                gap: 25px;
                padding: 24px;
                background: #f1f5f9;
              }

              .staff-card {
                background: #fff;
                border-radius: 16px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
                padding: 30px 24px;
                border: 1px solid #e2e8f0;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
              }

              .staff-card:hover {
                transform: translateY(-8px);
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
                border-color: #3b82f6;
              }

              .staff-avatar {
                width: 100px;
                height: 100px;
                border-radius: 50%;
                background: #f8fafc;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 45px;
                color: #94a3b8;
                margin-bottom: 20px;
                border: 4px solid #fff;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                overflow: hidden;
              }

              .staff-avatar img {
                width: 100%;
                height: 100%;
                object-fit: cover;
              }

              .staff-basic-info h4 {
                margin: 0;
                color: #1e293b;
                font-size: 20px;
                font-weight: 800;
              }

              .staff-basic-info p {
                margin: 4px 0 0 0;
                color: #3b82f6;
                font-size: 14px;
                font-weight: 700;
              }

              .designation-badge {
                display: inline-block;
                padding: 6px 14px;
                background: #eff6ff;
                color: #1d4ed8;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 700;
                margin: 15px 0;
                text-transform: uppercase;
                letter-spacing: 0.5px;
              }

              .staff-details-list {
                width: 100%;
                margin: 20px 0;
                padding-top: 15px;
                border-top: 1px dashed #e2e8f0;
                text-align: left;
              }

              .staff-detail-row {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 10px;
                margin-bottom: 10px;
                font-size: 13px;
              }

              .detail-label {
                color: #94a3b8;
                font-weight: 600;
              }

              .detail-value {
                color: #475569;
                font-weight: 700;
                text-align: right;
                word-break: break-word;
              }

              .staff-actions {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 14px;
                width: 100%;
                padding-top: 22px;
                border-top: 1px solid #f1f5f9;
              }

              .staff-action-btn {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                padding: 10px;
                border-radius: 10px;
                font-size: 12px;
                font-weight: 700;
                text-decoration: none !important;
                transition: all 0.2s;
                border: none;
                cursor: pointer;
                box-shadow: 0 6px 14px rgba(15, 23, 42, 0.12);
              }

              .staff-action-btn:hover {
                transform: translateY(-1px);
                box-shadow: 0 10px 20px rgba(15, 23, 42, 0.16);
              }

              .staff-action-btn.btn-details {
                grid-column: auto;
              }

              .btn-whatsapp {
                background: #e7f8ef;
                color: #166534;
              }

              .btn-whatsapp:hover {
                background: #dcf2e7;
              }

              .btn-details {
                background: #e8f0ff;
                color: #1e3a8a;
              }

              .btn-details:hover {
                background: #dbe7ff;
              }

              .staff-details-modal {
                width: 600px;
                margin-left: 0;
                border-radius: 12px;
                overflow: hidden;
                position: fixed !important;
                left: 50% !important;
                top: 50% !important;
                transform: translate(-50%, -50%);
                max-height: 90vh;
                z-index: 1055;
                max-width: calc(100vw - 40px);
              }

              .staff-details-modal.in {
                display: block !important;
                opacity: 1;
              }

              .modal-backdrop.in {
                opacity: 0.55;
              }

              @media (min-width: 768px) {
                .staff-details-modal {
                  width: 96% !important;
                  max-width: 760px;
                  left: 50% !important;
                  top: 50% !important;
                  transform: translate(-50%, -50%) !important;
                }

                #staffDetailsModal .modal-body {
                  padding: 14px !important;
                }

                #staffDetailsModal .modal-body > div[style*="grid-template-columns: 1fr 1fr"] {
                  grid-template-columns: 1fr !important;
                }
              }

              @media (min-width: 768px) and (max-width: 1100px) {
                .staff-grid {
                  grid-template-columns: repeat(2, minmax(0, 1fr));
                  gap: 16px;
                  padding: 16px;
                }

                .staff-card {
                  padding: 22px 16px;
                }

                .staff-details-modal {
                  width: 92% !important;
                  left: 50% !important;
                  top: 50% !important;
                  margin-left: 0 !important;
                  transform: translate(-50%, -50%);
                }

                #staffDetailsModal .modal-body > div[style*="grid-template-columns: 1fr 1fr"] {
                  grid-template-columns: 1fr !important;
                }
              }

              @media (max-width: 767px) {
                .staff-grid {
                  grid-template-columns: 1fr;
                  gap: 14px;
                  padding: 12px;
                }

                .staff-card {
                  padding: 18px 14px;
                  border-radius: 18px;
                }

                .staff-detail-row {
                  flex-direction: column;
                  gap: 4px;
                }

                .staff-actions {
                  grid-template-columns: 1fr;
                }

                .detail-value {
                  text-align: left;
                }

                .staff-details-modal {
                  width: 96% !important;
                  left: 50% !important;
                  top: 50% !important;
                  margin-left: 0 !important;
                  transform: translate(-50%, -50%);
                }

                #staffDetailsModal .modal-body {
                  padding: 14px !important;
                }

                #staffDetailsModal .modal-body > div[style*="grid-template-columns: 1fr 1fr"] {
                  grid-template-columns: 1fr !important;
                }
              }

              .btn-edit {
                background: #edf2f7;
                color: #334155;
              }

              .btn-edit:hover {
                background: #e2e8f0;
              }

              .btn-delete {
                background: #fdecec;
                color: #991b1b;
              }

              .btn-delete:hover {
                background: #f9dada;
              }
            </style>

            <div class="staff-grid" id="staffGrid">

              <?php
              include "dbcon.php";
              $branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : 0;
              $branch_where = $branch_id > 0 ? " WHERE s.branch_id = " . $branch_id : "";
              $qry = "SELECT s.*, b.branch_name FROM staffs s LEFT JOIN branches b ON s.branch_id = b.id" . $branch_where;
              $result = mysqli_query($con, $qry);
              $staffDetailMap = [];
              $errorMsg = '';
              if (!$result) {
                $errorMsg = 'Error loading staff: ' . htmlspecialchars(mysqli_error($con));
              }
              while ($row = mysqli_fetch_array($result)) {
                $uid = $row['user_id'];
                $photo = $row['photo'];
                $photo_path = (!empty($photo) && file_exists("../img/staff/" . $photo)) ? "../img/staff/" . $photo : "";

                // Prepare Data for Modal
                $staffData = [
                  'fullname' => $row['fullname'],
                  'username' => $row['username'],
                  'gender' => $row['gender'],
                  'designation' => $row['designation'],
                  'email' => $row['email'],
                  'address' => $row['address'],
                  'contact' => $row['contact'],
                  'photo' => $photo_path,
                  'created_at' => $row['created_at'] ?? null
                ];
                $detailKey = (string)$uid;
                $staffDetailMap[$detailKey] = $staffData;
              ?>

                <div class="staff-card">
                  <div class="staff-avatar">
                    <?php if ($photo_path): ?>
                      <img src="<?php echo $photo_path; ?>" alt="Staff Photo">
                    <?php else: ?>
                      <i class="fas fa-user-tie"></i>
                    <?php endif; ?>
                  </div>

                  <div class="staff-basic-info">
                    <h4><?php echo htmlspecialchars($row['fullname']); ?></h4>
                    <p>@<?php echo htmlspecialchars($row['username']); ?></p>
                  </div>

                  <div class="designation-badge">
                    <i class="fas fa-id-badge"></i> <?php echo htmlspecialchars($row['designation']); ?>
                  </div>

                  <div class="staff-details-list">
                    <div class="staff-detail-row">
                      <span class="detail-label">Branch:</span>
                      <span class="detail-value text-info" style="font-weight: 800;">
                        <?php echo htmlspecialchars($row['branch_name'] ?? 'Global / System'); ?>
                      </span>
                    </div>
                    <div class="staff-detail-row">
                      <span class="detail-label">Registered:</span>
                      <span class="detail-value">
                        <?php echo isset($row['created_at']) ? date('Y-m-d H:i', strtotime($row['created_at'])) : 'N/A'; ?>
                      </span>
                    </div>
                    <div class="staff-detail-row">
                      <span class="detail-label">Phone:</span>
                      <span class="detail-value"><?php echo htmlspecialchars($row['contact']); ?></span>
                    </div>
                    <div class="staff-detail-row">
                      <span class="detail-label">Gender:</span>
                      <span class="detail-value"><?php echo htmlspecialchars($row['gender']); ?></span>
                    </div>
                    <div class="staff-detail-row">
                      <span class="detail-label">Email:</span>
                      <span class="detail-value"><?php echo htmlspecialchars((string)($row['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="staff-detail-row">
                      <span class="detail-label">Address:</span>
                      <span class="detail-value"><?php echo htmlspecialchars((string)($row['address'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="staff-detail-row">
                      <span class="detail-label">Registered:</span>
                      <span class="detail-value"><?php echo isset($row['created_at']) ? date('Y-m-d H:i', strtotime($row['created_at'])) : 'N/A'; ?></span>
                    </div>
                    <div class="staff-detail-row">
                      <span class="detail-label">Last Updated:</span>
                      <span class="detail-value"><?php echo isset($row['updated_at']) && $row['updated_at'] ? date('Y-m-d H:i', strtotime($row['updated_at'])) : 'Never'; ?></span>
                    </div>
                  </div>

                  <?php $staff_contact_digits = preg_replace('/\D+/', '', (string)($row['contact'] ?? '')); ?>
                  <div class="staff-actions">
                    <?php if ($staff_contact_digits !== ''): ?>
                      <a class="staff-action-btn btn-whatsapp" href="https://wa.me/<?php echo $staff_contact_digits; ?>" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                      </a>
                    <?php endif; ?>
                    <a class="staff-action-btn btn-edit" href="edit-staff-form.php?id=<?php echo (int)$row['user_id']; ?>">
                      <i class="fas fa-edit"></i> Edit
                    </a>
                    <a class="staff-action-btn btn-delete" href="remove-staff.php?id=<?php echo (int)$row['user_id']; ?>" onclick="return confirm('Are you sure you want to delete this staff?');">
                      <i class="fas fa-trash"></i> Delete
                    </a>
                  </div>
                </div>

              <?php } ?>
              <?php if ($errorMsg): ?>
                <div class="alert alert-danger" style="grid-column: 1/-1;"> <?php echo $errorMsg; ?> </div>
              <?php endif; ?>
  <script>
    window.staffDetailMap = <?php echo json_encode($staffDetailMap, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    // For client-side filtering
    window.allStaff = Object.values(window.staffDetailMap);
    // Client-side filter for staff
    function filterStaff() {
      var input = document.getElementById('staffSearchInput').value.toLowerCase();
      var grid = document.getElementById('staffGrid');
      var allCards = grid.querySelectorAll('.staff-card');
      if (!input) {
        allCards.forEach(function(card) { card.style.display = ''; });
        return;
      }
      allCards.forEach(function(card) {
        var name = card.querySelector('.staff-basic-info h4')?.textContent.toLowerCase() || '';
        var phone = '';
        var phoneElem = card.querySelector('.staff-detail-row .detail-value');
        if (phoneElem) phone = phoneElem.textContent.toLowerCase();
        var designation = card.querySelector('.designation-badge')?.textContent.toLowerCase() || '';
        if (name.includes(input) || phone.includes(input) || designation.includes(input)) {
          card.style.display = '';
        } else {
          card.style.display = 'none';
        }
      });
    }

    function resetStaffFilter() {
      document.getElementById('staffSearchInput').value = '';
      filterStaff();
    }
  </script>

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
  <script src="../js/jquery.min.js"></script>
  <script src="../js/jquery.ui.custom.js"></script>
  <script src="../js/bootstrap.min.js"></script>
  <script src="../js/jquery.uniform.js"></script>
  <script src="../js/select2.min.js"></script>
  <script src="../js/jquery.dataTables.min.js"></script>
  <script src="../js/matrix.js"></script>
  <script src="../js/matrix.tables.js"></script>

  <!-- Staff Details Modal -->
  <div id="staffDetailsModal" class="modal hide fade staff-details-modal" tabindex="-1" role="dialog" aria-labelledby="staffDetailsLabel" aria-hidden="true">
    <div class="modal-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 20px;">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3 style="margin: 0; color: #1e293b; font-size: 20px; display: flex; align-items: center; gap: 10px;"><i class="fas fa-user-shield"></i> Staff Details</h3>
    </div>
    <div class="modal-body" style="padding: 25px;">
      <div style="display: flex; justify-content: center; margin-bottom: 25px;">
        <div id="staffModalDefaultIcon" style="width: 100px; height: 100px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 50px; color: #94a3b8; border: 4px solid #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
          <i class="fas fa-user-tie"></i>
        </div>
        <img id="staffModalPhoto" src="" alt="Staff Photo" style="display: none; width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 4px solid #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div style="display: flex; flex-direction: column;">
          <span style="font-size: 11px; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Full Name</span>
          <span id="stf_name" style="font-size: 15px; color: #1e293b; font-weight: 600;"></span>
        </div>
        <div style="display: flex; flex-direction: column;">
          <span style="font-size: 11px; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Username</span>
          <span id="stf_username" style="font-size: 15px; color: #0284c7; font-weight: 600;"></span>
        </div>
        <div style="display: flex; flex-direction: column;">
          <span style="font-size: 11px; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Designation</span>
          <span id="stf_designation" style="font-size: 15px; color: #059669; font-weight: 700;"></span>
        </div>
        <div style="display: flex; flex-direction: column;">
          <span style="font-size: 11px; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Gender</span>
          <span id="stf_gender" style="font-size: 15px; color: #1e293b; font-weight: 600;"></span>
        </div>
        <div style="display: flex; flex-direction: column; grid-column: span 2;">
          <span style="font-size: 11px; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Phone</span>
          <span id="stf_contact" style="font-size: 15px; color: #1e293b; font-weight: 600;"></span>
        </div>
        <div style="display: flex; flex-direction: column; grid-column: span 2;">
          <span style="font-size: 11px; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Email</span>
          <span id="stf_email" style="font-size: 15px; color: #1e293b; font-weight: 600;"></span>
        </div>
        <div style="display: flex; flex-direction: column; grid-column: span 2;">
          <span style="font-size: 11px; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Address</span>
          <span id="stf_address" style="font-size: 14px; color: #1e293b; font-weight: 500;"></span>
        </div>
      </div>
    </div>
    <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 15px;">
      <button class="btn" data-dismiss="modal" aria-hidden="true" style="border-radius: 6px; font-weight: 600;">Close</button>
    </div>
  </div>

  <script>
    window.staffDetailMap = <?php echo json_encode($staffDetailMap, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
  </script>

  <script type="text/javascript">
    function decodeBase64Json(base64Data) {
      var binary = atob(base64Data);

      try {
        return JSON.parse(binary);
      } catch (plainJsonError) {}

      var bytes = [];

      for (var index = 0; index < binary.length; index++) {
        bytes.push(binary.charCodeAt(index));
      }

      if (window.TextDecoder) {
        return JSON.parse(new TextDecoder('utf-8').decode(new Uint8Array(bytes)));
      }

      var encoded = '';
      for (var byteIndex = 0; byteIndex < bytes.length; byteIndex++) {
        encoded += '%' + ('00' + bytes[byteIndex].toString(16)).slice(-2);
      }

      return JSON.parse(decodeURIComponent(encoded));
    }

    function setTextSafe(id, value, fallback) {
      var element = document.getElementById(id);
      if (!element) {
        return null;
      }

      var finalValue = value;
      if (finalValue === undefined || finalValue === null || finalValue === '') {
        finalValue = fallback !== undefined ? fallback : 'N/A';
      }

      element.textContent = finalValue;
      return element;
    }

    function showModalSafe(modalId) {
      if (window.jQuery) {
        var modalInstance = window.jQuery('#' + modalId);
        if (modalInstance.length && typeof modalInstance.modal === 'function') {
          modalInstance.modal('show');
          return;
        }
      }

      var modalElement = document.getElementById(modalId);
      if (!modalElement) {
        throw new Error('Modal not found: ' + modalId);
      }

      modalElement.style.display = 'block';
      modalElement.className += ' in';
      modalElement.setAttribute('aria-hidden', 'false');
      document.body.classList.add('modal-open');
    }

    function viewStaffDetails(source) {
      try {
        var detailKey = typeof source === 'string' ? source : (source && source.getAttribute ? source.getAttribute('data-staff-id') : '');
        var data = window.staffDetailMap && detailKey ? window.staffDetailMap[detailKey] : null;

        if (!data) {
          var base64Data = typeof source === 'string' ? source : (source && source.getAttribute ? source.getAttribute('data-staff') : '');
          if (!base64Data) {
            throw new Error('Staff payload missing');
          }
          data = decodeBase64Json(base64Data);
        }

        setTextSafe('stf_name', data.fullname);
        setTextSafe('stf_username', '@' + (data.username || ''), '@');
        setTextSafe('stf_designation', data.designation);
        setTextSafe('stf_gender', data.gender);
        setTextSafe('stf_contact', data.contact);
        setTextSafe('stf_email', data.email);
        setTextSafe('stf_address', data.address);

        // Photo logic
        const modalPhoto = document.getElementById('staffModalPhoto');
        const modalIcon = document.getElementById('staffModalDefaultIcon');
        if (data.photo) {
          modalPhoto.src = data.photo;
          modalPhoto.style.display = 'block';
          modalIcon.style.display = 'none';
        } else {
          modalPhoto.style.display = 'none';
          modalIcon.style.display = 'flex';
        }

        // Show Modal
        showModalSafe('staffDetailsModal');
      } catch (e) {
        console.error("Error parsing staff data: ", e);
        alert("Sorry, the staff details cannot be displayed right now.");
      }
    }
  </script>
</body>

</html>