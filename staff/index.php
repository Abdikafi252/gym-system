<?php session_start();
include('dbcon.php');
require_once __DIR__ . '/../includes/security_core.php';
ensure_security_tables($con);
?>
<!DOCTYPE html>
<html lang="en">


<head>
    <title>M*A GYM System</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="css/matrix-style.css" />
    <link rel="stylesheet" href="css/matrix-login.css" />
    <link rel="stylesheet" href="../css/login-desktop.css" />
    <link href="font-awesome/css/fontawesome.css" rel="stylesheet" />
    <link href="font-awesome/css/all.css" rel="stylesheet" />
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>

</head>

<body>

    <div id="loginbox">
        <form id="loginform" method="POST" class="form-vertical" action="#">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
            <div class="control-group normal_text">
                <h3><img src="../img/logo.jpg" alt="M A Fitness Logo" /></h3>
            </div>
            <div class="control-group">
                <div class="controls">
                    <div class="main_input_box">
                        <span class="add-on bg_lg"><i class="fas fa-user"></i></span><input type="text" name="user" placeholder="Username" required />
                    </div>
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <div class="main_input_box">
                        <span class="add-on bg_ly"><i class="fas fa-lock"></i></span><input type="password" name="pass" placeholder="Password" required />
                    </div>
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <div class="main_input_box">
                        <span class="add-on bg_lo"><i class="fas fa-building"></i></span>
                        <select name="branch_id" required style="width: 83%; height: 38px;">
                            <option value="">-- Select Branch --</option>
                            <?php
                            $branch_qry = mysqli_query($con, "SELECT * FROM branches");
                            while ($branch_row = mysqli_fetch_array($branch_qry)) {
                                echo "<option value='" . $branch_row['id'] . "'>" . $branch_row['branch_name'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-actions center">
                <!-- <span class="pull-right"><a type="submit" href="index.html" class="btn btn-success" /> Login</a></span> -->
                <!-- <input type="submit" class="button" title="Log In" name="login" value="Admin Login"></input> -->
                <span class="pull-right"><button type="submit" class="btn btn-block btn-large btn-warning" title="Log In" name="login" value="Admin Login">Login</button></span>
            </div>
        </form>
        <?php
        if (isset($_POST['login'])) {
            if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
                echo "<div class='alert alert-danger alert-dismissible' role='alert'>
                            Your session has expired. Please try again.
                            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                <span aria-hidden='true'>&times;</span>
                            </button>
                            </div>";
            } else {
            $rawUser = trim($_POST['user'] ?? '');
            $username = mysqli_real_escape_string($con, $rawUser);
            $password = $_POST['pass'];
            $selected_branch = $_POST['branch_id'];
            $attemptKey = 'staff|' . strtolower($rawUser) . '|' . get_client_ip();

            if (is_rate_limited($con, $attemptKey, 5, 15)) {
                echo "<div class='alert alert-danger alert-dismissible' role='alert'>
                            Too many attempts. Please wait 15 minutes and try again.
                            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                <span aria-hidden='true'>&times;</span>
                            </button>
                            </div>";
            } else {

            $query = mysqli_prepare($con, "SELECT user_id, password, branch_id, designation FROM staffs WHERE username=?");
            mysqli_stmt_bind_param($query, "s", $username);
            mysqli_stmt_execute($query);
            mysqli_stmt_bind_result($query, $user_id, $db_password, $db_branch_id, $db_designation);
            mysqli_stmt_fetch($query);
            mysqli_stmt_close($query); // Close the fetch statement

            // Verification Logic
            if (!empty($db_password)) {
                if ($db_branch_id != $selected_branch) {
                    echo "<div class='alert alert-danger alert-dismissible' role='alert'>
                            The selected branch is not your correct branch.
                            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                <span aria-hidden='true'>&times;</span>
                            </button>
                            </div>";
                } else if (password_verify($password, $db_password)) {
                    // Secure Hash Match
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['username'] = $username;
                    $_SESSION['branch_id'] = $db_branch_id;
                    $_SESSION['designation'] = normalize_designation($db_designation);
                    clear_failed_attempts($con, $attemptKey);
                    record_login_attempt($con, $attemptKey, 1);
                    header('location:staff-pages/index.php');
                } else if (md5($password) === $db_password) {
                    // Legacy MD5 Match -> Migrate
                    $new_hash = password_hash($password, PASSWORD_DEFAULT);

                    // Update DB with new hash
                    $update_query = mysqli_prepare($con, "UPDATE staffs SET password=? WHERE user_id=?");
                    mysqli_stmt_bind_param($update_query, "si", $new_hash, $user_id);
                    mysqli_stmt_execute($update_query);
                    mysqli_stmt_close($update_query);

                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['username'] = $username;
                    $_SESSION['branch_id'] = $db_branch_id;
                    $_SESSION['designation'] = normalize_designation($db_designation);
                    clear_failed_attempts($con, $attemptKey);
                    record_login_attempt($con, $attemptKey, 1);
                    header('location:staff-pages/index.php');
                } else {
                    record_login_attempt($con, $attemptKey, 0);
                    // Invalid Password
                    echo "<div class='alert alert-danger alert-dismissible' role='alert'>
                                    Incorrect Username or Password
                                    <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                        <span aria-hidden='true'>&times;</span>
                                    </button>
                                    </div>";
                }
            } else {
                record_login_attempt($con, $attemptKey, 0);
                // User not found
                echo "<div class='alert alert-danger alert-dismissible' role='alert'>
                                Incorrect Username or Password
                                <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                    <span aria-hidden='true'>&times;</span>
                                </button>
                                </div>";
            }
            }
            }
        }
        ?>
        <div class="pull-left">
            <a href="../index.php">
                <h6>Admin Login</h6>
            </a>
        </div>

        <div class="pull-right">
            <a href="../customer">
                <h6>Customer Login</h6>
            </a>
        </div>

    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/matrix.login.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/matrix.js"></script>
</body>

</html>