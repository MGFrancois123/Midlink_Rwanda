<?php
require_once '../constant/connect.php';
require_once '../includes/payment_config.php';
require_once '../includes/pay_parse.php';
header('Content-Type: application/json');

try {
	$tx_ref = $_GET['tx_ref'] ?? $_POST['tx_ref'] ?? '';
	if ($tx_ref === '') {
		throw new Exception('tx_ref is required');
	}

	hdev_payment::api_id(HDEV_PAYMENT_API_ID);
	hdev_payment::api_key(HDEV_PAYMENT_API_KEY);

	$response = hdev_payment::get_pay($tx_ref);

	echo json_encode([
		'success' => true,
		'payload' => $response
	]);

} catch (Exception $e) {
	echo json_encode([
		'success' => false,
		'message' => $e->getMessage()
	]);
}
