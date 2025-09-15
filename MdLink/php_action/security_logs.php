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
        fetchSecurityLogs();
        break;
    case 'fetch_stats':
        fetchSecurityStats();
        break;
    case 'export_csv':
        exportSecurityLogsCSV();
        break;
    case 'export_pdf':
        exportSecurityLogsPDF();
        break;
    case 'generate_report':
        generateSecurityReport();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

function fetchSecurityLogs() {
    global $connect;
    
    // Get filter parameters
    $dateFrom = $_POST['date_from'] ?? '';
    $dateTo = $_POST['date_to'] ?? '';
    $severity = $_POST['severity_filter'] ?? '';
    $eventType = $_POST['event_filter'] ?? '';
    $limit = $_POST['limit'] ?? 100;
    $offset = $_POST['offset'] ?? 0;
    
    // Build WHERE clause
    $whereConditions = [];
    $params = [];
    $types = '';
    
    if (!empty($dateFrom)) {
        $whereConditions[] = "DATE(sl.created_at) >= ?";
        $params[] = $dateFrom;
        $types .= 's';
    }
    
    if (!empty($dateTo)) {
        $whereConditions[] = "DATE(sl.created_at) <= ?";
        $params[] = $dateTo;
        $types .= 's';
    }
    
    if (!empty($severity)) {
        $whereConditions[] = "sl.severity = ?";
        $params[] = $severity;
        $types .= 's';
    }
    
    if (!empty($eventType)) {
        $whereConditions[] = "sl.event_type = ?";
        $params[] = $eventType;
        $types .= 's';
    }
    
    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
    
    // Count total records
    $countSql = "SELECT COUNT(*) as total FROM security_logs sl $whereClause";
    $countStmt = $connect->prepare($countSql);
    
    if (!empty($params)) {
        $countStmt->bind_param($types, ...$params);
    }
    
    $countStmt->execute();
    $totalRecords = $countStmt->get_result()->fetch_assoc()['total'];
    
    // Fetch security logs with user information
    $sql = "SELECT 
                sl.log_id,
                sl.user_id,
                sl.event_type,
                sl.severity,
                sl.description,
                sl.ip_address,
                sl.user_agent,
                sl.metadata,
                sl.created_at,
                COALESCE(u.username, 'Unknown') as username,
                COALESCE(u.firstname, '') as firstname,
                COALESCE(u.lastname, '') as lastname
            FROM security_logs sl
            LEFT JOIN users u ON sl.user_id = u.user_id
            $whereClause
            ORDER BY sl.created_at DESC
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
        $severityClass = getSeverityClass($row['severity']);
        $user = trim($row['firstname'] . ' ' . $row['lastname']) ?: $row['username'];
        
        $logs[] = [
            'log_id' => $row['log_id'],
            'created_at' => date('Y-m-d H:i:s', strtotime($row['created_at'])),
            'severity' => $row['severity'],
            'severity_class' => $severityClass,
            'event_type' => ucwords(str_replace('_', ' ', $row['event_type'])),
            'user' => $user,
            'description' => htmlspecialchars($row['description']),
            'ip_address' => $row['ip_address'],
            'metadata' => $row['metadata']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $logs,
        'total' => $totalRecords,
        'filtered' => count($logs)
    ]);
}

function fetchSecurityStats() {
    global $connect;
    
    $stats = [];
    
    // Total events in last 24 hours
    $sql = "SELECT COUNT(*) as count FROM security_logs WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
    $result = $connect->query($sql);
    $stats['total_events'] = $result->fetch_assoc()['count'];
    
    // High/Critical severity events
    $sql = "SELECT COUNT(*) as count FROM security_logs WHERE severity IN ('high', 'critical') AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
    $result = $connect->query($sql);
    $stats['high_severity'] = $result->fetch_assoc()['count'];
    
    // Failed logins in last 24 hours
    $sql = "SELECT COUNT(*) as count FROM security_logs WHERE event_type = 'failed_login' AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
    $result = $connect->query($sql);
    $stats['failed_logins'] = $result->fetch_assoc()['count'];
    
    // Blocked attempts (assuming we track these)
    $sql = "SELECT COUNT(*) as count FROM security_logs WHERE event_type IN ('unauthorized_access', 'system_intrusion') AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
    $result = $connect->query($sql);
    $stats['blocked_attempts'] = $result->fetch_assoc()['count'];
    
    echo json_encode([
        'success' => true,
        'stats' => $stats
    ]);
}

