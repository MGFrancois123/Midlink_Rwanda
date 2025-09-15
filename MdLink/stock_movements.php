<?php include('./constant/layout/head.php');?>
<?php include('./constant/layout/header.php');?>
<?php include('./constant/layout/sidebar.php');?>
<?php include('./constant/connect.php');?>
<?php include('./constant/check.php');?>

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

        .stat-icon.success {
            background: linear-gradient(135deg, var(--success-color), var(--secondary-color));
        }

        .stat-icon.warning {
            background: linear-gradient(135deg, var(--warning-color), #ff9800);
        }

        .stat-icon.info {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }

        .stat-icon.danger {
            background: linear-gradient(135deg, var(--danger-color), #f44336);
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0.5rem 0;
        }

        .stat-value.success {
            color: var(--success-color);
        }

        .stat-value.warning {
            color: var(--warning-color);
        }

        .stat-value.info {
            color: var(--primary-color);
        }

        .stat-value.danger {
            color: var(--danger-color);
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
            background: var(--primary-color);
            color: white;
        }

        /* Movement indicators */
        .movement-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 0.5rem;
        }

        .movement-indicator.in {
            background: var(--success-color);
        }

        .movement-indicator.out {
            background: var(--warning-color);
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

<div class="page-wrapper">
    <div class="container-fluid py-4">
        <div class="row align-items-center mb-3">
            <div class="col-md-8">
                <h1 class="mb-2">
                    <i class="fas fa-chart-line me-2"></i>Stock Movements Management
                </h1>
                <p class="mb-0">Track and monitor all stock movements for Ineza Pharmacy</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="dashboard_ineza_pharmacy.php" class="btn btn-outline-primary me-2">
                    <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                </a>
                <a href="./constant/logout.php" class="btn btn-outline-danger">
                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="fas fa-chart-line me-2"></i>
                            Stock Movements - Ineza Pharmacy
                        </h4>
    </div>
                    <div class="card-body">
                        <!-- Statistics Cards -->
                        <div class="stats-grid">
                            <?php
                            // No role-based filtering
                            $where_clause = "";
                            
                            // Statistics queries with error handling
                            $stats_queries = [
                                'total_transactions' => "SELECT COUNT(*) as count FROM stock_movements",
                                'stock_in' => "SELECT COUNT(*) as count FROM stock_movements WHERE movement_type = 'IN'",
                                'stock_out' => "SELECT COUNT(*) as count FROM stock_movements WHERE movement_type = 'OUT'",
                                'today_movements' => "SELECT COUNT(*) as count FROM stock_movements WHERE DATE(created_at) = CURDATE()"
                            ];
                            
                            $stats = [];
                            foreach ($stats_queries as $key => $query) {
                                try {
                                    $result = $connect->query($query);
                                    if ($result) {
                                        $row = $result->fetch_assoc();
                                        $stats[$key] = $row['count'];
                                    } else {
                                        $stats[$key] = 0;
                                    }
                                } catch (Exception $e) {
                                    $stats[$key] = 0;
                                }
                            }
                            ?>
                            
                            <div class="stat-card animate-fade-in">
                                <div class="stat-header">
                                    <div>
                                        <div class="stat-value info"><?php echo number_format($stats['total_transactions']); ?></div>
                                        <div class="stat-label">Total Transactions</div>
      </div>
                                    <div class="stat-icon info">
                                        <i class="fas fa-exchange-alt"></i>
          </div>
        </div>
                                <small class="text-muted">All stock movements recorded</small>
                            </div>
                            
                            <div class="stat-card animate-fade-in">
                                <div class="stat-header">
                                    <div>
                                        <div class="stat-value success"><?php echo number_format($stats['stock_in']); ?></div>
                                        <div class="stat-label">Stock In</div>
      </div>
                                    <div class="stat-icon success">
                                        <i class="fas fa-arrow-down"></i>
          </div>
        </div>
                                <small class="text-muted">Items received into inventory</small>
      </div>
                            
                            <div class="stat-card animate-fade-in">
                                <div class="stat-header">
                                    <div>
                                        <div class="stat-value warning"><?php echo number_format($stats['stock_out']); ?></div>
                                        <div class="stat-label">Stock Out</div>
          </div>
                                    <div class="stat-icon warning">
                                        <i class="fas fa-arrow-up"></i>
        </div>
      </div>
                                <small class="text-muted">Items dispensed or removed</small>
    </div>

                            <div class="stat-card animate-fade-in">
                                <div class="stat-header">
                                    <div>
                                        <div class="stat-value danger"><?php echo number_format($stats['today_movements']); ?></div>
                                        <div class="stat-label">Today's Movements</div>
                                    </div>
                                    <div class="stat-icon danger">
                                        <i class="fas fa-calendar-day"></i>
                                    </div>
                                </div>
                                <small class="text-muted">Movements recorded today</small>
            </div>
          </div>

                        <!-- Filters -->
                        <div class="filters-section">
                            <h5 class="mb-3"><i class="fas fa-filter me-2"></i>Filter Movements</h5>
                            <div class="filter-group">
                                <div class="form-group">
                                    <label for="movementType" class="form-label fw-bold">Movement Type</label>
                                    <select class="form-select" id="movementType">
              <option value="">All Types</option>
                                        <option value="IN">Stock In</option>
                                        <option value="OUT">Stock Out</option>
            </select>
          </div>
                                
                                <div class="form-group">
                                    <label for="medicineFilter" class="form-label fw-bold">Medicine</label>
                                    <select class="form-select" id="medicineFilter">
                                        <option value="">All Medicines</option>
            </select>
          </div>
                                
                                <div class="form-group">
                                    <label for="startDate" class="form-label fw-bold">Start Date</label>
                                    <input type="date" class="form-control" id="startDate">
                                </div>
                                
                                <div class="form-group">
                                    <label for="endDate" class="form-label fw-bold">End Date</label>
                                    <input type="date" class="form-control" id="endDate">
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label fw-bold">&nbsp;</label>
                                    <div>
                                        <button type="button" class="btn btn-primary" onclick="applyFilters()">
                                            <i class="fas fa-search me-1"></i>Apply Filters
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
                                            <i class="fas fa-refresh me-1"></i>Clear
                                        </button>
          </div>
        </div>
      </div>
    </div>

                        <!-- Quick Actions -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h5 class="mb-3">Quick Actions</h5>
                                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                                            <button class="btn btn-outline-primary" onclick="exportMovements()">
                                                <i class="fas fa-file-excel me-2"></i>Export to Excel
                                            </button>
                                            <button class="btn btn-outline-success" onclick="generateReport()">
                                                <i class="fas fa-chart-bar me-2"></i>Generate Report
                                            </button>
                                            <button class="btn btn-outline-info" onclick="refreshData()">
                                                <i class="fas fa-sync-alt me-2"></i>Refresh Data
                                            </button>
                                            <button class="btn btn-outline-warning" onclick="viewAnalytics()">
                                                <i class="fas fa-chart-line me-2"></i>View Analytics
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
    </div>

                        <!-- Movements Table -->
    <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-list me-2"></i>Stock Movements
                                    <span class="badge bg-light text-dark ms-2" id="movementsCount">0</span>
                                </h5>
      </div>
                            <div class="card-body p-0">
        <div class="table-responsive">
                                    <table id="movementsTable" class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Date & Time</th>
                                                <th>Medicine</th>
                                                <th>Movement Type</th>
                                                <th>Quantity</th>
                                                <th>Reason/Notes</th>
                                                <th>Status</th>
                                                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
    <?php
                                    try {
                                        // Build the main query with role-based filtering
                                        $main_query = "SELECT sm.created_at, 
                                                              (SELECT name FROM medicines WHERE medicines.medicine_id = sm.medicine_id LIMIT 1) as medicine_name,
                                                              sm.movement_type, sm.quantity, COALESCE(sm.notes, 'N/A') as reason
                                                       FROM stock_movements sm 
                                                       ORDER BY sm.created_at DESC";
                                        
                                        $result = $connect->query($main_query);
                                        
                                        if ($result && $result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                $status_class = $row['movement_type'] == 'IN' ? 'badge-success' : 'badge-warning';
                                                $status_text = $row['movement_type'] == 'IN' ? 'Stock In' : 'Stock Out';
                                                $movement_indicator = $row['movement_type'] == 'IN' ? 'in' : 'out';
                                                ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-clock me-2 text-muted"></i>
                                                            <div>
                                                                <div class="fw-bold"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></div>
                                                                <small class="text-muted"><?php echo date('H:i', strtotime($row['created_at'])); ?></small>
                                                            </div>
                                                        </div>
                  </td>
                  <td>
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-pills me-2 text-primary"></i>
                                                            <strong><?php echo htmlspecialchars($row['medicine_name']); ?></strong>
                                                        </div>
                  </td>
                  <td>
                                                        <div class="d-flex align-items-center">
                                                            <span class="movement-indicator <?php echo $movement_indicator; ?>"></span>
                                                            <span class="badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                                        </div>
                  </td>
                  <td>
                                                        <span class="fw-bold"><?php echo number_format($row['quantity']); ?></span>
                  </td>
                  <td>
                                                        <div class="text-truncate" style="max-width: 200px;" title="<?php echo htmlspecialchars($row['reason']); ?>">
                                                            <?php echo htmlspecialchars($row['reason']); ?>
                                                        </div>
                  </td>
                  <td>
                                                        <span class="badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                  </td>
                  <td>
                                                        <div class="btn-group" role="group">
                                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewMovementDetails('<?php echo htmlspecialchars($row['created_at']); ?>')">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-info" onclick="printMovement('<?php echo htmlspecialchars($row['created_at']); ?>')">
                                                                <i class="fas fa-print"></i>
                                                            </button>
                                                        </div>
                  </td>
                </tr>
                                                <?php
                                            }
                                        } else { 
                                            echo '<tr><td colspan="7" class="text-center text-muted py-4">No movements found</td></tr>';
                                        }
                                    } catch (Exception $e) {
                                        echo '<tr><td colspan="6" class="text-center text-danger">Error loading movements: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
                                    }
                                    ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
    </div>

<?php include('./constant/layout/footer.php');?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

<script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#movementsTable').DataTable({
                "order": [[0, "desc"]],
                "pageLength": 10,
                "language": {
                    "search": "Search movements:",
                    "lengthMenu": "Show _MENU_ movements per page",
                    "info": "Showing _START_ to _END_ of _TOTAL_ movements"
                },
                "columnDefs": [
                    { "orderable": false, "targets": [6] } // Disable sorting for actions column
                ]
            });

            // Update movements count
            updateMovementsCount();

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

            // Populate medicine filter
            populateMedicineFilter(table);
        });

        // Update movements count
        function updateMovementsCount() {
            var table = $('#movementsTable').DataTable();
            var count = table.data().count();
            $('#movementsCount').text(count);
        }

        // Populate medicine filter
        function populateMedicineFilter(table) {
            var medicines = [];
            table.column(1).data().each(function(value) {
                if (value && medicines.indexOf(value) === -1) {
                    medicines.push(value);
                }
            });
            
            medicines.sort().forEach(function(medicine) {
                $('#medicineFilter').append('<option value="' + medicine + '">' + medicine + '</option>');
            });
        }

        // Apply filters
        function applyFilters() {
            var table = $('#movementsTable').DataTable();
            var movementType = $('#movementType').val();
            var medicine = $('#medicineFilter').val();
            var startDate = $('#startDate').val();
            var endDate = $('#endDate').val();

            // Clear existing filters
            table.search('').columns().search('').draw();

            // Apply movement type filter
            if (movementType) {
                table.column(2).search(movementType).draw();
            }

            // Apply medicine filter
            if (medicine) {
                table.column(1).search(medicine).draw();
            }

            // Apply date range filter
            if (startDate && endDate) {
                $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                    var date = new Date(data[0]);
                    var start = new Date(startDate);
                    var end = new Date(endDate);
                    return (date >= start && date <= end);
                });
                table.draw();
            }

            updateMovementsCount();
        }

        // Clear filters
        function clearFilters() {
            $('#movementType').val('');
            $('#medicineFilter').val('');
            $('#startDate').val('');
            $('#endDate').val('');
            
            var table = $('#movementsTable').DataTable();
            table.search('').columns().search('').draw();
            $.fn.dataTable.ext.search.pop();
            
            updateMovementsCount();
        }

        // Quick action functions
        function exportMovements() {
            alert('Export functionality will be implemented');
        }

        function generateReport() {
            alert('Report generation will be implemented');
        }

        function refreshData() {
            location.reload();
        }

        function viewAnalytics() {
            alert('Analytics view will be implemented');
        }

        function viewMovementDetails(ts) {
            var d = new Date(ts);
            alert('Viewing details for movement at: ' + (isNaN(d.getTime()) ? ts : d.toLocaleString()));
        }

        function printMovement(ts) {
            var d = new Date(ts);
            alert('Printing movement record for: ' + (isNaN(d.getTime()) ? ts : d.toLocaleString()));
        }
</script>
