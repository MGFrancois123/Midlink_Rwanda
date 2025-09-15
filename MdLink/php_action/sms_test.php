<?php
require_once 'db_connect.php';
@require_once dirname(__DIR__).'/includes/Sms_parse.php';
@require_once dirname(__DIR__).'/includes/sms_config.php';
header('Content-Type: application/json');

hdev_sms::api_id(SMS_API_ID);
hdev_sms::api_key(SMS_API_KEY);

$to = SMS_SUPER_ADMIN_PHONE;
$msg = 'MDLink test: If you receive this, SMS gateway works.';
$senders = isset($SMS_SENDER_CANDIDATES) && is_array($SMS_SENDER_CANDIDATES) ? $SMS_SENDER_CANDIDATES : array('MDLINK-RW');
$res = null; $sender_used = '';
foreach ($senders as $sid) {
  $res = @hdev_sms::send($sid, $to, $msg);
  @file_put_contents(dirname(__DIR__).'/logs_sms.txt', date('Y-m-d H:i:s')." TEST try sender='$sid' => ".json_encode($res)."\n", FILE_APPEND);
  if (is_object($res) && isset($res->status) && strtolower((string)$res->status) === 'success') { $sender_used = $sid; break; }
}

@file_put_contents(dirname(__DIR__).'/logs_sms.txt', date('Y-m-d H:i:s')." TEST SMS to $to => ".json_encode($res)."\n", FILE_APPEND);

echo json_encode(['to'=>$to,'sender_used'=>$sender_used,'response'=>$res], JSON_UNESCAPED_UNICODE);

