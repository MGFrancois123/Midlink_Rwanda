<?php
require_once '../includes/SystemLogger.php';
require_once '../constant/connect.php';
// Try PDO first; if unavailable, we'll fall back to mysqli direct queries
$pdo = null; $pdoOk = false;
if (class_exists('PDO')) {
  try {
    $pdo = new PDO("mysql:host={$localhost};dbname={$dbname};charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    $pdoOk = true;
  } catch (Exception $e) { $pdoOk = false; }
}
$systemLogger = $pdoOk ? new SystemLogger($pdo) : null;

// Get active sessions
if ($systemLogger) {
  $activeSessions = $systemLogger->getActiveSessions(100);
} else {
  // Fallback with mysqli
  $activeSessions = [];
  $sql = "SELECT s.*, u.username, u.email FROM system_sessions s LEFT JOIN admin_users u ON s.user_id = u.user_id WHERE s.status='active' ORDER BY s.last_activity DESC LIMIT 100";
  if ($res = $connect->query($sql)) {
    while ($row = $res->fetch_assoc()) { $activeSessions[] = $row; }
  }
}

// Handle session termination
if (isset($_POST['terminate_session'])) {
    $sessionId = $_POST['session_id'];
    $result = $systemLogger->terminateSession($sessionId, 'terminated_by_admin');
    
    if ($result) {
        $successMessage = 'Session terminated successfully';
        $activeSessions = $systemLogger->getActiveSessions(100);
    } else {
        $errorMessage = 'Failed to terminate session';
    }
}
// Helper: PHP timeAgo
if (!function_exists('timeAgo')) {
    function timeAgo($datetime) {
        $timestamp = is_numeric($datetime) ? (int)$datetime : strtotime($datetime);
        $diff = time() - $timestamp;
        if ($diff < 60) return $diff . ' seconds';
        $diff = floor($diff/60); if ($diff < 60) return $diff . ' minutes';
        $diff = floor($diff/60); if ($diff < 24) return $diff . ' hours';
        $diff = floor($diff/24); if ($diff < 30) return $diff . ' days';
        $diff = floor($diff/30); if ($diff < 12) return $diff . ' months';
        $diff = floor($diff/12); return $diff . ' years';
    }
}
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Active Sessions</h1>
        <div>
            <button type="button" class="btn btn-outline-secondary" id="refreshSessions">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
    </div>

    <?php if (isset($successMessage)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i> <?php echo $successMessage; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    
    <?php if (isset($errorMessage)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i> <?php echo $errorMessage; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <!-- Sessions Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Active Sessions (<?php echo count($activeSessions); ?>)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="sessionsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>IP Address</th>
                            <th>Device</th>
                            <th>Last Activity</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($activeSessions)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>No active sessions found</p>
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($activeSessions as $session): 
                                $isCurrentSession = $session['session_id'] === session_id();
                                $statusClass = $isCurrentSession ? 'success' : 'primary';
                                $statusText = $isCurrentSession ? 'Current' : 'Active';
                                
                                $lastActivity = new DateTime($session['last_activity']);
                                $lastActivityFormatted = $lastActivity->format('M j, Y g:i A');
                                
                                // Simple device detection
                                $userAgent = $session['user_agent'] ?? '';
                                $device = 'Unknown';
                                if (stripos($userAgent, 'mobile') !== false) {
                                    $device = 'Mobile';
                                } elseif (stripos($userAgent, 'tablet') !== false) {
                                    $device = 'Tablet';
                                } elseif (stripos($userAgent, 'windows') !== false || 
                                         stripos($userAgent, 'mac') !== false || 
                                         stripos($userAgent, 'linux') !== false) {
                                    $device = 'Desktop';
                                }
                                
                                $browser = 'Unknown';
                                if (stripos($userAgent, 'chrome') !== false) {
                                    $browser = 'Chrome';
                                } elseif (stripos($userAgent, 'firefox') !== false) {
                                    $browser = 'Firefox';
                                } elseif (stripos($userAgent, 'safari') !== false) {
                                    $browser = 'Safari';
                                } elseif (stripos($userAgent, 'edge') !== false) {
                                    $browser = 'Edge';
                                }
                                
                                $os = 'Unknown';
                                if (stripos($userAgent, 'windows') !== false) {
                                    $os = 'Windows';
                                } elseif (stripos($userAgent, 'mac') !== false) {
                                    $os = 'macOS';
                                } elseif (stripos($userAgent, 'linux') !== false) {
                                    $os = 'Linux';
                                } elseif (stripos($userAgent, 'android') !== false) {
                                    $os = 'Android';
                                } elseif (stripos($userAgent, 'iphone') !== false || stripos($userAgent, 'ipad') !== false) {
                                    $os = 'iOS';
                                }
                                
                                $deviceInfo = "<div class='small'><strong>$browser</strong> on $os<br><span class='text-muted'>$device</span></div>";
                                
                                // Check if session is idle (no activity for 5 minutes)
                                $isIdle = (time() - strtotime($session['last_activity'])) > 300;
                                $statusText = $isIdle ? 'Idle' : $statusText;
                                $statusClass = $isIdle ? 'warning' : $statusClass;
                                
                                // Check if session is expired (no activity for 30 minutes)
                                $isExpired = (time() - strtotime($session['last_activity'])) > 1800;
                                if ($isExpired) {
                                    $statusText = 'Expired';
                                    $statusClass = 'danger';
                                }
                            ?>
                            <tr class="<?php echo $isCurrentSession ? 'table-active' : ''; ?>">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-user-circle fa-2x text-gray-300"></i>
                                        </div>
                                        <div class="ms-3">
                                            <div class="fw-bold"><?php echo htmlspecialchars($session['username'] ?? 'Guest'); ?></div>
                                            <div class="text-muted small"><?php echo htmlspecialchars($session['email'] ?? 'Not logged in'); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($session['ip_address']); ?></td>
                                <td><?php echo $deviceInfo; ?></td>
                                <td>
                                    <div class="small">
                                        <?php echo $lastActivityFormatted; ?>
                                        <div class="text-muted"><?php echo timeAgo($session['last_activity']); ?> ago</div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $statusClass; ?>">
                                        <?php echo $statusText; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!$isCurrentSession): ?>
                                    <form method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to terminate this session?');">
                                        <input type="hidden" name="session_id" value="<?php echo $session['session_id']; ?>">
                                        <button type="submit" name="terminate_session" class="btn btn-sm btn-outline-danger" 
                                                data-bs-toggle="tooltip" title="Terminate Session">
                                            <i class="fas fa-power-off"></i>
                                        </button>
                                    </form>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-outline-secondary" disabled>
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize DataTable if available (non-blocking)
$(document).ready(function() {
    if ($.fn && $.fn.DataTable) {
        $('#sessionsTable').DataTable({
            responsive: true,
            order: [[3, 'desc']],
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            dom: '<"row"<"col-md-6"l><"col-md-6"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search sessions...",
                lengthMenu: "Show _MENU_ entries per page",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                infoFiltered: "(filtered from _MAX_ total entries)",
                zeroRecords: "No matching sessions found"
            },
            columnDefs: [
                { orderable: false, targets: [5] }
            ]
        });
    }
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Refresh button
    document.getElementById('refreshSessions').addEventListener('click', function() {
        window.location.href = window.location.href;
    });
});
</script>
