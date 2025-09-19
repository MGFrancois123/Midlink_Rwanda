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
$expiredMedicines = [
    [
        'id' => 1,
        'name' => 'Paracetamol 500mg',
        'category' => 'Pain Relief',
        'stock' => 150,
        'price' => 250,
        'expiry_date' => '2024-12-15',
        'days_expired' => 45,
        'batch' => 'BATCH-001-2024',
        'supplier' => 'MediPharm Ltd',
        'restricted' => false,
        'value' => 37500
    ],
    [
        'id' => 2,
        'name' => 'Amoxicillin 500mg',
        'category' => 'Antibiotics',
        'stock' => 80,
        'price' => 1200,
        'expiry_date' => '2024-11-20',
        'days_expired' => 70,
        'batch' => 'BATCH-002-2024',
        'supplier' => 'PharmaCare',
        'restricted' => true,
        'value' => 96000
    ],
    [
        'id' => 3,
        'name' => 'Omeprazole 20mg',
        'category' => 'Gastrointestinal',
        'stock' => 60,
        'price' => 800,
        'expiry_date' => '2024-10-30',
        'days_expired' => 91,
        'batch' => 'BATCH-003-2024',
        'supplier' => 'HealthMed',
        'restricted' => false,
        'value' => 48000
    ],
    [
        'id' => 4,
        'name' => 'Metformin 500mg',
        'category' => 'Diabetes',
        'stock' => 120,
        'price' => 450,
        'expiry_date' => '2024-12-01',
        'days_expired' => 59,
        'batch' => 'BATCH-004-2024',
        'supplier' => 'DiabeCare',
        'restricted' => true,
        'value' => 54000
    ],
    [
        'id' => 5,
        'name' => 'Salbutamol Inhaler',
        'category' => 'Respiratory',
        'stock' => 45,
        'price' => 1800,
        'expiry_date' => '2024-11-15',
        'days_expired' => 75,
        'batch' => 'BATCH-005-2024',
        'supplier' => 'RespirTech',
        'restricted' => true,
        'value' => 81000
    ]
];

$expiringSoon = [
    [
        'id' => 6,
        'name' => 'Ibuprofen 400mg',
        'category' => 'Pain Relief',
        'stock' => 200,
        'price' => 300,
        'expiry_date' => '2025-01-15',
        'days_left' => 15,
        'batch' => 'BATCH-006-2024',
        'supplier' => 'MediPharm Ltd',
        'restricted' => false,
        'value' => 60000
    ],
    [
        'id' => 7,
        'name' => 'Cetirizine 10mg',
        'category' => 'Allergy',
        'stock' => 90,
        'price' => 180,
        'expiry_date' => '2025-01-20',
        'days_left' => 20,
        'batch' => 'BATCH-007-2024',
        'supplier' => 'AllerCare',
        'restricted' => false,
        'value' => 16200
    ],
    [
        'id' => 8,
        'name' => 'Vitamin D3 1000IU',
        'category' => 'Vitamins',
        'stock' => 150,
        'price' => 120,
        'expiry_date' => '2025-01-25',
        'days_left' => 25,
        'batch' => 'BATCH-008-2024',
        'supplier' => 'VitaHealth',
        'restricted' => false,
        'value' => 18000
    ]
];

// Calculate statistics
$totalExpired = count($expiredMedicines);
$totalExpiringSoon = count($expiringSoon);
$totalValue = array_sum(array_column($expiredMedicines, 'value'));
$totalExpiringValue = array_sum(array_column($expiringSoon, 'value'));

// Category breakdown for charts
$categoryBreakdown = [];
foreach ($expiredMedicines as $medicine) {
    $category = $medicine['category'];
    $categoryBreakdown[$category] = ($categoryBreakdown[$category] ?? 0) + 1;
}

$chartLabels = array_keys($categoryBreakdown);
$chartValues = array_values($categoryBreakdown);
?>

