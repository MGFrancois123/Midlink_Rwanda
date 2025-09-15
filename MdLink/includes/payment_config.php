<?php
// HDEV Payment config
// Replace with your live keys
define('HDEV_PAYMENT_API_ID', 'HDEV-2f7b3554-eb27-477b-8ebb-2ca799f03412-ID');
define('HDEV_PAYMENT_API_KEY', 'HDEV-28407ece-5d24-438d-a9e8-73105c905a7d-KEY');

// Default callback/landing link (optional)
define('HDEV_PAYMENT_CALLBACK_LINK', '');

// Utility: sanitize MSISDN to local format 2507xxxxxxxx or 07xxxxxxxx (no plus)
function normalize_msisdn($phone) {
	$phone = trim((string)$phone);
	$digits = preg_replace('/\D+/', '', $phone); // keep digits only
	// Already 2507xxxxxxxx
	if (strpos($digits, '2507') === 0 && strlen($digits) === 12) {
		return $digits;
	}
	// 07xxxxxxxx -> 2507xxxxxxxx
	if (strpos($digits, '07') === 0 && strlen($digits) === 10) {
		return '250' . substr($digits, 1); // drop leading 0
	}
	// 9-digit starting with 7 -> 2507xxxxxxxx
	if (strlen($digits) === 9 && $digits[0] === '7') {
		return '250' . $digits;
	}
	return $digits; // fallback raw digits
}
