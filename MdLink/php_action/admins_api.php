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

function send($data, int $code = 200): void { http_response_code($code); echo json_encode($data); exit; }

if ($action === 'list' && $method === 'GET') {
    $rows = [];
    $q = $connect->query("SELECT admin_id, username, email, phone, role, created_at FROM admin_users ORDER BY created_at DESC");
    if ($q) { while($r=$q->fetch_assoc()){ $rows[] = $r; } }
    send(['items' => $rows]);
}

if ($action === 'create' && $method === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $role = isset($_POST['role']) ? trim($_POST['role']) : '';
    $password = isset($_POST['password']) ? (string)$_POST['password'] : '';
    if ($username === '' || $email === '' || $role === '') { send(['success'=>false,'message'=>'Username, Email and Role are required'], 400); }
    if (!in_array($role, ['super_admin','admin','staff'], true)) { send(['success'=>false,'message'=>'Invalid role'], 400); }

    // Ensure unique email
    if ($st = $connect->prepare('SELECT admin_id FROM admin_users WHERE LOWER(TRIM(email)) = LOWER(TRIM(?)) LIMIT 1')) {
        $st->bind_param('s', $email);
        $st->execute();
        $st->store_result();
        if ($st->num_rows > 0) { send(['success'=>false,'message'=>'Email already in use'], 409); }
    }

    $hash = $password !== '' ? md5($password) : md5(bin2hex(random_bytes(8)));
    $stmt = $connect->prepare('INSERT INTO admin_users (username, password_hash, role, email, phone, created_at) VALUES (?,?,?,?,?,NOW())');
    if (!$stmt) { send(['success'=>false,'message'=>$connect->error], 500); }
    $stmt->bind_param('sssss', $username, $hash, $role, $email, $phone);
    $ok = $stmt->execute();
    if ($ok) send(['success'=>true,'id'=>$stmt->insert_id]);
    send(['success'=>false,'message'=>$stmt->error], 500);
}

if ($action === 'update' && $method === 'POST') {
    $id = isset($_POST['admin_id']) ? (int)$_POST['admin_id'] : 0;
    if ($id <= 0) { send(['success'=>false,'message'=>'Invalid id'], 400); }
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $role = isset($_POST['role']) ? trim($_POST['role']) : '';
    $password = isset($_POST['password']) ? (string)$_POST['password'] : '';
    if ($username === '' || $email === '' || $role === '') { send(['success'=>false,'message'=>'Username, Email and Role are required'], 400); }
    if (!in_array($role, ['super_admin','admin','staff'], true)) { send(['success'=>false,'message'=>'Invalid role'], 400); }

    // Unique email (exclude current)
    if ($st = $connect->prepare('SELECT admin_id FROM admin_users WHERE LOWER(TRIM(email)) = LOWER(TRIM(?)) AND admin_id <> ? LIMIT 1')) {
        $st->bind_param('si', $email, $id);
        $st->execute();
        $st->store_result();
        if ($st->num_rows > 0) { send(['success'=>false,'message'=>'Email already in use'], 409); }
    }

    if ($password !== '') {
        $hash = md5($password);
        $stmt = $connect->prepare('UPDATE admin_users SET username=?, email=?, phone=?, role=?, password_hash=? WHERE admin_id=?');
        if (!$stmt) { send(['success'=>false,'message'=>$connect->error], 500); }
        $stmt->bind_param('sssssi', $username, $email, $phone, $role, $hash, $id);
        $ok = $stmt->execute();
        send(['success'=>(bool)$ok]);
    } else {
        $stmt = $connect->prepare('UPDATE admin_users SET username=?, email=?, phone=?, role=? WHERE admin_id=?');
        if (!$stmt) { send(['success'=>false,'message'=>$connect->error], 500); }
        $stmt->bind_param('ssssi', $username, $email, $phone, $role, $id);
        $ok = $stmt->execute();
        send(['success'=>(bool)$ok]);
    }
}

if ($action === 'delete' && $method === 'POST') {
    $id = isset($_POST['admin_id']) ? (int)$_POST['admin_id'] : 0;
    if ($id <= 0) { send(['success'=>false,'message'=>'Invalid id'], 400); }
    // Prevent deleting the primary super admin id 1 for safety
    if ($id === 1) { send(['success'=>false,'message'=>'Cannot delete primary super admin'], 403); }
    $stmt = $connect->prepare('DELETE FROM admin_users WHERE admin_id=? LIMIT 1');
    if (!$stmt) { send(['success'=>false,'message'=>$connect->error], 500); }
    $stmt->bind_param('i', $id);
    $ok = $stmt->execute();
    send(['success'=>(bool)$ok]);
}

send(['error'=>'Invalid action'], 400);

