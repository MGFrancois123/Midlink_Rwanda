<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'constant/connect.php';
require_once 'constant/check.php';

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send SMS - Ineza Pharmacy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
            background: linear-gradient(90deg, var(--success-color), #66bb6a);
        }

        .stat-card.warning::before {
            background: linear-gradient(90deg, var(--warning-color), #ff9800);
        }

        .stat-card.primary::before {
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
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
            background: linear-gradient(135deg, var(--success-color), #66bb6a);
        }

        .stat-icon.warning {
            background: linear-gradient(135deg, var(--warning-color), #ff9800);
        }

        .stat-icon.primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
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

        .stat-value.primary {
            color: var(--primary-color);
        }

        .stat-label {
            font-size: 1rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .sms-form-section {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow);
        }

        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid var(--border-color);
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(46, 125, 50, 0.25);
        }

        .btn {
            border-radius: 25px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .btn-info {
            background: linear-gradient(135deg, var(--info-color), #42a5f5);
            border: none;
            color: white;
        }

        .btn-info:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
            color: white;
        }

        .card {
            border: none;
            border-radius: 20px;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-5px);
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

        .badge-warning {
            background: var(--warning-color);
            color: white;
        }

        .badge-danger {
            background: var(--danger-color);
            color: white;
        }

        .badge-info {
            background: var(--info-color);
            color: white;
        }

        .message-preview {
            background: var(--light-bg);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
            border-left: 4px solid var(--info-color);
        }

        .character-count {
            font-size: 0.875rem;
            color: var(--text-secondary);
            margin-top: 0.5rem;
        }

        .character-count.warning {
            color: var(--warning-color);
        }

        .character-count.danger {
            color: var(--danger-color);
        }

        .template-card {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .template-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }

        .template-card.selected {
            border: 2px solid var(--primary-color);
            background: rgba(46, 125, 50, 0.05);
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
</head>
<body>
    <!-- Header -->
    <div class="sms-header">
        <div class="header-content">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-2">
                        <i class="fas fa-sms me-3"></i>Send SMS
                    </h1>
                    <p class="mb-0">Professional SMS communication platform for <?php echo htmlspecialchars($pharmacyName ?: 'Ineza Pharmacy'); ?></p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="dashboard_ineza_pharmacy.php" class="btn btn-outline-light me-2">
                        <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                    </a>
                    <a href="logout.php" class="btn btn-outline-light">
                        <i class="fas fa-sign-out-alt me-1"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container-fluid">
        <!-- SMS Statistics -->
        <div class="stats-grid">
            <div class="stat-card info animate-fade-in">
                <div class="stat-header">
                    <div>
                        <div class="stat-value info" id="totalSmsSent">0</div>
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
                        <div class="stat-value success" id="successRate">100%</div>
                        <div class="stat-label">Success Rate</div>
                    </div>
                    <div class="stat-icon success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <small class="text-muted">Successful deliveries</small>
            </div>
            
            <div class="stat-card warning animate-fade-in">
                <div class="stat-header">
                    <div>
                        <div class="stat-value warning" id="creditsLeft">1000</div>
                        <div class="stat-label">Credits Left</div>
                    </div>
                    <div class="stat-icon warning">
                        <i class="fas fa-coins"></i>
                    </div>
                </div>
                <small class="text-muted">Available SMS credits</small>
            </div>
            
            <div class="stat-card primary animate-fade-in">
                <div class="stat-header">
                    <div>
                        <div class="stat-value primary" id="todaySent">0</div>
                        <div class="stat-label">Sent Today</div>
                    </div>
                    <div class="stat-icon primary">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                </div>
                <small class="text-muted">SMS sent today</small>
            </div>
        </div>

        <!-- SMS Form Section -->
        <div class="sms-form-section">
            <h5 class="mb-3"><i class="fas fa-edit me-2"></i>Compose SMS Message</h5>
            
            <form id="smsForm">
                <div class="row">
                    <div class="col-lg-8">
                        <!-- Message Composition -->
                        <div class="form-group mb-3">
                            <label for="senderId" class="form-label fw-bold">Sender ID</label>
                            <input type="text" class="form-control" id="senderId" name="senderId" 
                                   value="<?php echo htmlspecialchars($pharmacyName ?: 'INEZA'); ?>" 
                                   maxlength="11" placeholder="Enter sender ID (max 11 characters)">
                            <small class="form-text text-muted">Sender ID will appear on recipient's phone</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="recipientPhone" class="form-label fw-bold">Recipient Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-text">+250</span>
                                <input type="tel" class="form-control" id="recipientPhone" name="recipientPhone" 
                                       placeholder="e.g., 786123456" maxlength="9" required>
                            </div>
                            <small class="form-text text-muted">Enter phone number without country code</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="messageContent" class="form-label fw-bold">Message Content</label>
                            <textarea class="form-control" id="messageContent" name="messageContent" 
                                      rows="6" maxlength="160" placeholder="Type your message here..." required></textarea>
                            <div class="character-count" id="characterCount">0/160 characters</div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="messageType" class="form-label fw-bold">Message Type</label>
                            <select class="form-select" id="messageType" name="messageType">
                                <option value="general">General Message</option>
                                <option value="reminder">Reminder</option>
                                <option value="alert">Alert</option>
                                <option value="promotion">Promotion</option>
                                <option value="notification">Notification</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <!-- Message Templates -->
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-layer-group me-2"></i>Message Templates</h6>
                            </div>
                            <div class="card-body">
                                <div class="template-card card mb-2 p-3" onclick="loadTemplate('reminder')">
                                    <h6 class="mb-1">📅 Appointment Reminder</h6>
                                    <small class="text-muted">Remind patients about upcoming appointments</small>
                                </div>
                                
                                <div class="template-card card mb-2 p-3" onclick="loadTemplate('stock')">
                                    <h6 class="mb-1">📦 Stock Alert</h6>
                                    <small class="text-muted">Notify about low stock or new arrivals</small>
                                </div>
                                
                                <div class="template-card card mb-2 p-3" onclick="loadTemplate('promotion')">
                                    <h6 class="mb-1">🎉 Special Offer</h6>
                                    <small class="text-muted">Promote special deals and discounts</small>
                                </div>
                                
                                <div class="template-card card mb-2 p-3" onclick="loadTemplate('notification')">
                                    <h6 class="mb-1">🔔 General Notification</h6>
                                    <small class="text-muted">Send general pharmacy updates</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Message Preview -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-eye me-2"></i>Message Preview</h6>
                            </div>
                            <div class="card-body">
                                <div class="message-preview" id="messagePreview">
                                    <strong>From:</strong> <span id="previewSender"><?php echo htmlspecialchars($pharmacyName ?: 'INEZA'); ?></span><br>
                                    <strong>To:</strong> <span id="previewPhone">+250 786123456</span><br>
                                    <strong>Message:</strong><br>
                                    <div id="previewContent" class="mt-2">Your message will appear here...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Send SMS
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="clearForm()">
                                <i class="fas fa-eraser me-2"></i>Clear Form
                            </button>
                            <button type="button" class="btn btn-outline-info" onclick="saveTemplate()">
                                <i class="fas fa-save me-2"></i>Save as Template
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- SMS History -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>SMS History
                    <span class="badge bg-light text-dark ms-2" id="smsHistoryCount">0</span>
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="smsHistoryTable">
                        <thead class="table-light">
                            <tr>
                                <th>Date & Time</th>
                                <th>Sender ID</th>
                                <th>Recipient</th>
                                <th>Message</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="smsHistoryBody">
                            <!-- Data will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#smsHistoryTable').DataTable({
                "order": [[0, "desc"]], // Sort by date descending
                "pageLength": 10,
                "language": {
                    "search": "Search SMS history:",
                    "lengthMenu": "Show _MENU_ records per page",
                    "info": "Showing _START_ to _END_ of _TOTAL_ records"
                },
                "columnDefs": [
                    { "orderable": false, "targets": [6] } // Disable sorting for actions column
                ]
            });

            // Load initial data
            loadSmsHistory();
            loadSmsStats();

            // Add animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-fade-in');
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.stat-card, .card').forEach(el => {
                observer.observe(el);
            });

            // Character count for message
            $('#messageContent').on('input', function() {
                const length = $(this).val().length;
                const maxLength = 160;
                const countElement = $('#characterCount');
                
                countElement.text(length + '/' + maxLength + ' characters');
                
                if (length > maxLength * 0.8) {
                    countElement.addClass('warning');
                    countElement.removeClass('danger');
                } else if (length > maxLength * 0.95) {
                    countElement.addClass('danger');
                    countElement.removeClass('warning');
                } else {
                    countElement.removeClass('warning danger');
                }
                
                updateMessagePreview();
            });

            // Update preview on input changes
            $('#senderId, #recipientPhone').on('input', updateMessagePreview);
        });

        // Load SMS statistics
        function loadSmsStats() {
            // This would typically fetch from an API
            $('#totalSmsSent').text('1,247');
            $('#successRate').text('98.5%');
            $('#creditsLeft').text('853');
            $('#todaySent').text('12');
        }

        // Load SMS history
        function loadSmsHistory() {
            // This would typically fetch from an API
            const sampleData = [
                {
                    date: '2024-01-15 14:30:00',
                    sender: 'INEZA',
                    recipient: '+250 786123456',
                    message: 'Your appointment is scheduled for tomorrow at 10:00 AM. Please arrive 10 minutes early.',
                    type: 'Reminder',
                    status: 'Delivered'
                },
                {
                    date: '2024-01-15 13:15:00',
                    sender: 'INEZA',
                    recipient: '+250 789456123',
                    message: 'New stock of antibiotics has arrived. Visit us for quality medications.',
                    type: 'Alert',
                    status: 'Delivered'
                }
            ];
            
            updateSmsHistoryTable(sampleData);
        }

        // Update SMS history table
        function updateSmsHistoryTable(data) {
            const tbody = $('#smsHistoryBody');
            tbody.empty();
            
            if (data.length === 0) {
                tbody.append('<tr><td colspan="7" class="text-center text-muted py-5">' +
                    '<i class="fas fa-inbox fa-3x d-block mb-3 text-muted"></i>' +
                    '<h5>No SMS history found</h5>' +
                    '<p class="text-muted">SMS history will appear here after sending messages.</p>' +
                    '</td></tr>');
                return;
            }
            
            data.forEach(function(sms) {
                const statusClass = sms.status === 'Delivered' ? 'badge-success' : 
                                  sms.status === 'Pending' ? 'badge-warning' : 'badge-danger';
                
                const row = `
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-clock me-2 text-secondary"></i>
                                <strong>${sms.date}</strong>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-info">${sms.sender}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-phone me-2 text-primary"></i>
                                ${sms.recipient}
                            </div>
                        </td>
                        <td>
                            <div class="text-truncate" style="max-width: 200px;" title="${sms.message}">
                                ${sms.message}
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-secondary">${sms.type}</span>
                        </td>
                        <td>
                            <span class="badge ${statusClass}">${sms.status}</span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                        onclick="viewSmsDetails('${sms.date}')"
                                        title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-success" 
                                        onclick="resendSms('${sms.recipient}', '${sms.message}')"
                                        title="Resend">
                                    <i class="fas fa-redo"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                tbody.append(row);
            });
            
            $('#smsHistoryCount').text(data.length);
            $('#smsHistoryTable').DataTable().draw();
        }

        // Update message preview
        function updateMessagePreview() {
            const sender = $('#senderId').val() || 'INEZA';
            const phone = $('#recipientPhone').val() ? '+250 ' + $('#recipientPhone').val() : '+250 786123456';
            const content = $('#messageContent').val() || 'Your message will appear here...';
            
            $('#previewSender').text(sender);
            $('#previewPhone').text(phone);
            $('#previewContent').text(content);
        }

        // Load message template
        function loadTemplate(type) {
            let message = '';
            
            switch(type) {
                case 'reminder':
                    message = 'Dear patient, your appointment is scheduled for tomorrow at 10:00 AM. Please arrive 10 minutes early. Thank you - Ineza Pharmacy';
                    break;
                case 'stock':
                    message = 'New stock of essential medicines has arrived! Visit Ineza Pharmacy for quality medications. Limited time offer available.';
                    break;
                case 'promotion':
                    message = 'Special offer! Get 15% discount on all antibiotics this week. Visit Ineza Pharmacy for quality healthcare. Valid until Sunday.';
                    break;
                case 'notification':
                    message = 'In case of emergency, Ineza Pharmacy is available 24/7. Call us for immediate assistance. Your health is our priority.';
                    break;
            }
            
            $('#messageContent').val(message).trigger('input');
            $('#messageType').val(type === 'reminder' ? 'reminder' : type === 'stock' ? 'alert' : type === 'promotion' ? 'promotion' : 'notification');
        }

        // Handle form submission
        $('#smsForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                senderId: $('#senderId').val(),
                recipientPhone: $('#recipientPhone').val(),
                messageContent: $('#messageContent').val(),
                messageType: $('#messageType').val()
            };
            
            // Validate form
            if (!formData.recipientPhone || !formData.messageContent) {
                alert('Please fill in all required fields');
                return;
            }
            
            if (formData.messageContent.length > 160) {
                alert('Message is too long. Maximum 160 characters allowed.');
                return;
            }
            
            // Send SMS
            sendSms(formData);
        });

        // Send SMS function
        function sendSms(formData) {
            // Show loading state
            const submitBtn = $('#smsForm button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Sending...').prop('disabled', true);
            
            // Prepare phone number
            const phoneNumber = '+250' + formData.recipientPhone.replace(/\D/g, '');
            
            $.ajax({
                url: 'php_action/send_sms.php',
                type: 'POST',
                data: {
                    sender_id: formData.senderId,
                    phone: phoneNumber,
                    message: formData.messageContent,
                    type: formData.messageType
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert('SMS sent successfully!');
                        clearForm();
                        loadSmsHistory();
                        loadSmsStats();
                    } else {
                        alert('Failed to send SMS: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error sending SMS: ' + error);
                },
                complete: function() {
                    submitBtn.html(originalText).prop('disabled', false);
                }
            });
        }

        // Clear form
        function clearForm() {
            $('#smsForm')[0].reset();
            $('#messageContent').trigger('input');
            updateMessagePreview();
        }

        // Save template
        function saveTemplate() {
            const message = $('#messageContent').val();
            if (!message) {
                alert('Please enter a message to save as template');
                return;
            }
            
            const templateName = prompt('Enter template name:');
            if (templateName) {
                // This would typically save to database
                alert('Template saved successfully!');
            }
        }

        // View SMS details
        function viewSmsDetails(date) {
            alert('Viewing SMS details for: ' + date);
        }

        // Resend SMS
        function resendSms(phone, message) {
            if (confirm('Are you sure you want to resend this SMS?')) {
                $('#recipientPhone').val(phone.replace('+250 ', ''));
                $('#messageContent').val(message);
                $('#messageContent').trigger('input');
                updateMessagePreview();
                $('#smsForm').scrollIntoView({ behavior: 'smooth' });
            }
        }
    </script>
</body>
</html>


