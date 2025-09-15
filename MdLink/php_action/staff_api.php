<?php
require_once 'db_connect.php';
header('Content-Type: application/json');

if (!isset($connect) || !($connect instanceof mysqli)) {
	http_response_code(500);
	echo json_encode(['error' => 'DB connection not available']);
	exit;
}

// Ensure staff table exists (idempotent)
@$connect->query("CREATE TABLE IF NOT EXISTS medical_staff (
  staff_id INT(11) NOT NULL AUTO_INCREMENT,
  full_name VARCHAR(150) NOT NULL,
  role ENUM('doctor','nurse') NOT NULL,
  license_number VARCHAR(100) DEFAULT NULL,
  specialty VARCHAR(150) DEFAULT NULL,
  phone VARCHAR(25) DEFAULT NULL,
  email VARCHAR(150) DEFAULT NULL,
  assigned_pharmacy_id INT(11) DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(staff_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? trim($_GET['action']) : 'list';

function respond_json($data, int $code = 200): void { http_response_code($code); echo json_encode($data); exit; }

if ($action === 'list' && $method === 'GET') {
	$rows = [];
	$q = $connect->query("SELECT s.staff_id, s.full_name, s.role, s.license_number, s.specialty, s.phone, s.email, s.assigned_pharmacy_id, p.name AS pharmacy_name, s.created_at
						 FROM medical_staff s LEFT JOIN pharmacies p ON s.assigned_pharmacy_id = p.pharmacy_id
						 ORDER BY s.created_at DESC");
	if ($q) { while($r=$q->fetch_assoc()){ $rows[] = $r; } }
	respond_json(['items' => $rows]);
}

if ($action === 'create' && $method === 'POST') {
	$name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
	$role = isset($_POST['role']) ? trim($_POST['role']) : '';
	$license = isset($_POST['license_number']) ? trim($_POST['license_number']) : null;
	$specialty = isset($_POST['specialty']) ? trim($_POST['specialty']) : null;
	$phone = isset($_POST['phone']) ? trim($_POST['phone']) : null;
	$email = isset($_POST['email']) ? trim($_POST['email']) : null;
	$pharmacyId = isset($_POST['assigned_pharmacy_id']) ? (int)$_POST['assigned_pharmacy_id'] : null;
	if ($name === '' || !in_array($role, ['doctor','nurse'], true)) { respond_json(['success'=>false,'message'=>'Name and valid role are required'], 400); }
	$stmt = $connect->prepare('INSERT INTO medical_staff (full_name, role, license_number, specialty, phone, email, assigned_pharmacy_id) VALUES (?,?,?,?,?,?,?)');
	if (!$stmt) { respond_json(['success'=>false,'message'=>$connect->error], 500); }
	$stmt->bind_param('ssssssi', $name, $role, $license, $specialty, $phone, $email, $pharmacyId);
	$ok = $stmt->execute();
	if ($ok) respond_json(['success'=>true,'id'=>$stmt->insert_id]);
	respond_json(['success'=>false,'message'=>$stmt->error], 500);
}

if ($action === 'update' && $method === 'POST') {
	$id = isset($_POST['staff_id']) ? (int)$_POST['staff_id'] : 0;
	if ($id <= 0) { respond_json(['success'=>false,'message'=>'Invalid id'], 400); }
	$name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
	$role = isset($_POST['role']) ? trim($_POST['role']) : '';
	$license = isset($_POST['license_number']) ? trim($_POST['license_number']) : null;
	$specialty = isset($_POST['specialty']) ? trim($_POST['specialty']) : null;
	$phone = isset($_POST['phone']) ? trim($_POST['phone']) : null;
	$email = isset($_POST['email']) ? trim($_POST['email']) : null;
	$pharmacyId = isset($_POST['assigned_pharmacy_id']) ? (int)$_POST['assigned_pharmacy_id'] : null;
	$stmt = $connect->prepare('UPDATE medical_staff SET full_name=?, role=?, license_number=?, specialty=?, phone=?, email=?, assigned_pharmacy_id=? WHERE staff_id=?');
	if (!$stmt) { respond_json(['success'=>false,'message'=>$connect->error], 500); }
	$stmt->bind_param('ssssssii', $name, $role, $license, $specialty, $phone, $email, $pharmacyId, $id);
	$ok = $stmt->execute();
	respond_json(['success'=>(bool)$ok]);
}

if ($action === 'delete' && $method === 'POST') {
	$id = isset($_POST['staff_id']) ? (int)$_POST['staff_id'] : 0;
	if ($id <= 0) { respond_json(['success'=>false,'message'=>'Invalid id'], 400); }
	$stmt = $connect->prepare('DELETE FROM medical_staff WHERE staff_id=? LIMIT 1');
	if (!$stmt) { respond_json(['success'=>false,'message'=>$connect->error], 500); }
	$stmt->bind_param('i', $id);
	$ok = $stmt->execute();
	respond_json(['success'=>(bool)$ok]);
}

respond_json(['error'=>'Invalid action'], 400);
