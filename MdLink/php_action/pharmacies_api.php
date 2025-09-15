<?php
require_once 'db_connect.php';
header('Content-Type: application/json');

if (!isset($connect) || !($connect instanceof mysqli)) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection not available']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? trim($_GET['action']) : 'list';

function respond($data, int $code = 200): void {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

if ($action === 'list' && $method === 'GET') {
    $items = [];
    $q = $connect->query("SELECT pharmacy_id, name, location, license_number, contact_person, contact_phone, created_at FROM pharmacies ORDER BY name");
    if ($q) { while ($r = $q->fetch_assoc()) { $items[] = $r; } }
    respond(['items' => $items]);
}

if ($action === 'create' && $method === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $location = isset($_POST['location']) ? trim($_POST['location']) : '';
    $license = isset($_POST['license_number']) ? trim($_POST['license_number']) : '';
    $person = isset($_POST['contact_person']) ? trim($_POST['contact_person']) : '';
    $phone = isset($_POST['contact_phone']) ? trim($_POST['contact_phone']) : '';

    if ($name === '' || $license === '') {
        respond(['success' => false, 'message' => 'Name and License are required'], 400);
    }

    $stmt = $connect->prepare('INSERT INTO pharmacies (name, location, license_number, contact_person, contact_phone, created_at) VALUES (?,?,?,?,?,NOW())');
    if (!$stmt) { respond(['success' => false, 'message' => $connect->error], 500); }
    $stmt->bind_param('sssss', $name, $location, $license, $person, $phone);
    $ok = $stmt->execute();
    if ($ok) { respond(['success' => true, 'id' => $stmt->insert_id]); }
    respond(['success' => false, 'message' => $stmt->error], 500);
}

if ($action === 'update' && $method === 'POST') {
    $id = isset($_POST['pharmacy_id']) ? (int)$_POST['pharmacy_id'] : 0;
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $location = isset($_POST['location']) ? trim($_POST['location']) : '';
    $license = isset($_POST['license_number']) ? trim($_POST['license_number']) : '';
    $person = isset($_POST['contact_person']) ? trim($_POST['contact_person']) : '';
    $phone = isset($_POST['contact_phone']) ? trim($_POST['contact_phone']) : '';
    if ($id <= 0) { respond(['success' => false, 'message' => 'Invalid id'], 400); }

    $stmt = $connect->prepare('UPDATE pharmacies SET name=?, location=?, license_number=?, contact_person=?, contact_phone=? WHERE pharmacy_id=?');
    if (!$stmt) { respond(['success' => false, 'message' => $connect->error], 500); }
    $stmt->bind_param('sssssi', $name, $location, $license, $person, $phone, $id);
    $ok = $stmt->execute();
    respond(['success' => (bool)$ok]);
}

if ($action === 'delete' && $method === 'POST') {
    $id = isset($_POST['pharmacy_id']) ? (int)$_POST['pharmacy_id'] : 0;
    if ($id <= 0) { respond(['success' => false, 'message' => 'Invalid id'], 400); }
    $stmt = $connect->prepare('DELETE FROM pharmacies WHERE pharmacy_id=? LIMIT 1');
    if (!$stmt) { respond(['success' => false, 'message' => $connect->error], 500); }
    $stmt->bind_param('i', $id);
    $ok = $stmt->execute();
    respond(['success' => (bool)$ok]);
}

respond(['error' => 'Invalid action'], 400);