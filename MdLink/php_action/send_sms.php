<?php
session_start();
require_once '../constant/connect.php';
header('Content-Type: application/json');

// Include the SMS parser class and configuration
require_once '../includes/Sms_parse.php';
require_once '../includes/sms_config.php';

try {
    // Get user role and pharmacy_id for data scoping
    $userRole = $_SESSION['userRole'] ?? '';
    $pharmacyId = $_SESSION['pharmacy_id'] ?? null;
    
    // Get SMS parameters
    $senderId = $_POST['sender_id'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $message = $_POST['message'] ?? '';
    $type = $_POST['type'] ?? 'general';
    
    // Validate required fields
    if (empty($senderId) || empty($phone) || empty($message)) {
        throw new Exception("All fields are required");
    }
    
    // Validate and normalize sender ID
    $senderId = validateSenderId($senderId);
    
    // Validate message length (max 160 characters)
    if (strlen($message) > 160) {
        throw new Exception("Message must be 160 characters or less");
    }
    
    // Normalize and validate phone number
    $phone = normalizePhoneNumber($phone);
    if (!preg_match('/^\+250[0-9]{9}$/', $phone)) {
        throw new Exception("Invalid phone number format. Use +250XXXXXXXXX");
    }
    
    // Initialize SMS gateway with configuration
    hdev_sms::api_id(HDEV_SMS_API_ID);
    hdev_sms::api_key(HDEV_SMS_API_KEY);
    
    // Send SMS
    $response = hdev_sms::send($senderId, $phone, $message);
    
    // Log the SMS attempt using the configuration function
    logSmsActivity($connect, $pharmacyId, $senderId, $phone, $message, $type, $response);
    
    // Check if SMS was sent successfully
    if ($response && isset($response->success) && $response->success) {
        echo json_encode([
            'success' => true,
            'message' => 'SMS sent successfully',
            'data' => $response
        ]);
    } else {
        $errorMessage = 'Failed to send SMS';
        if ($response && isset($response->message)) {
            $errorMessage = $response->message;
            
            // Provide helpful message for sender ID issues
            if (strpos($errorMessage, 'Sender ID Not Valid') !== false) {
                $errorMessage = "Sender ID not approved by HDEV. Please contact HDEV support (info@hdevtech.cloud) to approve sender ID: '$senderId'. See HDEV_SMS_Setup_Instructions.txt for details.";
            }
        }
        throw new Exception($errorMessage);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
