<?php
/*
 * HDEV SMS Gateway Configuration
 * @email : info@hdevtech.cloud
 * @link : https://sms-api.hdev.rw
 */

// API Credentials
define('HDEV_SMS_API_ID', 'HDEV-23fb1b59-aec0-4aef-a351-bfc1c3aa3c52-ID');
define('HDEV_SMS_API_KEY', 'HDEV-6e36c286-19bb-4b45-838e-8b5cd0240857-KEY');

// Approved Sender IDs (these must be pre-approved by HDEV)
// NOTE: Currently NO sender IDs are approved for this account
// Contact HDEV support (info@hdevtech.cloud) to get sender IDs approved
$APPROVED_SENDER_IDS = [
    'PENDING_APPROVAL', // Placeholder - needs HDEV approval
    'INEZA',           // Requested - pending approval
    'PHARMACY',        // Requested - pending approval
    'HEALTH',          // Requested - pending approval
    'ALERT',           // Requested - pending approval
    'INFO',            // Requested - pending approval
    'REMINDER'         // Requested - pending approval
];

// Default sender ID to use if none specified
// NOTE: This will fail until approved by HDEV
define('DEFAULT_SENDER_ID', 'INEZA');

// Phone number validation and formatting
function normalizePhoneNumber($phone) {
    // Remove all non-digit characters
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // If it's a 9-digit number, add +250
    if (strlen($phone) === 9) {
        return '+250' . $phone;
    }
    
    // If it's a 12-digit number starting with 250, add +
    if (strlen($phone) === 12 && substr($phone, 0, 3) === '250') {
        return '+' . $phone;
    }
    
    // If it already has +250, return as is
    if (strlen($phone) === 13 && substr($phone, 0, 4) === '250') {
        return '+' . $phone;
    }
    
    // If it's a 10-digit number starting with 0, replace with +250
    if (strlen($phone) === 10 && substr($phone, 0, 1) === '0') {
        return '+250' . substr($phone, 1);
    }
    
    return $phone;
}

// Validate sender ID
function validateSenderId($senderId) {
    global $APPROVED_SENDER_IDS;
    
    // Check if sender ID is in approved list
    if (in_array(strtoupper($senderId), $APPROVED_SENDER_IDS)) {
        return strtoupper($senderId);
    }
    
    // If not approved, use default
    return DEFAULT_SENDER_ID;
}

// Get available sender IDs for display
function getAvailableSenderIds() {
    global $APPROVED_SENDER_IDS;
    return $APPROVED_SENDER_IDS;
}

// Log SMS activity
function logSmsActivity($connect, $pharmacyId, $senderId, $phone, $message, $type, $apiResponse) {
    $logQuery = "INSERT INTO sms_logs (pharmacy_id, sender_id, recipient_phone, message, message_type, api_response, created_at) 
                 VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $connect->prepare($logQuery);
    $apiResponseJson = json_encode($apiResponse);
    $stmt->bind_param('isssss', $pharmacyId, $senderId, $phone, $message, $type, $apiResponseJson);
    return $stmt->execute();
}
?>

