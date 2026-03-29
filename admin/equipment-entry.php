<?php
session_start();
//the isset function to check username is already loged in and stored on the session
if(!isset($_SESSION['user_id'])){
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
  .equip-page-wrap {
    padding: 6px 4px 18px;
  }

  .equip-page-intro {
    margin-bottom: 20px;
    padding: 22px 24px;
    border-radius: 24px;
    background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 45%, #38bdf8 100%);
    color: #fff;
    box-shadow: 0 18px 42px rgba(30, 58, 138, 0.22);
  }

  .equip-page-intro h3 {
    margin: 0 0 8px;
    font-size: 26px;
    line-height: 1.15;
  }

  .equip-page-intro p {
    margin: 0;
    color: rgba(255, 255, 255, .9);
    font-size: 14px;
    max-width: 760px;
  }

  .equip-intro-chips {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 16px;
  }

  .equip-intro-chip {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 13px;
    border-radius: 999px;
    background: rgba(255,255,255,.14);
    border: 1px solid rgba(255,255,255,.16);
    font-size: 12px;
    font-weight: 700;
  }

  .form-modern {
    display: block;
  }

  .form-modern > .span12 {
    width: 100% !important;
    margin-left: 0 !important;
    float: none;
  }

  .form-sections {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 24px;
    padding: 22px;
  }

  .form-section {
    background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    border: 1px solid #e5edf6;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: inset 0 1px 0 rgba(255,255,255,.85);
  }

  .form-section.full {
    grid-column: 1 / -1;
  }

  .section-head {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 18px;
    background: linear-gradient(135deg, #eff6ff 0%, #f8fafc 100%);
    border-bottom: 1px solid #e5edf6;
    color: #0f172a;
    font-size: 15px;
    font-weight: 800;
  }

  .section-head i {
    color: #2563eb;
  }

  .section-body {
    padding: 6px 0;
  }

  .form-modern .widget-box {
    border-radius: 22px;
    overflow: hidden;
    box-shadow: 0 18px 40px rgba(15, 23, 42, 0.10);
    border: 1px solid #dbe4f0;
    background: #ffffff;
    margin-bottom: 18px;
  }

  .form-modern .widget-title {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    color: #fff;
    border-bottom: 0;
    padding-top: 4px;
    padding-bottom: 4px;
  }

  .form-modern .widget-title h5 {
    font-size: 15px;
    font-weight: 800;
    letter-spacing: .3px;
  }

  .form-modern .widget-content {
    background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
  }

  .form-modern .control-group {
    border-top: 1px solid #eef2f7;
    margin-bottom: 0;
    padding-top: 10px;
    padding-bottom: 10px;
  }

  .form-modern .control-group:first-child {
    border-top: 0;
  }

  .form-modern .control-label {
    font-weight: 700;
    color: #374151;
    font-size: 13px;
    padding-top: 13px;
  }

  .form-modern .widget-box::after {
    content: '';
    position: absolute;
    inset: auto 0 0 0;
    height: 4px;
    background: linear-gradient(90deg, #2563eb, #22c55e);
    opacity: .85;
  }

  .form-modern .widget-box {
    position: relative;
  }

  .form-modern .controls input,
  .form-modern .controls select,
  .form-modern .controls textarea {
    border-radius: 14px;
    border: 1px solid #cfd8e3;
    padding: 11px 14px;
    background: #ffffff;
    color: #0f172a;
    box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.04);
    transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
    box-sizing: border-box;
    width: 100%;
    min-height: 46px;
  }

  .form-modern .controls input:focus,
  .form-modern .controls select:focus,
  .form-modern .controls textarea:focus {
    border-color: #60a5fa;
    box-shadow: 0 0 0 4px rgba(96, 165, 250, 0.14);
    outline: none;
  }

  .form-modern .help-block {
    color: #6b7280;
    font-size: 12px;
  }

  .submit-wrap {
    background: linear-gradient(180deg, #f8fbff 0%, #f1f5f9 100%);
    border-top: 1px solid #e5e7eb;
  }

  .submit-wrap .btn {
    padding: 12px 30px;
    font-weight: 700;
    border-radius: 999px;
    font-size: 14px;
    box-shadow: 0 10px 20px rgba(34, 197, 94, 0.22);
    min-width: 180px;
  }

  .equip-preview-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 14px;
    margin-top: 14px;
    padding: 24px 18px;
    border-radius: 24px;
    border: 1px dashed #93c5fd;
    background: radial-gradient(circle at top, #ffffff 0%, #eff6ff 55%, #e0f2fe 100%);
    min-height: 230px;
    text-align: center;
    box-shadow: inset 0 1px 0 rgba(255,255,255,.85);
  }

  .equip-preview {
    width: 150px;
    height: 150px;
    border-radius: 22px;
    object-fit: cover;
    border: 4px solid #ffffff;
    background: #ffffff;
    display: none;
    box-shadow: 0 18px 40px rgba(37, 99, 235, 0.16);
  }

  .equip-preview-placeholder {
    width: 150px;
    height: 150px;
    border-radius: 22px;
    border: 2px dashed #60a5fa;
    background: linear-gradient(135deg, #ffffff 0%, #dbeafe 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #3b82f6;
    font-size: 42px;
    box-shadow: 0 18px 36px rgba(59, 130, 246, 0.10);
  }

  .equip-preview-box {
    width: 100%;
    min-width: 0;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .equip-photo-copy {
    width: 100%;
    max-width: 560px;
  }

  .equip-preview-text {
    max-width: 100%;
    color: #475569;
    font-size: 13px;
    line-height: 1.6;
    text-align: center;
  }

  .equip-preview-title {
    margin-bottom: 4px;
    color: #0f172a;
    font-size: 16px;
    font-weight: 800;
  }

  .form-modern .controls input[type="file"] {
    background: #fff;
    padding: 10px 12px;
    width: 100%;
    box-sizing: border-box;
    border-radius: 16px;
  }

  .form-modern .controls select,
  .form-modern .controls input[type="date"] {
    min-height: 46px;
  }

  .form-modern .input-append .add-on {
    height: 42px;
    line-height: 22px;
    background: #eef2f7;
    color: #334155;
    border-color: #cfd8e3;
  }

  .form-modern .input-append input {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
  }

  .photo-field-box {
    padding: 18px;
    border: 1px solid #dbeafe;
    border-radius: 20px;
    background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    box-shadow: 0 12px 28px rgba(37, 99, 235, 0.06);
  }

  .section-mini-note {
    margin-top: 9px;
    color: #64748b;
    font-size: 12px;
    display: block;
    line-height: 1.5;
  }

  .status-guide {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 12px;
  }

  .status-guide-item {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 12px;
    border-radius: 14px;
    background: #f8fafc;
    border: 1px solid #dbe4f0;
    color: #334155;
    font-size: 12px;
    font-weight: 700;
  }

  .status-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex: 0 0 10px;
  }

  .status-dot.new {
    background: #22c55e;
  }

  .status-dot.used {
    background: #f59e0b;
  }

  .status-dot.maintenance {
    background: #ef4444;
  }

  .upload-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: center;
    width: 100%;
  }

  .upload-meta-item {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 14px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.88);
    border: 1px solid #dbeafe;
    color: #1e3a8a;
    font-size: 12px;
    font-weight: 700;
  }

  .selected-file-pill {
    margin-top: 14px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    max-width: 100%;
    padding: 10px 14px;
    border-radius: 14px;
    background: #eff6ff;
    color: #1e40af;
    font-size: 12px;
    font-weight: 700;
  }

  .selected-file-pill span {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 280px;
    display: inline-block;
    vertical-align: middle;
  }

  .pricing-section-title {
    display: none;
  }

  @media (max-width: 991px) {
    .equip-page-intro h3 {
      font-size: 21px;
    }

    .form-sections {
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 767px) {
    .equip-page-wrap {
      padding-left: 0;
      padding-right: 0;
    }

    .equip-page-intro {
      padding: 18px;
      border-radius: 18px;
    }

    .form-modern .widget-box {
      border-radius: 18px;
    }

    .form-sections {
      padding: 14px;
      gap: 16px;
    }

    .form-section {
      border-radius: 16px;
    }

    .equip-preview-wrap {
      min-height: 170px;
    }

    .equip-photo-copy {
      width: 100%;
    }

    .equip-preview-text {
      text-align: center;
    }

    .selected-file-pill {
      width: 100%;
      justify-content: center;
    }

    .selected-file-pill span {
      max-width: 180px;
    }

    .form-modern .controls input,
    .form-modern .controls select,
    .form-modern .controls textarea,
    .form-modern .controls input[type="file"] {
      width: 100% !important;
    }

    .form-modern .input-append {
      display: flex;
      width: 100%;
    }

    .form-modern .input-append input {
      width: 100% !important;
    }

    .form-modern .control-label {
      text-align: left;
      padding-top: 0;
      margin-bottom: 8px;
    }

    .section-head {
      padding: 12px 14px;
      font-size: 14px;
    }

    .submit-wrap .btn {
      width: 100%;
      min-width: 0;
    }
  }
</style>
</head>
<body>

<!--Header-part-->
<?php include 'includes/header-content.php'; ?>
<!--close-Header-part--> 


<!--top-Header-menu-->
<?php include 'includes/topheader.php'?>
<!--close-top-Header-menu-->
<!--start-top-serch-->
<!-- <div id="search">
  <input type="hidden" placeholder="Search here..."/>
  <button type="submit" class="tip-bottom" title="Search"><i class="icon-search icon-white"></i></button>
</div> -->
<!--close-top-serch-->

<!--sidebar-menu-->
<?php $page='add-equip'; include 'includes/sidebar.php'?>
<!--sidebar-menu-->

<div id="content">
<div id="content-header">
  <div id="breadcrumb"> <a href="index.php" title="Go to Home" class="tip-bottom"><i class="fas fa-home"></i> Home</a> <a href="#" class="tip-bottom">Equipments</a> <a href="#" class="current">Add Equipments</a> </div>
  <h1>Equipment Entry Form</h1>
</div>
<div class="container-fluid">
  <hr>
  <div class="equip-page-wrap">
  <div class="equip-page-intro">
    <h3><i class="fas fa-dumbbell"></i> Add New Gym Equipment</h3>
    <div class="equip-intro-chips">
      <span class="equip-intro-chip"><i class="fas fa-image"></i> Photo Preview</span>
      <span class="equip-intro-chip"><i class="fas fa-layer-group"></i> Status Tracking</span>
      <span class="equip-intro-chip"><i class="fas fa-mobile-alt"></i> Mobile Friendly</span>
    </div>
  </div>
  <div class="row-fluid form-modern">
    <div class="span12">
      <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="fas fa-align-justify"></i> </span>
          <h5>Add Equipment Details</h5>
        </div>
        <div class="widget-content nopadding">
          <form action="add-equipment-req.php" method="POST" enctype="multipart/form-data" class="form-horizontal">
            <div class="form-sections">
              <div class="form-section">
                <div class="section-head"><i class="fas fa-dumbbell"></i> Equipment Info</div>
                <div class="section-body">
                  <div class="control-group">
                    <label class="control-label">Equipment :</label>
                    <div class="controls">
                      <input type="text" class="span11" name="ename" placeholder="Equipment Name" required />
                    </div>
                  </div>
                  <div class="control-group">
                    <label class="control-label">Description :</label>
                    <div class="controls">
                      <input type="text" class="span11" name="description" placeholder="Short Description" required />
                    </div>
                  </div>
                  <div class="control-group">
                    <label class="control-label">Date of Purchase :</label>
                    <div class="controls">
                      <input type="date" name="date" class="span11" />
                      <span class="help-block">Please mention the date of purchase</span> </div>
                  </div>
                  <div class="control-group">
                    <label class="control-label">Quantity :</label>
                    <div class="controls">
                      <input type="number" class="span11" name="quantity" placeholder="Equipment Qty" required />
                    </div>
                  </div>
                  <div class="control-group">
                    <label class="control-label">Status :</label>
                    <div class="controls">
                      <select class="span11" name="status" required>
                        <option value="" disabled selected>Select Equipment Status</option>
                        <option value="Brand New">Brand New</option>
                        <option value="Used">Used</option>
                        <option value="Maintenance Required">Maintenance Required</option>
                      </select>
                      <div class="status-guide">
                        <span class="status-guide-item"><span class="status-dot new"></span> Brand New</span>
                        <span class="status-guide-item"><span class="status-dot used"></span> Used</span>
                        <span class="status-guide-item"><span class="status-dot maintenance"></span> Maintenance Required</span>
                      </div>
                    </div>
                  </div>
                  <?php
                    include 'dbcon.php';
                    $branch_qry = "SELECT * FROM branches";
                    $branch_res = mysqli_query($con, $branch_qry);
                    $isStaffManager = (isset($_SESSION['designation']) && $_SESSION['designation'] == 'Manager');
                    $sessBranch = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : 0;
                  ?>
                  <div class="control-group">
                    <label class="control-label">Branch :</label>
                    <div class="controls">
                      <select class="span11" name="branch_id" required <?php echo $isStaffManager ? 'disabled' : ''; ?>>
                        <?php if (!$isStaffManager): ?>
                        <option value="" disabled selected>Select Branch</option>
                        <option value="0">Global / System</option>
                        <?php endif; ?>
                        <?php while ($b = mysqli_fetch_assoc($branch_res)) { ?>
                        <option value="<?php echo $b['id']; ?>" <?php if ($isStaffManager && $b['id'] == $sessBranch) echo 'selected'; ?>><?php echo htmlspecialchars($b['branch_name']); ?></option>
                        <?php } ?>
                      </select>
                      <?php if ($isStaffManager): ?>
                      <input type="hidden" name="branch_id" value="<?php echo $sessBranch; ?>">
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>

              <div class="form-section">
                <div class="section-head"><i class="fas fa-truck"></i> Other Details & Pricing</div>
                <div class="section-body">
                  <div class="control-group">
                    <label class="control-label">Vendor :</label>
                    <div class="controls">
                      <input type="text" class="span11" name="vendor" placeholder="Vendor"required />
                    </div>
                  </div>
                  <div class="control-group">
                    <label class="control-label">Address :</label>
                    <div class="controls">
                      <input type="text" class="span11" name="address" placeholder="Vendor Address" required />
                    </div>
                  </div>
                  <div class="control-group">
                    <label for="normal" class="control-label">Contact Number</label>
                    <div class="controls">
                      <input type="text" id="mask-phone" name="contact" minlength="10" maxlength="10" class="span11 mask text" required>
                      <span class="help-block blue span11" style="margin-left:0;">(999) 999-9999</span>
                    </div>
                  </div>
                  <div class="control-group">
                    <label class="control-label">Cost Per Item: </label>
                    <div class="controls">
                      <div class="input-append">
                        <span class="add-on">$</span> 
                        <input type="number" placeholder="269" name="amount" class="span11" required>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="form-section full">
                <div class="section-head"><i class="fas fa-image"></i> Equipment Photo</div>
                <div class="section-body">
                  <div class="control-group">
                    <label class="control-label">Equipment Photo :</label>
                    <div class="controls">
                      <div class="photo-field-box span11" style="margin-left:0;">
                        <input type="file" class="span12" id="equipment-photo-input" name="photo" accept="image/*" />
                        <div class="equip-preview-wrap">
                          <div class="equip-preview-box">
                            <img id="equipment-photo-preview" class="equip-preview" alt="Equipment Preview">
                            <div id="equipment-photo-placeholder" class="equip-preview-placeholder"><i class="fas fa-image"></i></div>
                          </div>
                          <div class="equip-photo-copy">
                            <div class="equip-preview-title">Image Preview</div>
                            <div class="upload-meta">
                              <div class="upload-meta-item"><i class="fas fa-images"></i> JPG, PNG, WEBP</div>
                              <div class="upload-meta-item"><i class="fas fa-expand"></i> Preview Enabled</div>
                            </div>
                            <div id="selected-file-pill" class="selected-file-pill"><i class="fas fa-paperclip"></i> <span>No image selected</span></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="form-actions text-center submit-wrap" style="margin: 0 22px 22px; border-radius: 18px;">
                <button type="submit" class="btn btn-success">Submit Details</button>
              </div>
            </div>
            </form>
        </div>
      </div>
	</div>
  </div>
  
 
</div></div></div>


<!--end-main-container-part-->

<!--Footer-part-->

<div class="row-fluid">
  <div id="footer" class="span12"> <?php echo date("Y");?> &copy; M*A GYM System Developed By Abdikafi</a> </div>
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
  function goPage (newURL) {

      // if url is empty, skip the menu dividers and reset the menu selection to default
      if (newURL != "") {
      
          // if url is "-", it is this page -- reset the menu:
          if (newURL == "-" ) {
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

(function(){
  var input = document.getElementById('equipment-photo-input');
  var preview = document.getElementById('equipment-photo-preview');
  var placeholder = document.getElementById('equipment-photo-placeholder');
  var filePill = document.getElementById('selected-file-pill');
  var fileName = filePill ? filePill.querySelector('span') : null;
  if (!input || !preview || !placeholder) return;

  input.addEventListener('change', function(e){
    var file = e.target.files && e.target.files[0];
    if (!file) {
      preview.style.display = 'none';
      placeholder.style.display = 'flex';
      preview.src = '';
      if (fileName) {
        fileName.textContent = 'No image selected';
      }
      return;
    }
    var reader = new FileReader();
    reader.onload = function(evt){
      preview.src = evt.target.result;
      preview.style.display = 'block';
      placeholder.style.display = 'none';
    };
    if (fileName) {
      fileName.textContent = file.name;
    }
    reader.readAsDataURL(file);
  });
})();
</script>
</body>
</html>
