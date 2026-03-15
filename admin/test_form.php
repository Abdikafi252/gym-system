<?php
mysqli_report(MYSQLI_REPORT_OFF);
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Full Query Test</h1>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $con = mysqli_connect("localhost", "root", "", "gymnsb");
    if ($con) {
        $fullname = "Test User " . time();
        $username = "test" . time();
        $password = md5("pass");
        $dor = date("Y-m-d");
        $gender = "Male";
        $services = "Fitness";
        $amount = 10;
        $p_year = date("Y");
        $paid_date = date("Y-m-d");
        $plan = 1;
        $address = "Mogadishu";
        $contact = "1234567890";
        $biometric_id = time(); // Unique
        $expiry_date = date("Y-m-d");
        $registered_by = "Admin";

        // Full Query matching add-member-req.php
        $qry = "INSERT INTO members(fullname,username,password,dor,gender,services,amount,p_year,paid_date,plan,address,contact,biometric_id,expiry_date,registered_by,attendance_count,ini_bodytype,curr_bodytype,progress_date) 
                VALUES ('$fullname','$username','$password','$dor','$gender','$services','$amount','$p_year','$paid_date','$plan','$address','$contact','$biometric_id','$expiry_date','$registered_by','0','','','$dor')";

        echo "Attempting Query: <pre>$qry</pre>";

        if (mysqli_query($con, $qry)) {
            echo "<h3 style='color:green'>INSERT SUCCESS! ID: " . mysqli_insert_id($con) . "</h3>";
        } else {
            echo "<h3 style='color:red'>INSERT FAILED: " . mysqli_error($con) . "</h3>";
        }
    } else {
        echo "DB Connection Failed.";
    }
}
?>
<form method="POST">
    <button type="submit">Run Full Query Test</button>
</form>