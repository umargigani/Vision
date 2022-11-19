<?php

/*
 * ==========================================================
 * INTEGRATION-MESSENGER.PHP
 * ==========================================================
 *
 * Facebook Messenger Webhook listener.
 *
 */

$SECRET_CODE = 'abcd1234567890';
$SUPPORT_BOARD_URL = 'https://sandbox.msgsmartly.com';

$response_json = file_get_contents('php://input');
$response = json_decode($response_json, true);
if (empty($response_json) && !isset($_GET['hub_mode'])) die();

// Send the Messenger message back to Support Board
$ch = curl_init($SUPPORT_BOARD_URL . '/apps/messenger/post.php');
if ($ch !== false) {
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $response_json);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

// Webhook verification
if (isset($_GET['hub_mode']) && isset($_GET['hub_verify_token'])) {
    if ($_GET['hub_mode'] === 'subscribe' && $_GET['hub_verify_token'] === $SECRET_CODE) {
        die($_GET['hub_challenge']);
    }
    die();
}

// Debug
function debug($value) {
    $value = is_string($value) ? $value : json_encode($value);
    if (file_exists('debug.txt')) {
        $value = file_get_contents('debug.txt') . PHP_EOL . $value;
    }
    $file = fopen('debug.txt', 'w');
    fwrite($file, $value);
    fclose($file);
}

?>
