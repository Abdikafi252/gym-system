<?php
include 'dbcon.php';
$res = mysqli_query($con, "DESCRIBE staffs");
while ($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}