function exportSecurityLogsCSV() {
    global $connect;
    
    // Get filter parameters
    $dateFrom = $_POST['date_from'] ?? '';
    $dateTo = $_POST['date_to'] ?? '';
    $severity = $_POST['severity_filter'] ?? '';
    $eventType = $_POST['event_filter'] ?? '';
    
    // Build WHERE clause
    $whereConditions = [];
    $params = [];
    $types = '';
    
    if (!empty($dateFrom)) {
        $whereConditions[] = "DATE(sl.created_at) >= ?";
        $params[] = $dateFrom;
        $types .= 's';
    }
    
    if (!empty($dateTo)) {
        $whereConditions[] = "DATE(sl.created_at) <= ?";
        $params[] = $dateTo;
        $types .= 's';
    }
    
    if (!empty($severity)) {
        $whereConditions[] = "sl.severity = ?";
        $params[] = $severity;
        $types .= 's';
    }
    
    if (!empty($eventType)) {
        $whereConditions[] = "sl.event_type = ?";
        $params[] = $eventType;
        $types .= 's';
    }
    
    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
    
    // Fetch all matching records
    $sql = "SELECT 
                sl.created_at,
                sl.severity,
                sl.event_type,
                COALESCE(u.username, 'Unknown') as username,
                sl.description,
                sl.ip_address,
                sl.metadata
            FROM security_logs sl
            LEFT JOIN users u ON sl.user_id = u.user_id
            $whereClause
            ORDER BY sl.created_at DESC";
    
    $stmt = $connect->prepare($sql);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="security_logs_' . date('Y-m-d_H-i-s') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // Write CSV header
    fputcsv($output, ['Date/Time', 'Severity', 'Event Type', 'User', 'Description', 'IP Address', 'Metadata']);
    
    // Write data rows
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['created_at'],
            $row['severity'],
            $row['event_type'],
            $row['username'],
            $row['description'],
            $row['ip_address'],
            $row['metadata']
        ]);
    }
    
    fclose($output);
}

function exportSecurityLogsPDF() {
    echo json_encode([
        'success' => false,
        'message' => 'PDF export feature requires additional PDF library installation'
    ]);
}

function generateSecurityReport() {
    global $connect;
    
    $dateFrom = $_POST['date_from'] ?? date('Y-m-d', strtotime('-30 days'));
    $dateTo = $_POST['date_to'] ?? date('Y-m-d');
    
    // Generate comprehensive security report
    $report = [
        'period' => ['from' => $dateFrom, 'to' => $dateTo],
        'summary' => [],
        'trends' => [],
        'top_threats' => []
    ];
    
    // Summary statistics
    $sql = "SELECT 
                COUNT(*) as total_events,
                COUNT(CASE WHEN severity = 'critical' THEN 1 END) as critical_events,
                COUNT(CASE WHEN severity = 'high' THEN 1 END) as high_events,
                COUNT(CASE WHEN severity = 'medium' THEN 1 END) as medium_events,
                COUNT(CASE WHEN severity = 'low' THEN 1 END) as low_events
            FROM security_logs 
            WHERE DATE(created_at) BETWEEN ? AND ?";
    
    $stmt = $connect->prepare($sql);
    $stmt->bind_param('ss', $dateFrom, $dateTo);
    $stmt->execute();
    $report['summary'] = $stmt->get_result()->fetch_assoc();
    
    // Event type breakdown
    $sql = "SELECT event_type, COUNT(*) as count 
            FROM security_logs 
            WHERE DATE(created_at) BETWEEN ? AND ?
            GROUP BY event_type 
            ORDER BY count DESC";
    
    $stmt = $connect->prepare($sql);
    $stmt->bind_param('ss', $dateFrom, $dateTo);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $report['event_types'] = [];
    while ($row = $result->fetch_assoc()) {
        $report['event_types'][] = $row;
    }
    
    // Daily trends
    $sql = "SELECT DATE(created_at) as date, COUNT(*) as count 
            FROM security_logs 
            WHERE DATE(created_at) BETWEEN ? AND ?
            GROUP BY DATE(created_at) 
            ORDER BY date";
    
    $stmt = $connect->prepare($sql);
    $stmt->bind_param('ss', $dateFrom, $dateTo);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $report['daily_trends'] = [];
    while ($row = $result->fetch_assoc()) {
        $report['daily_trends'][] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'report' => $report
    ]);
}

function getSeverityClass($severity) {
    switch ($severity) {
        case 'critical':
            return 'badge-danger';
        case 'high':
            return 'badge-warning';
        case 'medium':
            return 'badge-info';
        case 'low':
            return 'badge-secondary';
        default:
            return 'badge-light';
    }
}

// Function to log security events (to be called from other parts of the system)
function logSecurityEvent($userId, $eventType, $severity, $description, $metadata = null) {
    global $connect;
    
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    $sql = "INSERT INTO security_logs (user_id, event_type, severity, description, ip_address, user_agent, metadata) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $connect->prepare($sql);
    $stmt->bind_param('issssss', $userId, $eventType, $severity, $description, $ipAddress, $userAgent, json_encode($metadata));
    
    return $stmt->execute();
}
?>