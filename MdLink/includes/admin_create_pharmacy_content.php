<?php
require_once __DIR__ . '/../constant/connect.php';
if (session_status() === PHP_SESSION_NONE) { if (session_status() === PHP_SESSION_NONE) {
    session_start();
} }

// Check if user is super admin
if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'super_admin') {
    echo '<div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> <strong>Access Warning:</strong> Only Super Administrators should create pharmacies.</div>';
    echo '<div class="alert alert-info"><i class="fa fa-info-circle"></i> <strong>Debug Info:</strong> Current Role: ' . ($_SESSION['userRole'] ?? 'Not Set') . ' | Session ID: ' . (session_id() ?: 'None') . '</div>';
    // Don't return - allow the page to display for now
}

// Fetch existing pharmacies for display
$existingPharmacies = [];
try {
    $query = "
        SELECT p.*, 
               COUNT(DISTINCT au.admin_id) as admin_count,
               COUNT(DISTINCT m.medicine_id) as medicine_count
        FROM pharmacies p
        LEFT JOIN admin_users au ON p.pharmacy_id = au.pharmacy_id
        LEFT JOIN medicines m ON p.pharmacy_id = m.pharmacy_id
        GROUP BY p.pharmacy_id
        ORDER BY p.created_at DESC
        LIMIT 10
    ";
    $result = $connect->query($query);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $existingPharmacies[] = $row;
        }
    }
} catch (Exception $e) {
    // Ignore errors for display
}
?>

