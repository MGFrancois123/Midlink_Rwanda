<?php
require_once 'db_connect.php';
header('Content-Type: application/json');

// Auto-create recalls table
@$connect->query("CREATE TABLE IF NOT EXISTS recall_alerts (
  recall_id INT(11) NOT NULL AUTO_INCREMENT,
  medicine_name VARCHAR(191) NOT NULL,
  batch_number VARCHAR(100) DEFAULT NULL,
  category_name VARCHAR(150) DEFAULT NULL,
  pharmacy_id INT(11) DEFAULT NULL,
  reason TEXT,
  severity ENUM('info','warning','critical') NOT NULL DEFAULT 'warning',
  announced_on DATE NOT NULL,
  status ENUM('open','in_progress','resolved') NOT NULL DEFAULT 'open',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY(recall_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? trim($_GET['action']) : 'list';

function out($d,$c=200){ http_response_code($c); echo json_encode($d); exit; }

if ($action === 'list' && $method === 'GET') {
  $rows = [];
  $q = $connect->query("SELECT r.*, p.name AS pharmacy_name FROM recall_alerts r LEFT JOIN pharmacies p ON r.pharmacy_id = p.pharmacy_id ORDER BY r.announced_on DESC, r.recall_id DESC");
  if ($q) { while($r=$q->fetch_assoc()){ $rows[]=$r; } }
  out(['items'=>$rows]);
}

if ($action === 'create' && $method === 'POST') {
  $name = trim((string)($_POST['medicine_name'] ?? ''));
  $batch = trim((string)($_POST['batch_number'] ?? ''));
  $cat = trim((string)($_POST['category_name'] ?? ''));
  $ph = isset($_POST['pharmacy_id']) ? (int)$_POST['pharmacy_id'] : null;
  $reason = trim((string)($_POST['reason'] ?? ''));
  $severity = trim((string)($_POST['severity'] ?? 'warning'));
  $date = trim((string)($_POST['announced_on'] ?? date('Y-m-d')));
  if ($name==='') out(['success'=>false,'message'=>'Medicine name required'],400);
  $stmt = $connect->prepare('INSERT INTO recall_alerts (medicine_name,batch_number,category_name,pharmacy_id,reason,severity,announced_on) VALUES (?,?,?,?,?,?,?)');
  if(!$stmt) out(['success'=>false,'message'=>$connect->error],500);
  $stmt->bind_param('sssssss',$name,$batch,$cat,$ph,$reason,$severity,$date);
  $ok = $stmt->execute();
  out(['success'=>$ok,'id'=>$stmt->insert_id]);
}

if ($action === 'update' && $method === 'POST') {
  $id = (int)($_POST['recall_id'] ?? 0);
  if ($id<=0) out(['success'=>false,'message'=>'Invalid id'],400);
  $name = trim((string)($_POST['medicine_name'] ?? ''));
  $batch = trim((string)($_POST['batch_number'] ?? ''));
  $cat = trim((string)($_POST['category_name'] ?? ''));
  $ph = isset($_POST['pharmacy_id']) ? (int)$_POST['pharmacy_id'] : null;
  $reason = trim((string)($_POST['reason'] ?? ''));
  $severity = trim((string)($_POST['severity'] ?? 'warning'));
  $date = trim((string)($_POST['announced_on'] ?? date('Y-m-d')));
  $status = trim((string)($_POST['status'] ?? 'open'));
  $stmt = $connect->prepare('UPDATE recall_alerts SET medicine_name=?, batch_number=?, category_name=?, pharmacy_id=?, reason=?, severity=?, announced_on=?, status=? WHERE recall_id=?');
  if(!$stmt) out(['success'=>false,'message'=>$connect->error],500);
  $stmt->bind_param('ssssssssi',$name,$batch,$cat,$ph,$reason,$severity,$date,$status,$id);
  $ok = $stmt->execute();
  out(['success'=>(bool)$ok]);
}

if ($action === 'delete' && $method === 'POST') {
  $id = (int)($_POST['recall_id'] ?? 0);
  if ($id<=0) out(['success'=>false,'message'=>'Invalid id'],400);
  $stmt = $connect->prepare('DELETE FROM recall_alerts WHERE recall_id=? LIMIT 1');
  if(!$stmt) out(['success'=>false,'message'=>$connect->error],500);
  $stmt->bind_param('i',$id);
  $ok=$stmt->execute();
  out(['success'=>(bool)$ok]);
}

out(['error'=>'Invalid action'],400);

