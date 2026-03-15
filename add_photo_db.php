<?php
$conn = mysqli_connect("localhost", "root", "", "gymnsb");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

function ensurePhotoColumn($conn, $table)
{
    $tableEsc = mysqli_real_escape_string($conn, $table);
    $checkQry = "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '$tableEsc' AND COLUMN_NAME = 'photo' LIMIT 1";
    $checkRes = mysqli_query($conn, $checkQry);

    if ($checkRes && mysqli_num_rows($checkRes) > 0) {
        echo "photo already exists in $table\n";
        return;
    }

    $alterQry = "ALTER TABLE `$table` ADD COLUMN `photo` VARCHAR(255) DEFAULT 'img/user.jpg'";
    if (mysqli_query($conn, $alterQry)) {
        echo "Added photo to $table\n";
    } else {
        echo ucfirst($table) . " error: " . mysqli_error($conn) . "\n";
    }
}

ensurePhotoColumn($conn, 'admin');
ensurePhotoColumn($conn, 'staffs');
ensurePhotoColumn($conn, 'members');
