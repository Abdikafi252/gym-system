<?php

$app_debug = getenv('APP_DEBUG');
$is_debug = ($app_debug === '1' || strtolower((string)$app_debug) === 'true');

ini_set('display_errors', $is_debug ? '1' : '0');
ini_set('display_startup_errors', $is_debug ? '1' : '0');
error_reporting($is_debug ? E_ALL : 0);

set_exception_handler(function ($e) use ($is_debug) {
    error_log('[APP_EXCEPTION] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    if ($is_debug) {
        echo '<pre>' . htmlspecialchars((string)$e) . '</pre>';
    } else {
        echo 'Something went wrong. Please try again later.';
    }
    exit;
});

set_error_handler(function ($severity, $message, $file, $line) use ($is_debug) {
    error_log('[APP_ERROR] ' . $message . ' in ' . $file . ':' . $line);
    if ($is_debug) {
        echo '<pre>' . htmlspecialchars($message . ' in ' . $file . ':' . $line) . '</pre>';
    }
    return true;
});
