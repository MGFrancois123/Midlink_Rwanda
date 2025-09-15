<?php
require_once __DIR__ . '/../constant/connect.php';

function find_pharmacy_id(mysqli $db, string $needle): ?int {
	$needle = trim($needle);
	$q = $db->query("SELECT pharmacy_id FROM pharmacies WHERE LOWER(name) LIKE LOWER('%".$db->real_escape_string($needle)."%') LIMIT 1");
	if ($q && ($r=$q->fetch_assoc())) return (int)$r['pharmacy_id'];
	return null;
}

function upsert_medicine(mysqli $db, int $pharmacyId, string $name, float $price, int $qty, ?string $expiry, int $categoryId, int $restricted=0) {
	$stmt = $db->prepare("SELECT medicine_id FROM medicines WHERE pharmacy_id=? AND name=? LIMIT 1");
	$stmt->bind_param('is', $pharmacyId, $name);
	$stmt->execute();
	$res = $stmt->get_result();
	if ($row = $res->fetch_assoc()) return (int)$row['medicine_id'];
	$sql = "INSERT INTO medicines (pharmacy_id,name,description,price,stock_quantity,expiry_date,`Restricted Medicine`,category_id) VALUES (?,?,?,?,?,?,?,?)";
	$stmt = $db->prepare($sql);
	$desc = '';
	$stmt->bind_param('issdiisi', $pharmacyId, $name, $desc, $price, $qty, $expiry, $restricted, $categoryId);
	$stmt->execute();
	return $db->insert_id;
}

function add_stock_movement(mysqli $db, int $medicineId, string $type, int $qty, string $reason='Initial stock') {
	// Use 'notes' column as that's what exists in the table
	$stmt = $db->prepare("INSERT INTO stock_movements (medicine_id,movement_type,quantity,notes,created_at) VALUES (?,?,?,?,NOW())");
	$stmt->bind_param('isis', $medicineId, $type, $qty, $reason);
	$stmt->execute();
}

try {
	$inezaId = find_pharmacy_id($connect, 'Ineza');
	if (!$inezaId) throw new Exception('Ineza Pharmacy not found');

	$created = [];

	// Ineza Pharmacy - Hospital/Clinical Focus
	$medicines = [
		['Ceftriaxone 1g (Ineza)', 950.0, 60, '2025-02-15', 1, 1],
		['Ringers Lactate 500ml (Ineza)', 700.0, 120, '2025-09-15', 1, 0],
		['Surgical Spirit 100ml (Ineza)', 300.0, 80, '2026-03-15', 1, 0],
		['IV Cannula 18G (Ineza)', 250.0, 200, '2026-09-15', 1, 0],
		['Surgical Gloves L (Ineza)', 180.0, 150, '2025-12-15', 1, 0],
		['Antibiotic Injection (Ineza)', 1200.0, 45, '2024-12-15', 1, 1],
		['Surgical Masks (Ineza)', 120.0, 300, '2025-09-15', 1, 0],
		['Sterile Gauze (Ineza)', 200.0, 100, '2026-05-15', 1, 0],
		['Medical Tape (Ineza)', 150.0, 120, '2026-01-15', 1, 0],
		['Emergency Kit (Ineza)', 2500.0, 15, '2026-09-15', 1, 0]
	];
	
	foreach ($medicines as $med) {
		$id = upsert_medicine($connect, $inezaId, $med[0], $med[1], $med[2], $med[3], $med[4], $med[5]);
		add_stock_movement($connect, $id, 'IN', $med[2], 'Initial stock - Ineza Pharmacy');
		$created[] = $id;
	}

	echo json_encode(['success'=>true,'created'=>$created,'pharmacy'=>'Ineza']);
} catch (Exception $e) {
	echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
