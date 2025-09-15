<?php include('./constant/layout/head.php');?>
<?php include('./constant/layout/header.php');?>
<?php include('./constant/layout/sidebar.php');?>
<?php include('./constant/connect.php');?>
<?php include('./constant/check.php');?>

<?php
// Get user role and pharmacy context
$userRole = $_SESSION['userRole'] ?? '';
$pharmacyId = $_SESSION['pharmacy_id'] ?? null;
$pharmacyName = $_SESSION['pharmacy_name'] ?? 'Pharmacy';

// Get pickup reminders data
$reminders = [];
$sql = "SELECT 
            pr.id,
            pr.patient_name,
            pr.patient_phone,
            pr.medicine_name,
            pr.dosage_instructions,
            pr.pickup_date,
            pr.reminder_date,
            pr.status,
            pr.notes,
            pr.created_at,
            pr.updated_at
        FROM pickup_reminders pr
        WHERE pr.pharmacy_id = ?
        ORDER BY pr.reminder_date ASC, pr.created_at DESC";

$stmt = $connect->prepare($sql);
$stmt->bind_param("i", $pharmacyId);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $reminders[] = $row;
}

// Calculate statistics
$stats = [];
$stats['total'] = count($reminders);
$stats['pending'] = count(array_filter($reminders, function($r) { return $r['status'] === 'pending'; }));
$stats['sent'] = count(array_filter($reminders, function($r) { return $r['status'] === 'sent'; }));
$stats['completed'] = count(array_filter($reminders, function($r) { return $r['status'] === 'completed'; }));
$stats['cancelled'] = count(array_filter($reminders, function($r) { return $r['status'] === 'cancelled'; }));

// Get overdue reminders (past pickup date)
$today = date('Y-m-d');
$stats['overdue'] = count(array_filter($reminders, function($r) use ($today) { 
    return $r['status'] !== 'completed' && $r['pickup_date'] < $today; 
}));
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

