<?php
/**
 * SMS Configuration for Africa's Talking Integration
 * This file contains configuration settings for SMS functionality
 */

// SMS Service Configuration (Your hosted Africa's Talking service)
define('SMS_SERVICE_URL', 'https://sms-system-aelu.onrender.com/');
define('SMS_DELIVERY_CALLBACK_URL', 'https://sms-system-aelu.onrender.com/delivery');

// Default SMS Settings
define('DEFAULT_SENDER_ID', 'INEZA');
define('MAX_SMS_LENGTH', 160);
define('SMS_TIMEOUT', 60); // Increased timeout for service wake-up

// Approved Sender IDs for Africa's Talking
$approved_sender_ids = [
    'INEZA',
    'PHARMACY', 
    'HEALTH',
    'ALERT',
    'INFO',
    'REMINDER'
];

// SMS Templates
$sms_templates = [
    'reminder' => 'Hello! This is a reminder that your prescription is ready for pickup at our pharmacy. Please visit us during business hours.',
    'expiry' => 'Alert: Some medicines in your prescription are expiring soon. Please check with us for replacements.',
    'lowstock' => 'Notice: We are currently low on some medicines. Please contact us to confirm availability before visiting.',
    'welcome' => 'Welcome to our pharmacy! We are here to serve your healthcare needs. Thank you for choosing us.',
    'test' => 'This is a test SMS from MdLink Pharmacy Management System.'
];

// Message Types
$message_types = [
    'general' => 'General',
    'reminder' => 'Reminder',
    'alert' => 'Alert',
    'notification' => 'Notification',
    'promotional' => 'Promotional'
];

/**
 * Validate phone number format for Rwanda
 */
function validateRwandanPhoneNumber($phone) {
    return preg_match('/^\+250[0-9]{9}$/', $phone);
}

/**
 * Format phone number to Rwanda standard
 */
function formatRwandanPhoneNumber($phone) {
    // Remove all non-digits
    $phone = preg_replace('/\D/', '', $phone);
    
    // Handle different input formats
    if (strlen($phone) == 9 && substr($phone, 0, 1) == '7') {
        return '+250' . $phone;
    } elseif (strlen($phone) == 12 && substr($phone, 0, 3) == '250') {
        return '+' . $phone;
    } elseif (strlen($phone) == 13 && substr($phone, 0, 4) == '2507') {
        return '+' . $phone;
    } elseif (strlen($phone) == 10 && substr($phone, 0, 1) == '0') {
        return '+250' . substr($phone, 1);
    }
    
    return $phone; // Return as is if format not recognized
}

/**
 * Get SMS statistics from database with error handling
 */
function getSmsStatistics($connect) {
    $stats = [
        'total_sent' => 0,
        'today_sent' => 0,
        'failed_sms' => 0,
        'success_rate' => 0
    ];
    
    try {
        // Check if sms_logs table exists first
        $tableCheck = $connect->query("SHOW TABLES LIKE 'sms_logs'");
        if (!$tableCheck || $tableCheck->num_rows == 0) {
            return $stats; // Return default stats if table doesn't exist
        }
        
        // Total SMS sent
        $result = $connect->query("SELECT COUNT(*) as count FROM sms_logs");
        if ($result) {
            $stats['total_sent'] = $result->fetch_assoc()['count'];
        }
        
        // Today's SMS
        $result = $connect->query("SELECT COUNT(*) as count FROM sms_logs WHERE DATE(created_at) = CURDATE()");
        if ($result) {
            $stats['today_sent'] = $result->fetch_assoc()['count'];
        }
        
        // Failed SMS (check for error indicators in response)
        $result = $connect->query("SELECT COUNT(*) as count FROM sms_logs WHERE api_response LIKE '%\"success\":false%' OR api_response LIKE '%error%' OR api_response LIKE '%fail%'");
        if ($result) {
            $stats['failed_sms'] = $result->fetch_assoc()['count'];
        }
        
        // Success rate
        $stats['success_rate'] = $stats['total_sent'] > 0 ? 
            round((($stats['total_sent'] - $stats['failed_sms']) / $stats['total_sent']) * 100, 1) : 0;
            
    } catch (Exception $e) {
        error_log('Error getting SMS statistics: ' . $e->getMessage());
    }
    
    return $stats;
}

/**
 * Create SMS logs table if it doesn't exist
 */
function createSmsLogsTable($connect) {
    $sql = "CREATE TABLE IF NOT EXISTS sms_logs (
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
    
    try {
        $result = $connect->query($sql);
        if (!$result) {
            error_log('Failed to create sms_logs table: ' . $connect->error);
            return false;
        }
        return true;
    } catch (Exception $e) {
        error_log('Exception creating sms_logs table: ' . $e->getMessage());
        return false;
    }
}

/**
 * Test SMS service connectivity
 */
function testSmsServiceConnectivity() {
    $healthUrl = SMS_SERVICE_URL . 'health';
    
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 10,
            'header' => 'User-Agent: MdLink-Health-Check/1.0'
        ]
    ]);
    
    try {
        $response = @file_get_contents($healthUrl, false, $context);
        if ($response === false) {
            return [
                'status' => 'error',
                'message' => 'Service unreachable'
            ];
        }
        
        $data = json_decode($response, true);
        if ($data && isset($data['status']) && $data['status'] === 'healthy') {
            return [
                'status' => 'healthy',
                'message' => 'Service is online'
            ];
        }
        
        return [
            'status' => 'warning',
            'message' => 'Service responded but status unclear'
        ];
        
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => 'Service check failed: ' . $e->getMessage()
        ];
    }
}

// Initialize SMS logs table if database connection exists
if (isset($connect) && $connect instanceof mysqli) {
    createSmsLogsTable($connect);
}
?>