<?php
/**
 * SMS Configuration for Africa's Talking Integration
 * This file contains configuration settings for SMS functionality
 */

// SMS Service Configuration
define('SMS_SERVICE_URL', 'https://sms-system-aelu.onrender.com/');
define('SMS_DELIVERY_CALLBACK_URL', 'https://sms-system-aelu.onrender.com/delivery');

// Default SMS Settings
define('DEFAULT_SENDER_ID', 'INEZA');
define('MAX_SMS_LENGTH', 160);
define('SMS_TIMEOUT', 30); // seconds

// Approved Sender IDs (these need HDEV approval)
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
    }
    
    return $phone; // Return as is if format not recognized
}

/**
 * Get SMS statistics from database
 */
function getSmsStatistics($connect) {
    $stats = [];
    
    try {
        // Total SMS sent
        $result = $connect->query("SELECT COUNT(*) as count FROM sms_logs");
        $stats['total_sent'] = $result ? $result->fetch_assoc()['count'] : 0;
        
        // Today's SMS
        $result = $connect->query("SELECT COUNT(*) as count FROM sms_logs WHERE DATE(created_at) = CURDATE()");
        $stats['today_sent'] = $result ? $result->fetch_assoc()['count'] : 0;
        
        // Failed SMS (basic check for error in response)
        $result = $connect->query("SELECT COUNT(*) as count FROM sms_logs WHERE api_response LIKE '%error%' OR api_response LIKE '%fail%'");
        $stats['failed_sms'] = $result ? $result->fetch_assoc()['count'] : 0;
        
        // Success rate
        $stats['success_rate'] = $stats['total_sent'] > 0 ? 
            round((($stats['total_sent'] - $stats['failed_sms']) / $stats['total_sent']) * 100, 1) : 0;
    } catch (Exception $e) {
        // If there's an error, return default stats
        $stats = [
            'total_sent' => 0,
            'today_sent' => 0,
            'failed_sms' => 0,
            'success_rate' => 0
        ];
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
        $connect->query($sql);
        return true;
    } catch (Exception $e) {
        error_log('Failed to create sms_logs table: ' . $e->getMessage());
        return false;
    }
}

// Initialize SMS logs table
if (isset($connect)) {
    createSmsLogsTable($connect);
}
?>
