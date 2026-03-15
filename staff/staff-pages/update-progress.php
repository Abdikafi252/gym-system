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
<!-- Visit codeastro.com for more projects -->
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
  <link href="../../font-awesome/css/fontawesome.css" rel="stylesheet" />
  <link href="../../font-awesome/css/all.css" rel="stylesheet" />
  <link rel="stylesheet" href="../../css/jquery.gritter.css" />
  <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
</head>

<body>

  <!--Header-part-->
  <?php include '../includes/header-content.php'; ?>
  <!--close-Header-part-->
  <!-- Visit codeastro.com for more projects -->

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
  <?php $page = 'manage-customer-progress';
  include '../includes/sidebar.php' ?>
  <!--sidebar-menu-->

  <?php
  include 'dbcon.php';
  $id = $_GET['id'];
  $qry = "select * from members where user_id='$id'";
  $result = mysqli_query($conn, $qry);
  while ($row = mysqli_fetch_array($result)) {
  ?>

    <div id="content">
      <div id="content-header">
        <div id="breadcrumb"> <a href="index.php" title="Go to Home" class="tip-bottom"><i class="fas fa-home"></i> Home</a> <a href="customer-progress.php">Customer Progress</a> <a href="#" class="current">Update Progress</a> </div>
        <h1 class="text-center">Update Customer's Progress <i class="fas fa-signal"></i></h1>
      </div>


      <div class="container-fluid" style="margin-top:-38px;">
        <div class="row-fluid">
          <div class="span12">
            <div class="widget-box">
              <div class="widget-title"> <span class="icon"> <i class="fas fa-signal" style="color:#28b779;"></i> </span>
                <h5>Customer Progress Metrics</h5>
              </div>
              <div class="widget-content">
                <form action="userprogress-req.php" method="POST" class="form-horizontal">
                  <input type="hidden" name="id" value="<?php echo $row['user_id']; ?>">

                  <div class="row-fluid">
                    <!-- Member Info Card -->
                    <div class="span12" style="background:#f8fafc; padding:20px; border-radius:8px; border:1px solid #e2e8f0; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                      <div class="row-fluid">
                        <div class="span6">
                          <h4 style="margin-top:0; color:#334155; font-weight:700;"><i class="fas fa-user-circle"></i> <?php echo $row['fullname']; ?></h4>
                          <p style="margin-bottom:0; color:#64748b;"><strong>Service Taken:</strong> <span class="badge badge-info" style="font-size:14px; padding:5px 10px;"><?php echo $row['services']; ?></span></p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="row-fluid">
                    <!-- Weight Metrics -->
                    <div class="span6">
                      <div class="widget-box" style="border:none; box-shadow:0 2px 5px rgba(0,0,0,0.05); border-radius:8px;">
                        <div class="widget-title" style="background:#fff; border-bottom:1px solid #f1f5f9; border-radius:8px 8px 0 0;">
                          <span class="icon"><i class="fas fa-balance-scale" style="color:#0284c7;"></i></span>
                          <h5 style="color:#334155;">Weight & Core Metrics</h5>
                        </div>
                        <div class="widget-content nopadding" style="background:#fff; border-radius:0 0 8px 8px;">
                          <div class="control-group" style="border-bottom:1px solid #f8fafc;">
                            <label class="control-label" style="font-weight:600; color:#475569;">Initial Weight (KG) :</label>
                            <div class="controls">
                              <input type="number" name="ini_weight" value='<?php echo $row['ini_weight']; ?>' class="span11" style="border-radius:4px; border:1px solid #cbd5e1;" />
                            </div>
                          </div>
                          <div class="control-group" style="border-bottom:1px solid #f8fafc;">
                            <label class="control-label" style="font-weight:600; color:#475569;">Current Weight (KG) :</label>
                            <div class="controls">
                              <input type="number" name="curr_weight" value='<?php echo $row['curr_weight']; ?>' class="span11" style="border-radius:4px; border:1px solid #cbd5e1; box-shadow:0 0 0 2px rgba(2,132,199,0.1);" />
                            </div>
                          </div>
                          <div class="control-group" style="border-bottom:1px solid #f8fafc;">
                            <label class="control-label" style="font-weight:600; color:#475569;">Height (Meters) :</label>
                            <div class="controls">
                              <input type="number" step="0.01" name="height" value='<?php echo isset($row['height']) ? $row['height'] : ''; ?>' class="span11" style="border-radius:4px; border:1px solid #cbd5e1;" placeholder="e.g., 1.75" />
                            </div>
                          </div>
                          <div class="control-group">
                            <label class="control-label" style="font-weight:600; color:#475569;">Body Fat (%) :</label>
                            <div class="controls">
                              <input type="number" step="0.1" name="fat" value='<?php echo isset($row['fat']) ? $row['fat'] : ''; ?>' class="span11" style="border-radius:4px; border:1px solid #cbd5e1; width:45%;" placeholder="e.g., 15.5" />
                              <span id="fatStatus" style="display:inline-block; margin-left:10px; font-size:13px; font-weight:bold;"></span>
                            </div>
                          </div>

                          <div class="control-group" style="padding:15px; background:#f8fafc; margin:10px 15px; border-radius:6px; text-align:center; border:1px dashed #cbd5e1;">
                            <h5 style="margin:0; color:#334155;">BMI: <span id="bmiDisplay">0.0</span></h5>
                            <span id="bmiStatus" style="font-weight:bold; padding:4px 10px; border-radius:4px; margin-top:8px; display:inline-block; color:#64748b; background:#e2e8f0;">Waxaa la sugayaa Miisaanka & Dhererka...</span>
                          </div>

                        </div>
                      </div>
                    </div>

                    <!-- Body Measurements -->
                    <div class="span6">
                      <div class="widget-box" style="border:none; box-shadow:0 2px 5px rgba(0,0,0,0.05); border-radius:8px;">
                        <div class="widget-title" style="background:#fff; border-bottom:1px solid #f1f5f9; border-radius:8px 8px 0 0;">
                          <span class="icon"><i class="fas fa-ruler-combined" style="color:#ea580c;"></i></span>
                          <h5 style="color:#334155;">Body Measurements (CM)</h5>
                        </div>
                        <div class="widget-content nopadding" style="background:#fff; border-radius:0 0 8px 8px;">
                          <div class="control-group" style="border-bottom:1px solid #f8fafc;">
                            <label class="control-label" style="font-weight:600; color:#475569;">Chest :</label>
                            <div class="controls">
                              <input type="number" step="0.1" name="chest" value='<?php echo isset($row['chest']) ? $row['chest'] : ''; ?>' class="span11" style="border-radius:4px; border:1px solid #cbd5e1;" placeholder="e.g., 100" />
                            </div>
                          </div>
                          <div class="control-group" style="border-bottom:1px solid #f8fafc;">
                            <label class="control-label" style="font-weight:600; color:#475569;">Waist :</label>
                            <div class="controls">
                              <input type="number" step="0.1" name="waist" value='<?php echo isset($row['waist']) ? $row['waist'] : ''; ?>' class="span11" style="border-radius:4px; border:1px solid #cbd5e1;" placeholder="e.g., 85" />
                            </div>
                          </div>
                          <div class="control-group" style="border-bottom:1px solid #f8fafc;">
                            <label class="control-label" style="font-weight:600; color:#475569;">Neck :</label>
                            <div class="controls">
                              <input type="number" step="0.1" name="neck" value='<?php echo isset($row['neck']) ? $row['neck'] : ''; ?>' class="span11" style="border-radius:4px; border:1px solid #cbd5e1;" placeholder="e.g., 38" />
                            </div>
                          </div>
                          <div class="control-group" style="border-bottom:1px solid #f8fafc;">
                            <label class="control-label" style="font-weight:600; color:#475569;">Hip (Dumar) :</label>
                            <div class="controls">
                              <input type="number" step="0.1" name="hip" value='<?php echo isset($row['hip']) ? $row['hip'] : ''; ?>' class="span11" style="border-radius:4px; border:1px solid #cbd5e1;" placeholder="e.g., 90" />
                            </div>
                          </div>
                          <div class="control-group" style="border-bottom:1px solid #f8fafc;">
                            <label class="control-label" style="font-weight:600; color:#475569;">Arms :</label>
                            <div class="controls">
                              <input type="number" step="0.1" name="arms" value='<?php echo isset($row['arms']) ? $row['arms'] : ''; ?>' class="span11" style="border-radius:4px; border:1px solid #cbd5e1;" placeholder="e.g., 35" />
                            </div>
                          </div>
                          <div class="control-group">
                            <label class="control-label" style="font-weight:600; color:#475569;">Thighs :</label>
                            <div class="controls">
                              <input type="number" step="0.1" name="thigh" value='<?php echo isset($row['thigh']) ? $row['thigh'] : ''; ?>' class="span11" style="border-radius:4px; border:1px solid #cbd5e1;" placeholder="e.g., 55" />
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Hidden inputs to retain unwanted legacy fields just in case they're required elsewhere -->
                  <input type="hidden" name="ini_bodytype" value="<?php echo $row['ini_bodytype']; ?>" />
                  <input type="hidden" name="curr_bodytype" value="<?php echo $row['curr_bodytype']; ?>" />

                  <div class="form-actions text-center" style="background:transparent; border:none; padding-top:20px;">
                    <button type="submit" class="btn btn-primary btn-large" style="border-radius:6px; padding:12px 35px; font-weight:bold; font-size:16px; background:#28b779; border:none; box-shadow:0 4px 6px rgba(40,183,121,0.2); transition:transform 0.2s;"><i class="fas fa-save"></i> Save Progress Changes</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>

      <?php
    }
      ?>
      </div><!-- widget-content ends here -->


    </div><!-- widget-box ends here -->
    </div><!-- span12 ends here -->
    </div> <!-- row-fluid ends here -->
    </div> <!-- container-fluid ends here -->
    </div> <!-- div id content ends here -->



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


    <!-- Footer ends here -->

    <!-- Auto Body Fat Calculation Script -->
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        // Find the input fields
        const weightInput = document.querySelector('input[name="curr_weight"]');
        const heightInput = document.querySelector('input[name="height"]');
        const fatInput = document.querySelector('input[name="fat"]');

        const waistInput = document.querySelector('input[name="waist"]');
        const neckInput = document.querySelector('input[name="neck"]');
        const hipInput = document.querySelector('input[name="hip"]');

        const bmiDisplay = document.getElementById('bmiDisplay');
        const bmiStatus = document.getElementById('bmiStatus');
        const fatStatus = document.getElementById('fatStatus');

        function calculateBodyFat() {
          if (!weightInput || !heightInput || !fatInput) return;

          let weight = parseFloat(weightInput.value);

          // Calculate only if both values are valid numbers greater than 0
          if (weight > 0 && heightInput.value.length > 0) {
            let heightM = parseFloat(heightInput.value); // User already enters in meters (e.g., 1.67)
            let bmi = weight / (heightM * heightM);
            let realHeightCm = heightM * 100;

            let waist = waistInput && waistInput.value ? parseFloat(waistInput.value) : 0;
            let neck = neckInput && neckInput.value ? parseFloat(neckInput.value) : 0;
            let hip = hipInput && hipInput.value ? parseFloat(hipInput.value) : 0;

            // Parse gender from PHP
            let memberGender = "<?php echo isset($row['gender']) ? $row['gender'] : 'Male'; ?>";
            let numGender = (memberGender.toLowerCase() === 'male' || memberGender.toLowerCase() === 'lab') ? 1 : 0;

            let bodyFat = 0;

            // US Navy Method Execution if Waist & Neck are provided
            if (waist > 0 && neck > 0) {
              if (numGender === 1) { // Male
                if (waist > neck) {
                  bodyFat = 495 / (1.0324 - 0.19077 * Math.log10(waist - neck) + 0.15456 * Math.log10(realHeightCm)) - 450;
                }
              } else { // Female requires Hip
                if (hip > 0 && waist + hip > neck) {
                  bodyFat = 495 / (1.29579 - 0.35004 * Math.log10(waist + hip - neck) + 0.22100 * Math.log10(realHeightCm)) - 450;
                }
              }
            }

            if (bodyFat > 0) {
              // Keep it within realistic bounds
              if (bodyFat < 3) bodyFat = 3.0;
              if (bodyFat > 60) bodyFat = 60.0;

              fatInput.value = bodyFat.toFixed(1);

              let fatCategory = "";
              let fatColor = "";

              if (numGender === 1) { // Male
                if (bodyFat >= 6 && bodyFat <= 13) {
                  fatCategory = "Ciyaaryahan (Athlete)";
                  fatColor = "#16a34a";
                } else if (bodyFat > 13 && bodyFat <= 17) {
                  fatCategory = "Caato/Fit (Fitness)";
                  fatColor = "#0284c7";
                } else if (bodyFat > 17 && bodyFat <= 24) {
                  fatCategory = "Caadi (Normal)";
                  fatColor = "#ca8a04";
                } else if (bodyFat > 24) {
                  fatCategory = "Cayil (Overfat)";
                  fatColor = "#dc2626";
                } else {
                  fatCategory = "Aad u hooseeya";
                  fatColor = "#ea580c";
                }
              } else { // Female
                if (bodyFat >= 14 && bodyFat <= 20) {
                  fatCategory = "Ciyaaryahan (Athlete)";
                  fatColor = "#16a34a";
                } else if (bodyFat > 20 && bodyFat <= 24) {
                  fatCategory = "Caato/Fit (Fitness)";
                  fatColor = "#0284c7";
                } else if (bodyFat > 24 && bodyFat <= 31) {
                  fatCategory = "Caadi (Normal)";
                  fatColor = "#ca8a04";
                } else if (bodyFat > 31) {
                  fatCategory = "Cayil (Overfat)";
                  fatColor = "#dc2626";
                } else {
                  fatCategory = "Aad u hooseeya";
                  fatColor = "#ea580c";
                }
              }

              if (fatStatus) {
                fatStatus.innerHTML = "(Cabirka Dufanka: <span style='color:" + fatColor + ";'>" + fatCategory + "</span>)";
              }
            } else {
              if (fatStatus) {
                fatStatus.innerHTML = "<span style='color:#ca8a04; font-size:11px;'>(Geli Cabirka Jirka...)</span>";
              }
            }

            if (bmiDisplay && bmiStatus) {
              bmiDisplay.innerHTML = bmi.toFixed(1);

              let bmiText = "";
              let bmiColor = "";
              let bmiBg = "";

              if (bmi < 18.5) {
                bmiText = "Miisaan yar (Underweight)";
                bmiColor = "#ea580c";
                bmiBg = "#ffedd5";
              } else if (bmi >= 18.5 && bmi <= 24.9) {
                bmiText = "Miisaan caadi (Normal)";
                bmiColor = "#16a34a";
                bmiBg = "#dcfce7";
              } else if (bmi >= 25 && bmi <= 29.9) {
                bmiText = "Miisaan dheeraad (Overweight)";
                bmiColor = "#ca8a04";
                bmiBg = "#fef9c3";
              } else {
                bmiText = "Cayil aad u badan (Obese)";
                bmiColor = "#dc2626";
                bmiBg = "#fee2e2";
              }

              bmiStatus.textContent = bmiText;
              bmiStatus.style.color = bmiColor;
              bmiStatus.style.backgroundColor = bmiBg;
            }
          } else {
            if (fatStatus) fatStatus.textContent = "";
            if (bmiDisplay && bmiStatus) {
              bmiDisplay.textContent = "0.0";
              bmiStatus.textContent = "Waxaa la sugayaa Miisaanka & Dhererka...";
              bmiStatus.style.color = "#64748b";
              bmiStatus.style.backgroundColor = "#e2e8f0";
            }
          }
        }

        // Add event listeners to trigger calculation when values change
        if (weightInput) weightInput.addEventListener('input', calculateBodyFat);
        if (heightInput) heightInput.addEventListener('input', calculateBodyFat);

        const waistListener = document.querySelector('input[name="waist"]');
        if (waistListener) waistListener.addEventListener('input', calculateBodyFat);
        const neckListener = document.querySelector('input[name="neck"]');
        if (neckListener) neckListener.addEventListener('input', calculateBodyFat);
        const hipListener = document.querySelector('input[name="hip"]');
        if (hipListener) hipListener.addEventListener('input', calculateBodyFat);
        const chestListener = document.querySelector('input[name="chest"]');
        if (chestListener) chestListener.addEventListener('input', calculateBodyFat);
        const armsListener = document.querySelector('input[name="arms"]');
        if (armsListener) armsListener.addEventListener('input', calculateBodyFat);
        const thighListener = document.querySelector('input[name="thigh"]');
        if (thighListener) thighListener.addEventListener('input', calculateBodyFat);
      });
    </script>
</body>

</html>