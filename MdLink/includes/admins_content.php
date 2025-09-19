<?php
require_once __DIR__ . '/../constant/connect.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
}

// Get pharmacy context
$pharmacyId = $_SESSION['pharmacy_id'] ?? 8; // Default to Ineza Pharmacy
$pharmacyName = $_SESSION['pharmacy_name'] ?? 'Ineza Pharmacy';

// Force set session for testing
if (!isset($_SESSION['pharmacy_id'])) {
    $_SESSION['pharmacy_id'] = 8;
    $_SESSION['pharmacy_name'] = 'Ineza Pharmacy';
}

// Sample data for demonstration (replace with actual database queries)
$admins = [
    [
        'id' => 1,
        'username' => 'superadmin',
        'email' => 'superadmin@mdlink.rw',
        'role' => 'super_admin',
        'role_display' => 'Super Administrator',
        'pharmacy_name' => 'System Wide',
        'status' => 'active',
        'last_login' => '2024-12-15 14:30:00',
        'created_date' => '2024-01-15',
        'permissions' => 'Full System Access',
        'avatar' => 'SA'
    ],
    [
        'id' => 2,
        'username' => 'ineza_manager',
        'email' => 'manager@ineza.rw',
        'role' => 'pharmacy_admin',
        'role_display' => 'Pharmacy Manager',
        'pharmacy_name' => 'Ineza Pharmacy',
        'status' => 'active',
        'last_login' => '2024-12-15 16:45:00',
        'created_date' => '2024-02-01',
        'permissions' => 'Pharmacy Management',
        'avatar' => 'IM'
    ],
    [
        'id' => 3,
        'username' => 'ineza_finance',
        'email' => 'finance@ineza.rw',
        'role' => 'finance_admin',
        'role_display' => 'Finance Manager',
        'pharmacy_name' => 'Ineza Pharmacy',
        'status' => 'active',
        'last_login' => '2024-12-15 12:15:00',
        'created_date' => '2024-02-01',
        'permissions' => 'Financial Operations',
        'avatar' => 'IF'
    ],
    [
        'id' => 4,
        'username' => 'keza_manager',
        'email' => 'manager@keza.rw',
        'role' => 'pharmacy_admin',
        'role_display' => 'Pharmacy Manager',
        'pharmacy_name' => 'Keza Pharmacy',
        'status' => 'inactive',
        'last_login' => '2024-11-20 09:30:00',
        'created_date' => '2024-01-20',
        'permissions' => 'Pharmacy Management',
        'avatar' => 'KM'
    ],
    [
        'id' => 5,
        'username' => 'keza_finance',
        'email' => 'finance@keza.rw',
        'role' => 'finance_admin',
        'role_display' => 'Finance Manager',
        'pharmacy_name' => 'Keza Pharmacy',
        'status' => 'inactive',
        'last_login' => '2024-11-18 15:20:00',
        'created_date' => '2024-01-20',
        'permissions' => 'Financial Operations',
        'avatar' => 'KF'
    ]
];

// Calculate statistics
$totalAdmins = count($admins);
$activeAdmins = count(array_filter($admins, fn($admin) => $admin['status'] === 'active'));
$inactiveAdmins = count(array_filter($admins, fn($admin) => $admin['status'] === 'inactive'));
$superAdmins = count(array_filter($admins, fn($admin) => $admin['role'] === 'super_admin'));
$pharmacyAdmins = count(array_filter($admins, fn($admin) => $admin['role'] === 'pharmacy_admin'));
$financeAdmins = count(array_filter($admins, fn($admin) => $admin['role'] === 'finance_admin'));

// Role breakdown for charts
$roleBreakdown = [
    'Super Admin' => $superAdmins,
    'Pharmacy Admin' => $pharmacyAdmins,
    'Finance Admin' => $financeAdmins
];

$chartLabels = array_keys($roleBreakdown);
$chartValues = array_values($roleBreakdown);
?>

<!-- Hero Section -->
<div class="hero-section mb-4">
    <div class="hero-content">
        <div class="hero-text">
            <h2><i class="fas fa-user-shield me-3"></i>System Administrators</h2>
            <p>Manage pharmacy and finance administrators across all branches</p>
        </div>
        <div class="hero-actions">
            <button class="btn btn-primary btn-lg" onclick="openAddAdminModal()">
                <i class="fas fa-plus me-2"></i>Add New Admin
            </button>
            <button class="btn btn-outline-secondary btn-lg" onclick="exportAdminList()">
                <i class="fas fa-download me-2"></i>Export List
            </button>
        </div>
    </div>