<!-- Hero Section -->
<div class="row mb-4">
  <div class="col-12">
    <div class="card bg-gradient-primary text-white">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col-md-8">
            <h2 class="mb-2"><i class="fa fa-hospital-o"></i> Pharmacy Management</h2>
            <p class="mb-0">Create new pharmacies and manage their administrative accounts. Each pharmacy gets a manager and finance account automatically.</p>
          </div>
          <div class="col-md-4 text-right">
            <button class="btn btn-light btn-lg" data-toggle="modal" data-target="#createPharmacyModal">
              <i class="fa fa-plus"></i> Create New Pharmacy
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
  <div class="col-md-3">
    <div class="card bg-primary text-white">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <h4 class="mb-0"><?php echo count($existingPharmacies); ?></h4>
            <p class="mb-0">Total Pharmacies</p>
          </div>
          <div class="align-self-center">
            <i class="fa fa-hospital-o fa-2x"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card bg-success text-white">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <h4 class="mb-0"><?php echo array_sum(array_column($existingPharmacies, 'admin_count')); ?></h4>
            <p class="mb-0">Admin Accounts</p>
          </div>
          <div class="align-self-center">
            <i class="fa fa-users fa-2x"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card bg-info text-white">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <h4 class="mb-0"><?php echo array_sum(array_column($existingPharmacies, 'medicine_count')); ?></h4>
            <p class="mb-0">Total Medicines</p>
          </div>
          <div class="align-self-center">
            <i class="fa fa-medkit fa-2x"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card bg-warning text-white">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <h4 class="mb-0">Active</h4>
            <p class="mb-0">System Status</p>
          </div>
          <div class="align-self-center">
            <i class="fa fa-check-circle fa-2x"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Existing Pharmacies Table -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="mb-0"><i class="fa fa-list"></i> Recent Pharmacies</h4>
      </div>
      <div class="card-body">
        <?php if (!empty($existingPharmacies)) { ?>
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead class="thead-dark">
              <tr>
                <th>Pharmacy</th>
                <th>Location</th>
                <th>Contact</th>
                <th>Admin Accounts</th>
                <th>Medicines</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($existingPharmacies as $pharmacy) { ?>
              <tr>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3">
                      <i class="fa fa-hospital-o"></i>
                    </div>
                    <div>
                      <h6 class="mb-0"><?php echo htmlspecialchars($pharmacy['name']); ?></h6>
                      <small class="text-muted"><?php echo htmlspecialchars($pharmacy['license_number']); ?></small>
                    </div>
                  </div>
                </td>
                <td>
                  <div>
                    <i class="fa fa-map-marker text-danger"></i>
                    <?php echo htmlspecialchars($pharmacy['location'] ?: 'Location not specified'); ?>
                  </div>
                </td>
                <td>
                  <div>
                    <div><i class="fa fa-user text-info"></i> <?php echo htmlspecialchars($pharmacy['contact_person']); ?></div>
                    <div><i class="fa fa-phone text-success"></i> <?php echo htmlspecialchars($pharmacy['contact_phone']); ?></div>
                  </div>
                </td>
                <td>
                  <span class="badge badge-primary"><?php echo (int)$pharmacy['admin_count']; ?> accounts</span>
                </td>
                <td>
                  <span class="badge badge-info"><?php echo (int)$pharmacy['medicine_count']; ?> medicines</span>
                </td>
                <td>
                  <span class="badge badge-success">Active</span>
                </td>
                <td>
                  <button class="btn btn-sm btn-outline-primary" title="View Details">
                    <i class="fa fa-eye"></i>
                  </button>
                  <button class="btn btn-sm btn-outline-warning" title="Edit">
                    <i class="fa fa-pencil"></i>
                  </button>
                  <button class="btn btn-sm btn-outline-danger" title="Deactivate">
                    <i class="fa fa-ban"></i>
                  </button>
                </td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
        <?php } else { ?>
        <!-- Sample Data Display -->
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead class="thead-dark">
              <tr>
                <th>Pharmacy</th>
                <th>Location</th>
                <th>Contact</th>
                <th>Admin Accounts</th>
                <th>Medicines</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3">
                      <i class="fa fa-hospital-o"></i>
                    </div>
                    <div>
                      <h6 class="mb-0">Ineza Pharmacy</h6>
                      <small class="text-muted">RL-2024-001</small>
                    </div>
                  </div>
                </td>
                <td>
                  <div>
                    <i class="fa fa-map-marker text-danger"></i>
                    Kigali, Gasabo District
                  </div>
                </td>
                <td>
                  <div>
                    <div><i class="fa fa-user text-info"></i> Dr. Jean Bosco</div>
                    <div><i class="fa fa-phone text-success"></i> +250 788 123 456</div>
                  </div>
                </td>
                <td>
                  <span class="badge badge-primary">2 accounts</span>
                </td>
                <td>
                  <span class="badge badge-info">156 medicines</span>
                </td>
                <td>
                  <span class="badge badge-success">Active</span>
                </td>
                <td>
                  <button class="btn btn-sm btn-outline-primary" title="View Details">
                    <i class="fa fa-eye"></i>
                  </button>
                  <button class="btn btn-sm btn-outline-warning" title="Edit">
                    <i class="fa fa-pencil"></i>
                  </button>
                  <button class="btn btn-sm btn-outline-danger" title="Deactivate">
                    <i class="fa fa-ban"></i>
                  </button>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="avatar-sm bg-success text-white rounded-circle d-flex align-items-center justify-content-center mr-3">
                      <i class="fa fa-hospital-o"></i>
                    </div>
                    <div>
                      <h6 class="mb-0">Keza Pharmacy</h6>
                      <small class="text-muted">RL-2024-002</small>
                    </div>
                  </div>
                </td>
                <td>
                  <div>
                    <i class="fa fa-map-marker text-danger"></i>
                    Kigali, Kicukiro District
                  </div>
                </td>
                <td>
                  <div>
                    <div><i class="fa fa-user text-info"></i> Dr. Marie Claire</div>
                    <div><i class="fa fa-phone text-success"></i> +250 789 987 654</div>
                  </div>
                </td>
                <td>
                  <span class="badge badge-primary">2 accounts</span>
                </td>
                <td>
                  <span class="badge badge-info">89 medicines</span>
                </td>
                <td>
                  <span class="badge badge-success">Active</span>
                </td>
                <td>
                  <button class="btn btn-sm btn-outline-primary" title="View Details">
                    <i class="fa fa-eye"></i>
                  </button>
                  <button class="btn btn-sm btn-outline-warning" title="Edit">
                    <i class="fa fa-pencil"></i>
                  </button>
                  <button class="btn btn-sm btn-outline-danger" title="Deactivate">
                    <i class="fa fa-ban"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <?php } ?>
      </div>
    </div>
  </div>
</div>

<!-- Create Pharmacy Modal -->
<div class="modal fade" id="createPharmacyModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="fa fa-plus"></i> Create New Pharmacy</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div id="pharmacyMessage"></div>
        
        <form id="createPharmacyForm">
          <!-- Pharmacy Information -->
          <div class="row">
            <div class="col-md-12">
              <h6 class="text-primary mb-3"><i class="fa fa-hospital-o"></i> Pharmacy Information</h6>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label><i class="fa fa-hospital-o"></i> Pharmacy Name *</label>
                <input type="text" class="form-control" name="name" placeholder="e.g., Ineza Pharmacy" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label><i class="fa fa-id-badge"></i> License Number *</label>
                <input type="text" class="form-control" name="license_number" placeholder="e.g., RL-2024-XXX" required>
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label><i class="fa fa-user-md"></i> Contact Person *</label>
                <input type="text" class="form-control" name="contact_person" placeholder="e.g., Dr. Jean Bosco" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label><i class="fa fa-phone"></i> Contact Phone *</label>
                <input type="tel" class="form-control" name="contact_phone" placeholder="+250 788 XXX XXX" required>
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label><i class="fa fa-map-marker"></i> Location/Address *</label>
                <textarea class="form-control" name="location" rows="2" placeholder="Full address including district, sector, etc." required></textarea>
              </div>
            </div>
          </div>
          
          <!-- Manager Account -->
          <div class="row mt-4">
            <div class="col-md-12">
              <h6 class="text-success mb-3"><i class="fa fa-user-shield"></i> Manager Account</h6>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label><i class="fa fa-envelope"></i> Manager Email *</label>
                <input type="email" class="form-control" name="manager_email" placeholder="manager@pharmacy.com" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label><i class="fa fa-lock"></i> Manager Password *</label>
                <input type="password" class="form-control" name="manager_password" placeholder="Minimum 8 characters" required>
              </div>
            </div>
          </div>
          
          <!-- Finance Account -->
          <div class="row mt-4">
            <div class="col-md-12">
              <h6 class="text-info mb-3"><i class="fa fa-calculator"></i> Finance Account</h6>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label><i class="fa fa-envelope"></i> Finance Email *</label>
                <input type="email" class="form-control" name="finance_email" placeholder="finance@pharmacy.com" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label><i class="fa fa-lock"></i> Finance Password *</label>
                <input type="password" class="form-control" name="finance_password" placeholder="Minimum 8 characters" required>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="btnCreatePharmacy">
          <i class="fa fa-plus"></i> Create Pharmacy
        </button>
      </div>
    </div>
  </div>
</div>

<style>
.avatar-sm {
  width: 40px;
  height: 40px;
}

.bg-gradient-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.card {
  border: none;
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
  transition: all 0.3s ease;
}

.card:hover {
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
  transform: translateY(-2px);
}

.table th {
  border-top: none;
  font-weight: 600;
}

.badge {
  font-size: 0.75rem;
  padding: 0.375rem 0.75rem;
}

.modal-header.bg-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.form-group label {
  font-weight: 600;
  color: #495057;
}

.form-control:focus {
  border-color: #667eea;
  box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}
</style>

<script>
(function(){
  // Handle form submission
  document.getElementById('btnCreatePharmacy').addEventListener('click', function(){
    const form = document.getElementById('createPharmacyForm');
    const formData = new FormData(form);
    const btn = this;
    
    // Validate form
    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }
    
    // Disable button and show loading
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Creating...';
    
    // Submit form
    fetch('php_action/admin_create_pharmacy.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Show success message
        document.getElementById('pharmacyMessage').innerHTML = 
          '<div class="alert alert-success"><i class="fa fa-check-circle"></i> Pharmacy created successfully! ' +
          'Manager and Finance accounts have been set up.</div>';
        
        // Reset form
        form.reset();
        
        // Close modal after 2 seconds
        setTimeout(() => {
          $('#createPharmacyModal').modal('hide');
          location.reload(); // Refresh page to show new pharmacy
        }, 2000);
      } else {
        // Show error message
        document.getElementById('pharmacyMessage').innerHTML = 
          '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> ' + 
          (data.message || 'Failed to create pharmacy') + '</div>';
      }
    })
    .catch(error => {
      // Show error message
      document.getElementById('pharmacyMessage').innerHTML = 
        '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> ' + 
        'Network error. Please try again.</div>';
    })
    .finally(() => {
      // Re-enable button
      btn.disabled = false;
      btn.innerHTML = '<i class="fa fa-plus"></i> Create Pharmacy';
    });
  });
  
  // Clear messages when modal is closed
  $('#createPharmacyModal').on('hidden.bs.modal', function () {
    document.getElementById('pharmacyMessage').innerHTML = '';
    document.getElementById('createPharmacyForm').reset();
  });
  
  // Add some interactivity to the table rows
  document.querySelectorAll('tbody tr').forEach(row => {
    row.addEventListener('mouseenter', function() {
      this.style.backgroundColor = '#f8f9fa';
    });
    
    row.addEventListener('mouseleave', function() {
      this.style.backgroundColor = '';
    });
  });
})();
</script>
