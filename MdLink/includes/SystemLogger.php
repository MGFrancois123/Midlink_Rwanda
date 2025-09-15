<?php
/**
 * System Logger and Analytics Class
 * Handles all system logging, analytics, and monitoring functionality
 */
class SystemLogger {
    private $db;
    private $user_id;
    
    public function __construct($db) {
        $this->db = $db;
        $this->user_id = $_SESSION['userId'] ?? null;
    }
    
    /**
     * Log user session activity
     */
    public function logSessionActivity($session_id, $user_id, $ip_address, $user_agent, $status = 'active') {
        $sql = "INSERT INTO system_sessions (session_id, user_id, ip_address, user_agent, last_activity, status) 
                VALUES (?, ?, ?, ?, NOW(), ?) 
                ON DUPLICATE KEY UPDATE 
                    last_activity = NOW(), 
                    status = ?,
                    updated_at = NOW()";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$session_id, $user_id, $ip_address, $user_agent, $status, $status]);
    }
    
    /**
     * Log audit trail
     */
    public function logAudit($action, $entity_type, $entity_id = null, $old_value = null, $new_value = null) {
        $sql = "INSERT INTO audit_logs 
                (user_id, action, entity_type, entity_id, old_value, new_value, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $this->user_id, 
            $action, 
            $entity_type, 
            $entity_id,
            is_array($old_value) || is_object($old_value) ? json_encode($old_value) : $old_value,
            is_array($new_value) || is_object($new_value) ? json_encode($new_value) : $new_value,
            $this->getClientIP(),
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
    }
    
    /**
     * Log security events
     */
    public function logSecurityEvent($event_type, $description, $severity = 'medium', $metadata = null) {
        $sql = "INSERT INTO security_logs 
                (user_id, event_type, severity, description, ip_address, user_agent, metadata) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $this->user_id,
            $event_type,
            $severity,
            $description,
            $this->getClientIP(),
            $_SERVER['HTTP_USER_AGENT'] ?? '',
            is_array($metadata) || is_object($metadata) ? json_encode($metadata) : $metadata
        ]);
    }
    
    /**
     * Track page views and usage
     */
    public function trackPageView() {
        if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
            return; // Skip API requests
        }
        
        $sql = "INSERT INTO usage_analytics 
                (user_id, page_url, http_method, ip_address, user_agent, referrer, query_string) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $this->user_id,
            parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),
            $_SERVER['REQUEST_METHOD'],
            $this->getClientIP(),
            $_SERVER['HTTP_USER_AGENT'] ?? '',
            $_SERVER['HTTP_REFERER'] ?? null,
            $_SERVER['QUERY_STRING'] ?? null
        ]);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Record system metrics
     */
    public function recordMetric($metric_name, $value, $metadata = null) {
        $hour = date('H');
        $today = date('Y-m-d');
        
        $sql = "INSERT INTO system_metrics 
                (metric_date, metric_hour, metric_name, metric_value, metadata) 
                VALUES (?, ?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE 
                    metric_value = VALUES(metric_value),
                    metadata = VALUES(metadata)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $today,
            $hour,
            $metric_name,
            $value,
            is_array($metadata) || is_object($metadata) ? json_encode($metadata) : $metadata
        ]);
    }
    
    /**
     * Submit regulatory document
     */
    public function submitRegulatoryDocument($data) {
        $required = ['submission_type', 'title', 'submitted_by', 'submission_date'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("Missing required field: $field");
            }
        }
        
        $fields = [
            'submission_type', 'reference_number', 'title', 'description',
            'submitted_by', 'submission_date', 'due_date', 'status',
            'attachments', 'notes'
        ];
        
        $placeholders = array_fill(0, count($fields), '?');
        $values = [];
        
        foreach ($fields as $field) {
            $values[] = $data[$field] ?? null;
        }
        
        $sql = "INSERT INTO regulatory_submissions (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute($values);
        
        if ($result) {
            $submission_id = $this->db->lastInsertId();
            $this->logAudit('submission_created', 'regulatory_submission', $submission_id, null, $data);
            return $submission_id;
        }
        
        return false;
    }
    
    /**
     * Get client IP address
     */
    private function getClientIP() {
        $ip = '';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        }
        return $ip;
    }
    
    /**
     * Get active sessions
     */
    public function getActiveSessions($limit = 50) {
        $sql = "SELECT s.*, u.username, u.email 
                FROM system_sessions s 
                LEFT JOIN admin_users u ON s.user_id = u.user_id 
                WHERE s.status = 'active' 
                ORDER BY s.last_activity DESC 
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get audit logs with filters
     */
    public function getAuditLogs($filters = [], $limit = 50, $offset = 0) {
        $where = [];
        $params = [];
        
        if (!empty($filters['user_id'])) {
            $where[] = 'a.user_id = ?';
            $params[] = $filters['user_id'];
        }
        
        if (!empty($filters['action'])) {
            $where[] = 'a.action = ?';
            $params[] = $filters['action'];
        }
        
        if (!empty($filters['entity_type'])) {
            $where[] = 'a.entity_type = ?';
            $params[] = $filters['entity_type'];
        }
        
        if (!empty($filters['date_from'])) {
            $where[] = 'a.created_at >= ?';
            $params[] = $filters['date_from'] . ' 00:00:00';
        }
        
        if (!empty($filters['date_to'])) {
            $where[] = 'a.created_at <= ?';
            $params[] = $filters['date_to'] . ' 23:59:59';
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $sql = "SELECT a.*, u.username, u.email 
                FROM audit_logs a 
                LEFT JOIN admin_users u ON a.user_id = u.user_id 
                $whereClause 
                ORDER BY a.created_at DESC 
                LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get system metrics
     */
    public function getSystemMetrics($metric_name, $period = 'day', $limit = 24) {
        $groupBy = '';
        $selectFields = 'metric_date, metric_name, AVG(metric_value) as avg_value';
        
        switch ($period) {
            case 'hour':
                $groupBy = 'metric_date, metric_hour';
                $selectFields = 'metric_date, metric_hour, metric_name, AVG(metric_value) as avg_value';
                $limit = min($limit, 168); // Max 1 week of hourly data
                break;
                
            case 'week':
                $groupBy = 'YEAR(metric_date), WEEK(metric_date, 1)';
                $selectFields = 'CONCAT(YEAR(metric_date), "-W", LPAD(WEEK(metric_date, 1), 2, "0")) as week,
                               metric_name, AVG(metric_value) as avg_value';
                $limit = min($limit, 52); // Max 1 year of weekly data
                break;
                
            case 'month':
                $groupBy = 'YEAR(metric_date), MONTH(metric_date)';
                $selectFields = 'DATE_FORMAT(metric_date, "%Y-%m") as month,
                               metric_name, AVG(metric_value) as avg_value';
                $limit = min($limit, 36); // Max 3 years of monthly data
                break;
                
            default: // day
                $groupBy = 'metric_date';
                $limit = min($limit, 90); // Default to 90 days
        }
        
        $sql = "SELECT $selectFields 
                FROM system_metrics 
                WHERE metric_name = ? 
                GROUP BY $groupBy, metric_name 
                ORDER BY metric_date DESC 
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$metric_name, $limit]);
        return array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC)); // Return in chronological order
    }
    
    /**
     * Get usage statistics
     */
    public function getUsageStatistics($period = 'day', $limit = 30) {
        $dateFormat = '';
        
        switch ($period) {
            case 'hour':
                $dateFormat = '%Y-%m-%d %H:00:00';
                $limit = min($limit, 168); // Max 1 week of hourly data
                break;
                
            case 'week':
                $dateFormat = '%x-W%v';
                $limit = min($limit, 52); // Max 1 year of weekly data
                break;
                
            case 'month':
                $dateFormat = '%Y-%m';
                $limit = min($limit, 36); // Max 3 years of monthly data
                break;
                
            default: // day
                $dateFormat = '%Y-%m-%d';
                $limit = min($limit, 90); // Default to 90 days
        }
        
        $sql = "SELECT 
                    DATE_FORMAT(created_at, ?) as period,
                    COUNT(*) as total_visits,
                    COUNT(DISTINCT ip_address) as unique_visitors,
                    COUNT(DISTINCT user_id) as registered_users
                FROM usage_analytics 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? $period)
                GROUP BY period 
                ORDER BY period DESC 
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$dateFormat, $limit, $limit]);
        return array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC)); // Return in chronological order
    }
}
?>
