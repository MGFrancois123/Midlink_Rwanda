<?php
require_once __DIR__ . '/../constant/connect.php';
header('Content-Type: application/json');

function upsert_pharmacy($connect, $name, $license, $location, $contact_person, $contact_phone) {
	$stmt = $connect->prepare("SELECT pharmacy_id FROM pharmacies WHERE name=? LIMIT 1");
	$stmt->bind_param('s', $name);
	$stmt->execute();
	$res = $stmt->get_result();
	if ($row = $res->fetch_assoc()) return (int)$row['pharmacy_id'];
	$stmt = $connect->prepare("INSERT INTO pharmacies (name, license_number, location, contact_person, contact_phone) VALUES (?,?,?,?,?)");
	$stmt->bind_param('sssss', $name, $license, $location, $contact_person, $contact_phone);
	$stmt->execute();
	return $connect->insert_id;
}

function upsert_user($connect, $email, $password, $role, $pharmacy_id, $username_hint) {
	$stmt = $connect->prepare("SELECT admin_id FROM admin_users WHERE email=? LIMIT 1");
	$stmt->bind_param('s', $email);
	$stmt->execute();
	$res = $stmt->get_result();
	if ($row = $res->fetch_assoc()) return (int)$row['admin_id'];
	$username = preg_replace('/[^a-z0-9_]+/i', '', strtolower(str_replace(' ', '', $username_hint))).'_'.substr($role,0,3);
	$hash = password_hash($password, PASSWORD_BCRYPT);
	$stmt = $connect->prepare("INSERT INTO admin_users (username,email,password_hash,role,pharmacy_id) VALUES (?,?,?,?,?)");
	$stmt->bind_param('ssssi', $username, $email, $hash, $role, $pharmacy_id);
	$stmt->execute();
	return $connect->insert_id;
}

$created = [];

$keza_id = upsert_pharmacy($connect, 'Keza Pharmacy', 'MDLink-KEZA', 'Kigali', 'Keza Manager', '0786000000');
$created[] = ['pharmacy'=>'Keza Pharmacy','id'=>$keza_id];
$kez_mgr = upsert_user($connect, 'kezapharma@gmail.com', 'kezapharma@123', 'pharmacy_admin', $keza_id, 'KezaPharmacy');
$kez_fin = upsert_user($connect, 'kezafinance@gmail.com', 'kezafinance@123', 'finance_admin', $keza_id, 'KezaFinance');

$ineza_id = upsert_pharmacy($connect, 'Ineza Pharmacy', 'MDLink-INEZA', 'Kigali', 'Ineza Manager', '0786111111');
$created[] = ['pharmacy'=>'Ineza Pharmacy','id'=>$ineza_id];
$ine_mgr = upsert_user($connect, 'inezapharma@gmail.com', 'inezapharma@123', 'pharmacy_admin', $ineza_id, 'InezaPharmacy');
$ine_fin = upsert_user($connect, 'inezafinance@gmail.com', 'inezafinance@123', 'finance_admin', $ineza_id, 'InezaFinance');

echo json_encode([
	'success' => true,
	'pharmacies' => $created,
	'accounts' => [
		['email'=>'kezapharma@gmail.com','password'=>'kezapharma@123','role'=>'pharmacy_admin'],
		['email'=>'kezafinance@gmail.com','password'=>'kezafinance@123','role'=>'finance_admin'],
		['email'=>'inezapharma@gmail.com','password'=>'inezapharma@123','role'=>'pharmacy_admin'],
		['email'=>'inezafinance@gmail.com','password'=>'inezafinance@123','role'=>'finance_admin']
	]
]);
