<?php
require_once __DIR__ . '/../constant/connect.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

try {
	// Require super_admin
	if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'super_admin') {
		throw new Exception('Unauthorized');
	}
	$name = trim($_POST['name'] ?? '');
	$license = trim($_POST['license_number'] ?? '');
	$location = trim($_POST['location'] ?? '');
	$contact_person = trim($_POST['contact_person'] ?? '');
	$contact_phone = trim($_POST['contact_phone'] ?? '');
	$manager_email = trim($_POST['manager_email'] ?? '');
	$manager_password = trim($_POST['manager_password'] ?? '');
	$finance_email = trim($_POST['finance_email'] ?? '');
	$finance_password = trim($_POST['finance_password'] ?? '');

	if ($name === '' || $manager_email === '' || $manager_password === '' || $finance_email === '' || $finance_password === '') {
		throw new Exception('Missing required fields');
	}

	$connect->begin_transaction();

	// Create pharmacy (upsert by name)
	$stmt = $connect->prepare("SELECT pharmacy_id FROM pharmacies WHERE name=? LIMIT 1");
	$stmt->bind_param('s', $name);
	$stmt->execute();
	$res = $stmt->get_result();
	if ($row = $res->fetch_assoc()) {
		$pharmacy_id = (int)$row['pharmacy_id'];
	} else {
		$stmt = $connect->prepare("INSERT INTO pharmacies (name, license_number, location, contact_person, contact_phone) VALUES (?,?,?,?,?)");
		$stmt->bind_param('sssss', $name, $license, $location, $contact_person, $contact_phone);
		if (!$stmt->execute()) { throw new Exception('Failed to create pharmacy: '.$stmt->error); }
		$pharmacy_id = $connect->insert_id;
	}

	// Manager (pharmacy_admin)
	$username_manager = preg_replace('/[^a-z0-9_]+/i', '', strtolower(str_replace(' ', '', $name))).'_mgr';
	$phash_manager = password_hash($manager_password, PASSWORD_BCRYPT);
	$stmt = $connect->prepare("INSERT INTO admin_users (username,email,password_hash,role,pharmacy_id) VALUES (?,?,?,?,?)");
	$role_manager = 'pharmacy_admin';
	$stmt->bind_param('ssssi', $username_manager, $manager_email, $phash_manager, $role_manager, $pharmacy_id);
	if (!$stmt->execute()) { throw new Exception('Failed to create manager: '.$stmt->error); }

	// Finance (finance_admin)
	$username_fin = preg_replace('/[^a-z0-9_]+/i', '', strtolower(str_replace(' ', '', $name))).'_fin';
	$phash_fin = password_hash($finance_password, PASSWORD_BCRYPT);
	$role_fin = 'finance_admin';
	$stmt->bind_param('ssssi', $username_fin, $finance_email, $phash_fin, $role_fin, $pharmacy_id);
	if (!$stmt->execute()) { throw new Exception('Failed to create finance: '.$stmt->error); }

	$connect->commit();
	echo json_encode([
		'success' => true,
		'pharmacy_id' => $pharmacy_id,
		'accounts' => [
			['email'=>$manager_email, 'role'=>'pharmacy_admin'],
			['email'=>$finance_email, 'role'=>'finance_admin']
		]
	]);

} catch (Exception $e) {
	if ($connect) { @$connect->rollback(); }
	echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}


