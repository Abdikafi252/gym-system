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
 * WhatsApp Meta Cloud API Configuration
 */
define('WA_META_PHONE_ID', envOrDefault('WA_META_PHONE_ID', '1053371104529021'));
define('WA_META_TOKEN', envOrDefault('WA_META_TOKEN', 'EAAX16bZCBZBXUBRE2HG8VN40MyYG84VkoE5IbZC4x4Gc8HUme1BHS4RivR7HJGe8Ff4hCBJwQScR3KaxeQl7xIyM7mwm9dm8UHbpw3rwMrdejZAaDRh2BxXRUztpAB2Mg0UbDpdq1FSdXSOiDJ4rIF7kj3or2ZA8XacxxCAAEmkg1N57ZBk7BEmO6xz3cOmRPXrwPIpYm7bluhs5pu9Sp4DtjZBNqHqjvKjPvZCCb3vctfEnuwZCDzgurIWlZBNTZCDQJ2kx1xZB1HXdLSblRER7ZCzdQ6AZDZD'));

