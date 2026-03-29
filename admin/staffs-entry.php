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


  <!--sidebar-menu-->
  <?php $page = 'staff-management';
  include 'includes/sidebar.php' ?>
  <!--sidebar-menu-->

  <div id="content">
    <div id="content-header">
      <div id="breadcrumb"> <a href="index.php" title="Go to Home" class="tip-bottom"><i class="fas fa-home"></i> Home</a> <a href="staffs.php">Staffs</a> <a href="staffs-entry.php" class="current">Staff Entry</a> </div>
      <h1 class="text-center">GYM's Staff Entry Form <i class="fas fa-briefcase"></i></h1>
    </div>
    <div class="container-fluid">
      <hr>
      <div class="row-fluid">
        <div class="span12">
          <div class="widget-box">
            <div class="widget-title"> <span class="icon"> <i class="fas fa-briefcase"></i> </span>
              <h5>Staff Details</h5>
            </div>
            <div class="widget-content nopadding">
              <style>
                .avatar-upload {
                  position: relative;
                  max-width: 200px;
                  margin: 0 auto 20px auto;
                }

                .avatar-edit {
                  position: absolute;
                  right: 12px;
                  z-index: 1;
                  top: 10px;
                }

                .avatar-edit input {
                  display: none;
                }

                .avatar-edit label {
                  display: inline-block;
                  width: 34px;
                  height: 34px;
                  margin-bottom: 0;
                  border-radius: 100%;
                  background: #FFFFFF;
                  border: 1px solid transparent;
                  box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.12);
                  cursor: pointer;
                  font-weight: normal;
                  transition: all .2s ease-in-out;
                }

                .avatar-edit label:hover {
                  background: #f1f1f1;
                  border-color: #d6d6d6;
                }

                .avatar-edit label:after {
                  content: "\f040";
                  font-family: 'Font Awesome 5 Free';
                  color: #757575;
                  position: absolute;
                  top: 7px;
                  left: 0;
                  right: 0;
                  text-align: center;
                  font-weight: 900;
                }

                .avatar-preview {
                  width: 140px;
                  height: 140px;
                  position: relative;
                  border-radius: 100%;
                  border: 6px solid #f8f8f8;
                  box-shadow: 0px 2px 10px 0px rgba(0, 0, 0, 0.1);
                }

                .avatar-preview>div {
                  width: 100%;
                  height: 100%;
                  border-radius: 100%;
                  background-size: cover;
                  background-repeat: no-repeat;
                  background-position: center;
                  background-image: url('../img/staff/default.png');
                }
              </style>
              <form id="form-wizard" action="added-staffs.php" class="form-horizontal" method="POST" enctype="multipart/form-data">
                <div id="form-wizard-1" class="step">
                  <div class="control-group">
                    <div class="avatar-upload">
                      <div class="avatar-edit">
                        <input type='file' name="image" id="imageUpload" accept=".png, .jpg, .jpeg" onchange="previewFile()" />
                        <label for="imageUpload"></label>
                      </div>
                      <div class="avatar-preview">
                        <div id="imagePreview"></div>
                      </div>
                    </div>
                  </div>

                  <div class="control-group">
                    <label class="control-label">Enter Staff's Fullname</label>
                    <div class="controls">
                      <input id="fullname" type="text" name="fullname" required />
                    </div>
                  </div>



                  <div class="control-group">
                    <label class="control-label">Enter a Username</label>
                    <div class="controls">
                      <input id="username" type="text" name="username" />
                    </div>
                  </div>

                  <div class="control-group">
                    <label class="control-label">Select Branch</label>
                    <div class="controls">
                      <select name="branch_id" required>
                        <option value="">-- Select Branch --</option>
                        <?php
                        include 'dbcon.php';
                        $branch_qry = "SELECT * FROM branches";
                        $branch_res = mysqli_query($con, $branch_qry);
                        while ($branch_row = mysqli_fetch_assoc($branch_res)) {
                          echo "<option value='" . $branch_row['id'] . "'>" . htmlspecialchars($branch_row['branch_name']) . "</option>";
                        }
                        ?>
                      </select>
                    </div>
                  </div>

                  <div class="control-group">
                    <label class="control-label">Password</label>
                    <div class="controls">
                      <input id="password" type="password" name="password" />
                    </div>
                  </div>

                  <div class="control-group">
                    <label class="control-label">Confirm Password</label>
                    <div class="controls">
                      <input id="password2" type="password" name="password2" />
                    </div>
                  </div>
                </div>

                <div id="form-wizard-2" class="step">
                  <div class="control-group">
                    <label class="control-label">Email ID</label>
                    <div class="controls">
                      <input id="email" type="text" name="email" required />
                    </div>
                  </div>

                  <div class="control-group">
                    <label class="control-label">Address</label>
                    <div class="controls">
                      <input id="address" type="text" name="address" required />
                    </div>
                  </div>

                  <div class="control-group">
                    <label class="control-label">Designation</label>
                    <div class="controls">
                      <select name="designation" id="designation">
                        <option value="Cashier">Cashier</option>
                        <option value="Trainer">Trainer</option>
                        <option value="Trainer Assistant">Trainer Assistant</option>
                        <option value="Manager">Manager</option>
                        <option value="Cleaner">Cleaner</option>
                      </select>
                    </div>
                  </div>

                  <div class="control-group">
                    <label class="control-label">Gender</label>
                    <div class="controls">
                      <select name="gender" id="gender">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                      </select>
                    </div>
                  </div>

                  <div class="control-group">
                    <label class="control-label">Monthly Salary ($)</label>
                    <div class="controls">
                      <input id="salary" type="number" name="salary" step="0.01" required placeholder="0.00" />
                    </div>
                  </div>

                  <div class="control-group">
                    <label class="control-label">Contact Number</label>
                    <div class="controls">
                      <input id="contact" type="number" name="contact" required />
                    </div>
                  </div>

                </div>

                <div class="form-actions">
                  <input id="back" class="btn btn-primary" type="reset" value="Back" />
                  <input id="next" class="btn btn-primary" type="submit" value="Proceed Next Step" />
                  <div id="status"></div>
                </div>
                <div id="submitted"></div>
              </form>
            </div>
          </div><!--end of widget box-->
        </div><!--end of span 12 -->
      </div>
    </div>
  </div>
  <!--Footer-part-->
  <div class="row-fluid">
    <div id="footer" class="span12"> <?php echo date("Y"); ?> &copy; M*A GYM System Developed By Abdikafi</a> </div>
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
  <script src="../js/jquery.validate.js"></script>
  <script src="../js/jquery.wizard.js"></script>
  <script src="../js/matrix.js"></script>
  <script src="../js/matrix.wizard.js"></script>
  <script type="text/javascript">
    function previewFile() {
      var preview = document.querySelector('#imagePreview');
      var file = document.querySelector('#imageUpload').files[0];
      var reader = new FileReader();

      reader.onloadend = function() {
        preview.style.backgroundImage = "url(" + reader.result + ")";
      }

      if (file) {
        reader.readAsDataURL(file);
      } else {
        preview.style.backgroundImage = "url('../img/staff/default.png')";
      }
    }
  </script>
</body>

</html>