<?php
header('Content-Type: application/json');
require_once '../constant/connect.php';

if (session_status() === PHP_SESSION_NONE) { 
    if (session_status() === PHP_SESSION_NONE) {
    session_start();
} 
}

if (!isset($_SESSION['adminId'])) {
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

$pharmacy_id = $_SESSION['pharmacy_id'] ?? 1;
$admin_id = $_SESSION['adminId'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'create':
            createRefundRequest();
            break;
        case 'update':
            updateRefundRequest();
            break;
        case 'delete':
            deleteRefundRequest();
            break;
        case 'approve':
            approveRefundRequest();
            break;
        case 'reject':
            rejectRefundRequest();
            break;
        case 'list':
            getRefundRequests();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function createRefundRequest() {
    global $connect, $pharmacy_id, $admin_id;
    
    $patient_name = $_POST['patient_name'] ?? '';
    $patient_phone = $_POST['patient_phone'] ?? '';
    $medicine_id = $_POST['medicine_id'] ?? '';
    $quantity = $_POST['quantity'] ?? 0;
    $unit_price = $_POST['unit_price'] ?? 0;
    $refund_amount = $_POST['refund_amount'] ?? 0;
    $reason = $_POST['reason'] ?? '';
    $notes = $_POST['notes'] ?? '';
    
    if (empty($patient_name) || empty($patient_phone) || empty($medicine_id)) {
        echo json_encode(['success' => false, 'message' => 'Required fields missing']);
        return;
    }
    
    $total_amount = $quantity * $unit_price;
    
    $stmt = $connect->prepare("
        INSERT INTO refund_requests 
        (patient_name, patient_phone, medicine_id, quantity, unit_price, total_amount, refund_amount, reason, notes, pharmacy_id, admin_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->bind_param("ssiiddsssii", $patient_name, $patient_phone, $medicine_id, $quantity, $unit_price, $total_amount, $refund_amount, $reason, $notes, $pharmacy_id, $admin_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Refund request created successfully', 'id' => $connect->insert_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create refund request']);
    }
    $stmt->close();
}

function updateRefundRequest() {
    global $connect, $pharmacy_id;
    
    $refund_id = $_POST['refund_id'] ?? '';
    $patient_name = $_POST['patient_name'] ?? '';
    $patient_phone = $_POST['patient_phone'] ?? '';
    $medicine_id = $_POST['medicine_id'] ?? '';
    $quantity = $_POST['quantity'] ?? 0;
    $unit_price = $_POST['unit_price'] ?? 0;
    $refund_amount = $_POST['refund_amount'] ?? 0;
    $reason = $_POST['reason'] ?? '';
    $notes = $_POST['notes'] ?? '';
    
    if (empty($refund_id)) {
        echo json_encode(['success' => false, 'message' => 'Refund ID required']);
        return;
    }
    
    $total_amount = $quantity * $unit_price;
    
    $stmt = $connect->prepare("
        UPDATE refund_requests 
        SET patient_name = ?, patient_phone = ?, medicine_id = ?, quantity = ?, unit_price = ?, 
            total_amount = ?, refund_amount = ?, reason = ?, notes = ?
        WHERE refund_id = ? AND pharmacy_id = ?
    ");
    
    $stmt->bind_param("ssiiddsssii", $patient_name, $patient_phone, $medicine_id, $quantity, $unit_price, $total_amount, $refund_amount, $reason, $notes, $refund_id, $pharmacy_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Refund request updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update refund request']);
    }
    $stmt->close();
}

function deleteRefundRequest() {
    global $connect, $pharmacy_id;
    
    $refund_id = $_POST['refund_id'] ?? $_GET['refund_id'] ?? '';
    
    if (empty($refund_id)) {
        echo json_encode(['success' => false, 'message' => 'Refund ID required']);
        return;
    }
    
    $stmt = $connect->prepare("DELETE FROM refund_requests WHERE refund_id = ? AND pharmacy_id = ?");
    $stmt->bind_param("ii", $refund_id, $pharmacy_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Refund request deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete refund request']);
    }
    $stmt->close();
}

function approveRefundRequest() {
    global $connect, $pharmacy_id, $admin_id;
    
    $refund_id = $_POST['refund_id'] ?? $_GET['refund_id'] ?? '';
    
    if (empty($refund_id)) {
        echo json_encode(['success' => false, 'message' => 'Refund ID required']);
        return;
    }
    
    $stmt = $connect->prepare("
        UPDATE refund_requests 
        SET status = 'APPROVED', processed_date = NOW(), admin_id = ?
        WHERE refund_id = ? AND pharmacy_id = ?
    ");
    
    $stmt->bind_param("iii", $admin_id, $refund_id, $pharmacy_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Refund request approved successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to approve refund request']);
    }
    $stmt->close();
}

function rejectRefundRequest() {
    global $connect, $pharmacy_id, $admin_id;
    
    $refund_id = $_POST['refund_id'] ?? $_GET['refund_id'] ?? '';
    
    if (empty($refund_id)) {
        echo json_encode(['success' => false, 'message' => 'Refund ID required']);
        return;
    }
    
    $stmt = $connect->prepare("
        UPDATE refund_requests 
        SET status = 'REJECTED', processed_date = NOW(), admin_id = ?
        WHERE refund_id = ? AND pharmacy_id = ?
    ");
    
    $stmt->bind_param("iii", $admin_id, $refund_id, $pharmacy_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Refund request rejected successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to reject refund request']);
    }
    $stmt->close();
}

function getRefundRequests() {
    global $connect, $pharmacy_id;
    
    $stmt = $connect->prepare("
        SELECT 
            rr.refund_id,
            rr.patient_name,
            rr.patient_phone,
            m.medicine_name,
            rr.quantity,
            rr.unit_price,
            rr.total_amount,
            rr.refund_amount,
            rr.reason,
            rr.status,
            rr.request_date,
            au.username as admin_username,
            rr.notes
        FROM refund_requests rr
        LEFT JOIN medicines m ON rr.medicine_id = m.medicine_id
        LEFT JOIN admin_users au ON rr.admin_id = au.admin_id
        WHERE rr.pharmacy_id = ?
        ORDER BY rr.request_date DESC
    ");
    
    $stmt->bind_param("i", $pharmacy_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $refunds = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    echo json_encode(['success' => true, 'data' => $refunds]);
}
?>