</div>

<!-- Statistics Dashboard -->
<div class="stats-dashboard mb-4">
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card stat-card-primary">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $totalAdmins; ?></h3>
                    <p>Total Administrators</p>
                    <small class="text-primary">System-wide access</small>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card stat-card-success">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $activeAdmins; ?></h3>
                    <p>Active Users</p>
                    <small class="text-success">Currently online</small>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card stat-card-warning">
                <div class="stat-icon">
                    <i class="fas fa-user-md"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $pharmacyAdmins; ?></h3>
                    <p>Pharmacy Managers</p>
                    <small class="text-warning">Branch operations</small>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card stat-card-info">
                <div class="stat-icon">
                    <i class="fas fa-calculator"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $financeAdmins; ?></h3>
                    <p>Finance Managers</p>
                    <small class="text-info">Financial control</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="charts-section mb-4">
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="chart-card">
                <div class="chart-header">
                    <h5><i class="fas fa-chart-pie me-2"></i>Administrator Roles Distribution</h5>
                    <div class="chart-actions">
                        <button class="btn btn-sm btn-outline-primary" onclick="downloadChart('roles')">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                </div>
                <div class="chart-body">
                    <canvas id="rolesChart" height="300"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="chart-card">
                <div class="chart-header">
                    <h5><i class="fas fa-chart-bar me-2"></i>Admin Status Overview</h5>
                    <div class="chart-actions">
                        <button class="btn btn-sm btn-outline-primary" onclick="downloadChart('status')">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                </div>
                <div class="chart-body">
                    <canvas id="statusChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions mb-4">
    <div class="row">
        <div class="col-12">
            <div class="actions-panel">
                <h5><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                <div class="actions-grid">
                    <button class="action-btn action-primary" onclick="openAddAdminModal()">
                        <i class="fas fa-user-plus"></i>
                        <span>Add Admin</span>
                    </button>
                    <button class="action-btn action-success" onclick="bulkActivate()">
                        <i class="fas fa-check-circle"></i>
                        <span>Bulk Activate</span>
                    </button>
                    <button class="action-btn action-warning" onclick="bulkDeactivate()">
                        <i class="fas fa-pause-circle"></i>
                        <span>Bulk Deactivate</span>
                    </button>
                    <button class="action-btn action-info" onclick="generateReport()">
                        <i class="fas fa-file-alt"></i>
                        <span>Generate Report</span>
                    </button>
                    <button class="action-btn action-secondary" onclick="exportAdminList()">
                        <i class="fas fa-download"></i>
                        <span>Export Data</span>
                    </button>
                    <button class="action-btn action-danger" onclick="bulkDelete()">
                        <i class="fas fa-trash"></i>
                        <span>Bulk Delete</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Admins Table -->
