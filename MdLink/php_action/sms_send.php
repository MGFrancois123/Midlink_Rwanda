<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once('../constant/connect.php');

// Set content type to JSON
header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit;
}

try {
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
        'message' => $message,
        'from' => $sender_id
    ];
    
    // Send SMS using your hosted service
    $response = sendSmsViaService($smsData);
    
    // Parse the response
    $apiResponse = json_decode($response, true);
    
    // Log SMS to database
    logSmsToDatabase($connect, $sender_id, $phone, $message, $message_type, $response);
    
    // Check if SMS was sent successfully
    if ($apiResponse && isset($apiResponse['SMSMessageData'])) {
        $messageData = $apiResponse['SMSMessageData'];
        
        // Check if there are recipients and if any failed
        if (isset($messageData['Recipients']) && is_array($messageData['Recipients'])) {
            $recipient = $messageData['Recipients'][0];
            
            if (isset($recipient['status']) && strtolower($recipient['status']) === 'success') {
                echo json_encode([
                    'success' => true,
                    'message' => 'SMS sent successfully',
                    'data' => $apiResponse
                ]);
            } else {
                throw new Exception($recipient['status'] ?? 'Failed to send SMS');
            }
        } else {
            echo json_encode([
                'success' => true,
                'message' => 'SMS queued successfully',
                'data' => $apiResponse
            ]);
        }
    } else {
        throw new Exception('Invalid response from SMS service');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/**
 * Send SMS via your hosted Africa's Talking service
 */
function sendSmsViaService($smsData) {
    $serviceUrl = 'https://sms-system-aelu.onrender.com/';
    
    // Prepare POST data
    $postData = json_encode($smsData);
    
    // Initialize cURL
    $curl = curl_init();
    
    curl_setopt_array($curl, [
        CURLOPT_URL => $serviceUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
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
        throw new Exception('HTTP Error: ' . $httpCode);
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
        $createTableQuery = "CREATE TABLE IF NOT EXISTS sms_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            sender_id VARCHAR(50) NOT NULL,
            recipient_phone VARCHAR(20) NOT NULL,
            message TEXT NOT NULL,
            message_type VARCHAR(50) DEFAULT 'general',
            api_response TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        $connect->query($createTableQuery);
        
        // Insert SMS log
        $stmt = $connect->prepare("INSERT INTO sms_logs (sender_id, recipient_phone, message, message_type, api_response) VALUES (?, ?, ?, ?, ?)");
        
        if ($stmt) {
            $stmt->bind_param('sssss', $sender_id, $phone, $message, $message_type, $api_response);
            $stmt->execute();
            $stmt->close();
        }
    } catch (Exception $e) {
        // Log error but don't fail the SMS sending
        error_log('Database logging error: ' . $e->getMessage());
    }
}
?>

