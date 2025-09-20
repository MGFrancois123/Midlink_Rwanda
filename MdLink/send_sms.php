<?php include('./constant/layout/head.php');?>
<?php include('./constant/layout/header.php');?>
<?php include('./constant/layout/sidebar.php');?>
<?php include('./constant/connect.php');?>
<?php include('./constant/check.php');?>

<?php
// Get user role and pharmacy_id for data scoping
$userRole = $_SESSION['userRole'] ?? '';
$pharmacyId = $_SESSION['pharmacy_id'] ?? null;

// Fetch pharmacy name if not set
$pharmacyName = $_SESSION['pharmacy_name'] ?? '';
if (empty($pharmacyName) && $pharmacyId) {
    $pharmacy_query = "SELECT name FROM pharmacies WHERE pharmacy_id = ?";
    $stmt = $connect->prepare($pharmacy_query);
    $stmt->bind_param('i', $pharmacyId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $pharmacyName = $row['name'];
        $_SESSION['pharmacy_name'] = $pharmacyName;
    }
}

// Include SMS configuration
require_once __DIR__ . '/includes/sms_config.php';

// Get SMS statistics
$stats = [];
$stats['total_sent'] = $connect->query("SELECT COUNT(*) as count FROM sms_logs")->fetch_assoc()['count'];
$stats['today_sent'] = $connect->query("SELECT COUNT(*) as count FROM sms_logs WHERE DATE(created_at) = CURDATE()")->fetch_assoc()['count'];
$stats['failed_sms'] = $connect->query("SELECT COUNT(*) as count FROM sms_logs WHERE api_response LIKE '%error%'")->fetch_assoc()['count'];
$stats['success_rate'] = $stats['total_sent'] > 0 ? round((($stats['total_sent'] - $stats['failed_sms']) / $stats['total_sent']) * 100, 1) : 0;

// Get recent SMS logs
$recentSms = [];
$smsQuery = "SELECT * FROM sms_logs ORDER BY created_at DESC LIMIT 10";
$result = $connect->query($smsQuery);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $recentSms[] = $row;
    }
}
?>

<style>
:root {
    --primary-color: #2e7d32;
    --secondary-color: #4caf50;
    --accent-color: #81c784;
    --success-color: #388e3c;
    --warning-color: #f57c00;
    --danger-color: #d32f2f;
    --info-color: #1976d2;
    --light-bg: #f8f9fa;
    --dark-bg: #1b5e20;
    --text-primary: #212529;
    --text-secondary: #6c757d;
    --border-color: #dee2e6;
    --shadow: 0 4px 20px rgba(46, 125, 50, 0.1);
    --shadow-hover: 0 8px 30px rgba(46, 125, 50, 0.2);
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    color: var(--text-primary);
    line-height: 1.6;
}

.page-wrapper { width: 100%; }
.page-wrapper .container-fluid { width: 100%; max-width: 100%; padding-left: 15px; padding-right: 15px; }

.sms-header {
    background: linear-gradient(135deg, var(--info-color) 0%, #42a5f5 100%);
    color: white;
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: var(--shadow);
    position: relative;
    overflow: hidden;
}

.sms-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    pointer-events: none;
}

.header-content {
    position: relative;
    z-index: 1;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: var(--shadow);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
}

