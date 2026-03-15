<?php
mysqli_report(MYSQLI_REPORT_OFF);
$con = mysqli_connect("localhost", "root", "", "gymnsb");

if (!$con) {
    die("Connection Failed: " . mysqli_connect_error());
}

echo "Connection Success.\n";

$fullname = "Test User " . time();
$username = "testuser" . time();
$password = md5("password");
// Ensure valid date formats
$p_year = date('Y');
$paid_date = date("Y-m-d");
$expiry_date = date("Y-m-d", strtotime("+1 month"));
$dor = date("Y-m-d");
$biometric_id = 99999;

// Simple Insert
$qry = "INSERT INTO members(fullname,username,password,dor,gender,services,amount,p_year,paid_date,plan,address,contact,biometric_id,expiry_date,registered_by) 
        VALUES ('$fullname','$username','$password','$dor','Male','Fitness','10','$p_year','$paid_date','1','Mogadishu','123456','$biometric_id','$expiry_date','Admin')";

echo "Attempting Query: $qry\n";

if (mysqli_query($con, $qry)) {
    echo "INSERT SUCCESS!\n";
    // Clean up
    mysqli_query($con, "DELETE FROM members WHERE biometric_id = 99999");
} else {
    echo "INSERT FAILED!\n";
    echo "Error: " . mysqli_error($con) . "\n";
}
