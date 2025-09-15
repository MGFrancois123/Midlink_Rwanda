<?php 
require_once 'core.php';
header('Content-Type: application/json');

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'list') {
    $rows = array();
    $q = $connect->query("SELECT pharmacy_id, name, contact_person, contact_phone, created_at FROM pharmacies ORDER BY name");
    if ($q) { while ($r = $q->fetch_assoc()) { $rows[] = $r; } }
    echo json_encode(array('items' => $rows));
    exit;
}

if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['pharmacy_id']) ? intval($_POST['pharmacy_id']) : 0;
    $person = isset($_POST['contact_person']) ? trim($_POST['contact_person']) : '';
    $phone = isset($_POST['contact_phone']) ? trim($_POST['contact_phone']) : '';
    if ($id > 0) {
        $personEsc = mysqli_real_escape_string($connect, $person);
        $phoneEsc = mysqli_real_escape_string($connect, $phone);
        $connect->query("UPDATE pharmacies SET contact_person='$personEsc', contact_phone='$phoneEsc' WHERE pharmacy_id=$id");
        echo json_encode(array('success' => true));
        exit;
    }
    echo json_encode(array('success' => false));
    exit;
}

if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['pharmacy_id']) ? intval($_POST['pharmacy_id']) : 0;
    if ($id > 0) {
        $ok = $connect->query("DELETE FROM pharmacies WHERE pharmacy_id=$id LIMIT 1");
        echo json_encode(array('success' => (bool)$ok));
        exit;
    }
    echo json_encode(array('success' => false));
    exit;
}

echo json_encode(array('error' => 'Invalid action'));


