<?php
require_once __DIR__ . '/../dbcon.php';

header('Content-Type: application/json');

/**
 * Database Connection Diagnostics
 */

$response = [
    'status' => 'error',
    'message' => 'Starting diagnostics...',
    'details' => []
];

try {
    // Check if the connection variable is available from dbcon.php
    if (isset($con) && $con instanceof mysqli) {
        if ($con->ping()) {
            $response['status'] = 'success';
            $response['message'] = 'Database connection successful!';
            $response['details'] = [
                'host' => getenv('DB_HOST') ?: 'Loaded from .env',
                'database' => getenv('DB_NAME') ?: 'Loaded from .env',
                'server_info' => mysqli_get_server_info($con)
            ];
        } else {
            $response['message'] = 'mysqli connection failed.';
        }
    } else {
        $response['message'] = 'DATABASE_CONNECTION_NOT_FOUND: Check your dbcon.php logic.';
    }
} catch (Exception $e) {
    echo "<h2>DATABASE ERROR</h2>";
    echo "<p>Qalad: " . htmlspecialchars($e->getMessage()) . "</p>";
    
    if (strpos($e->getMessage(), 'sql103.infinityfree.com') !== false) {
        echo "<div style='color: red; border: 1px solid red; padding: 10px;'>";
        echo "<strong>MUHIIM:</strong> InfinityFree ma ogola in meel ka baxsan laga isticmaalo database-ka. <br>";
        echo "Fadlan u guur <strong>Aiven.io</strong> ama <strong>TiDB Cloud</strong> si site-kaagu u shaqeeyo.";
        echo "</div>";
    }
}

echo json_encode($response, JSON_PRETTY_PRINT);
