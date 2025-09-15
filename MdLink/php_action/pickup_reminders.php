<?php
require_once '../constant/connect.php';
require_once '../includes/sms_config.php';

header('Content-Type: application/json');

// Get pharmacy context
$pharmacyId = $_SESSION['pharmacy_id'] ?? 1;

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        createReminder($connect, $pharmacyId);
        break;
        
    case 'get':
        getReminder($connect, $pharmacyId);
        break;
        
    case 'update_status':
        updateReminderStatus($connect, $pharmacyId);
        break;
        
    case 'delete':
        deleteReminder($connect, $pharmacyId);
        break;
        
    case 'send_bulk':
        sendBulkReminders($connect, $pharmacyId);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

function createReminder($connect, $pharmacyId) {
    $patientName = $_POST['patient_name'] ?? '';
    $patientPhone = $_POST['patient_phone'] ?? '';
    $medicineName = $_POST['medicine_name'] ?? '';
    $dosageInstructions = $_POST['dosage_instructions'] ?? '';
    $pickupDate = $_POST['pickup_date'] ?? '';
    $reminderDate = $_POST['reminder_date'] ?? '';
    $status = $_POST['status'] ?? 'pending';
    $notes = $_POST['notes'] ?? '';
    
    if (empty($patientName) || empty($patientPhone) || empty($medicineName) || empty($pickupDate) || empty($reminderDate)) {
        echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
        return;
    }
    
    $sql = "INSERT INTO pickup_reminders (pharmacy_id, patient_name, patient_phone, medicine_name, dosage_instructions, pickup_date, reminder_date, status, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("issssssss", $pharmacyId, $patientName, $patientPhone, $medicineName, $dosageInstructions, $pickupDate, $reminderDate, $status, $notes);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Pickup reminder created successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create reminder: ' . $connect->error]);
    }
}

function getReminder($connect, $pharmacyId) {
    $id = $_GET['id'] ?? 0;
    
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Reminder ID required']);
        return;
    }
    
    $sql = "SELECT * FROM pickup_reminders WHERE id = ? AND pharmacy_id = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("ii", $id, $pharmacyId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($reminder = $result->fetch_assoc()) {
        // Add pharmacy name for SMS
        $reminder['pharmacy_name'] = $_SESSION['pharmacy_name'] ?? 'Pharmacy';
        echo json_encode(['success' => true, 'data' => $reminder]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Reminder not found']);
    }
}

function updateReminderStatus($connect, $pharmacyId) {
    $id = $_POST['id'] ?? 0;
    $status = $_POST['status'] ?? '';
    
    if (!$id || !$status) {
        echo json_encode(['success' => false, 'message' => 'ID and status required']);
        return;
    }
    
    $sql = "UPDATE pickup_reminders SET status = ? WHERE id = ? AND pharmacy_id = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("sii", $status, $id, $pharmacyId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status: ' . $connect->error]);
    }
}

function deleteReminder($connect, $pharmacyId) {
    $id = $_POST['id'] ?? 0;
    
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Reminder ID required']);
        return;
    }
    
    $sql = "DELETE FROM pickup_reminders WHERE id = ? AND pharmacy_id = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("ii", $id, $pharmacyId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Reminder deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete reminder: ' . $connect->error]);
    }
}

function sendBulkReminders($connect, $pharmacyId) {
    // Get all pending reminders
    $sql = "SELECT * FROM pickup_reminders WHERE pharmacy_id = ? AND status = 'pending' AND reminder_date <= NOW()";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("i", $pharmacyId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $sent = 0;
    $failed = 0;
    
    while ($reminder = $result->fetch_assoc()) {
        // Send SMS reminder
        $message = "Dear {$reminder['patient_name']}, this is a reminder from {$_SESSION['pharmacy_name'] ?? 'Pharmacy'} that your medicine {$reminder['medicine_name']} is ready for pickup on {$reminder['pickup_date']}. Please visit us to collect your medication. Dosage: {$reminder['dosage_instructions']}. Thank you for choosing us for your healthcare needs.";
        
        // Use HDEV SMS API
        hdev_sms::api_id(HDEV_SMS_API_ID);
        hdev_sms::api_key(HDEV_SMS_API_KEY);
        
        $response = hdev_sms::send(DEFAULT_SENDER_ID, $reminder['patient_phone'], $message);
        
        if ($response && isset($response->success) && $response->success) {
            // Update status to sent
            $updateSql = "UPDATE pickup_reminders SET status = 'sent' WHERE id = ?";
            $updateStmt = $connect->prepare($updateSql);
            $updateStmt->bind_param("i", $reminder['id']);
            $updateStmt->execute();
            
            $sent++;
        } else {
            $failed++;
        }
        
        // Small delay to avoid rate limiting
        usleep(500000); // 0.5 seconds
    }
    
    echo json_encode([
        'success' => true, 
        'message' => "Bulk reminders sent: $sent successful, $failed failed"
    ]);
}
?>

