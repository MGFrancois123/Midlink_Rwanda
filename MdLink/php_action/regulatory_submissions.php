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
    case 'create':
        createSubmission();
        break;
    case 'update':
        updateSubmission();
        break;
    case 'fetch':
        fetchSubmissions();
        break;
    case 'fetch_single':
        fetchSingleSubmission();
        break;
    case 'fetch_stats':
        fetchSubmissionStats();
        break;
    case 'delete':
        deleteSubmission();
        break;
    case 'export_csv':
        exportSubmissionsCSV();
        break;
    case 'generate_compliance_report':
        generateComplianceReport();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

function createSubmission() {
    global $connect;
    
    $submissionType = $_POST['submission_type'] ?? '';
    $referenceNumber = $_POST['reference_number'] ?? '';
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $submissionDate = $_POST['submission_date'] ?? '';
    $dueDate = $_POST['due_date'] ?? null;
    $status = $_POST['status'] ?? 'draft';
    $notes = $_POST['notes'] ?? '';
    $submittedBy = $_SESSION['userId'];
    
    // Validate required fields
    if (empty($submissionType) || empty($title) || empty($submissionDate)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
        return;
    }
    
    // Generate reference number if not provided
    if (empty($referenceNumber)) {
        $referenceNumber = generateReferenceNumber($submissionType);
    }
    
    // Handle file uploads
    $attachments = [];
    if (isset($_FILES['attachments']) && !empty($_FILES['attachments']['name'][0])) {
        $attachments = handleFileUploads($_FILES['attachments']);
        if ($attachments === false) {
            echo json_encode(['success' => false, 'message' => 'File upload failed']);
            return;
        }
    }
    
    $sql = "INSERT INTO regulatory_submissions 
            (submission_type, reference_number, title, description, submitted_by, 
             submission_date, due_date, status, attachments, notes) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $connect->prepare($sql);
    $attachmentsJson = json_encode($attachments);
    $stmt->bind_param('ssssisssss', $submissionType, $referenceNumber, $title, $description, 
                      $submittedBy, $submissionDate, $dueDate, $status, $attachmentsJson, $notes);
    
    if ($stmt->execute()) {
        $submissionId = $connect->insert_id;
        
        // Log audit event
        if (function_exists('logAuditEvent')) {
            logAuditEvent($_SESSION['userId'], 'CREATE', 'regulatory_submission', $submissionId, null, [
                'reference_number' => $referenceNumber,
                'title' => $title,
                'type' => $submissionType,
                'status' => $status
            ]);
        }
        
        echo json_encode([
            'success' => true, 
            'message' => 'Submission created successfully',
            'submission_id' => $submissionId,
            'reference_number' => $referenceNumber
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create submission: ' . $connect->error]);
    }
}

function updateSubmission() {
    global $connect;
    
    $submissionId = $_POST['submission_id'] ?? '';
    $submissionType = $_POST['submission_type'] ?? '';
    $referenceNumber = $_POST['reference_number'] ?? '';
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $submissionDate = $_POST['submission_date'] ?? '';
    $dueDate = $_POST['due_date'] ?? null;
    $status = $_POST['status'] ?? '';
    $notes = $_POST['notes'] ?? '';
    
    if (empty($submissionId)) {
        echo json_encode(['success' => false, 'message' => 'Submission ID is required']);
        return;
    }
    
    // Get old values for audit log
    $oldSql = "SELECT * FROM regulatory_submissions WHERE submission_id = ?";
    $oldStmt = $connect->prepare($oldSql);
    $oldStmt->bind_param('i', $submissionId);
    $oldStmt->execute();
    $oldData = $oldStmt->get_result()->fetch_assoc();
    
    if (!$oldData) {
        echo json_encode(['success' => false, 'message' => 'Submission not found']);
        return;
    }
    
    // Handle file uploads if new files are provided
    $attachments = json_decode($oldData['attachments'], true) ?? [];
    if (isset($_FILES['attachments']) && !empty($_FILES['attachments']['name'][0])) {
        $newAttachments = handleFileUploads($_FILES['attachments']);
        if ($newAttachments !== false) {
            $attachments = array_merge($attachments, $newAttachments);
        }
    }
    
    $sql = "UPDATE regulatory_submissions SET 
            submission_type = ?, reference_number = ?, title = ?, description = ?, 
            submission_date = ?, due_date = ?, status = ?, attachments = ?, notes = ?,
            updated_at = CURRENT_TIMESTAMP
            WHERE submission_id = ?";
    
    $stmt = $connect->prepare($sql);
    $attachmentsJson = json_encode($attachments);
    $stmt->bind_param('sssssssssi', $submissionType, $referenceNumber, $title, $description, 
                      $submissionDate, $dueDate, $status, $attachmentsJson, $notes, $submissionId);
    
    if ($stmt->execute()) {
        // Log audit event
        if (function_exists('logAuditEvent')) {
            logAuditEvent($_SESSION['userId'], 'UPDATE', 'regulatory_submission', $submissionId, 
                         $oldData, [
                             'reference_number' => $referenceNumber,
                             'title' => $title,
                             'type' => $submissionType,
                             'status' => $status
                         ]);
        }
        
        echo json_encode(['success' => true, 'message' => 'Submission updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update submission: ' . $connect->error]);
    }
}

function fetchSubmissions() {
    global $connect;
    
    // Get filter parameters
    $status = $_POST['status_filter'] ?? '';
    $type = $_POST['type_filter'] ?? '';
    $dateFrom = $_POST['date_from'] ?? '';
    $dateTo = $_POST['date_to'] ?? '';
    $limit = $_POST['limit'] ?? 100;
    $offset = $_POST['offset'] ?? 0;
    
    // Build WHERE clause
    $whereConditions = [];
    $params = [];
    $types = '';
    
    if (!empty($status)) {
        $whereConditions[] = "rs.status = ?";
        $params[] = $status;
        $types .= 's';
    }
    
    if (!empty($type)) {
        $whereConditions[] = "rs.submission_type = ?";
        $params[] = $type;
        $types .= 's';
    }
    
    if (!empty($dateFrom)) {
        $whereConditions[] = "DATE(rs.submission_date) >= ?";
        $params[] = $dateFrom;
        $types .= 's';
    }
    
    if (!empty($dateTo)) {
        $whereConditions[] = "DATE(rs.submission_date) <= ?";
        $params[] = $dateTo;
        $types .= 's';
    }
    
    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
    
    // Count total records
    $countSql = "SELECT COUNT(*) as total FROM regulatory_submissions rs $whereClause";
    $countStmt = $connect->prepare($countSql);
    
    if (!empty($params)) {
        $countStmt->bind_param($types, ...$params);
    }
    
    $countStmt->execute();
    $totalRecords = $countStmt->get_result()->fetch_assoc()['total'];
    
    // Fetch submissions with user information
    $sql = "SELECT 
                rs.*,
                CONCAT(u.firstname, ' ', u.lastname) as submitted_by_name,
                u.username as submitted_by_username,
                CASE 
                    WHEN rs.due_date IS NOT NULL AND rs.due_date < CURDATE() AND rs.status NOT IN ('approved', 'rejected', 'withdrawn') 
                    THEN 1 ELSE 0 
                END as is_overdue
            FROM regulatory_submissions rs
            LEFT JOIN users u ON rs.submitted_by = u.user_id
            $whereClause
            ORDER BY rs.created_at DESC
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
    
    $submissions = [];
    while ($row = $result->fetch_assoc()) {
        $statusClass = getStatusClass($row['status']);
        $submittedBy = trim($row['submitted_by_name']) ?: $row['submitted_by_username'];
        
        $submissions[] = [
            'submission_id' => $row['submission_id'],
            'reference_number' => $row['reference_number'],
            'title' => $row['title'],
            'submission_type' => ucwords(str_replace('_', ' ', $row['submission_type'])),
            'submitted_by' => $submittedBy,
            'submission_date' => $row['submission_date'],
            'due_date' => $row['due_date'],
            'status' => $row['status'],
            'status_class' => $statusClass,
            'is_overdue' => (bool)$row['is_overdue'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $submissions,
        'total' => $totalRecords,
        'filtered' => count($submissions)
    ]);
}

function fetchSingleSubmission() {
    global $connect;
    
    $submissionId = $_GET['submission_id'] ?? '';
    
    if (empty($submissionId)) {
        echo json_encode(['success' => false, 'message' => 'Submission ID is required']);
        return;
    }
    
    $sql = "SELECT rs.*, CONCAT(u.firstname, ' ', u.lastname) as submitted_by_name
            FROM regulatory_submissions rs
            LEFT JOIN users u ON rs.submitted_by = u.user_id
            WHERE rs.submission_id = ?";
    
    $stmt = $connect->prepare($sql);
    $stmt->bind_param('i', $submissionId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $row['attachments'] = json_decode($row['attachments'], true) ?? [];
        echo json_encode(['success' => true, 'data' => $row]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Submission not found']);
    }
}

function fetchSubmissionStats() {
    global $connect;
    
    $stats = [];
    
    // Total submissions
    $sql = "SELECT COUNT(*) as count FROM regulatory_submissions";
    $result = $connect->query($sql);
    $stats['total'] = $result->fetch_assoc()['count'];
    
    // Pending submissions (draft + submitted)
    $sql = "SELECT COUNT(*) as count FROM regulatory_submissions WHERE status IN ('draft', 'submitted')";
    $result = $connect->query($sql);
    $stats['pending'] = $result->fetch_assoc()['count'];
    
    // Under review
    $sql = "SELECT COUNT(*) as count FROM regulatory_submissions WHERE status = 'under_review'";
    $result = $connect->query($sql);
    $stats['under_review'] = $result->fetch_assoc()['count'];
    
    // Approved
    $sql = "SELECT COUNT(*) as count FROM regulatory_submissions WHERE status = 'approved'";
    $result = $connect->query($sql);
    $stats['approved'] = $result->fetch_assoc()['count'];
    
    // Rejected
    $sql = "SELECT COUNT(*) as count FROM regulatory_submissions WHERE status = 'rejected'";
    $result = $connect->query($sql);
    $stats['rejected'] = $result->fetch_assoc()['count'];
    
    // Overdue
    $sql = "SELECT COUNT(*) as count FROM regulatory_submissions 
            WHERE due_date IS NOT NULL AND due_date < CURDATE() 
            AND status NOT IN ('approved', 'rejected', 'withdrawn')";
    $result = $connect->query($sql);
    $stats['overdue'] = $result->fetch_assoc()['count'];
    
    echo json_encode([
        'success' => true,
        'stats' => $stats
    ]);
}

function deleteSubmission() {
    global $connect;
    
    $submissionId = $_POST['submission_id'] ?? '';
    
    if (empty($submissionId)) {
        echo json_encode(['success' => false, 'message' => 'Submission ID is required']);
        return;
    }
    
    // Get submission data for audit log
    $sql = "SELECT * FROM regulatory_submissions WHERE submission_id = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param('i', $submissionId);
    $stmt->execute();
    $submissionData = $stmt->get_result()->fetch_assoc();
    
    if (!$submissionData) {
        echo json_encode(['success' => false, 'message' => 'Submission not found']);
        return;
    }
    
    // Delete submission
    $sql = "DELETE FROM regulatory_submissions WHERE submission_id = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param('i', $submissionId);
    
    if ($stmt->execute()) {
        // Log audit event
        if (function_exists('logAuditEvent')) {
            logAuditEvent($_SESSION['userId'], 'DELETE', 'regulatory_submission', $submissionId, $submissionData, null);
        }
        
        echo json_encode(['success' => true, 'message' => 'Submission deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete submission: ' . $connect->error]);
    }
}

function exportSubmissionsCSV() {
    global $connect;
    
    // Get filter parameters
    $status = $_POST['status_filter'] ?? '';
    $type = $_POST['type_filter'] ?? '';
    $dateFrom = $_POST['date_from'] ?? '';
    $dateTo = $_POST['date_to'] ?? '';
    
    // Build WHERE clause
    $whereConditions = [];
    $params = [];
    $types = '';
    
    if (!empty($status)) {
        $whereConditions[] = "rs.status = ?";
        $params[] = $status;
        $types .= 's';
    }
    
    if (!empty($type)) {
        $whereConditions[] = "rs.submission_type = ?";
        $params[] = $type;
        $types .= 's';
    }
    
    if (!empty($dateFrom)) {
        $whereConditions[] = "DATE(rs.submission_date) >= ?";
        $params[] = $dateFrom;
        $types .= 's';
    }
    
    if (!empty($dateTo)) {
        $whereConditions[] = "DATE(rs.submission_date) <= ?";
        $params[] = $dateTo;
        $types .= 's';
    }
    
    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
    
    // Fetch all matching records
    $sql = "SELECT 
                rs.reference_number,
                rs.title,
                rs.submission_type,
                CONCAT(u.firstname, ' ', u.lastname) as submitted_by,
                rs.submission_date,
                rs.due_date,
                rs.status,
                rs.description,
                rs.notes
            FROM regulatory_submissions rs
            LEFT JOIN users u ON rs.submitted_by = u.user_id
            $whereClause
            ORDER BY rs.created_at DESC";
    
    $stmt = $connect->prepare($sql);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="regulatory_submissions_' . date('Y-m-d_H-i-s') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // Write CSV header
    fputcsv($output, ['Reference Number', 'Title', 'Type', 'Submitted By', 'Submission Date', 'Due Date', 'Status', 'Description', 'Notes']);
    
    // Write data rows
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['reference_number'],
            $row['title'],
            $row['submission_type'],
            $row['submitted_by'],
            $row['submission_date'],
            $row['due_date'],
            $row['status'],
            $row['description'],
            $row['notes']
        ]);
    }
    
    fclose($output);
}

function generateComplianceReport() {
    global $connect;
    
    $dateFrom = $_POST['date_from'] ?? date('Y-m-d', strtotime('-12 months'));
    $dateTo = $_POST['date_to'] ?? date('Y-m-d');
    
    $report = [
        'period' => ['from' => $dateFrom, 'to' => $dateTo],
        'summary' => [],
        'by_type' => [],
        'by_status' => [],
        'overdue_analysis' => []
    ];
    
    // Summary statistics
    $sql = "SELECT 
                COUNT(*) as total_submissions,
                COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved,
                COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected,
                COUNT(CASE WHEN due_date IS NOT NULL AND due_date < CURDATE() AND status NOT IN ('approved', 'rejected', 'withdrawn') THEN 1 END) as overdue,
                AVG(DATEDIFF(updated_at, created_at)) as avg_processing_days
            FROM regulatory_submissions 
            WHERE DATE(submission_date) BETWEEN ? AND ?";
    
    $stmt = $connect->prepare($sql);
    $stmt->bind_param('ss', $dateFrom, $dateTo);
    $stmt->execute();
    $report['summary'] = $stmt->get_result()->fetch_assoc();
    
    // By submission type
    $sql = "SELECT submission_type, COUNT(*) as count 
            FROM regulatory_submissions 
            WHERE DATE(submission_date) BETWEEN ? AND ?
            GROUP BY submission_type 
            ORDER BY count DESC";
    
    $stmt = $connect->prepare($sql);
    $stmt->bind_param('ss', $dateFrom, $dateTo);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $report['by_type'][] = $row;
    }
    
    // By status
    $sql = "SELECT status, COUNT(*) as count 
            FROM regulatory_submissions 
            WHERE DATE(submission_date) BETWEEN ? AND ?
            GROUP BY status 
            ORDER BY count DESC";
    
    $stmt = $connect->prepare($sql);
    $stmt->bind_param('ss', $dateFrom, $dateTo);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $report['by_status'][] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'report' => $report
    ]);
}