<div class="data-section mb-4">
    <div class="data-header">
        <div class="header-content">
            <h4><i class="fas fa-users me-2"></i>Administrator Management</h4>
            <p>Complete list of system administrators and their permissions</p>
        </div>
        <div class="header-actions">
            <div class="search-box">
                <input type="text" id="searchAdmins" placeholder="Search administrators..." class="form-control">
            </div>
            <div class="filter-dropdown">
                <select id="roleFilter" class="form-control">
                    <option value="">All Roles</option>
                    <option value="super_admin">Super Admin</option>
                    <option value="pharmacy_admin">Pharmacy Admin</option>
                    <option value="finance_admin">Finance Admin</option>
                </select>
            </div>
            <div class="filter-dropdown">
                <select id="statusFilter" class="form-control">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <button class="btn btn-outline-primary" onclick="selectAll()">
                <i class="fas fa-check-square me-2"></i>Select All
            </button>
        </div>
    </div>
    
    <div class="data-table">
        <table class="table table-hover" id="adminsTable">
            <thead>
                <tr>
                    <th width="30">
                        <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()">
                    </th>
                    <th>Administrator</th>
                    <th>Role & Permissions</th>
                    <th>Pharmacy Assignment</th>
                    <th>Status & Activity</th>
                    <th>Contact Information</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($admins as $admin): ?>
                <tr class="admin-row" data-id="<?php echo $admin['id']; ?>" data-role="<?php echo $admin['role']; ?>" data-status="<?php echo $admin['status']; ?>">
                    <td>
                        <input type="checkbox" class="admin-checkbox" value="<?php echo $admin['id']; ?>">
                    </td>
                    <td>
                        <div class="admin-profile">
                            <div class="admin-avatar <?php echo $admin['role']; ?>">
                                <?php echo $admin['avatar']; ?>
                            </div>
                            <div class="admin-details">
                                <div class="admin-name"><?php echo htmlspecialchars($admin['username']); ?></div>
                                <div class="admin-email"><?php echo htmlspecialchars($admin['email']); ?></div>
                                <div class="admin-id">ID: <?php echo $admin['id']; ?></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="role-info">
                            <span class="role-badge <?php echo $admin['role']; ?>"><?php echo htmlspecialchars($admin['role_display']); ?></span>
                            <div class="permissions"><?php echo htmlspecialchars($admin['permissions']); ?></div>
                        </div>
                    </td>
                    <td>
                        <div class="pharmacy-assignment">
                            <div class="pharmacy-name"><?php echo htmlspecialchars($admin['pharmacy_name']); ?></div>
                            <div class="assignment-date">Since <?php echo date('M d, Y', strtotime($admin['created_date'])); ?></div>
                        </div>
                    </td>
                    <td>
                        <div class="status-activity">
                            <span class="status-badge <?php echo $admin['status']; ?>">
                                <?php echo ucfirst($admin['status']); ?>
                            </span>
                            <div class="last-login">
                                Last: <?php echo date('M d, H:i', strtotime($admin['last_login'])); ?>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="contact-info">
                            <div class="email"><?php echo htmlspecialchars($admin['email']); ?></div>
                            <div class="username">@<?php echo htmlspecialchars($admin['username']); ?></div>
                        </div>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-sm btn-outline-primary" onclick="editAdmin(<?php echo $admin['id']; ?>)" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <?php if ($admin['status'] === 'active'): ?>
                            <button class="btn btn-sm btn-outline-warning" onclick="deactivateAdmin(<?php echo $admin['id']; ?>)" title="Deactivate">
                                <i class="fas fa-pause"></i>
                            </button>
                            <?php else: ?>
                            <button class="btn btn-sm btn-outline-success" onclick="activateAdmin(<?php echo $admin['id']; ?>)" title="Activate">
                                <i class="fas fa-play"></i>
                            </button>
                            <?php endif; ?>
                            <button class="btn btn-sm btn-outline-info" onclick="viewAdminDetails(<?php echo $admin['id']; ?>)" title="Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <?php if ($admin['role'] !== 'super_admin'): ?>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteAdmin(<?php echo $admin['id']; ?>)" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Admin Modal -->
<div class="modal fade" id="adminModal" tabindex="-1" aria-labelledby="adminModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adminModalLabel">
                    <i class="fas fa-user-plus me-2"></i>Add New Administrator
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="adminForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-control" id="role" name="role" required>
                                <option value="">Select Role</option>
                                <option value="pharmacy_admin">Pharmacy Administrator</option>
                                <option value="finance_admin">Finance Administrator</option>
                                <option value="super_admin">Super Administrator</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pharmacy Assignment</label>
                            <select class="form-control" id="pharmacy_id" name="pharmacy_id">
                                <option value="">Select Pharmacy</option>
                                <option value="8">Ineza Pharmacy</option>
                                <option value="1">Keza Pharmacy</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Permissions</label>
                            <div class="permissions-grid">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="perm_medicines" name="permissions[]" value="medicines">
                                    <label class="form-check-label" for="perm_medicines">Manage Medicines</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="perm_inventory" name="permissions[]" value="inventory">
                                    <label class="form-check-label" for="perm_inventory">Manage Inventory</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="perm_finance" name="permissions[]" value="finance">
                                    <label class="form-check-label" for="perm_finance">Financial Operations</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="perm_reports" name="permissions[]" value="reports">
                                    <label class="form-check-label" for="perm_reports">Generate Reports</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="perm_users" name="permissions[]" value="users">
                                    <label class="form-check-label" for="perm_users">Manage Users</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="perm_system" name="permissions[]" value="system">
                                    <label class="form-check-label" for="perm_system">System Settings</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveAdmin()">Save Administrator</button>
            </div>
        </div>
    </div>
