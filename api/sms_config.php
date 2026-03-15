<?php

if (!function_exists('loadEnvFile')) {
	function loadEnvFile($path)
	{
		if (!file_exists($path)) {
			return;
		}

		$lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		foreach ($lines as $line) {
			if (strpos(trim($line), '#') === 0 || strpos($line, '=') === false) {
				continue;
			}

			list($name, $value) = explode('=', $line, 2);
			$name = trim($name);
			$value = trim($value);

			if ($name === '') {
				continue;
			}

			if ((str_starts_with($value, '"') && str_ends_with($value, '"')) || (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
				$value = substr($value, 1, -1);
			}

			if (getenv($name) === false) {
				putenv("$name=$value");
				$_ENV[$name] = $value;
			}
		}
	}
}

$rootEnvPath = dirname(__DIR__) . '/.env';
loadEnvFile($rootEnvPath);

if (!function_exists('envOrDefault')) {
	function envOrDefault($key, $default = '')
	{
		$value = getenv($key);
		return $value !== false ? $value : $default;
	}
}

/**
 * Hormuud SMS API Configuration
 */
define('SMS_USERNAME', envOrDefault('SMS_USERNAME', ''));
define('SMS_PASSWORD', envOrDefault('SMS_PASSWORD', ''));
define('SMS_SENDER_ID', envOrDefault('SMS_SENDER_ID', 'M*AGYM'));
define('SMS_TOKEN_URL', envOrDefault('SMS_TOKEN_URL', 'https://smsapi.hormuud.com/token'));
define('SMS_SEND_URL', envOrDefault('SMS_SEND_URL', 'https://smsapi.hormuud.com/api/SendSMS'));

/**
 * WhatsApp API Configuration (e.g., Ultramsg)
 */
define('WA_INSTANCE_ID', envOrDefault('WA_INSTANCE_ID', ''));
define('WA_TOKEN', envOrDefault('WA_TOKEN', ''));
define('WA_API_URL', envOrDefault('WA_API_URL', 'https://api.ultramsg.com/'));