<!-- Hero Section with Alert Banner -->
<div class="alert-banner mb-4">
    <div class="alert-content">
        <div class="alert-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="alert-text">
            <h4>⚠️ Expired Medicines Alert</h4>
            <p>You have <strong><?php echo $totalExpired; ?> expired medicines</strong> and <strong><?php echo $totalExpiringSoon; ?> expiring soon</strong> that require immediate attention.</p>
        </div>
        <div class="alert-actions">
            <button class="btn btn-danger" onclick="generateDisposalReport()">
                <i class="fas fa-file-alt me-2"></i>Generate Disposal Report
            </button>
        </div>
    </div>
</div>

<!-- Statistics Dashboard -->
<div class="stats-dashboard mb-4">
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card stat-card-danger">
                <div class="stat-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $totalExpired; ?></h3>
                    <p>Expired Medicines</p>
                    <small class="text-danger">Requires immediate action</small>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card stat-card-warning">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $totalExpiringSoon; ?></h3>
                    <p>Expiring Soon</p>
                    <small class="text-warning">Within 30 days</small>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card stat-card-info">
                <div class="stat-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-content">
                    <h3>Rwf <?php echo number_format($totalValue, 0); ?></h3>
                    <p>Expired Value</p>
                    <small class="text-info">Financial impact</small>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card stat-card-primary">
                <div class="stat-icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo count($chartLabels); ?></h3>
                    <p>Categories Affected</p>
                    <small class="text-primary">Medicine types</small>
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
                    <h5><i class="fas fa-chart-pie me-2"></i>Expired Medicines by Category</h5>
                    <div class="chart-actions">
                        <button class="btn btn-sm btn-outline-primary" onclick="downloadChart('category')">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                </div>
                <div class="chart-body">
                    <canvas id="categoryChart" height="300"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="chart-card">
                <div class="chart-header">
                    <h5><i class="fas fa-chart-bar me-2"></i>Financial Impact by Category</h5>
                    <div class="chart-actions">
                        <button class="btn btn-sm btn-outline-primary" onclick="downloadChart('financial')">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                </div>
                <div class="chart-body">
                    <canvas id="financialChart" height="300"></canvas>
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
                    <button class="action-btn action-danger" onclick="bulkDispose()">
                        <i class="fas fa-trash"></i>
                        <span>Bulk Disposal</span>
                    </button>
                    <button class="action-btn action-warning" onclick="returnToSuppliers()">
                        <i class="fas fa-undo"></i>
                        <span>Return to Suppliers</span>
                    </button>
                    <button class="action-btn action-info" onclick="generateReport()">
                        <i class="fas fa-file-alt"></i>
                        <span>Generate Report</span>
                    </button>
                    <button class="action-btn action-success" onclick="updateInventory()">
                        <i class="fas fa-sync-alt"></i>
                        <span>Update Inventory</span>
                    </button>
                    <button class="action-btn action-primary" onclick="exportData()">
                        <i class="fas fa-download"></i>
                        <span>Export Data</span>
                    </button>
                    <button class="action-btn action-secondary" onclick="sendAlerts()">
                        <i class="fas fa-bell"></i>
                        <span>Send Alerts</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Expired Medicines Table -->