.stat-card.info::before {
    background: linear-gradient(90deg, var(--info-color), #42a5f5);
}

.stat-card.success::before {
    background: linear-gradient(90deg, var(--success-color), #4caf50);
}

.stat-card.warning::before {
    background: linear-gradient(90deg, var(--warning-color), #ff9800);
}

.stat-card.danger::before {
    background: linear-gradient(90deg, var(--danger-color), #f44336);
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-hover);
}

.stat-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.stat-icon.info {
    background: linear-gradient(135deg, var(--info-color), #42a5f5);
}

.stat-icon.success {
    background: linear-gradient(135deg, var(--success-color), #4caf50);
}

.stat-icon.warning {
    background: linear-gradient(135deg, var(--warning-color), #ff9800);
}

.stat-icon.danger {
    background: linear-gradient(135deg, var(--danger-color), #f44336);
}

.stat-value {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0.5rem 0;
}

.stat-value.info {
    color: var(--info-color);
}

.stat-value.success {
    color: var(--success-color);
}

.stat-value.warning {
    color: var(--warning-color);
}

.stat-value.danger {
    color: var(--danger-color);
}

.stat-label {
    font-size: 1rem;
    color: var(--text-secondary);
    font-weight: 500;
}

.card {
    border: none;
    border-radius: 20px;
    box-shadow: var(--shadow);
    transition: all 0.3s ease;
    overflow: hidden;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-hover);
}

.card-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    color: white;
    border: none;
    padding: 1.5rem;
    font-weight: 600;
    font-size: 1.1rem;
}

.card-body {
    padding: 2rem;
}

.form-control, .form-select {
    border-radius: 10px;
    border: 2px solid var(--border-color);
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: var(--info-color);
    box-shadow: 0 0 0 0.2rem rgba(25, 118, 210, 0.25);
}

.btn {
    border-radius: 25px;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, var(--info-color), #42a5f5);
    border: none;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow);
}

.btn-success {
    background: linear-gradient(135deg, var(--success-color), #4caf50);
    border: none;
}

.btn-warning {
    background: linear-gradient(135deg, var(--warning-color), #ff9800);
    border: none;
}

.table {
    border: none;
}

.table thead th {
    background: var(--light-bg);
    border: none;
    font-weight: 600;
    color: var(--text-primary);
    padding: 1rem;
}

.table tbody td {
    border: none;
    padding: 1rem;
    vertical-align: middle;
}

.table tbody tr:hover {
    background: var(--light-bg);
}

.badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 500;
}

.badge-success {
    background: var(--success-color);
    color: white;
}

.badge-danger {
    background: var(--danger-color);
    color: white;
}

.badge-warning {
    background: var(--warning-color);
    color: white;
}

.badge-info {
    background: var(--info-color);
    color: white;
}

.alert {
    border-radius: 15px;
    border: none;
    padding: 1rem 1.5rem;
}

.alert-warning {
    background: linear-gradient(135deg, var(--warning-color), #ff9800);
    color: white;
}

.alert-info {
    background: linear-gradient(135deg, var(--info-color), #42a5f5);
    color: white;
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .stat-value {
        font-size: 2rem;
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeInUp 0.6s ease-out;
}
</style>

<div class="page-wrapper">
    <div class="container-fluid py-4">
        <!-- Enhanced Header -->
        <div class="sms-header">
            <div class="header-content">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="mb-2">
                            <i class="fas fa-sms me-2"></i>SMS Management
                        </h1>
                        <p class="mb-0">Send SMS notifications and manage communication with patients and staff</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="d-flex gap-2 justify-content-end">
                            <button class="btn btn-light" onclick="refreshStats()">
                                <i class="fas fa-sync-alt me-1"></i>Refresh
                            </button>
                            <button class="btn btn-light" onclick="viewSmsLogs()">
                                <i class="fas fa-history me-1"></i>View Logs
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card info animate-fade-in">
                <div class="stat-header">
                    <div>
                        <div class="stat-value info"><?php echo $stats['total_sent']; ?></div>
                        <div class="stat-label">Total SMS Sent</div>
                    </div>
                    <div class="stat-icon info">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                </div>
                <small class="text-muted">All time SMS count</small>
            </div>
            
            <div class="stat-card success animate-fade-in">
                <div class="stat-header">
                    <div>
                        <div class="stat-value success"><?php echo $stats['today_sent']; ?></div>
                        <div class="stat-label">Today's SMS</div>
                    </div>
                    <div class="stat-icon success">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                </div>
                <small class="text-muted">SMS sent today</small>
            </div>
            
            <div class="stat-card warning animate-fade-in">
                <div class="stat-header">
                    <div>
                        <div class="stat-value warning"><?php echo $stats['failed_sms']; ?></div>
                        <div class="stat-label">Failed SMS</div>
                    </div>
                    <div class="stat-icon warning">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
                <small class="text-muted">Failed delivery attempts</small>
            </div>
            
            <div class="stat-card danger animate-fade-in">
                <div class="stat-header">
                    <div>
                        <div class="stat-value danger"><?php echo $stats['success_rate']; ?>%</div>
                        <div class="stat-label">Success Rate</div>
                    </div>
                    <div class="stat-icon danger">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
                <small class="text-muted">Overall delivery success</small>
            </div>
        </div>

        <!-- SMS Configuration Alert -->
        <div class="alert alert-warning mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-3 fa-2x"></i>
                <div>
                    <h5 class="mb-1">SMS Configuration Notice</h5>
                    <p class="mb-0">Sender IDs are currently pending approval from HDEV. Contact <strong>info@hdevtech.cloud</strong> to get your sender IDs approved for production use.</p>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Send SMS Form -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Send SMS Message</h5>
                    </div>
                    <div class="card-body">
                        <form id="smsForm">
                            <div class="row">
                            <div class="col-md-6 mb-3">
    <label for="senderId" class="form-label">Sender ID</label>
    <select class="form-select" id="senderId" name="sender_id">
        <option value="">Use Default (AFRICASTKNG)</option>
        <?php 
        $senderIds = ['AFRICASTKNG','INEZA', 'PHARMACY', 'HEALTH', 'ALERT', 'INFO', 'REMINDER'];
        foreach ($senderIds as $sender) {
            echo '<option value="' . $sender . '">' . $sender . '</option>';
        }
        ?>
    </select>
    <small class="text-muted">Note: Sender IDs need HDEV approval. Leave blank for default.</small>
</div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                           placeholder="+250XXXXXXXXX" required>
                                    <small class="text-muted">Format: +250XXXXXXXXX</small>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="messageType" class="form-label">Message Type</label>
                                    <select class="form-select" id="messageType" name="type">
                                        <option value="general">General</option>
                                        <option value="reminder">Reminder</option>
                                        <option value="alert">Alert</option>
                                        <option value="notification">Notification</option>
                                        <option value="promotional">Promotional</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="characterCount" class="form-label">Character Count</label>
                                    <div class="form-control-plaintext">
                                        <span id="characterCount">0</span> / 160 characters
                                    </div>
                                </div>
                                
                                <div class="col-12 mb-3">
                                    <label for="message" class="form-label">Message</label>
                                    <textarea class="form-control" id="message" name="message" rows="4" 
                                              placeholder="Enter your SMS message here..." required maxlength="160"></textarea>
                                </div>
                                
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="fas fa-paper-plane me-1"></i>Send SMS
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="clearForm()">
                                        <i class="fas fa-eraser me-1"></i>Clear Form
                                    </button>
                                    <button type="button" class="btn btn-warning" onclick="testSms()">
                                        <i class="fas fa-vial me-1"></i>Test SMS
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions & Templates -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary" onclick="sendReminder()">
                                <i class="fas fa-bell me-1"></i>Send Reminder
                            </button>
                            <button class="btn btn-outline-success" onclick="sendAlert()">
                                <i class="fas fa-exclamation-circle me-1"></i>Send Alert
                            </button>
                            <button class="btn btn-outline-info" onclick="sendNotification()">
                                <i class="fas fa-info-circle me-1"></i>Send Notification
                            </button>
                            <button class="btn btn-outline-warning" onclick="bulkSms()">
                                <i class="fas fa-users me-1"></i>Bulk SMS
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-file-text me-2"></i>Message Templates</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-secondary btn-sm" onclick="useTemplate('reminder')">
                                <i class="fas fa-clock me-1"></i>Pickup Reminder
                            </button>
                            <button class="btn btn-outline-secondary btn-sm" onclick="useTemplate('expiry')">
                                <i class="fas fa-calendar-times me-1"></i>Expiry Alert
                            </button>
                            <button class="btn btn-outline-secondary btn-sm" onclick="useTemplate('lowstock')">
                                <i class="fas fa-exclamation-triangle me-1"></i>Low Stock Alert
                            </button>
                            <button class="btn btn-outline-secondary btn-sm" onclick="useTemplate('welcome')">
                                <i class="fas fa-handshake me-1"></i>Welcome Message
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent SMS Logs -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Recent SMS Activity</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Time</th>
                                <th>Sender ID</th>
                                <th>Recipient</th>
                                <th>Message</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Response</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentSms)) { ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-3x d-block mb-3 text-muted"></i>
                                        <h5>No SMS activity yet</h5>
                                        <p class="text-muted">Send your first SMS to see activity here.</p>
                                    </td>
                                </tr>
                            <?php } else { 
                                foreach ($recentSms as $sms) {
                                    $response = json_decode($sms['api_response'], true);
                                    $isSuccess = isset($response['status']) && strtolower($response['status']) === 'success';
                                    $statusBadge = $isSuccess ? 'success' : 'danger';
                                    $statusText = $isSuccess ? 'Sent' : 'Failed';
                            ?>
                                <tr>
                                    <td><?php echo date('M j, Y H:i', strtotime($sms['created_at'])); ?></td>
                                    <td><span class="badge badge-info"><?php echo htmlspecialchars($sms['sender_id']); ?></span></td>
                                    <td><?php echo htmlspecialchars($sms['recipient_phone']); ?></td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 200px;" title="<?php echo htmlspecialchars($sms['message']); ?>">
                                            <?php echo htmlspecialchars($sms['message']); ?>
                                        </div>
                                    </td>
                                    <td><span class="badge badge-secondary"><?php echo htmlspecialchars($sms['message_type']); ?></span></td>
                                    <td><span class="badge badge-<?php echo $statusBadge; ?>"><?php echo $statusText; ?></span></td>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo isset($response['message']) ? htmlspecialchars($response['message']) : 'No response'; ?>
                                        </small>
                                    </td>
                                </tr>
                            <?php } } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('./constant/layout/footer.php');?>

<script>

// Character counter
document.getElementById('message').addEventListener('input', function() {
    const count = this.value.length;
    document.getElementById('characterCount').textContent = count;
    
    if (count > 160) {
        document.getElementById('characterCount').style.color = 'red';
    } else if (count > 140) {
        document.getElementById('characterCount').style.color = 'orange';
    } else {
        document.getElementById('characterCount').style.color = 'green';
    }
});

// SMS Form submission
document.getElementById('smsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const phone = formData.get('phone');
    const message = formData.get('message');
    
    // Validate phone number format
    if (!phone.match(/^\+250[0-9]{9}$/)) {
        showAlert('Please enter a valid Rwandan phone number in format +250XXXXXXXXX', 'error');
        return;
    }
    
    // Validate message length
    if (message.length === 0) {
        showAlert('Please enter a message', 'error');
        return;
    }
    
    if (message.length > 160) {
        showAlert('Message is too long. Maximum 160 characters allowed.', 'error');
        return;
    }
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Sending...';
    submitBtn.disabled = true;
    
    // Send SMS
    fetch('php_action/send_sms.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('SMS sent successfully!', 'success');
            clearForm();
            // Refresh page after a short delay to show new SMS in logs
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            showAlert('Failed to send SMS: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while sending SMS', 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

// Template functions
function useTemplate(type) {
    const templates = {
        reminder: 'Hello! This is a reminder that your prescription is ready for pickup at our pharmacy. Please visit us during business hours.',
        expiry: 'Alert: Some medicines in your prescription are expiring soon. Please check with us for replacements.',
        lowstock: 'Notice: We are currently low on some medicines. Please contact us to confirm availability before visiting.',
        welcome: 'Welcome to our pharmacy! We are here to serve your healthcare needs. Thank you for choosing us.'
    };
    
    document.getElementById('message').value = templates[type] || '';
    document.getElementById('message').dispatchEvent(new Event('input'));
}

// Quick action functions
function sendReminder() {
    useTemplate('reminder');
    document.getElementById('messageType').value = 'reminder';
    document.getElementById('senderId').value = 'REMINDER';
}

function sendAlert() {
    useTemplate('expiry');
    document.getElementById('messageType').value = 'alert';
    document.getElementById('senderId').value = 'ALERT';
}

function sendNotification() {
    useTemplate('welcome');
    document.getElementById('messageType').value = 'notification';
    document.getElementById('senderId').value = 'INFO';
}

function bulkSms() {
    showAlert('Bulk SMS functionality will be implemented in the next version', 'info');
}

function testSms() {
    // Fill form with test data
    document.getElementById('phone').value = '+250786980814';
    document.getElementById('message').value = 'This is a test SMS from MdLink Pharmacy Management System sent at ' + new Date().toLocaleString();
    document.getElementById('message').dispatchEvent(new Event('input'));
    document.getElementById('messageType').value = 'general';
    document.getElementById('senderId').value = 'INEZA';
    
    // Optionally send test SMS directly
    const confirmed = confirm('Do you want to send this test SMS immediately?');
    if (confirmed) {
        const formData = new FormData();
        formData.append('phone', '+250786980814');
        formData.append('message', document.getElementById('message').value);
        
        fetch('php_action/test_sms.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Test SMS sent successfully!', 'success');
                setTimeout(() => location.reload(), 2000);
            } else {
                showAlert('Test SMS failed: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Test SMS Error:', error);
            showAlert('Error sending test SMS', 'error');
        });
    }
}

function clearForm() {
    document.getElementById('smsForm').reset();
    document.getElementById('characterCount').textContent = '0';
    document.getElementById('characterCount').style.color = 'green';
}

function refreshStats() {
    location.reload();
}

function viewSmsLogs() {
    // This could open a modal or redirect to a detailed logs page
    showAlert('Detailed SMS logs view will be implemented', 'info');
}

// Auto-format phone number
document.getElementById('phone').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    
    if (value.length === 9 && value.startsWith('7')) {
        this.value = '+250' + value;
    } else if (value.length === 12 && value.startsWith('250')) {
        this.value = '+' + value;
    }
});

// Enhanced alert function
function showAlert(message, type = 'info') {
    // Remove any existing alerts
    const existingAlerts = document.querySelectorAll('.custom-alert');
    existingAlerts.forEach(alert => alert.remove());
    
    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show custom-alert`;
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';
    alertDiv.style.boxShadow = '0 4px 20px rgba(0,0,0,0.15)';
    
    const icon = {
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-circle',
        warning: 'fas fa-exclamation-triangle',
        info: 'fas fa-info-circle'
    };
    
    alertDiv.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="${icon[type] || icon.info} me-2"></i>
            <span>${message}</span>
        </div>
        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (alertDiv && alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Service health check function
function checkSmsServiceHealth() {
    fetch('https://sms-system-aelu.onrender.com/health')
        .then(response => response.json())
        .then(data => {
            console.log('SMS Service Health:', data);
            if (data.status === 'healthy') {
                showAlert('SMS Service is online and healthy', 'success');
            } else {
                showAlert('SMS Service health check failed', 'warning');
            }
        })
        .catch(error => {
            console.error('Health check failed:', error);
            showAlert('Cannot connect to SMS service', 'error');
        });
}

function validateSmsForm() {
    const senderId = document.getElementById('senderId').value;  // Can be empty now
    const phone = document.getElementById('phone').value;
    const message = document.getElementById('message').value;
    
    // Remove senderId required check
    // if (!senderId) { ... }  // Comment out or remove
    
    if (!phone) {
        showAlert('Please enter a phone number', 'error');
        return false;
    }
    
    if (!phone.match(/^\+250[0-9]{9}$/)) {
        showAlert('Please enter a valid Rwandan phone number (+250XXXXXXXXX)', 'error');
        return false;
    }
    
    if (!message) {
        showAlert('Please enter a message', 'error');
        return false;
    }
    
    if (message.length > 160) {
        showAlert('Message is too long (maximum 160 characters)', 'error');
        return false;
    }
    
    return true;
}

// Enhanced phone number formatting
document.getElementById('phone').addEventListener('blur', function() {
    const phone = this.value.trim();
    if (phone) {
        // Try to format the phone number
        let formatted = phone;
        
        // Remove any spaces, dashes, or other formatting
        formatted = formatted.replace(/[\s\-\(\)]/g, '');
        
        // Handle different input patterns
        if (formatted.match(/^7[0-9]{8}$/)) {
            // Local format: 7XXXXXXXX
            this.value = '+250' + formatted;
        } else if (formatted.match(/^2507[0-9]{8}$/)) {
            // International without plus: 2507XXXXXXXX  
            this.value = '+' + formatted;
        } else if (formatted.match(/^\+2507[0-9]{8}$/)) {
            // Already correct format
            this.value = formatted;
        }
        
        // Validate final format
        if (!this.value.match(/^\+250[0-9]{9}$/)) {
            this.classList.add('is-invalid');
            showAlert('Invalid phone number format. Use +250XXXXXXXXX', 'error');
        } else {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        }
    }
});

// Message type change handler
document.getElementById('messageType').addEventListener('change', function() {
    const messageType = this.value;
    const senderIdSelect = document.getElementById('senderId');
    
    // Suggest appropriate sender ID based on message type
    const senderSuggestions = {
        'reminder': 'REMINDER',
        'alert': 'ALERT',
        'notification': 'INFO',
        'promotional': 'PHARMACY',
        'general': 'INEZA'
    };
    
    if (senderSuggestions[messageType] && senderIdSelect.value === '') {
        senderIdSelect.value = senderSuggestions[messageType];
    }
});

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Add form validation classes
    const form = document.getElementById('smsForm');
    form.classList.add('needs-validation');
    
    // Check SMS service health on page load (optional)
    // checkSmsServiceHealth();
    
    // Add tooltips if Bootstrap is available
    if (typeof bootstrap !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});


</script>
