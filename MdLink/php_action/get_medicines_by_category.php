<?php
require_once '../constant/connect.php';
header('Content-Type: application/json');

$categoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
if ($categoryId <= 0) {
    echo json_encode(['success' => false, 'items' => []]);
    exit;
}

$items = [];
$stmt = $connect->prepare('SELECT medicine_id, name, COALESCE(description, "") AS description FROM medicines WHERE category_id = ? ORDER BY name');
if ($stmt) {
    $stmt->bind_param('i', $categoryId);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $items[] = $row;
    }
}

echo json_encode(['success' => true, 'items' => $items]);
?>