<div class="data-section mb-4">
    <div class="data-header">
        <div class="header-content">
            <h4><i class="fas fa-times-circle me-2"></i>Expired Medicines Inventory</h4>
            <p>Complete list of expired medicines requiring disposal or return</p>
        </div>
        <div class="header-actions">
            <div class="search-box">
                <input type="text" id="searchExpired" placeholder="Search expired medicines..." class="form-control">
            </div>
            <button class="btn btn-outline-danger" onclick="selectAll()">
                <i class="fas fa-check-square me-2"></i>Select All
            </button>
        </div>
    </div>
    
    <div class="data-table">
        <table class="table table-hover" id="expiredTable">
            <thead>
                <tr>
                    <th width="30">
                        <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()">
                    </th>
                    <th>Medicine Details</th>
                    <th>Category</th>
                    <th>Stock & Price</th>
                    <th>Expiry Information</th>
                    <th>Financial Impact</th>
                    <th>Supplier</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($expiredMedicines as $medicine): ?>
                <tr class="expired-row" data-id="<?php echo $medicine['id']; ?>">
                    <td>
                        <input type="checkbox" class="medicine-checkbox" value="<?php echo $medicine['id']; ?>">
                    </td>
                    <td>
                        <div class="medicine-details">
                            <div class="medicine-name"><?php echo htmlspecialchars($medicine['name']); ?></div>
                            <div class="medicine-batch"><?php echo htmlspecialchars($medicine['batch']); ?></div>
                            <?php if ($medicine['restricted']): ?>
                            <span class="restricted-badge">Restricted</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <span class="category-tag"><?php echo htmlspecialchars($medicine['category']); ?></span>
                    </td>
                    <td>
                        <div class="stock-price">
                            <div class="stock-amount"><?php echo $medicine['stock']; ?> units</div>
                            <div class="unit-price">Rwf <?php echo number_format($medicine['price'], 0); ?> each</div>
                        </div>
                    </td>
                    <td>
                        <div class="expiry-info">
                            <div class="expiry-date"><?php echo date('M d, Y', strtotime($medicine['expiry_date'])); ?></div>
                            <div class="days-expired"><?php echo $medicine['days_expired']; ?> days expired</div>
                        </div>
                    </td>
                    <td>
                        <div class="financial-impact">
                            <div class="total-value">Rwf <?php echo number_format($medicine['value'], 0); ?></div>
                            <div class="impact-level high">High Impact</div>
                        </div>
                    </td>
                    <td>
                        <div class="supplier-info">
                            <div class="supplier-name"><?php echo htmlspecialchars($medicine['supplier']); ?></div>
                        </div>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-sm btn-outline-danger" onclick="disposeMedicine(<?php echo $medicine['id']; ?>)" title="Dispose">
                                <i class="fas fa-trash"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-warning" onclick="returnMedicine(<?php echo $medicine['id']; ?>)" title="Return">
                                <i class="fas fa-undo"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-info" onclick="viewDetails(<?php echo $medicine['id']; ?>)" title="Details">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Expiring Soon Section -->
<?php if (!empty($expiringSoon)): ?>
<div class="data-section mb-4">
    <div class="data-header">
        <div class="header-content">
            <h4><i class="fas fa-clock me-2"></i>Medicines Expiring Soon</h4>
            <p>Medicines that will expire within the next 30 days</p>
        </div>
        <div class="header-actions">
            <button class="btn btn-outline-warning" onclick="extendExpiry()">
                <i class="fas fa-calendar-plus me-2"></i>Extend Expiry
            </button>
        </div>
    </div>
    
    <div class="data-table">
        <table class="table table-hover" id="expiringTable">
            <thead>
                <tr>
                    <th>Medicine Details</th>
                    <th>Category</th>
                    <th>Stock & Price</th>
                    <th>Expiry Information</th>
                    <th>Financial Impact</th>
                    <th>Supplier</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($expiringSoon as $medicine): ?>
                <tr class="expiring-row">
                    <td>
                        <div class="medicine-details">
                            <div class="medicine-name"><?php echo htmlspecialchars($medicine['name']); ?></div>
                            <div class="medicine-batch"><?php echo htmlspecialchars($medicine['batch']); ?></div>
                            <?php if ($medicine['restricted']): ?>
                            <span class="restricted-badge">Restricted</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <span class="category-tag"><?php echo htmlspecialchars($medicine['category']); ?></span>
                    </td>
                    <td>
                        <div class="stock-price">
                            <div class="stock-amount"><?php echo $medicine['stock']; ?> units</div>
                            <div class="unit-price">Rwf <?php echo number_format($medicine['price'], 0); ?> each</div>
                        </div>
                    </td>
                    <td>
                        <div class="expiry-info">
                            <div class="expiry-date"><?php echo date('M d, Y', strtotime($medicine['expiry_date'])); ?></div>
                            <div class="days-left"><?php echo $medicine['days_left']; ?> days left</div>
                        </div>
                    </td>
                    <td>
                        <div class="financial-impact">
                            <div class="total-value">Rwf <?php echo number_format($medicine['value'], 0); ?></div>
                            <div class="impact-level medium">Medium Impact</div>
                        </div>
                    </td>
                    <td>
                        <div class="supplier-info">
                            <div class="supplier-name"><?php echo htmlspecialchars($medicine['supplier']); ?></div>
                        </div>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-sm btn-outline-success" onclick="extendExpiry(<?php echo $medicine['id']; ?>)" title="Extend">
                                <i class="fas fa-calendar-plus"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-primary" onclick="updateStock(<?php echo $medicine['id']; ?>)" title="Update Stock">
                                <i class="fas fa-boxes"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-info" onclick="viewDetails(<?php echo $medicine['id']; ?>)" title="Details">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<script>
