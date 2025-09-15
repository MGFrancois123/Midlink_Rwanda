<?php include('./constant/check.php'); ?>
<?php include('./constant/layout/head.php'); ?>
<?php include('./constant/layout/header.php'); ?>
<?php include('./constant/layout/sidebar.php'); ?>

<style>
/* Stock level indicators */
.stock-indicator {
    display: inline-block;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin-right: 0.5rem;
}

.stock-indicator.critical {
    background: #d32f2f;
}

.stock-indicator.warning {
    background: #f57c00;
}

.stock-indicator.safe {
    background: #388e3c;
}
</style>

<div class="page-wrapper">
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Low Stock Alerts</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard_super.php">Home</a></li>
                    <li class="breadcrumb-item active">Inventory Management</li>
                    <li class="breadcrumb-item active">Low Stock Alerts</li>
                </ol>
            </div>
        </div>

        <?php
        // No role-based filtering
        $where_clause = "";
        
        $threshold = 10;
        $rows = [];
        $q = $connect->query("SELECT m.medicine_id, m.name, m.stock_quantity, m.expiry_date, m.price, c.category_name as category_name, p.name AS pharmacy_name
                              FROM medicines m
                              LEFT JOIN category c ON m.category_id = c.category_id
                              LEFT JOIN pharmacies p ON m.pharmacy_id = p.pharmacy_id
                              WHERE m.stock_quantity <= {$threshold} $where_clause
                              ORDER BY m.stock_quantity ASC, m.medicine_id DESC");
        if ($q) { 
            while($r=$q->fetch_assoc()){ 
                $rows[] = $r; 
            } 
        }
        
        // Calculate statistics
        $criticalCount = 0;
        $warningCount = 0;
        $pharmacies = [];
        $totalValue = 0;
        
        foreach($rows as $row) {
            if ((int)$row['stock_quantity'] <= 5) {
                $criticalCount++;
            } else {
                $warningCount++;
            }
            
            if (!empty($row['pharmacy_name'])) {
                $pharmacies[$row['pharmacy_name']] = true;
            }
            
            $totalValue += (float)$row['price'] * (int)$row['stock_quantity'];
        }
        ?>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="p-10">
                                <h3 class="text-white"><?php echo $criticalCount; ?></h3>
                                <h6 class="text-white">Critical Stock (≤5)</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-fire fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="p-10">
                                <h3 class="text-white"><?php echo $warningCount; ?></h3>
                                <h6 class="text-white">Low Stock (6-10)</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-exclamation-triangle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="p-10">
                                <h3 class="text-white"><?php echo count($pharmacies); ?></h3>
                                <h6 class="text-white">Pharmacies Affected</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-hospital-o fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="p-10">
                                <h3 class="text-white">Rwf <?php echo number_format($totalValue, 0); ?></h3>
                                <h6 class="text-white">Total Value at Risk</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-money fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Filter Low Stock Items</h4>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Search Medicine</label>
                                    <input type="text" class="form-control" id="lowStockSearch" placeholder="Search medicines...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Category</label>
                                    <select class="form-control" id="lowStockCategory">
                                        <option value="">All Categories</option>
                                        <?php 
                                        $catQuery = $connect->query("SELECT DISTINCT c.category_name FROM medicines m LEFT JOIN category c ON m.category_id=c.category_id WHERE m.stock_quantity<=10 ORDER BY c.category_name"); 
                                        if($catQuery){ 
                                            while($cat=$catQuery->fetch_assoc()){ 
                                                echo '<option value="'.htmlspecialchars($cat['category_name']).'">'.htmlspecialchars($cat['category_name']).'</option>'; 
                                            } 
                                        } 
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Severity</label>
                                    <select class="form-control" id="lowStockSeverity">
                                        <option value="">All Severity</option>
                                        <option value="critical">Critical (≤5)</option>
                                        <option value="warning">Warning (6-10)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label><br>
                                    <button type="button" class="btn btn-primary" onclick="applyFilters()">
                                        <i class="fa fa-search"></i> Apply Filters
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="clearFilters()">
                                        <i class="fa fa-refresh"></i> Clear
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Low Stock Items</h4>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" id="lowStockTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Medicine Name</th>
                                        <th>Category</th>
                                        <th>Stock Level</th>
                                        <th>Price (Rwf)</th>
                                        <th>Expiry Date</th>
                                        <th>Days Left</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($rows)) { 
                                        echo '<tr><td colspan="9" class="text-center text-muted py-5">
                                                <i class="fa fa-check-circle fa-3x d-block mb-3 text-success"></i>
                                                <h5>No low stock items found!</h5>
                                                <p class="text-muted">All medicines are well stocked.</p>
                                              </td></tr>'; 
                                    } else {
                                        $counter = 1;
                                        foreach($rows as $r){ 
                                            $isCritical = (int)$r['stock_quantity'] <= 5; 
                                            $sevText = $isCritical ? 'Critical' : 'Warning'; 
                                            $sevBadge = $isCritical ? 'danger' : 'warning'; 
                                            $daysToExpiry = $r['expiry_date'] ? (strtotime($r['expiry_date']) - time()) / (60 * 60 * 24) : null;
                                            $isExpired = $r['expiry_date'] && strtotime($r['expiry_date']) < time();
                                            $stockIndicator = $isCritical ? 'critical' : 'warning';
                                    ?>
                                        <tr data-category="<?php echo htmlspecialchars($r['category_name'] ?? ''); ?>" 
                                            data-severity="<?php echo $isCritical ? 'critical' : 'warning'; ?>">
                                            <td>
                                                <span class="badge badge-secondary"><?php echo $counter; ?></span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fa fa-pills me-2 text-primary"></i>
                                                    <strong><?php echo htmlspecialchars($r['name']); ?></strong>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-info"><?php echo htmlspecialchars($r['category_name'] ?? 'N/A'); ?></span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="stock-indicator <?php echo $stockIndicator; ?>"></span>
                                                    <span class="badge badge-<?php echo $sevBadge; ?>"><?php echo (int)$r['stock_quantity']; ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <strong>Rwf <?php echo number_format($r['price'], 0); ?></strong>
                                            </td>
                                            <td>
                                                <?php echo $r['expiry_date'] ? date('M j, Y', strtotime($r['expiry_date'])) : '—'; ?>
                                            </td>
                                            <td>
                                                <?php if ($daysToExpiry !== null) { 
                                                    $label = $isExpired ? 'Expired '.abs(round($daysToExpiry)).' days ago' : round($daysToExpiry).' days left';
                                                    $badge = $isExpired ? 'danger' : ($daysToExpiry <= 7 ? 'warning' : 'info');
                                                    echo '<span class="badge badge-'.$badge.'">'.$label.'</span>';
                                                } else { 
                                                    echo '<span class="badge badge-secondary">—</span>'; 
                                                } ?>
                                            </td>
                                            <td>
                                                <?php if ($isExpired) { ?>
                                                    <span class="badge badge-danger">Expired</span>
                                                <?php } elseif ($isCritical) { ?>
                                                    <span class="badge badge-danger">Critical</span>
                                                <?php } else { ?>
                                                    <span class="badge badge-warning">Low Stock</span>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                                            onclick="viewMedicineDetails(<?php echo (int)$r['medicine_id']; ?>)"
                                                            title="View Details">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-success" 
                                                            onclick="restockMedicine(<?php echo (int)$r['medicine_id']; ?>, '<?php echo htmlspecialchars($r['name']); ?>')"
                                                            title="Restock">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-info" 
                                                            onclick="viewStockHistory(<?php echo (int)$r['medicine_id']; ?>)"
                                                            title="Stock History">
                                                        <i class="fa fa-history"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php 
                                            $counter++; 
                                        } 
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('./constant/layout/footer.php'); ?>

<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#lowStockTable').DataTable({
        "order": [[3, "asc"]], // Sort by stock level ascending
        "pageLength": 10,
        "language": {
            "search": "Search low stock items:",
            "lengthMenu": "Show _MENU_ items per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ items"
        },
        "columnDefs": [
            { "orderable": false, "targets": [8] } // Disable sorting for actions column
        ]
    });
});

