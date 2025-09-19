<?php
/**
 * Activity Logger for MdLink Rwanda System
 * This file provides functions to log real user activities
 */

function logActivity($admin_id, $action, $table_name, $record_id = null, $description = '', $old_data = null, $new_data = null) {
    global $connect;
    
    if (!isset($connect)) {
        require_once './constant/connect.php';
    }
    
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    
    try {
        $stmt = $connect->prepare('INSERT INTO audit_logs (admin_id, action, table_name, record_id, description, ip_address, user_agent, old_data, new_data) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ississsss', $admin_id, $action, $table_name, $record_id, $description, $ip_address, $user_agent, $old_data, $new_data);
        return $stmt->execute();
    } catch (Exception $e) {
        error_log("Activity logging error: " . $e->getMessage());
        return false;
    }
}

function logLogin($admin_id, $username) {
    return logActivity($admin_id, 'LOGIN', 'admin_users', $admin_id, "User '{$username}' logged into the system");
}

function logLogout($admin_id, $username) {
    return logActivity($admin_id, 'LOGOUT', 'admin_users', $admin_id, "User '{$username}' logged out of the system");
}

function logView($admin_id, $table_name, $description = '') {
    return logActivity($admin_id, 'VIEW', $table_name, null, $description ?: "Viewed {$table_name} list");
}

function logCreate($admin_id, $table_name, $record_id, $description = '') {
    return logActivity($admin_id, 'CREATE', $table_name, $record_id, $description ?: "Created new {$table_name} record");
}

function logUpdate($admin_id, $table_name, $record_id, $description = '', $old_data = null, $new_data = null) {
    return logActivity($admin_id, 'UPDATE', $table_name, $record_id, $description ?: "Updated {$table_name} record", $old_data, $new_data);
}

function logDelete($admin_id, $table_name, $record_id, $description = '') {
    return logActivity($admin_id, 'DELETE', $table_name, $record_id, $description ?: "Deleted {$table_name} record");
}

function logSearch($admin_id, $table_name, $search_term) {
    return logActivity($admin_id, 'SEARCH', $table_name, null, "Searched for '{$search_term}' in {$table_name}");
}

function logExport($admin_id, $table_name, $format = 'CSV') {
    return logActivity($admin_id, 'EXPORT', $table_name, null, "Exported {$table_name} data as {$format}");
}
?>