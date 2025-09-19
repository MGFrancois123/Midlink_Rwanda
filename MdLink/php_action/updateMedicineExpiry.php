<?php
require_once '../constant/connect.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $medicine_id = (int)$_POST['medicine_id'];
    $expiry_date = $_POST['expiry_date'];

    // Validate inputs
    if (!$medicine_id || !$expiry_date) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }

    // Validate date format
    $date = DateTime::createFromFormat('Y-m-d', $expiry_date);
    if (!$date || $date->format('Y-m-d') !== $expiry_date) {
        echo json_encode(['success' => false, 'message' => 'Invalid date format. Use YYYY-MM-DD']);
        exit;
    }

    // Check if medicine exists
    $sql = "SELECT medicine_id, name FROM medicines WHERE medicine_id = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param('i', $medicine_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Medicine not found']);
        exit;
    }

    $medicine = $result->fetch_assoc();

    // Update expiry date
    $sql = "UPDATE medicines SET expiry_date = ? WHERE medicine_id = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param('si', $expiry_date, $medicine_id);
    
    if ($stmt->execute()) {
        // Log the change (optional - you can create an audit log table)
        $user_id = $_SESSION['userId'] ?? 1;
        $notes = "Expiry date updated to " . $expiry_date . " for medicine: " . $medicine['name'];
        
        // You can add audit logging here if needed
        
        echo json_encode([
            'success' => true, 
            'message' => 'Expiry date updated successfully',
            'medicine_name' => $medicine['name'],
            'new_expiry_date' => $expiry_date
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update expiry date']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>