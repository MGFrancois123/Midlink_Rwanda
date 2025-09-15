<?php
require_once 'db_connect.php';
@require_once dirname(__DIR__).'/includes/Sms_parse.php';
@require_once dirname(__DIR__).'/includes/sms_config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['success'=>false,'message'=>'POST required']); exit; }

$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';
$sender = isset($_POST['sender']) ? trim($_POST['sender']) : '';

if ($phone === '' || $message === '') { echo json_encode(['success'=>false,'message'=>'Phone and message are required']); exit; }

// Configure API keys
hdev_sms::api_id(defined('SMS_API_ID') ? SMS_API_ID : '');
hdev_sms::api_key(defined('SMS_API_KEY') ? SMS_API_KEY : '');

$candidates = [];
if ($sender !== '') { $candidates[] = $sender; }
if (isset($SMS_SENDER_CANDIDATES) && is_array($SMS_SENDER_CANDIDATES)) { $candidates = array_merge($candidates, $SMS_SENDER_CANDIDATES); }
if (empty($candidates)) { $candidates = array(''); }

$lastRes = null; $used = '';
foreach ($candidates as $sid) {
  $res = @hdev_sms::send($sid, $phone, $message);
  $lastRes = $res; $used = $sid;
  @file_put_contents(dirname(__DIR__).'/logs_sms.txt', date('Y-m-d H:i:s')." SEND to $phone sender='$sid' => ".json_encode($res)."\n", FILE_APPEND);
  if (is_object($res) && isset($res->status) && strtolower((string)$res->status) === 'success') { break; }
}

echo json_encode(['success'=> (is_object($lastRes) && isset($lastRes->status) && strtolower((string)$lastRes->status) === 'success'), 'sender_used'=>$used, 'response'=>$lastRes], JSON_UNESCAPED_UNICODE);

