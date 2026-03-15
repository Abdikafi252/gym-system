<?php
require_once __DIR__ . '/includes/error_handler.php';
// Enable strict error reporting for mysqli (throws exceptions instead of warnings)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
  $envValues = [];
  $envPath = __DIR__ . '/.env';
  if (is_file($envPath) && is_readable($envPath)) {
    if (function_exists('parse_ini_file')) {
      $parsed = @parse_ini_file($envPath, false, INI_SCANNER_RAW);
      if (is_array($parsed)) {
        $envValues = $parsed;
      }
    }

    if (empty($envValues)) {
      // Fallback parser for environments where parse_ini_file is unavailable.
      $lines = @file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
      if (is_array($lines)) {
        foreach ($lines as $line) {
          $line = trim($line);
          if ($line === '' || $line[0] === '#' || $line[0] === ';') {
            continue;
          }
          $parts = explode('=', $line, 2);
          if (count($parts) !== 2) {
            continue;
          }
          $key = trim($parts[0]);
          $value = trim($parts[1]);
          if ($key === '') {
            continue;
          }
          if (
            (strlen($value) >= 2) &&
            (($value[0] === '"' && substr($value, -1) === '"') || ($value[0] === "'" && substr($value, -1) === "'"))
          ) {
            $value = substr($value, 1, -1);
          }
          $envValues[$key] = $value;
        }
      }
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