</div>

<!-- Admin Details Modal -->
<div class="modal fade" id="adminDetailsModal" tabindex="-1" aria-labelledby="adminDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adminDetailsModalLabel">
                    <i class="fas fa-user me-2"></i>Administrator Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="adminDetailsContent">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize charts when document is ready
$(document).ready(function() {
    initializeCharts();
    initializeSearch();
    initializeFilters();
});

// Initialize charts
function initializeCharts() {
    // Roles Chart
    const rolesCtx = document.getElementById('rolesChart').getContext('2d');
    new Chart(rolesCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($chartLabels); ?>,
            datasets: [{
                data: <?php echo json_encode($chartValues); ?>,
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', 
                    '#e74a3b', '#6f42c1', '#fd7e14', '#20c9a6'
                ],
                borderWidth: 3,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: { size: 12 }
                    }
                }
            }
        }
    });
    
    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'bar',
        data: {
            labels: ['Active', 'Inactive'],
            datasets: [{
                label: 'Administrators',
                data: [<?php echo $activeAdmins; ?>, <?php echo $inactiveAdmins; ?>],
                backgroundColor: ['#1cc88a', '#e74a3b'],
                borderColor: ['#17a673', '#c0392b'],
                borderWidth: 2,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            },
            plugins: { legend: { display: false } }
        }
    });
}

// Initialize search functionality
function initializeSearch() {
    $('#searchAdmins').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('.admin-row').each(function() {
            const rowText = $(this).text().toLowerCase();
            $(this).toggle(rowText.includes(searchTerm));
        });
    });
}

// Initialize filters
function initializeFilters() {
    $('#roleFilter, #statusFilter').on('change', function() {
        const roleFilter = $('#roleFilter').val();
        const statusFilter = $('#statusFilter').val();
        
        $('.admin-row').each(function() {
            const row = $(this);
            const role = row.data('role');
            const status = row.data('status');
            
            const roleMatch = !roleFilter || role === roleFilter;
            const statusMatch = !statusFilter || status === statusFilter;
            
            row.toggle(roleMatch && statusMatch);
        });
    });
}

// Modal functions
function openAddAdminModal() {
    $('#adminModalLabel').html('<i class="fas fa-user-plus me-2"></i>Add New Administrator');
    $('#adminForm')[0].reset();
    $('#adminModal').modal('show');
}

function editAdmin(id) {
    $('#adminModalLabel').html('<i class="fas fa-user-edit me-2"></i>Edit Administrator');
    // Load admin data and populate form
    $('#adminModal').modal('show');
}

function viewAdminDetails(id) {
    // Load admin details and show in modal
    $('#adminDetailsContent').html(`
        <div class="admin-details-view">
            <div class="row">
                <div class="col-md-6">
                    <h6>Basic Information</h6>
                    <p><strong>Username:</strong> admin_${id}</p>
                    <p><strong>Email:</strong> admin${id}@example.com</p>
                    <p><strong>Role:</strong> Administrator</p>
                    <p><strong>Status:</strong> Active</p>
                </div>
                <div class="col-md-6">
                    <h6>Activity Information</h6>
                    <p><strong>Last Login:</strong> ${new Date().toLocaleString()}</p>
                    <p><strong>Created:</strong> ${new Date().toLocaleDateString()}</p>
                    <p><strong>Login Count:</strong> 15</p>
                </div>
            </div>
        </div>
    `);
    $('#adminDetailsModal').modal('show');
}

// Action functions
function saveAdmin() {
    // Validate form and save admin
    alert('Administrator saved successfully!');
    $('#adminModal').modal('hide');
}

function activateAdmin(id) {
    if (confirm('Are you sure you want to activate this administrator?')) {
        alert('Administrator activated successfully!');
    }
}

function deactivateAdmin(id) {
    if (confirm('Are you sure you want to deactivate this administrator?')) {
        alert('Administrator deactivated successfully!');
    }
}