.pickup-header {
    background: linear-gradient(135deg, var(--success-color) 0%, #4caf50 100%);
    color: white;
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: var(--shadow);
    position: relative;
    overflow: hidden;
}

.pickup-header::before {
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
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 20px;
    padding: 1.5rem;
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
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
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
    font-size: 2rem;
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
    font-size: 0.9rem;
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
    border-color: var(--success-color);
    box-shadow: 0 0 0 0.2rem rgba(56, 142, 60, 0.25);
}

.btn {
    border-radius: 25px;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, var(--success-color), #4caf50);
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

.btn-danger {
    background: linear-gradient(135deg, var(--danger-color), #f44336);
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

.badge-secondary {
    background: var(--text-secondary);
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
        font-size: 1.5rem;
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
        <div class="pickup-header">
            <div class="header-content">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="mb-2">
                            <i class="fas fa-bell me-2"></i>Pickup Reminders
                        </h1>
                        <p class="mb-0">Manage prescription pickup reminders and patient notifications</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="d-flex gap-2 justify-content-end">
                            <button class="btn btn-light" onclick="refreshData()">
                                <i class="fas fa-sync-alt me-1"></i>Refresh
                            </button>
                            <button class="btn btn-light" onclick="sendBulkReminders()">
                                <i class="fas fa-paper-plane me-1"></i>Send Bulk
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
                        <div class="stat-value info"><?php echo $stats['total']; ?></div>
                        <div class="stat-label">Total Reminders</div>
                    </div>
                    <div class="stat-icon info">
                        <i class="fas fa-list"></i>
                    </div>
                </div>
                <small class="text-muted">All pickup reminders</small>
            </div>
            
            <div class="stat-card warning animate-fade-in">
                <div class="stat-header">
                    <div>
                        <div class="stat-value warning"><?php echo $stats['pending']; ?></div>
                        <div class="stat-label">Pending</div>
                    </div>
                    <div class="stat-icon warning">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <small class="text-muted">Awaiting pickup</small>
            </div>
            
            <div class="stat-card success animate-fade-in">
                <div class="stat-header">
                    <div>
                        <div class="stat-value success"><?php echo $stats['sent']; ?></div>
                        <div class="stat-label">Sent</div>
                    </div>
                    <div class="stat-icon success">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                </div>
                <small class="text-muted">SMS notifications sent</small>
            </div>
            
            <div class="stat-card success animate-fade-in">
                <div class="stat-header">
                    <div>
                        <div class="stat-value success"><?php echo $stats['completed']; ?></div>
                        <div class="stat-label">Completed</div>
                    </div>
                    <div class="stat-icon success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <small class="text-muted">Successfully picked up</small>
            </div>
            
            <div class="stat-card danger animate-fade-in">
                <div class="stat-header">
                    <div>
                        <div class="stat-value danger"><?php echo $stats['overdue']; ?></div>
                        <div class="stat-label">Overdue</div>
                    </div>
                    <div class="stat-icon danger">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
                <small class="text-muted">Past pickup date</small>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="mb-3">Quick Actions</h5>
                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            <button class="btn btn-primary" onclick="showCreateModal()">
                                <i class="fas fa-plus me-2"></i>Create Reminder
                            </button>
                            <button class="btn btn-success" onclick="sendBulkReminders()">
                                <i class="fas fa-paper-plane me-2"></i>Send Bulk SMS
                            </button>
                            <button class="btn btn-warning" onclick="markOverdue()">
                                <i class="fas fa-exclamation-triangle me-2"></i>Mark Overdue
                            </button>
                            <button class="btn btn-info" onclick="exportReminders()">
                                <i class="fas fa-file-excel me-2"></i>Export Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pickup Reminders Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Pickup Reminders
                    <span class="badge bg-light text-dark ms-2" id="reminderCount"><?php echo count($reminders); ?></span>
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="remindersTable">
                        <thead class="table-light">
                            <tr>
                                <th>Patient</th>
                                <th>Phone</th>
                                <th>Medicine</th>
                                <th>Pickup Date</th>
                                <th>Reminder Date</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($reminders)) { ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">
                                        <i class="fas fa-bell-slash fa-3x d-block mb-3 text-muted"></i>
                                        <h5>No pickup reminders found</h5>
                                        <p class="text-muted">Create your first pickup reminder to get started.</p>
                                    </td>
                                </tr>
                            <?php } else {
                                foreach ($reminders as $reminder) {
                                    $isOverdue = $reminder['status'] !== 'completed' && $reminder['pickup_date'] < date('Y-m-d');
                                    $statusBadge = '';
                                    $statusText = '';
                                    
                                    switch ($reminder['status']) {
                                        case 'pending':
                                            $statusBadge = 'warning';
                                            $statusText = 'Pending';
                                            break;
                                        case 'sent':
                                            $statusBadge = 'info';
                                            $statusText = 'Sent';
                                            break;
                                        case 'completed':
                                            $statusBadge = 'success';
                                            $statusText = 'Completed';
                                            break;
                                        case 'cancelled':
                                            $statusBadge = 'secondary';
                                            $statusText = 'Cancelled';
                                            break;
                                    }
                                    
                                    if ($isOverdue) {
                                        $statusBadge = 'danger';
                                        $statusText = 'Overdue';
                                    }
                            ?>
                                <tr data-id="<?php echo $reminder['id']; ?>">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user me-2 text-primary"></i>
                                            <strong><?php echo htmlspecialchars($reminder['patient_name']); ?></strong>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($reminder['patient_phone']); ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-pills me-2 text-success"></i>
                                            <span><?php echo htmlspecialchars($reminder['medicine_name']); ?></span>
                                        </div>
                                        <?php if ($reminder['dosage_instructions']) { ?>
                                            <small class="text-muted d-block"><?php echo htmlspecialchars($reminder['dosage_instructions']); ?></small>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-info"><?php echo date('M j, Y', strtotime($reminder['pickup_date'])); ?></span>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary"><?php echo date('M j, Y', strtotime($reminder['reminder_date'])); ?></span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?php echo $statusBadge; ?>"><?php echo $statusText; ?></span>
                                    </td>
                                    <td><?php echo date('M j, Y H:i', strtotime($reminder['created_at'])); ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="viewReminder(<?php echo $reminder['id']; ?>)"
                                                    title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <?php if ($reminder['status'] === 'pending') { ?>
                                                <button type="button" class="btn btn-sm btn-outline-success" 
                                                        onclick="sendReminder(<?php echo $reminder['id']; ?>)"
                                                        title="Send SMS">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            <?php } ?>
                                            <?php if ($reminder['status'] !== 'completed') { ?>
                                                <button type="button" class="btn btn-sm btn-outline-success" 
                                                        onclick="markCompleted(<?php echo $reminder['id']; ?>)"
                                                        title="Mark Completed">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            <?php } ?>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteReminder(<?php echo $reminder['id']; ?>)"
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
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

<!-- Create Reminder Modal -->
<div class="modal fade" id="createReminderModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Pickup Reminder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createReminderForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="patientName" class="form-label">Patient Name *</label>
                            <input type="text" class="form-control" id="patientName" name="patient_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="patientPhone" class="form-label">Patient Phone *</label>
                            <input type="tel" class="form-control" id="patientPhone" name="patient_phone" 
                                   placeholder="+250XXXXXXXXX" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="medicineName" class="form-label">Medicine Name *</label>
                            <input type="text" class="form-control" id="medicineName" name="medicine_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="dosageInstructions" class="form-label">Dosage Instructions</label>
                            <input type="text" class="form-control" id="dosageInstructions" name="dosage_instructions">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="pickupDate" class="form-label">Pickup Date *</label>
                            <input type="date" class="form-control" id="pickupDate" name="pickup_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="reminderDate" class="form-label">Reminder Date *</label>
                            <input type="datetime-local" class="form-control" id="reminderDate" name="reminder_date" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="createReminder()">Create Reminder</button>
            </div>
        </div>
    </div>
</div>

<?php include('./constant/layout/footer.php');?>

<script>
// Initialize DataTable
$(document).ready(function() {
    $('#remindersTable').DataTable({
        "order": [[4, "asc"]], // Sort by reminder date
        "pageLength": 10,
        "language": {
            "search": "Search reminders:",
            "lengthMenu": "Show _MENU_ reminders per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ reminders"
        },
        "columnDefs": [
            { "orderable": false, "targets": [7] } // Disable sorting for actions column
        ]
    });
});

// Show create modal
function showCreateModal() {
    $('#createReminderModal').modal('show');
}

// Create reminder
function createReminder() {
    const formData = new FormData(document.getElementById('createReminderForm'));
    formData.append('action', 'create');
    
    fetch('php_action/pickup_reminders.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Pickup reminder created successfully!');
            $('#createReminderModal').modal('hide');
            location.reload();
        } else {
            alert('Failed to create reminder: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while creating the reminder');
    });
}

// Send individual reminder
function sendReminder(id) {
    if (confirm('Send SMS reminder to this patient?')) {
        const formData = new FormData();
        formData.append('action', 'send_bulk');
        formData.append('reminder_ids[]', id);
        
        fetch('php_action/pickup_reminders.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('SMS reminder sent successfully!');
                location.reload();
            } else {
                alert('Failed to send reminder: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while sending the reminder');
        });
    }
}

// Send bulk reminders
function sendBulkReminders() {
    if (confirm('Send SMS reminders to all pending patients?')) {
        const formData = new FormData();
        formData.append('action', 'send_bulk');
        
        fetch('php_action/pickup_reminders.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Failed to send bulk reminders: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while sending bulk reminders');
        });
    }
}

// Mark reminder as completed
function markCompleted(id) {
    if (confirm('Mark this reminder as completed?')) {
        const formData = new FormData();
        formData.append('action', 'update_status');
        formData.append('id', id);
        formData.append('status', 'completed');
        
        fetch('php_action/pickup_reminders.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Reminder marked as completed!');
                location.reload();
            } else {
                alert('Failed to update status: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the reminder');
        });
    }
}

