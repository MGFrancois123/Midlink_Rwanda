<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'constant/connect.php';
require_once 'constant/check.php';

// Ensure this is Ineza pharmacy user
if (!isset($_SESSION['pharmacy_id']) || $_SESSION['pharmacy_id'] != 7) {
    header("Location: login.php");
    exit();
}

// Get user info
$username = $_SESSION['username'] ?? 'User';
$pharmacyName = $_SESSION['pharmacy_name'] ?? 'Ineza Pharmacy';

// Get filter parameters
$filter_days = $_GET['days'] ?? '30';
$filter_status = $_GET['status'] ?? 'all';
$search_term = $_GET['search'] ?? '';

// Build query based on filters
$where_conditions = ["m.pharmacy_id = 7"];
$params = [];
$param_types = '';

if ($filter_days !== 'all') {
    $where_conditions[] = "m.expiry_date <= DATE_ADD(CURDATE(), INTERVAL ? DAY)";
    $params[] = $filter_days;
    $param_types .= 'i';
}

if ($filter_status === 'expired') {
    $where_conditions[] = "m.expiry_date < CURDATE()";
} elseif ($filter_status === 'expiring_soon') {
    $where_conditions[] = "m.expiry_date >= CURDATE() AND m.expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)";
} elseif ($filter_status === 'expiring_later') {
    $where_conditions[] = "m.expiry_date > DATE_ADD(CURDATE(), INTERVAL 30 DAY)";
}

if (!empty($search_term)) {
    $where_conditions[] = "m.name LIKE ?";
    $params[] = "%$search_term%";
    $param_types .= 's';
}

$where_clause = implode(' AND ', $where_conditions);

$query = "SELECT m.medicine_id, m.name, m.stock_quantity, m.price, m.expiry_date, 
                 c.name as category, m.`Restricted Medicine`,
                 DATEDIFF(m.expiry_date, CURDATE()) as days_until_expiry
          FROM medicines m 
          LEFT JOIN categories c ON m.category_id = c.category_id 
          WHERE $where_clause 
          ORDER BY m.expiry_date ASC";