function deleteAdmin(id) {
    if (confirm('Are you sure you want to delete this administrator? This action cannot be undone.')) {
        alert('Administrator deleted successfully!');
    }
}

function bulkActivate() {
    const selectedIds = $('.admin-checkbox:checked').map(function() {
        return $(this).val();
    }).get();
    
    if (selectedIds.length === 0) {
        alert('Please select administrators to activate');
        return;
    }
    
    if (confirm(`Are you sure you want to activate ${selectedIds.length} administrators?`)) {
        alert('Bulk activation initiated...');
    }
}

function bulkDeactivate() {
    const selectedIds = $('.admin-checkbox:checked').map(function() {
        return $(this).val();
    }).get();
    
    if (selectedIds.length === 0) {
        alert('Please select administrators to deactivate');
        return;
    }
    
    if (confirm(`Are you sure you want to deactivate ${selectedIds.length} administrators?`)) {
        alert('Bulk deactivation initiated...');
    }
}

function bulkDelete() {
    const selectedIds = $('.admin-checkbox:checked').map(function() {
        return $(this).val();
    }).get();
    
    if (selectedIds.length === 0) {
        alert('Please select administrators to delete');
        return;
    }
    
    if (confirm(`Are you sure you want to delete ${selectedIds.length} administrators? This action cannot be undone.`)) {
        alert('Bulk deletion initiated...');
    }
}

function generateReport() {
    alert('Generating administrator report...');
}

function exportAdminList() {
    alert('Exporting administrator list...');
}

function selectAll() {
    const isChecked = $('#selectAllCheckbox').prop('checked');
    $('.admin-checkbox').prop('checked', !isChecked);
    $('#selectAllCheckbox').prop('checked', !isChecked);
}

function toggleSelectAll() {
    const isChecked = $('#selectAllCheckbox').prop('checked');
    $('.admin-checkbox').prop('checked', isChecked);
}

function downloadChart(type) {
    alert(`Downloading ${type} chart...`);
}
</script>

<style>
/* Hero Section */
.hero-section {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    border-radius: 20px;
    padding: 2rem;
    color: white;
    box-shadow: 0 10px 30px rgba(78, 115, 223, 0.3);
}

.hero-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.hero-text h2 {
    margin: 0 0 0.5rem 0;
    font-weight: 700;
    font-size: 2.5rem;
}

.hero-text p {
    margin: 0;
    opacity: 0.9;
    font-size: 1.1rem;
}

.hero-actions {
    display: flex;
    gap: 1rem;
}

/* Statistics Dashboard */
.stat-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border: none;
    position: relative;
    overflow: hidden;
    height: 100%;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
}

