<?php
// Prevent any output before JSON response
ob_start();

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set content type to JSON first thing
header('Content-Type: application/json');

// Suppress PHP errors from being displayed (but still log them)
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

try {
    // Clear any accidental output
    ob_clean();
    
    // Check if request is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }
    
    // Include database connection and SMS config
    require_once('../constant/connect.php');
    require_once('../includes/sms_config.php');
    
    // Get form data
    $sender_id = $_POST['sender_id'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $message = $_POST['message'] ?? '';
    $message_type = $_POST['type'] ?? 'general';
    
    // Validate required fields
    if (empty($sender_id) || empty($phone) || empty($message)) {
        throw new Exception('All fields are required');
    }
    
    // Validate phone number format
    if (!preg_match('/^\+250[0-9]{9}$/', $phone)) {
        throw new Exception('Invalid phone number format. Use +250XXXXXXXXX');
    }
    
    // Validate message length
    if (strlen($message) > 160) {
        throw new Exception('Message too long. Maximum 160 characters allowed');
    }
    
    // Prepare SMS data for Africa's Talking service
$smsData = [
    'to' => $phone,
    'message' => $message
];

// Only add 'from' if provided (non-empty)
if (!empty($sender_id)) {
    $smsData['from'] = $sender_id;
}
    
    // Send SMS using your hosted Africa's Talking service
    $response = sendSmsViaAfricasTalking($smsData);
    
    // Parse the response
    $apiResponse = json_decode($response, true);
    
    // Log SMS to database
    logSmsToDatabase($connect, $sender_id, $phone, $message, $message_type, $response);
    
    // Check if SMS was sent successfully
    if ($apiResponse && isset($apiResponse['success']) && $apiResponse['success'] === true) {
        echo json_encode([
            'success' => true,
            'message' => 'SMS sent successfully',
            'data' => $apiResponse
        ]);
    } else {
        // Handle different error formats
        $errorMessage = 'Failed to send SMS';
        if ($apiResponse && isset($apiResponse['message'])) {
            $errorMessage = $apiResponse['message'];
        } elseif ($apiResponse && isset($apiResponse['error'])) {
            $errorMessage = $apiResponse['error'];
        }
        throw new Exception($errorMessage);
    }
    
} catch (Exception $e) {
    // Clear any output buffer
    ob_clean();
    
    // Log the error
    error_log('SMS Error: ' . $e->getMessage());
    
    // Return JSON error response
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error_type' => 'sms_error'
    ]);
} catch (Error $e) {
    // Handle fatal errors
    ob_clean();
    error_log('SMS Fatal Error: ' . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error',
        'error_type' => 'fatal_error'
    ]);
}

// End output buffering and send response
ob_end_flush();

/**
 * Send SMS via your hosted Africa's Talking service
 */
function sendSmsViaAfricasTalking($smsData) {
    $serviceUrl = 'https://sms-system-aelu.onrender.com/';
    
    // Prepare POST data
    $postData = json_encode($smsData);
    
    // Initialize cURL with comprehensive error handling
    $curl = curl_init();
    
    if (!$curl) {
        throw new Exception('Failed to initialize cURL');
    }
    
    curl_setopt_array($curl, [
        CURLOPT_URL => $serviceUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 60, // Increased timeout for Render service wake-up
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: MdLink-SMS-Client/1.0'
        ],
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_CONNECTTIMEOUT => 30
    ]);
    
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $error = curl_error($curl);
    $info = curl_getinfo($curl);
    
    curl_close($curl);
    
    // Log request details for debugging
    error_log('SMS Request: ' . $postData);
    error_log('SMS Response Code: ' . $httpCode);
    error_log('SMS Response: ' . $response);
    
    if ($error) {
        throw new Exception('Connection Error: ' . $error);
    }
    
    if ($httpCode === 0) {
        throw new Exception('No response from SMS service. Service may be starting up, please wait a moment and try again.');
    }
    
    if ($httpCode !== 200) {
        $errorMsg = "Service Error: HTTP $httpCode";
        if ($response) {
            $errorMsg .= " - " . substr($response, 0, 200);
        }
        throw new Exception($errorMsg);
    }
    
    if (!$response) {
        throw new Exception('Empty response from SMS service');
    }
    
    return $response;
}

/**
 * Log SMS to database
 */
function logSmsToDatabase($connect, $sender_id, $phone, $message, $message_type, $api_response) {
    try {
        // Create sms_logs table if it doesn't exist
        $createTableSql = "CREATE TABLE IF NOT EXISTS sms_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            sender_id VARCHAR(50) NOT NULL,
            recipient_phone VARCHAR(20) NOT NULL,
            message TEXT NOT NULL,
            message_type VARCHAR(50) DEFAULT 'general',
            api_response TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_created_at (created_at),
            INDEX idx_recipient (recipient_phone),
            INDEX idx_sender_id (sender_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $connect->query($createTableSql);
        
        // Insert SMS log
        $stmt = $connect->prepare("INSERT INTO sms_logs (sender_id, recipient_phone, message, message_type, api_response) VALUES (?, ?, ?, ?, ?)");
        
        if ($stmt) {
            $stmt->bind_param('sssss', $sender_id, $phone, $message, $message_type, $api_response);
            $result = $stmt->execute();
            $stmt->close();
            
            if (!$result) {
                error_log('Failed to log SMS to database: ' . $connect->error);
            }
        } else {
            error_log('Failed to prepare SMS log statement: ' . $connect->error);
        }
    } catch (Exception $e) {
        // Log error but don't fail the SMS sending
        error_log('Database logging error: ' . $e->getMessage());
    }
}
?>