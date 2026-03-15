<?php
require 'dbcon.php';
$r = mysqli_query($con, 'SELECT user_id, username, photo FROM staffs');
if (!$r) die(mysqli_error($con));
while ($row = mysqli_fetch_assoc($r)) {
    echo "ID: " . $row['user_id'] . " | Name: " . $row['username'] . " | Photo: '" . $row['photo'] . "'\n";
}