.stat-card-primary::before { background: #4e73df; }
.stat-card-success::before { background: #1cc88a; }
.stat-card-warning::before { background: #f6c23e; }
.stat-card-info::before { background: #36b9cc; }

.stat-card {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    flex-shrink: 0;
}

.stat-card-primary .stat-icon { background: #4e73df; }
.stat-card-success .stat-icon { background: #1cc88a; }
.stat-card-warning .stat-icon { background: #f6c23e; }
.stat-card-info .stat-icon { background: #36b9cc; }

.stat-content h3 {
    font-size: 2rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
    color: #2c3e50;
}

.stat-content p {
    margin: 0 0 0.25rem 0;
    font-weight: 600;
    color: #495057;
}

.stat-content small {
    font-size: 0.8rem;
    font-weight: 500;
}

/* Charts Section */
.chart-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    overflow: hidden;
}

.chart-header {
    background: #f8f9fa;
    padding: 1.5rem;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chart-header h5 {
    color: #2c3e50;
    margin: 0;
    font-weight: 600;
}

.chart-body {
    padding: 1.5rem;
}

/* Quick Actions */
.actions-panel {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}

.actions-panel h5 {
    color: #2c3e50;
    margin-bottom: 1.5rem;
    font-weight: 600;
}

.actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
}

.action-btn {
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 1.5rem 1rem;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
    text-decoration: none;
    color: #495057;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
}

.action-btn:hover {
    transform: translateY(-3px);
    color: #495057;
    text-decoration: none;
}

.action-btn i {
    font-size: 1.5rem;
}

.action-btn span {
    font-weight: 500;
    font-size: 0.9rem;
}

.action-primary:hover { background: #d6eaf8; border-color: #4e73df; color: #1b4f72; }
.action-success:hover { background: #d4edda; border-color: #1cc88a; color: #155724; }
.action-warning:hover { background: #fff3cd; border-color: #f6c23e; color: #856404; }
.action-info:hover { background: #d1ecf1; border-color: #36b9cc; color: #0c5460; }
.action-secondary:hover { background: #e9ecef; border-color: #6c757d; color: #495057; }
.action-danger:hover { background: #f8d7da; border-color: #e74a3b; color: #721c24; }

/* Data Section */
.data-section {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    overflow: hidden;
}

.data-header {
    background: #f8f9fa;
    padding: 1.5rem;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header-content h4 {
    color: #2c3e50;
    margin: 0 0 0.5rem 0;
    font-weight: 600;
}

.header-content p {
    color: #6c757d;
    margin: 0;
}

.header-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.search-box {
    width: 250px;
}

.filter-dropdown {
    width: 150px;
}

.data-table {
    padding: 1.5rem;
}

/* Table Styles */
.table {
    margin: 0;
}

.table th {
    background: #f8f9fa;
    border: none;
    padding: 1rem;
    font-weight: 600;
    color: #2c3e50;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table td {
    padding: 1rem;
    vertical-align: middle;
    border: none;
    border-bottom: 1px solid #f1f3f4;
}

.admin-row:hover {
    background: #f8f9fa;
}

/* Admin Profile */
.admin-profile {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.admin-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: white;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.admin-avatar.super_admin { background: #6f42c1; }
.admin-avatar.pharmacy_admin { background: #1cc88a; }
.admin-avatar.finance_admin { background: #36b9cc; }

.admin-details {
    flex: 1;
}

.admin-name {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.25rem;
}

.admin-email {
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 0.25rem;
}

.admin-id {
    font-size: 0.8rem;
    color: #adb5bd;
}

/* Role Information */
.role-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    display: inline-block;
    margin-bottom: 0.5rem;
}

.role-badge.super_admin {
    background: #f3e5f5;
    color: #6f42c1;
}

.role-badge.pharmacy_admin {
    background: #e8f5e8;
    color: #1cc88a;
}

.role-badge.finance_admin {
    background: #e3f2fd;
    color: #36b9cc;
}

.permissions {
    font-size: 0.8rem;
    color: #6c757d;
}

/* Pharmacy Assignment */
.pharmacy-name {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.25rem;
}

.assignment-date {
    font-size: 0.8rem;
    color: #6c757d;
}

/* Status & Activity */
.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 500;
    display: inline-block;
    margin-bottom: 0.5rem;
}

.status-badge.active {
    background: #d4edda;
    color: #155724;
}

.status-badge.inactive {
    background: #f8d7da;
    color: #721c24;
}

.last-login {
    font-size: 0.8rem;
    color: #6c757d;
}

/* Contact Information */
.contact-info .email {
    font-weight: 500;
    color: #495057;
    margin-bottom: 0.25rem;
}

.contact-info .username {
    font-size: 0.8rem;
    color: #6c757d;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 0.25rem;
}

.action-buttons .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
}

/* Modal Styles */
.modal-content {
    border-radius: 15px;
    border: none;
    box-shadow: 0 20px 60px rgba(0,0,0,0.2);
}

.modal-header {
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    border-radius: 15px 15px 0 0;
}

.modal-title {
    color: #2c3e50;
    font-weight: 600;
}

.permissions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 0.5rem;
}

.form-check {
    margin: 0;
}

.form-check-label {
    font-weight: 500;
    color: #495057;
}

/* Responsive Design */
@media (max-width: 768px) {
    .hero-content {
        flex-direction: column;
        text-align: center;
        gap: 1.5rem;
    }
    
    .hero-text h2 {
        font-size: 2rem;
    }
    
    .hero-actions {
        flex-direction: column;
        width: 100%;
    }
    
    .actions-grid {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    }
    
    .header-actions {
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
    }
    
    .search-box, .filter-dropdown {
        width: 100%;
    }
    
    .stat-card {
        flex-direction: column;
        text-align: center;
    }
    
    .admin-profile {
        flex-direction: column;
        text-align: center;
    }
}
</style>