// Initialize charts when document is ready
$(document).ready(function() {
    initializeCharts();
    initializeSearch();
});

// Initialize charts
function initializeCharts() {
    // Category Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($chartLabels); ?>,
            datasets: [{
                data: <?php echo json_encode($chartValues); ?>,
                backgroundColor: [
                    '#e74a3b', '#f6c23e', '#36b9cc', '#4e73df', 
                    '#1cc88a', '#6f42c1', '#fd7e14', '#20c9a6'
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
                        font: {
                            size: 12
                        }
                    }
                }
            }
        }
    });
    
    // Financial Chart
    const financialCtx = document.getElementById('financialChart').getContext('2d');
    new Chart(financialCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($chartLabels); ?>,
            datasets: [{
                label: 'Financial Impact (Rwf)',
                data: [<?php 
                    $financialData = [];
                    foreach ($chartLabels as $category) {
                        $total = 0;
                        foreach ($expiredMedicines as $medicine) {
                            if ($medicine['category'] === $category) {
                                $total += $medicine['value'];
                            }
                        }
                        $financialData[] = $total;
                    }
                    echo implode(', ', $financialData);
                ?>],
                backgroundColor: '#e74a3b',
                borderColor: '#c0392b',
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
                    ticks: {
                        callback: function(value) {
                            return 'Rwf ' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

// Initialize search functionality
function initializeSearch() {
    $('#searchExpired').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('.expired-row').each(function() {
            const rowText = $(this).text().toLowerCase();
            $(this).toggle(rowText.includes(searchTerm));
        });
    });
}

// Action functions
function generateDisposalReport() {
    alert('Generating disposal report...');
}

function bulkDispose() {
    const selectedIds = $('.medicine-checkbox:checked').map(function() {
        return $(this).val();
    }).get();
    
    if (selectedIds.length === 0) {
        alert('Please select medicines to dispose');
        return;
    }
    
    if (confirm(`Are you sure you want to dispose of ${selectedIds.length} expired medicines?`)) {
        alert('Bulk disposal initiated...');
    }
}

function returnToSuppliers() {
    alert('Return to suppliers feature coming soon!');
}

function generateReport() {
    alert('Generating comprehensive report...');
}

function updateInventory() {
    alert('Updating inventory...');
}

function exportData() {
    alert('Exporting data...');
}

function sendAlerts() {
    alert('Sending alerts to staff...');
}

function disposeMedicine(id) {
    if (confirm('Are you sure you want to dispose of this expired medicine?')) {
        alert('Medicine disposal initiated...');
    }
}

function returnMedicine(id) {
    if (confirm('Do you want to return this medicine to the supplier?')) {
        alert('Return process initiated...');
    }
}

function viewDetails(id) {
    alert('Viewing medicine details...');
}

function extendExpiry(id) {
    alert('Extending expiry date...');
}

function updateStock(id) {
    alert('Updating stock...');
}

function selectAll() {
    const isChecked = $('#selectAllCheckbox').prop('checked');
    $('.medicine-checkbox').prop('checked', !isChecked);
    $('#selectAllCheckbox').prop('checked', !isChecked);
}

function toggleSelectAll() {
    const isChecked = $('#selectAllCheckbox').prop('checked');
    $('.medicine-checkbox').prop('checked', isChecked);
}

function downloadChart(type) {
    alert(`Downloading ${type} chart...`);
}
</script>

<style>
/* Alert Banner */
.alert-banner {
    background: linear-gradient(135deg, #e74a3b 0%, #c0392b 100%);
    border-radius: 15px;
    padding: 1.5rem;
    color: white;
    box-shadow: 0 8px 25px rgba(231, 76, 59, 0.3);
}

.alert-content {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.alert-icon {
    font-size: 2.5rem;
    opacity: 0.9;
}

.alert-text h4 {
    margin: 0 0 0.5rem 0;
    font-weight: 600;
}

.alert-text p {
    margin: 0;
    opacity: 0.9;
}

.alert-actions {
    margin-left: auto;
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

.stat-card-danger::before { background: #e74a3b; }
.stat-card-warning::before { background: #f6c23e; }
.stat-card-info::before { background: #36b9cc; }
.stat-card-primary::before { background: #4e73df; }

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

.stat-card-danger .stat-icon { background: #e74a3b; }
.stat-card-warning .stat-icon { background: #f6c23e; }
.stat-card-info .stat-icon { background: #36b9cc; }
.stat-card-primary .stat-icon { background: #4e73df; }

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

.action-danger:hover { background: #f8d7da; border-color: #e74a3b; color: #721c24; }
.action-warning:hover { background: #fff3cd; border-color: #f6c23e; color: #856404; }
.action-info:hover { background: #d1ecf1; border-color: #36b9cc; color: #0c5460; }
.action-success:hover { background: #d4edda; border-color: #1cc88a; color: #155724; }
.action-primary:hover { background: #d6eaf8; border-color: #4e73df; color: #1b4f72; }
.action-secondary:hover { background: #e9ecef; border-color: #6c757d; color: #495057; }

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
    width: 300px;
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

.expired-row:hover, .expiring-row:hover {
    background: #f8f9fa;
}

/* Medicine Details */
.medicine-details {
    position: relative;
}

.medicine-name {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.25rem;
}

.medicine-batch {
    font-size: 0.8rem;
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.restricted-badge {
    background: #f8d7da;
    color: #721c24;
    padding: 0.2rem 0.5rem;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 500;
}

/* Category Tags */
.category-tag {
    background: #e9ecef;
    color: #495057;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    display: inline-block;
}

/* Stock and Price */
.stock-amount {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.25rem;
}

.unit-price {
    font-size: 0.8rem;
    color: #6c757d;
}

/* Expiry Information */
.expiry-date {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.25rem;
}

.days-expired {
    background: #f8d7da;
    color: #721c24;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 500;
}

.days-left {
    background: #fff3cd;
    color: #856404;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 500;
}

/* Financial Impact */
.total-value {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.25rem;
}

.impact-level {
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 500;
    text-align: center;
}

.impact-level.high {
    background: #f8d7da;
    color: #721c24;
}

.impact-level.medium {
    background: #fff3cd;
    color: #856404;
}

/* Supplier Info */
.supplier-name {
    font-weight: 500;
    color: #495057;
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

/* Responsive Design */
@media (max-width: 768px) {
    .alert-content {
        flex-direction: column;
        text-align: center;
    }
    
    .alert-actions {
        margin-left: 0;
        margin-top: 1rem;
    }
    
    .actions-grid {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    }
    
    .header-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .search-box {
        width: 100%;
    }
    
    .stat-card {
        flex-direction: column;
        text-align: center;
    }
}
</style>
