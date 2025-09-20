<?php
/**
 * SMS Testing Utility
 * This file is used to test the SMS functionality
 */

// Include database connection and SMS config
require_once('../constant/connect.php');
require_once('../includes/sms_config.php');

// Set content type to JSON
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit;
}

try {
    // Test SMS data
    $testData = [
        'to' => '+250786980814', // Default test number
        'message' => 'This is a test SMS from MdLink Pharmacy Management System at ' . date('Y-m-d H:i:s'),
        'from' => 'INEZA'
    ];
    
    // Allow custom test number if provided
    if (isset($_POST['phone']) && !empty($_POST['phone'])) {
        if (validateRwandanPhoneNumber($_POST['phone'])) {
            $testData['to'] = $_POST['phone'];
        } else {
            throw new Exception('Invalid phone number format');
        }
    }
    
    // Allow custom message if provided
    if (isset($_POST['message']) && !empty($_POST['message'])) {
        if (strlen($_POST['message']) <= 160) {
            $testData['message'] = $_POST['message'];
        } else {
            throw new Exception('Test message too long');
        }
    }
    
    // Send test SMS
    $response = sendTestSms($testData);
    
    // Parse response
    $apiResponse = json_decode($response, true);
    
    // Log test SMS
    logSmsToDatabase($connect, $testData['from'], $testData['to'], $testData['message'], 'test', $response);
    
    echo json_encode([
        'success' => true,
        'message' => 'Test SMS sent successfully',
        'data' => $apiResponse,
        'test_data' => $testData
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/**
 * Send test SMS
 */
function sendTestSms($testData) {
    $serviceUrl = SMS_SERVICE_URL . 'test';
    
    // Prepare POST data
    $postData = json_encode($testData);
    
    // Initialize cURL
    $curl = curl_init();
    
    curl_setopt_array($curl, [
        CURLOPT_URL => $serviceUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => SMS_TIMEOUT,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json'
        ],
    ]);
    
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $error = curl_error($curl);
    
    curl_close($curl);
    
    if ($error) {
        throw new Exception('cURL Error: ' . $error);
    }
    
    if ($httpCode !== 200) {
        throw new Exception('HTTP Error: ' . $httpCode . ' - Response: ' . $response);
    }
    
    if (!$response) {
        throw new Exception('Empty response from SMS service');
    }
    
    return $response;
}

/**
 * Log SMS to database (copy from send_sms.php for consistency)
 */
function logSmsToDatabase($connect, $sender_id, $phone, $message, $message_type, $api_response) {
    try {
        $stmt = $connect->prepare("INSERT INTO sms_logs (sender_id, recipient_phone, message, message_type, api_response) VALUES (?, ?, ?, ?, ?)");
        
        if ($stmt) {
            $stmt->bind_param('sssss', $sender_id, $phone, $message, $message_type, $api_response);
            $stmt->execute();
            $stmt->close();
        }
    } catch (Exception $e) {
        error_log('Database logging error: ' . $e->getMessage());
    }
}
?>