<?php
require_once '../constant/connect.php';
require_once '../includes/payment_config.php';
require_once '../includes/pay_parse.php';
header('Content-Type: application/json');

$logFile = __DIR__ . '/../logs_payment.txt';
function log_line($msg){
	global $logFile;
	@file_put_contents($logFile, '['.date('Y-m-d H:i:s').'] '.$msg.PHP_EOL, FILE_APPEND);
}

try {
	$telIn = $_POST['tel'] ?? '';
	$amountIn = $_POST['amount'] ?? '';
	$tx_ref = $_POST['tx_ref'] ?? ('TX'.time().rand(100,999));
	$link = $_POST['link'] ?? HDEV_PAYMENT_CALLBACK_LINK;

	if ($telIn === '' || $amountIn === '') {
		throw new Exception('Phone and amount are required');
	}
	$tel = normalize_msisdn($telIn);
	$amount = floatval($amountIn);
	if (!is_numeric($amountIn) || $amount <= 0) {
		throw new Exception('Invalid amount');
	}

	log_line('INIT telIn='.$telIn.' telNorm='.$tel.' amount='.$amount.' tx_ref='.$tx_ref);

	// Configure keys
	hdev_payment::api_id(HDEV_PAYMENT_API_ID);
	hdev_payment::api_key(HDEV_PAYMENT_API_KEY);

	// Initiate payment
	$response = hdev_payment::pay($tel, $amount, $tx_ref, $link);
	log_line('RESP1 tx_ref='.$tx_ref.' raw='.json_encode($response));

	$attempts = [ ['tel'=>$tel, 'resp'=>$response] ];

	// If invalid phone, retry once with +250 format
	if (is_array($response) && isset($response['status']) && strtolower($response['status'])==='error' && stripos((string)($response['message']??''), 'invalid phone')!==false) {
		if (strpos($tel, '2507')===0) {
			$alt = '+'.$tel; // +2507xxxxxxxx
			log_line('RETRY with alt format '.$alt);
			$response2 = hdev_payment::pay($alt, $amount, $tx_ref, $link);
			log_line('RESP2 tx_ref='.$tx_ref.' raw='.json_encode($response2));
			$attempts[] = ['tel'=>$alt, 'resp'=>$response2];
			$response = $response2; // return latest
		}
	}

	echo json_encode([
		'success' => true,
		'tx_ref' => $tx_ref,
		'attempts' => $attempts,
		'payload' => $response
	]);

} catch (Exception $e) {
	log_line('ERROR '.$e->getMessage());
	echo json_encode([
		'success' => false,
		'message' => $e->getMessage()
	]);
}
