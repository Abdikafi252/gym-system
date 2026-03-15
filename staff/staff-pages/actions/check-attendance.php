<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header('location:../index.php');
}

include('dbcon.php');
date_default_timezone_set('Africa/Nairobi');
$todays_date = date('Y-m-d');
$curr_time = date('h:i A');

$user_id = $_GET['id'];

// Check if already checked in today
$check_qry = "SELECT * FROM attendance WHERE curr_date = '$todays_date' AND user_id = '$user_id'";
$check_res = mysqli_query($con, $check_qry);

if (mysqli_num_rows($check_res) == 0) {
  $branch_id = $_SESSION['branch_id'];
  $sql = "INSERT INTO attendance (user_id, member_id, curr_date, curr_time, present, check_in, branch_id) 
          VALUES ('$user_id', '$user_id', '$todays_date', '$curr_time', 1, NOW(), '$branch_id')";

  if ($con->query($sql) === TRUE) {
    $sql1 = "UPDATE members SET attendance_count = attendance_count + 1 WHERE user_id='$user_id'";
    $con->query($sql1);


    // $_SESSION['success']='Record Successfully Added';

?>
    <script type="text/javascript">
      window.location = "../attendance.php";
    </script>
<?php
  }
}
?>

//}





//}

?>