// Delete reminder
function deleteReminder(id) {
    if (confirm('Are you sure you want to delete this reminder?')) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);
        
        fetch('php_action/pickup_reminders.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Reminder deleted successfully!');
                location.reload();
            } else {
                alert('Failed to delete reminder: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the reminder');
        });
    }
}

// View reminder details
function viewReminder(id) {
    alert('View reminder details for ID: ' + id);
    // This could open a modal with detailed information
}

// Mark overdue reminders
function markOverdue() {
    alert('Marking overdue reminders functionality will be implemented');
}

// Export reminders
function exportReminders() {
    alert('Export functionality will be implemented');
}

// Refresh data
function refreshData() {
    location.reload();
}

// Auto-format phone number
document.getElementById('patientPhone').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    
    if (value.length === 9 && value.startsWith('7')) {
        this.value = '+250' + value;
    } else if (value.length === 12 && value.startsWith('250')) {
        this.value = '+' + value;
    }
});

// Set default reminder date to tomorrow
document.getElementById('pickupDate').addEventListener('change', function() {
    const pickupDate = new Date(this.value);
    const reminderDate = new Date(pickupDate);
    reminderDate.setDate(reminderDate.getDate() - 1); // Day before pickup
    
    const formattedDate = reminderDate.toISOString().slice(0, 16);
    document.getElementById('reminderDate').value = formattedDate;
});
</script>