// Apply filters
function applyFilters() {
    var table = $('#lowStockTable').DataTable();
    var searchTerm = $('#lowStockSearch').val();
    var category = $('#lowStockCategory').val();
    var severity = $('#lowStockSeverity').val();

    // Clear existing filters
    table.search('').columns().search('').draw();

    // Apply search filter
    if (searchTerm) {
        table.search(searchTerm).draw();
    }

    // Apply category filter
    if (category) {
        table.column(2).search(category).draw();
    }

    // Apply severity filter
    if (severity) {
        if (severity === 'critical') {
            table.column(3).search('≤5').draw();
        } else if (severity === 'warning') {
            table.column(3).search('6|7|8|9|10').draw();
        }
    }
}

// Clear filters
function clearFilters() {
    $('#lowStockSearch').val('');
    $('#lowStockCategory').val('');
    $('#lowStockSeverity').val('');
    
    var table = $('#lowStockTable').DataTable();
    table.search('').columns().search('').draw();
}

// Action functions
function viewMedicineDetails(medicineId) {
    alert('Viewing details for medicine ID: ' + medicineId);
}

function restockMedicine(medicineId, medicineName) {
    var quantity = prompt('Enter restock quantity for ' + medicineName + ':');
    if (quantity && !isNaN(quantity) && quantity > 0) {
        // Here you would make an AJAX call to update stock
        alert('Restocking ' + medicineName + ' with ' + quantity + ' units');
    }
}

function viewStockHistory(medicineId) {
    alert('Viewing stock history for medicine ID: ' + medicineId);
}
</script>
