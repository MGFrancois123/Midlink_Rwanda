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
    
    // Only allow POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }
    
    // Include database connection and SMS config
    require_once('../constant/connect.php');
    require_once('../includes/sms_config.php');
    
    // Test SMS data
    $testData = [
        'to' => '+250786980814', // Default test number
        'message' => 'Test SMS from MdLink Pharmacy at ' . date('Y-m-d H:i:s'),
        'from' => 'INEZA'
    ];
    
    // Allow custom test number if provided
    if (isset($_POST['phone']) && !empty($_POST['phone'])) {
        if (preg_match('/^\+250[0-9]{9}$/', $_POST['phone'])) {
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
    
    // Send test SMS using the test endpoint
    $response = sendTestSmsViaService($testData);
    
    // Parse response
    $apiResponse = json_decode($response, true);
    
    // Log test SMS
    logTestSmsToDatabase($connect, $testData['from'], $testData['to'], $testData['message'], 'test', $response);
    
    // Check if test was successful
    if ($apiResponse && isset($apiResponse['success']) && $apiResponse['success'] === true) {
        echo json_encode([
            'success' => true,
            'message' => 'Test SMS sent successfully',
            'data' => $apiResponse,
            'test_data' => $testData
        ]);
    } else {
        $errorMessage = 'Test SMS failed';
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
    error_log('Test SMS Error: ' . $e->getMessage());
    
    // Return JSON error response
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error_type' => 'test_sms_error'
    ]);
} catch (Error $e) {
    // Handle fatal errors
    ob_clean();
    error_log('Test SMS Fatal Error: ' . $e->getMessage());
    
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
 * Send test SMS via the test endpoint
 */
function sendTestSmsViaService($testData) {
    $serviceUrl = 'https://sms-system-aelu.onrender.com/test';
    
    // Prepare POST data
    $postData = json_encode($testData);
    
    // Initialize cURL
    $curl = curl_init();
    
    if (!$curl) {
        throw new Exception('Failed to initialize cURL');
    }
    
    curl_setopt_array($curl, [
        CURLOPT_URL => $serviceUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 60, // Increased timeout for service wake-up
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: MdLink-Test-SMS-Client/1.0'
        ],
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_CONNECTTIMEOUT => 30
    ]);
    
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $error = curl_error($curl);
    
    curl_close($curl);
    
    // Log request details for debugging
    error_log('Test SMS Request: ' . $postData);
    error_log('Test SMS Response Code: ' . $httpCode);
    error_log('Test SMS Response: ' . $response);
    
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
function logTestSmsToDatabase($connect, $sender_id, $phone, $message, $message_type, $api_response) {
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
        
        $stmt = $connect->prepare("INSERT INTO sms_logs (sender_id, recipient_phone, message, message_type, api_response) VALUES (?, ?, ?, ?, ?)");
        
        if ($stmt) {
            $stmt->bind_param('sssss', $sender_id, $phone, $message, $message_type, $api_response);
            $result = $stmt->execute();
            $stmt->close();
            
            if (!$result) {
                error_log('Failed to log test SMS to database: ' . $connect->error);
            }
        }
    } catch (Exception $e) {
        error_log('Test SMS database logging error: ' . $e->getMessage());
    }
}
?>