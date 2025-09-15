<?php
require_once __DIR__ . '/../constant/connect.php';
header('Content-Type: application/json');

$email = 'superadmin@mdlink.rw';
$username = 'superadmin';
$plain = 'Super@123';
$hash = password_hash($plain, PASSWORD_BCRYPT);

// Upsert
$stmt = $connect->prepare("SELECT admin_id FROM admin_users WHERE email=? LIMIT 1");
$stmt->bind_param('s', $email);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
	$aid = (int)$row['admin_id'];
	$stmt = $connect->prepare("UPDATE admin_users SET username=?, password_hash=?, role='super_admin' WHERE admin_id=?");
	$stmt->bind_param('ssi', $username, $hash, $aid);
	$stmt->execute();
	echo json_encode(['success'=>true,'updated'=>true,'admin_id'=>$aid]);
} else {
	$stmt = $connect->prepare("INSERT INTO admin_users (username,email,password_hash,role) VALUES (?,?,?,'super_admin')");
	$stmt->bind_param('sss', $username, $email, $hash);
	$stmt->execute();
	echo json_encode(['success'=>true,'created'=>true,'admin_id'=>$connect->insert_id]);
}


