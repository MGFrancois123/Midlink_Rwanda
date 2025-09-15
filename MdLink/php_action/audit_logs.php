<?php
require_once 'db_connect.php';
require_once 'core.php';

// Check if user is logged in
if (!isset($_SESSION['userId'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'fetch':
        fetchAuditLogs();
        break;
    case 'export_csv':
        exportAuditLogsCSV();
        break;
    case 'export_pdf':
        exportAuditLogsPDF();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

function fetchAuditLogs() {
    global $connect;
    
    // Get filter parameters
    $dateFrom = $_POST['date_from'] ?? '';
    $dateTo = $_POST['date_to'] ?? '';
    $action = $_POST['action_filter'] ?? '';
    $entity = $_POST['entity_filter'] ?? '';
    $limit = $_POST['limit'] ?? 100;
    $offset = $_POST['offset'] ?? 0;
    
    // Build WHERE clause
    $whereConditions = [];
    $params = [];
    $types = '';
    
    if (!empty($dateFrom)) {
        $whereConditions[] = "DATE(al.created_at) >= ?";
        $params[] = $dateFrom;
        $types .= 's';
    }
    
    if (!empty($dateTo)) {
        $whereConditions[] = "DATE(al.created_at) <= ?";
        $params[] = $dateTo;
        $types .= 's';
    }
    
    if (!empty($action)) {
        $whereConditions[] = "al.action = ?";
        $params[] = $action;
        $types .= 's';
    }
    
    if (!empty($entity)) {
        $whereConditions[] = "al.entity_type = ?";
        $params[] = $entity;
        $types .= 's';
    }
    
    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
    
    // Count total records
    $countSql = "SELECT COUNT(*) as total FROM audit_logs al $whereClause";
    $countStmt = $connect->prepare($countSql);
    
    if (!empty($params)) {
        $countStmt->bind_param($types, ...$params);
    }
    
    $countStmt->execute();
    $totalRecords = $countStmt->get_result()->fetch_assoc()['total'];
    
    // Fetch audit logs with user information
    $sql = "SELECT 
                al.log_id,
                al.user_id,
                al.action,
                al.entity_type,
                al.entity_id,
                al.old_value,
                al.new_value,
                al.ip_address,
                al.user_agent,
                al.created_at,
                COALESCE(u.username, 'System') as username,
                COALESCE(u.firstname, '') as firstname,
                COALESCE(u.lastname, '') as lastname
            FROM audit_logs al
            LEFT JOIN users u ON al.user_id = u.user_id
            $whereClause
            ORDER BY al.created_at DESC
            LIMIT ? OFFSET ?";
    
    $stmt = $connect->prepare($sql);
    
    // Add limit and offset to parameters
    $params[] = (int)$limit;
    $params[] = (int)$offset;
    $types .= 'ii';
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $logs = [];
    while ($row = $result->fetch_assoc()) {
        // Format changes for display
        $changes = '';
        if (!empty($row['old_value']) || !empty($row['new_value'])) {
            $oldData = json_decode($row['old_value'], true) ?? [];
            $newData = json_decode($row['new_value'], true) ?? [];
            
            $changesList = [];
            
            // Compare old and new values
            if ($row['action'] === 'CREATE') {
                foreach ($newData as $field => $value) {
                    if (!empty($value)) {
                        $changesList[] = "<strong>$field:</strong> " . htmlspecialchars($value);
                    }
                }
            } elseif ($row['action'] === 'UPDATE') {
                foreach ($newData as $field => $newValue) {
                    $oldValue = $oldData[$field] ?? '';
                    if ($oldValue != $newValue) {
                        $changesList[] = "<strong>$field:</strong> " . htmlspecialchars($oldValue) . " → " . htmlspecialchars($newValue);
                    }
                }
            } elseif ($row['action'] === 'DELETE') {
                foreach ($oldData as $field => $value) {
                    if (!empty($value)) {
                        $changesList[] = "<strong>$field:</strong> " . htmlspecialchars($value);
                    }
                }
            }
            
            $changes = implode('<br>', $changesList);
        }
        
        $logs[] = [
            'log_id' => $row['log_id'],
            'created_at' => date('Y-m-d H:i:s', strtotime($row['created_at'])),
            'user' => trim($row['firstname'] . ' ' . $row['lastname']) ?: $row['username'],
            'action' => $row['action'],
            'entity_type' => ucfirst($row['entity_type']),
            'entity_id' => $row['entity_id'],
            'changes' => $changes,
            'ip_address' => $row['ip_address']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $logs,
        'total' => $totalRecords,
        'filtered' => count($logs)
    ]);
}

function exportAuditLogsCSV() {
    global $connect;
    
    // Get filter parameters (same as fetchAuditLogs)
    $dateFrom = $_POST['date_from'] ?? '';
    $dateTo = $_POST['date_to'] ?? '';
    $action = $_POST['action_filter'] ?? '';
    $entity = $_POST['entity_filter'] ?? '';
    
    // Build WHERE clause
    $whereConditions = [];
    $params = [];
    $types = '';
    
    if (!empty($dateFrom)) {
        $whereConditions[] = "DATE(al.created_at) >= ?";
        $params[] = $dateFrom;
        $types .= 's';
    }
    
    if (!empty($dateTo)) {
        $whereConditions[] = "DATE(al.created_at) <= ?";
        $params[] = $dateTo;
        $types .= 's';
    }
    
    if (!empty($action)) {
        $whereConditions[] = "al.action = ?";
        $params[] = $action;
        $types .= 's';
    }
    
    if (!empty($entity)) {
        $whereConditions[] = "al.entity_type = ?";
        $params[] = $entity;
        $types .= 's';
    }
    
    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
    
    // Fetch all matching records
    $sql = "SELECT 
                al.created_at,
                COALESCE(u.username, 'System') as username,
                al.action,
                al.entity_type,
                al.entity_id,
                al.old_value,
                al.new_value,
                al.ip_address
            FROM audit_logs al
            LEFT JOIN users u ON al.user_id = u.user_id
            $whereClause
            ORDER BY al.created_at DESC";
    
    $stmt = $connect->prepare($sql);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="audit_logs_' . date('Y-m-d_H-i-s') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // Write CSV header
    fputcsv($output, ['Date/Time', 'User', 'Action', 'Entity Type', 'Entity ID', 'Old Value', 'New Value', 'IP Address']);
    
    // Write data rows
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['created_at'],
            $row['username'],
            $row['action'],
            $row['entity_type'],
            $row['entity_id'],
            $row['old_value'],
            $row['new_value'],
            $row['ip_address']
        ]);
    }
    
    fclose($output);
}

function exportAuditLogsPDF() {
    // For PDF export, you would typically use a library like TCPDF or FPDF
    // For now, we'll return a message indicating this feature needs implementation
    echo json_encode([
        'success' => false,
        'message' => 'PDF export feature requires additional PDF library installation'
    ]);
}

// Function to log audit events (to be called from other parts of the system)
function logAuditEvent($userId, $action, $entityType, $entityId, $oldValue = null, $newValue = null) {
    global $connect;
    
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    $sql = "INSERT INTO audit_logs (user_id, action, entity_type, entity_id, old_value, new_value, ip_address, user_agent) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $connect->prepare($sql);
    $stmt->bind_param('isssssss', $userId, $action, $entityType, $entityId, 
                      json_encode($oldValue), json_encode($newValue), $ipAddress, $userAgent);
    
    return $stmt->execute();
}
?>