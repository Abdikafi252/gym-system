<?php

// Load .env values early so APP_DEBUG is available for the error handler.
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

foreach ($envValues as $k => $v) {
  if (getenv($k) === false) {
    putenv($k . '=' . $v);
  }
}

require_once __DIR__ . '/includes/error_handler.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
  $db_host = getenv('DB_HOST') ?: ($envValues['DB_HOST'] ?? 'localhost');
  $db_user = getenv('DB_USER') ?: ($envValues['DB_USER'] ?? 'root');
  $db_pass = getenv('DB_PASS') ?: ($envValues['DB_PASS'] ?? '');
  $db_name = getenv('DB_NAME') ?: ($envValues['DB_NAME'] ?? 'gymnsb');
  $db_port = (int)(getenv('DB_PORT') ?: ($envValues['DB_PORT'] ?? '3306'));
  $db_ssl = strtolower((string)(getenv('DB_SSL') ?: ($envValues['DB_SSL'] ?? 'false')));
  $use_ssl = in_array($db_ssl, ['1', 'true', 'yes', 'on'], true);

  // Auto-enable SSL for Aiven hosts if not explicitly disabled
  if (!$use_ssl && strpos($db_host, 'aivencloud.com') !== false && $db_ssl !== 'false') {
    $use_ssl = true;
  }

  $con = mysqli_init();
  if (!$con) {
    throw new Exception('mysqli_init failed');
  }

  mysqli_options($con, MYSQLI_OPT_CONNECT_TIMEOUT, 5);

  try {
    @mysqli_real_connect($con, $db_host, $db_user, $db_pass, $db_name, $db_port, null, $use_ssl ? MYSQLI_CLIENT_SSL : 0);
  } catch (mysqli_sql_exception $firstErr) {
    // Some shared hosts/local stacks fail when SSL is requested; retry without SSL.
    if ($use_ssl) {
      $con = mysqli_init();
      mysqli_options($con, MYSQLI_OPT_CONNECT_TIMEOUT, 5);
      mysqli_real_connect($con, $db_host, $db_user, $db_pass, $db_name, $db_port);
    } else {
      throw $firstErr;
    }
  }

  $conn = $con;
  mysqli_set_charset($con, 'utf8mb4');
} catch (Throwable $e) {
  error_log('Connection failed: ' . $e->getMessage());
  die('Database connection failed. Please check your DB settings.');
}
