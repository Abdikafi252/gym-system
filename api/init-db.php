<?php
require_once __DIR__ . '/../dbcon.php';

header('Content-Type: application/json');

/**
 * Database Schema Initialization
 * This script creates necessary tables if they don't exist.
 */

$response = [
    'status' => 'error',
    'message' => 'Initializing database...',
    'log' => []
];

try {
    if (!isset($con)) {
        throw new Exception("Database connection not established. Check your environment variables.");
    }

    $queries = [
        "CREATE TABLE IF NOT EXISTS `packages` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `packagename` varchar(100) NOT NULL,
            `duration` int(11) NOT NULL,
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
    ];

    foreach ($queries as $sql) {
        if ($con->query($sql) === TRUE) {
            $response['log'][] = "Success: Table/Query executed.";
        } else {
            $response['log'][] = "Error: " . $con->error;
        }
    }

    $response['status'] = 'success';
    $response['message'] = 'Database schema initialized successfully!';
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response, JSON_PRETTY_PRINT);
