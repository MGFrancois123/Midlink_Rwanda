<?php 
require_once 'core.php';
header('Content-Type: application/json');

$license = isset($_GET['q']) ? trim($_GET['q']) : '';
$search = isset($_GET['search']) ? intval($_GET['search']) : 0;
$all = isset($_GET['all']) ? intval($_GET['all']) : 0;
$result = array('found' => false);

if ($search === 1) {
    $term = mysqli_real_escape_string($connect, $license);
    $rows = array();
    $sql = "SELECT license_number, name FROM pharmacies WHERE license_number LIKE '%$term%' OR name LIKE '%$term%' ORDER BY license_number LIMIT 10";
    $q = $connect->query($sql);
    if ($q) { while ($r = $q->fetch_assoc()) { $rows[] = $r; } }
    echo json_encode(array('suggestions' => $rows));
    exit;
}

if ($all === 1) {
    $rows = array();
    $sql = "SELECT license_number, name FROM pharmacies ORDER BY name";
    $q = $connect->query($sql);
    if ($q) { while ($r = $q->fetch_assoc()) { $rows[] = $r; } }
    echo json_encode(array('items' => $rows));
    exit;
}

if ($license !== '') {
    $licenseEsc = mysqli_real_escape_string($connect, $license);
    $sql = "SELECT name, license_number, location, contact_person, contact_phone, created_at FROM pharmacies WHERE license_number = '$licenseEsc' LIMIT 1";
    $query = $connect->query($sql);
    if ($query && $query->num_rows > 0) {
        $row = $query->fetch_assoc();
        $result['found'] = true;
        $result['pharmacy'] = $row;
    }
}

echo json_encode($result);


