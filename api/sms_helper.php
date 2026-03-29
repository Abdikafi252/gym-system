<?php

/**
 * SMS Helper for GYM System
 * Integration with Hormuud/Twilio
 */

function sendSMS($to, $message)
{
    require_once __DIR__ . '/sms_config.php';

    // Check if cURL is enabled
    if (!function_exists('curl_init')) {
        file_put_contents(__DIR__ . '/sms_error.txt', date('Y-m-d H:i:s') . " | ERROR: cURL extension not enabled.\n", FILE_APPEND);
        return false;
    }

    // 1. Get Authentication Token
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_URL, SMS_TOKEN_URL);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array(
        'Username' => SMS_USERNAME,
        'Password' => SMS_PASSWORD,
        'grant_type' => 'password'
    )));

    $response = curl_exec($curl);
    $auth_data = json_decode($response);

    if (!isset($auth_data->access_token)) {
        // Log failure to a file if token fails
        $error_log = date('Y-m-d H:i:s') . " | AUTH FAILED | Response: " . $response . "\n";
        file_put_contents(__DIR__ . '/sms_error.txt', $error_log, FILE_APPEND);
        return false;
    }

    $token = $auth_data->access_token;

    // 2. Prepare headers with Token
    $headers = array(
        "Content-Type: application/json; charset=utf-8",
        "Authorization: Bearer " . $token
    );

    // Format number (Ensure it starts with 252 for Somalia if needed)
    if (strlen($to) == 9) $to = '252' . $to;

    $data = array(
        "mobile" => $to,
        "message" => $message,
        "senderid" => SMS_SENDER_ID
    );

    $postdata = json_encode($data);

    // 3. Send SMS
    $ch = curl_init(SMS_SEND_URL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // Using 0 as per user snippet, though 1 is recommended
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    // curl_close($ch); // Deprecated in PHP 8.0+

    // Log the transaction
    $log_entry = date('Y-m-d H:i:s') . " | TO: $to | STATUS: $http_code | RESPONSE: $result\n";
    file_put_contents(__DIR__ . '/sms_log.txt', $log_entry, FILE_APPEND);

    return $http_code == 200;
}

function sendExpiryAlert($name, $to)
{
    $message = "Asc $name, Xubinimadaada gym-ka way dhacday. Fadlan cusbooneysii si aad u sii wadato adeegga.";
    return sendSMS($to, $message);
}

function sendWhatsApp($to, $message_or_template = 'hello_world', $language_code = 'en_US', $template_params = [])
{
    require_once __DIR__ . '/sms_config.php';

    if (!function_exists('curl_init')) {
        file_put_contents(__DIR__ . '/wa_error.txt', date('Y-m-d H:i:s') . " | WA ERROR: cURL extension not enabled.\n", FILE_APPEND);
        return false;
    }

    $to = preg_replace('/[^0-9]/', '', $to);
    if (strlen($to) == 9 && substr($to, 0, 1) == '6') {
        $to = '252' . $to;
    }

    // Meta API requires approved templates for business-initiated messages.
    // We fall back to 'hello_world' (the default) if a raw text message is passed.
    $template_name = (strpos($message_or_template, ' ') !== false) ? 'hello_world' : $message_or_template;

    $url = "https://graph.facebook.com/v22.0/" . WA_META_PHONE_ID . "/messages";

    $template_data = [
        "name" => $template_name,
        "language" => [
            "code" => $language_code
        ]
    ];

    if (!empty($template_params)) {
        $parameters = [];
        foreach ($template_params as $param) {
            $parameters[] = [
                "type" => "text",
                "text" => (string)$param
            ];
        }
        $template_data["components"] = [
            [
                "type" => "body",
                "parameters" => $parameters
            ]
        ];
    }

    $data = [
        "messaging_product" => "whatsapp",
        "to" => $to,
        "type" => "template",
        "template" => $template_data
    ];

    $headers = [
        "Authorization: Bearer " . WA_META_TOKEN,
        "Content-Type: application/json"
    ];

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => $headers,
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    if ($err || $http_code >= 400) {
        $error_log = date('Y-m-d H:i:s') . " | WA ERROR: " . ($err ? $err : $response) . "\n";
        file_put_contents(__DIR__ . '/wa_error.txt', $error_log, FILE_APPEND);
        return false;
    } else {
        return true;
    }
}