function generateReferenceNumber($submissionType) {
    global $connect;
    
    $prefix = strtoupper(substr($submissionType, 0, 3));
    $year = date('Y');
    
    // Get next sequence number for this type and year
    $sql = "SELECT COUNT(*) + 1 as next_seq FROM regulatory_submissions 
            WHERE submission_type = ? AND YEAR(created_at) = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param('si', $submissionType, $year);
    $stmt->execute();
    $nextSeq = $stmt->get_result()->fetch_assoc()['next_seq'];
    
    return $prefix . '-' . $year . '-' . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
}

function handleFileUploads($files) {
    $uploadDir = '../uploads/regulatory_submissions/';
    
    // Create directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $uploadedFiles = [];
    $allowedTypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];
    $maxFileSize = 10 * 1024 * 1024; // 10MB
    
    for ($i = 0; $i < count($files['name']); $i++) {
        if ($files['error'][$i] === UPLOAD_ERR_OK) {
            $fileName = $files['name'][$i];
            $fileSize = $files['size'][$i];
            $fileTmp = $files['tmp_name'][$i];
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            // Validate file type
            if (!in_array($fileExt, $allowedTypes)) {
                continue;
            }
            
            // Validate file size
            if ($fileSize > $maxFileSize) {
                continue;
            }
            
            // Generate unique filename
            $newFileName = uniqid() . '_' . $fileName;
            $uploadPath = $uploadDir . $newFileName;
            
            if (move_uploaded_file($fileTmp, $uploadPath)) {
                $uploadedFiles[] = [
                    'original_name' => $fileName,
                    'stored_name' => $newFileName,
                    'file_size' => $fileSize,
                    'upload_date' => date('Y-m-d H:i:s')
                ];
            }
        }
    }
    
    return $uploadedFiles;
}

function getStatusClass($status) {
    switch ($status) {
        case 'approved':
            return 'badge-success';
        case 'rejected':
            return 'badge-danger';
        case 'under_review':
            return 'badge-info';
        case 'submitted':
            return 'badge-warning';
        case 'draft':
            return 'badge-secondary';
        case 'withdrawn':
            return 'badge-dark';
        default:
            return 'badge-light';
    }
}
?>