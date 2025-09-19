<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Movements Test</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid mt-4">
        <h1>🔍 Stock Movements Test Page</h1>
        
        <?php
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        // Start session and set a default user
        if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
        $_SESSION['userId'] = 1;
        $_SESSION['userRole'] = 'super_admin';
        
        try {
            require_once 'constant/connect.php';
            echo "<div class='alert alert-success'>✅ Database connected successfully</div>";
            
            // Test the Stock Movements section directly
            $title = 'Stock Movements';
            
            if (strcasecmp($title, 'Stock Movements') === 0) {
                ?>
                <!-- Stock Movements Section -->
                <div class="row">
                  <div class="col-12">
                    <div class="card">
                      <div class="card-body">
                        <h4 class="card-title">Stock Movement History</h4>
                        
                        <!-- Filters -->
                        <div class="row mb-3">
                          <div class="col-md-3">
                            <label>Medicine:</label>
                            <select class="form-control" id="filterMedicine">
                              <option value="">All Medicines</option>
                              <?php
                              $sql = "SELECT DISTINCT name FROM medicines ORDER BY name";
                              $result = $connect->query($sql);
                              if ($result) {
                                while ($row = $result->fetch_assoc()) {
                                  echo '<option value="' . htmlspecialchars($row['name']) . '">' . htmlspecialchars($row['name']) . '</option>';
                                }
                              }
                              ?>
                            </select>
                          </div>
                          <div class="col-md-3">
                            <label>Movement Type:</label>
                            <select class="form-control" id="filterMovementType">
                              <option value="">All Types</option>
                              <option value="IN">Stock In</option>
                              <option value="OUT">Stock Out</option>
                              <option value="ADJUSTMENT">Adjustment</option>
                              <option value="EXPIRED">Expired</option>
                            </select>
                          </div>
                          <div class="col-md-3">
                            <label>Date From:</label>
                            <input type="date" class="form-control" id="filterDateFrom">
                          </div>
                          <div class="col-md-3">
                            <label>Date To:</label>
                            <input type="date" class="form-control" id="filterDateTo">
                          </div>
                        </div>
                        
                        <!-- Add Stock Movement Button -->
                        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addStockMovementModal">
                          <i class="fa fa-plus"></i> Add Stock Movement
                        </button>
                        
                        <!-- Stock Movements Table -->
                        <div class="table-responsive">
                          <table id="stockMovementsTable" class="table table-bordered table-striped">
                            <thead>
                              <tr>
                                <th>Date</th>
                                <th>Medicine</th>
                                <th>Pharmacy</th>
                                <th>Movement Type</th>
                                <th>Quantity</th>
                                <th>Previous Stock</th>
                                <th>New Stock</th>
                                <th>Reference</th>
                                <th>Notes</th>
                                <th>User</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
                              $sql = "SELECT sm.*, m.name as medicine_name, p.name as pharmacy_name, u.username
                                      FROM stock_movements sm
                                      LEFT JOIN medicines m ON sm.medicine_id = m.medicine_id
                                      LEFT JOIN pharmacies p ON m.pharmacy_id = p.pharmacy_id
                                      LEFT JOIN users u ON sm.user_id = u.user_id
                                      ORDER BY sm.movement_date DESC, sm.created_at DESC
                                      LIMIT 100";
                              $result = $connect->query($sql);
                              if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                  $movementClass = '';
                                  $movementIcon = '';
                                  switch($row['movement_type']) {
                                    case 'IN':
                                      $movementClass = 'badge bg-success';
                                      $movementIcon = 'fa-arrow-up';
                                      break;
                                    case 'OUT':
                                      $movementClass = 'badge bg-danger';
                                      $movementIcon = 'fa-arrow-down';
                                      break;
                                    case 'ADJUSTMENT':
                                      $movementClass = 'badge bg-warning';
                                      $movementIcon = 'fa-edit';
                                      break;
                                    case 'EXPIRED':
                                      $movementClass = 'badge bg-dark';
                                      $movementIcon = 'fa-times';
                                      break;
                                  }
                                  echo '<tr>';
                                  echo '<td>' . date('Y-m-d H:i', strtotime($row['movement_date'])) . '</td>';
                                  echo '<td>' . htmlspecialchars($row['medicine_name'] ?? 'Unknown') . '</td>';
                                  echo '<td>' . htmlspecialchars($row['pharmacy_name'] ?? 'Unknown') . '</td>';
                                  echo '<td><span class="' . $movementClass . '"><i class="fa ' . $movementIcon . '"></i> ' . $row['movement_type'] . '</span></td>';
                                  echo '<td>' . ($row['movement_type'] == 'OUT' || $row['movement_type'] == 'EXPIRED' ? '-' : '+') . $row['quantity'] . '</td>';
                                  echo '<td>' . $row['previous_stock'] . '</td>';
                                  echo '<td>' . $row['new_stock'] . '</td>';
                                  echo '<td>' . htmlspecialchars($row['reference_number'] ?? '') . '</td>';
                                  echo '<td>' . htmlspecialchars($row['notes'] ?? '') . '</td>';
                                  echo '<td>' . htmlspecialchars($row['username'] ?? 'System') . '</td>';
                                  echo '</tr>';
                                }
                              } else {
                                echo '<tr><td colspan="10" class="text-center">No stock movements found</td></tr>';
                              }
                              ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Add Stock Movement Modal -->
                <div class="modal fade" id="addStockMovementModal" tabindex="-1">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">Add Stock Movement</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <form id="addStockMovementForm">
                        <div class="modal-body">
                          <div class="mb-3">
                            <label class="form-label">Medicine:</label>
                            <select class="form-control" name="medicine_id" required>
                              <option value="">Select Medicine</option>
                              <?php
                              $sql = "SELECT m.medicine_id, m.name, p.name as pharmacy_name, m.stock_quantity
                                      FROM medicines m
                                      LEFT JOIN pharmacies p ON m.pharmacy_id = p.pharmacy_id
                                      ORDER BY m.name";
                              $result = $connect->query($sql);
                              if ($result) {
                                while ($row = $result->fetch_assoc()) {
                                  echo '<option value="' . $row['medicine_id'] . '" data-current-stock="' . $row['stock_quantity'] . '">';
                                  echo htmlspecialchars($row['name']) . ' (' . htmlspecialchars($row['pharmacy_name'] ?? 'Unknown') . ') - Current: ' . $row['stock_quantity'];
                                  echo '</option>';
                                }
                              }
                              ?>
                            </select>
                          </div>
                          <div class="mb-3">
                            <label class="form-label">Movement Type:</label>
                            <select class="form-control" name="movement_type" required>
                              <option value="">Select Type</option>
                              <option value="IN">Stock In (Purchase/Delivery)</option>
                              <option value="OUT">Stock Out (Sale/Dispensed)</option>
                              <option value="ADJUSTMENT">Stock Adjustment</option>
                              <option value="EXPIRED">Expired Stock Removal</option>
                            </select>
                          </div>
                          <div class="mb-3">
                            <label class="form-label">Quantity:</label>
                            <input type="number" class="form-control" name="quantity" min="1" required>
                          </div>
                          <div class="mb-3">
                            <label class="form-label">Reference Number (Optional):</label>
                            <input type="text" class="form-control" name="reference_number" placeholder="e.g., Invoice #, Prescription #">
                          </div>
                          <div class="mb-3">
                            <label class="form-label">Notes (Optional):</label>
                            <textarea class="form-control" name="notes" rows="3" placeholder="Additional notes about this movement"></textarea>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                          <button type="submit" class="btn btn-primary">Add Movement</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
                <?php
            }
            
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>❌ Error: " . $e->getMessage() . "</div>";
        }
        ?>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <script>
    $(document).ready(function() {
      // Initialize DataTable
      $('#stockMovementsTable').DataTable({
        "order": [[ 0, "desc" ]],
        "pageLength": 10
      });
      
      // Handle stock movement form submission
      $('#addStockMovementForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
          url: 'php_action/addStockMovement.php',
          type: 'POST',
          data: $(this).serialize(),
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              alert('Stock movement added successfully!');
              location.reload();
            } else {
              alert('Error: ' + response.message);
            }
          },
          error: function() {
            alert('Error processing request');
          }
        });
      });
    });
    </script>
</body>
</html>