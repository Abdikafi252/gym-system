<?php
require_once __DIR__ . '/includes/error_handler.php';
// Enable strict error reporting for mysqli (throws exceptions instead of warnings)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
  $envValues = [];
  $envPath = __DIR__ . '/.env';
  if (is_file($envPath) && is_readable($envPath)) {
    $parsed = parse_ini_file($envPath, false, INI_SCANNER_RAW);
    if (is_array($parsed)) {
      $envValues = $parsed;
    }
  }

  $db_host = getenv('DB_HOST') ?: ($envValues['DB_HOST'] ?? 'localhost');
  $db_user = getenv('DB_USER') ?: ($envValues['DB_USER'] ?? 'root');
  $db_pass = getenv('DB_PASS') ?: ($envValues['DB_PASS'] ?? '');
  $db_name = getenv('DB_NAME') ?: ($envValues['DB_NAME'] ?? 'gymnsb');

  $con = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
  // Set charset to utf8mb4 for security and proper character encoding
  mysqli_set_charset($con, "utf8mb4");
} catch (mysqli_sql_exception $e) {
  // Log error and show a user-friendly message
  error_log("Connection failed: " . $e->getMessage());
  die("Database connection failed. Please try again later.");
}