$stmt = $connect->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($param_types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Get statistics
$stats_query = "SELECT 
    COUNT(*) as total_medicines,
    SUM(CASE WHEN expiry_date < CURDATE() THEN 1 ELSE 0 END) as expired,
    SUM(CASE WHEN expiry_date >= CURDATE() AND expiry_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as expiring_7_days,
    SUM(CASE WHEN expiry_date >= CURDATE() AND expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as expiring_30_days,
    SUM(CASE WHEN expiry_date >= CURDATE() AND expiry_date <= DATE_ADD(CURDATE(), INTERVAL 90 DAY) THEN 1 ELSE 0 END) as expiring_90_days,
    SUM(CASE WHEN expiry_date < CURDATE() THEN stock_quantity * price ELSE 0 END) as expired_value
FROM medicines WHERE pharmacy_id = 7";

$stats_result = $connect->query($stats_query);
$stats = $stats_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expiry Alerts - Ineza Pharmacy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2e7d32;
            --secondary-color: #4caf50;
            --accent-color: #81c784;
            --success-color: #388e3c;
            --warning-color: #f57c00;
            --danger-color: #d32f2f;
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

        /* Header */
        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 2rem 0;
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
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

        /* Cards */
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

        /* Stats Cards */
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

        .stat-icon.danger {
            background: linear-gradient(135deg, var(--danger-color), #f44336);
        }

        .stat-icon.warning {
            background: linear-gradient(135deg, var(--warning-color), #ff9800);
        }

        .stat-icon.success {
            background: linear-gradient(135deg, var(--success-color), var(--secondary-color));
        }

        .stat-icon.info {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0.5rem 0;
        }

        .stat-value.danger {
            color: var(--danger-color);
        }

        .stat-value.warning {
            color: var(--warning-color);
        }

        .stat-value.success {
            color: var(--success-color);
        }

        .stat-value.info {
            color: var(--primary-color);
        }

        .stat-label {
            font-size: 1rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        /* Filters */
        .filters-section {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow);
        }

        .filter-group {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
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

        /* Table */
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

        /* Badges */
        .badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 500;
        }

        .badge-danger {
            background: var(--danger-color);
            color: white;
        }

        .badge-warning {
            background: var(--warning-color);
            color: white;
        }

        .badge-success {
            background: var(--success-color);
            color: white;
        }

        .badge-info {
            background: var(--primary-color);
            color: white;
        }

        /* Alert levels */
        .alert-level {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 0.5rem;
        }

        .alert-level.critical {
            background: var(--danger-color);
        }

        .alert-level.high {
            background: var(--warning-color);
        }

        .alert-level.medium {
            background: #ffc107;
        }

        .alert-level.low {
            background: var(--success-color);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .filter-group {
                flex-direction: column;
                align-items: stretch;
            }
            
            .stat-value {
                font-size: 2rem;
            }
        }

        /* Animations */
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
    <header class="page-header">
        <div class="container-fluid">
            <div class="row align-items-center header-content">
                <div class="col-md-8">
                    <h1 class="mb-2">
                        <i class="fas fa-clock me-3"></i>Expiry Alerts Management
                    </h1>
                    <p class="mb-0">Monitor and manage medicine expiry dates for Ineza Pharmacy</p>
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
    </header>

    <!-- Main Content -->
    <div class="container-fluid py-4">
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card animate-fade-in">
                <div class="stat-header">
                    <div>
                        <div class="stat-value danger"><?php echo number_format($stats['expired']); ?></div>
                        <div class="stat-label">Expired Medicines</div>
                    </div>
                    <div class="stat-icon danger">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
                <small class="text-muted">Requires immediate attention</small>
            </div>

            <div class="stat-card animate-fade-in">
                <div class="stat-header">
                    <div>
                        <div class="stat-value warning"><?php echo number_format($stats['expiring_7_days']); ?></div>
                        <div class="stat-label">Expiring in 7 Days</div>
                    </div>
                    <div class="stat-icon warning">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
                <small class="text-muted">Critical priority</small>
            </div>

            <div class="stat-card animate-fade-in">
                <div class="stat-header">
                    <div>
                        <div class="stat-value warning"><?php echo number_format($stats['expiring_30_days']); ?></div>
                        <div class="stat-label">Expiring in 30 Days</div>
                    </div>
                    <div class="stat-icon warning">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <small class="text-muted">High priority</small>
            </div>

            <div class="stat-card animate-fade-in">
                <div class="stat-header">
                    <div>
                        <div class="stat-value info"><?php echo number_format($stats['expiring_90_days']); ?></div>
                        <div class="stat-label">Expiring in 90 Days</div>
                    </div>
                    <div class="stat-icon info">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
                <small class="text-muted">Monitor closely</small>
            </div>

            <div class="stat-card animate-fade-in">
                <div class="stat-header">
                    <div>
                        <div class="stat-value danger">Rwf <?php echo number_format($stats['expired_value']); ?></div>
                        <div class="stat-label">Expired Stock Value</div>
                    </div>
                    <div class="stat-icon danger">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
                <small class="text-muted">Potential loss</small>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-section">
            <h5 class="mb-3"><i class="fas fa-filter me-2"></i>Filter Alerts</h5>
            <form method="GET" class="filter-group">
                <div class="form-group">
                    <label for="days" class="form-label">Time Range</label>
                    <select name="days" id="days" class="form-select">
                        <option value="7" <?php echo $filter_days == '7' ? 'selected' : ''; ?>>7 Days</option>
                        <option value="30" <?php echo $filter_days == '30' ? 'selected' : ''; ?>>30 Days</option>
                        <option value="90" <?php echo $filter_days == '90' ? 'selected' : ''; ?>>90 Days</option>
                        <option value="all" <?php echo $filter_days == 'all' ? 'selected' : ''; ?>>All</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="all" <?php echo $filter_status == 'all' ? 'selected' : ''; ?>>All Status</option>
                        <option value="expired" <?php echo $filter_status == 'expired' ? 'selected' : ''; ?>>Expired</option>
                        <option value="expiring_soon" <?php echo $filter_status == 'expiring_soon' ? 'selected' : ''; ?>>Expiring Soon (30 days)</option>
                        <option value="expiring_later" <?php echo $filter_status == 'expiring_later' ? 'selected' : ''; ?>>Expiring Later</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="search" class="form-label">Search Medicine</label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Enter medicine name..." value="<?php echo htmlspecialchars($search_term); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i>Filter
                        </button>
                        <a href="expiry_alerts_new.php" class="btn btn-outline-secondary">
                            <i class="fas fa-refresh me-1"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Alerts Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Expiry Alerts
                    <span class="badge bg-light text-dark ms-2"><?php echo $result->num_rows; ?> items</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="expiryTable">
                        <thead>
                            <tr>
                                <th>Alert Level</th>
                                <th>Medicine Name</th>
                                <th>Category</th>
                                <th>Stock Quantity</th>
                                <th>Price (Rwf)</th>
                                <th>Expiry Date</th>
                                <th>Days Until Expiry</th>
                                <th>Stock Value</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $days_left = $row['days_until_expiry'];
                                    $stock_value = $row['stock_quantity'] * $row['price'];
                                    
                                    // Determine alert level
                                    if ($days_left < 0) {
                                        $alert_level = 'critical';
                                        $alert_text = 'Expired';
                                        $alert_class = 'badge-danger';
                                    } elseif ($days_left <= 7) {
                                        $alert_level = 'high';
                                        $alert_text = 'Critical';
                                        $alert_class = 'badge-danger';
                                    } elseif ($days_left <= 30) {
                                        $alert_level = 'medium';
                                        $alert_text = 'High';
                                        $alert_class = 'badge-warning';
                                    } else {
                                        $alert_level = 'low';
                                        $alert_text = 'Low';
                                        $alert_class = 'badge-success';
                                    }
                                    ?>
                                    <tr>
                                        <td>
                                            <span class="alert-level <?php echo $alert_level; ?>"></span>
                                            <span class="badge <?php echo $alert_class; ?>"><?php echo $alert_text; ?></span>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($row['name']); ?></strong>
                                            <?php if ($row['Restricted Medicine']): ?>
                                                <span class="badge badge-info ms-1">Restricted</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['category']); ?></td>
                                        <td><?php echo number_format($row['stock_quantity']); ?></td>
                                        <td>Rwf <?php echo number_format($row['price']); ?></td>
                                        <td>
                                            <strong><?php echo date('M d, Y', strtotime($row['expiry_date'])); ?></strong>
                                        </td>
                                        <td>
                                            <?php if ($days_left < 0): ?>
                                                <span class="text-danger fw-bold">Expired <?php echo abs($days_left); ?> days ago</span>
                                            <?php else: ?>
                                                <span class="fw-bold"><?php echo $days_left; ?> days</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>Rwf <?php echo number_format($stock_value); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                                        onclick="viewMedicine(<?php echo $row['medicine_id']; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-warning" 
                                                        onclick="extendExpiry(<?php echo $row['medicine_id']; ?>)">
                                                    <i class="fas fa-calendar-plus"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        onclick="disposeMedicine(<?php echo $row['medicine_id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo '<tr><td colspan="9" class="text-center text-muted py-4">No expiry alerts found for the selected criteria.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="mb-3">Quick Actions</h5>
                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            <button class="btn btn-primary" onclick="generateExpiryReport()">
                                <i class="fas fa-file-pdf me-2"></i>Generate Report
                            </button>
                            <button class="btn btn-warning" onclick="sendExpiryNotifications()">
                                <i class="fas fa-bell me-2"></i>Send Notifications
                            </button>
                            <button class="btn btn-info" onclick="exportToExcel()">
                                <i class="fas fa-file-excel me-2"></i>Export to Excel
                            </button>
                            <button class="btn btn-success" onclick="scheduleDisposal()">
                                <i class="fas fa-calendar-check me-2"></i>Schedule Disposal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#expiryTable').DataTable({
                "order": [[6, "asc"]], // Sort by days until expiry
                "pageLength": 10,
                "language": {
                    "search": "Search medicines:",
                    "lengthMenu": "Show _MENU_ medicines per page",
                    "info": "Showing _START_ to _END_ of _TOTAL_ medicines"
                },
                "columnDefs": [
                    { "orderable": false, "targets": [0, 8] } // Disable sorting for alert level and actions
                ]
            });

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
        });

        // Action functions
        function viewMedicine(medicineId) {
            alert('View medicine details for ID: ' + medicineId);
            // Implement view functionality
        }

        function extendExpiry(medicineId) {
            if (confirm('Do you want to extend the expiry date for this medicine?')) {
                alert('Extend expiry functionality for ID: ' + medicineId);
                // Implement extend expiry functionality
            }
        }

        function disposeMedicine(medicineId) {
            if (confirm('Are you sure you want to dispose of this expired medicine?')) {
                alert('Dispose medicine functionality for ID: ' + medicineId);
                // Implement disposal functionality
            }
        }

        function generateExpiryReport() {
            alert('Generating expiry report...');
            // Implement report generation
        }

        function sendExpiryNotifications() {
            alert('Sending expiry notifications...');
            // Implement notification system
        }

        function exportToExcel() {
            alert('Exporting to Excel...');
            // Implement Excel export
        }

        function scheduleDisposal() {
            alert('Opening disposal scheduler...');
            // Implement disposal scheduling
        }
    </script>
</body>
</html>


