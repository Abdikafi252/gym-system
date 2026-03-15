<?php session_start();
include('dbcon.php');
require_once __DIR__ . '/../includes/security_core.php';
ensure_security_tables($con);
?>
<!DOCTYPE html>
<html lang="en">
<!-- Visit codeastro.com for more projects -->

<head>
    <title>M * A GYM System</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="css/matrix-style.css" />
    <link rel="stylesheet" href="css/matrix-login.css" />
    <link rel="stylesheet" href="../css/login-desktop.css" />
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet" />

    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>

</head>

<body>

    <div id="loginbox">
        <form id="loginform" class="form-vertical" method="POST" action="#">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
            <div class="control-group normal_text">
                <h3>
                    <img src="../img/logo.jpg" alt="M A Fitness Logo" />
                </h3>
            </div>
            <div class="control-group">
                <div class="controls">
                    <div class="main_input_box">
                        <span class="add-on bg_lg"><i class="fas fa-user"></i></span><input type="text" name="user" placeholder="Magaca Isticmaalaha" />
                    </div>
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <div class="main_input_box">
                        <span class="add-on bg_ly"><i class="fas fa-lock"></i></span><input type="password" name="pass" placeholder="Lambarka Sirta" />
                    </div>
                </div>
            </div>
            <div class="form-actions customer-actions">
                <span class="pull-right"><button type="submit" name="login" class="btn btn-success">Gal (Macaamiil)</button></span>
            </div>
            <div class="g">
                <a href="../index.php">
                    <h6>Dib u laabo</h6>
                </a>
            </div>

            <?php
            if (isset($_POST['login'])) {
                if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
                    echo "<div class='alert alert-danger alert-dismissible' role='alert'>
                                Session-kaaga wuu dhacay. Fadlan dib isku day.
                                <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                    <span aria-hidden='true'>&times;</span>
                                </button>
                                </div>";
                } else {
                $rawUser = trim($_POST['user'] ?? '');
                $username = mysqli_real_escape_string($con, $rawUser);
                $password = $_POST['pass'];
                $attemptKey = 'customer|' . strtolower($rawUser) . '|' . get_client_ip();

                if (is_rate_limited($con, $attemptKey, 5, 15)) {
                    echo "<div class='alert alert-danger alert-dismissible' role='alert'>
                                Isku dayo badan ayaa dhacay. Sug 15 daqiiqo kadibna mar kale isku day.
                                <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                    <span aria-hidden='true'>&times;</span>
                                </button>
                                </div>";
                } else {

                $query = mysqli_prepare($con, "SELECT user_id, password, status FROM members WHERE username=?");
                mysqli_stmt_bind_param($query, "s", $username);
                mysqli_stmt_execute($query);
                mysqli_stmt_bind_result($query, $user_id, $db_password, $status);
                mysqli_stmt_fetch($query);
                mysqli_stmt_close($query); // Close the fetch statement

                // Verification Logic
                if (!empty($db_password)) {
                    if ($status == 'Active') {
                        if (password_verify($password, $db_password)) {
                            // Secure Hash Match
                            $_SESSION['user_id'] = $user_id;
                            clear_failed_attempts($con, $attemptKey);
                            record_login_attempt($con, $attemptKey, 1);
                            header('location:pages/index.php');
                        } else if (md5($password) === $db_password) {
                            // Legacy MD5 Match -> Migrate
                            $new_hash = password_hash($password, PASSWORD_DEFAULT);

                            // Update DB with new hash
                            $update_query = mysqli_prepare($con, "UPDATE members SET password=? WHERE user_id=?");
                            mysqli_stmt_bind_param($update_query, "si", $new_hash, $user_id);
                            mysqli_stmt_execute($update_query);
                            mysqli_stmt_close($update_query);

                            $_SESSION['user_id'] = $user_id;
                            clear_failed_attempts($con, $attemptKey);
                            record_login_attempt($con, $attemptKey, 1);
                            header('location:pages/index.php');
                        } else {
                            record_login_attempt($con, $attemptKey, 0);
                            // Invalid Password
                            echo "<div class='alert alert-danger alert-dismissible' role='alert'>
                                        Magaca ama Lambarka Sirta waa qalad ama akoonkaagu wuu dhacay!
                                        <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                            <span aria-hidden='true'>&times;</span>
                                        </button>
                                        </div>";
                        }
                    } else {
                        // Inactive Account
                        echo "<div class='alert alert-danger alert-dismissible' role='alert'>
                                    Magaca ama Lambarka Sirta waa qalad ama akoonkaagu wuu dhacay!
                                    <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                        <span aria-hidden='true'>&times;</span>
                                    </button>
                                    </div>";
                    }
                } else {
                    record_login_attempt($con, $attemptKey, 0);
                    // User not found
                    echo "<div class='alert alert-danger alert-dismissible' role='alert'>
                                Magaca ama Lambarka Sirta waa qalad ama akoonkaagu wuu dhacay!
                                <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                    <span aria-hidden='true'>&times;</span>
                                </button>
                                </div>";
                }
                }
                }
            }
            ?>
        </form>

    </div>



    <script src="js/jquery.min.js"></script>
    <script src="js/matrix.login.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/matrix.js"></script>
</body>

</html>
