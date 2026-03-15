<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "gymnsb";

// Create connection
$con = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}
echo "Connected successfully\n";

$queries = [
    "CREATE TABLE IF NOT EXISTS `packages` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `packagename` varchar(100) NOT NULL,
        `duration` int(11) NOT NULL COMMENT 'Duration in months',
        `amount` int(11) NOT NULL,
        `description` varchar(255) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

    "CREATE TABLE IF NOT EXISTS `expenses` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `date` date NOT NULL,
        `amount` int(11) NOT NULL,
        `category` varchar(100) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

    "CREATE TABLE IF NOT EXISTS `workout_plans` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `member_id` int(11) NOT NULL,
        `instruction` text NOT NULL,
        `assigned_by` int(11) NOT NULL,
        `date_assigned` date NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

    "CREATE TABLE IF NOT EXISTS `diet_plans` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `member_id` int(11) NOT NULL,
        `instruction` text NOT NULL,
        `assigned_by` int(11) NOT NULL,
        `date_assigned` date NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

    "CREATE TABLE IF NOT EXISTS `notifications` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `member_id` int(11) NOT NULL,
        `message` text NOT NULL,
        `status` varchar(20) NOT NULL DEFAULT 'Unread',
        `sent_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
];

foreach ($queries as $sql) {
    if ($con->query($sql) === TRUE) {
        echo "Query executed successfully: " . substr($sql, 0, 30) . "...\n";
    } else {
        echo "Error: " . $con->error . "\n";
    }
}

// Add column to rates
$sql = "ALTER TABLE `rates` ADD COLUMN `description` varchar(255) NOT NULL DEFAULT ''";
if ($con->query($sql) === TRUE) {
    echo "Column 'description' added to rates table.\n";
} else {
    if ($con->errno == 1060) {
        echo "Column 'description' already exists in rates table.\n";
    } else {
        echo "Error adding column: " . $con->error . "\n";
    }
}

$con->close();
