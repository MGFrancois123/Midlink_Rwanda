<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../constant/connect.php';
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

function json_response($ok, $msg = '', $extra = []) {
	$res = array_merge(['success' => $ok, 'message' => $msg], $extra);
	echo json_encode($res);
	exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	json_response(false, 'Invalid request method');
}

$action = isset($_POST['action']) ? $_POST['action'] : '';
if ($action !== 'create') {
	json_response(false, 'Unsupported action');
}

// Log received data for debugging
error_log("Stock Movement Request - Action: $action, POST data: " . print_r($_POST, true));

$pharmacyId = isset($_POST['pharmacy_id']) ? (int)$_POST['pharmacy_id'] : 0;
$medicineId = isset($_POST['medicine_id']) ? (int)$_POST['medicine_id'] : 0;
$type = isset($_POST['movement_type']) ? strtolower(trim($_POST['movement_type'])) : '';
$qty = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
$notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';

if (!$pharmacyId || !$medicineId || !in_array($type, ['in','out','adjustment'])) {
	json_response(false, 'Invalid input');
}
if (($type === 'in' || $type === 'out') && $qty <= 0) {
	json_response(false, 'Quantity must be greater than zero');
}
if ($type === 'adjustment' && $qty < 0) {
	json_response(false, 'New stock level cannot be negative');
}

// Validate medicine belongs to pharmacy
$check = $connect->prepare("SELECT stock_quantity, pharmacy_id FROM medicines WHERE medicine_id = ? LIMIT 1");
if (!$check) { json_response(false, 'DB error: '.$connect->error); }
$check->bind_param('i', $medicineId);
$check->execute();
$res = $check->get_result();
if (!$res || $res->num_rows === 0) { json_response(false, 'Medicine not found'); }
$row = $res->fetch_assoc();
if ((int)$row['pharmacy_id'] !== $pharmacyId) {
	json_response(false, 'Medicine does not belong to selected pharmacy');
}
$currentStock = (int)$row['stock_quantity'];

// Compute new stock and movement record quantity
$newStock = $currentStock;
$recordQty = $qty; // stored in stock_movements.quantity
if ($type === 'in') {
	$newStock += $qty;
} elseif ($type === 'out') {
	if ($qty > $currentStock) { json_response(false, 'Insufficient stock'); }
	$newStock -= $qty;
} else { // adjustment: set absolute stock level
	$newStock = $qty;
	$recordQty = $newStock - $currentStock; // can be negative
}
if ($newStock < 0) { json_response(false, 'Resulting stock cannot be negative'); }

$connect->begin_transaction();
	try {
		// Generate reference number
		$refPrefix = strtoupper($type);
		$refQuery = $connect->prepare("SELECT COUNT(*) as count FROM stock_movements WHERE movement_type = ?");
		$refQuery->bind_param('s', $type);
		$refQuery->execute();
		$refResult = $refQuery->get_result();
		$refCount = $refResult->fetch_assoc()['count'] + 1;
		$referenceNumber = $refPrefix . '-' . date('Y') . '-' . str_pad($refCount, 3, '0', STR_PAD_LEFT);
		
		// Get current user ID (you may need to adjust this based on your session system)
		$userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 1;
		
		// Insert movement with all required fields
		$ins = $connect->prepare("INSERT INTO stock_movements (medicine_id, movement_type, quantity, previous_stock, new_stock, reference_number, notes, user_id, movement_date, created_at) VALUES (?,?,?,?,?,?,?,?,NOW(),NOW())");
		if (!$ins) { throw new Exception('DB error: '.$connect->error); }
		$ins->bind_param('siissssi', $medicineId, $type, $recordQty, $currentStock, $newStock, $referenceNumber, $notes, $userId);
		$ins->execute();

		// Update stock
		$upd = $connect->prepare("UPDATE medicines SET stock_quantity = ? WHERE medicine_id = ?");
		if (!$upd) { throw new Exception('DB error: '.$connect->error); }
		$upd->bind_param('ii', $newStock, $medicineId);
		$upd->execute();

		$connect->commit();
		json_response(true, 'Movement recorded successfully. Reference: ' . $referenceNumber);
	} catch (Exception $e) {
		$connect->rollback();
		json_response(false, $e->getMessage());
	}
?>


