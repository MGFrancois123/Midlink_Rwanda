<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once './constant/connect.php';
require_once './constant/check.php';

// Get existing medical staff data
$existingStaff = [];
$staffQuery = "SELECT ms.*, p.name as pharmacy_name 
               FROM medical_staff ms 
               LEFT JOIN pharmacies p ON ms.pharmacy_id = p.pharmacy_id 
               ORDER BY ms.created_at DESC";
$staffResult = $connect->query($staffQuery);

if ($staffResult && $staffResult->num_rows > 0) {
    while ($row = $staffResult->fetch_assoc()) {
        $existingStaff[] = $row;
    }
}

// Get statistics
$stats = [
    'total_staff' => 0,
    'doctors' => 0,
    'nurses' => 0,
    'assigned_staff' => 0
];

$totalQuery = "SELECT COUNT(*) as count FROM medical_staff";
$totalResult = $connect->query($totalQuery);
if ($totalResult) {
    $stats['total_staff'] = $totalResult->fetch_assoc()['count'];
}

$doctorsQuery = "SELECT COUNT(*) as count FROM medical_staff WHERE role = 'doctor'";
$doctorsResult = $connect->query($doctorsQuery);
if ($doctorsResult) {
    $stats['doctors'] = $doctorsResult->fetch_assoc()['count'];
}

$nursesQuery = "SELECT COUNT(*) as count FROM medical_staff WHERE role = 'nurse'";
$nursesResult = $connect->query($nursesQuery);
if ($nursesResult) {
    $stats['nurses'] = $nursesResult->fetch_assoc()['count'];
}

$assignedQuery = "SELECT COUNT(*) as count FROM medical_staff WHERE pharmacy_id IS NOT NULL";
$assignedResult = $connect->query($assignedQuery);
if ($assignedResult) {
    $stats['assigned_staff'] = $assignedResult->fetch_assoc()['count'];
}

// Get pharmacies for dropdown
$pharmacies = [];
$pharmacyQuery = "SELECT pharmacy_id, name FROM pharmacies ORDER BY name";
$pharmacyResult = $connect->query($pharmacyQuery);
if ($pharmacyResult && $pharmacyResult->num_rows > 0) {
    while ($row = $pharmacyResult->fetch_assoc()) {
        $pharmacies[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Staff Management - MdLink Rwanda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <style>
        :root {
            --primary-color: #2c5aa0;
            --secondary-color: #f8f9fa;
            --accent-color: #4facfe;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
            --text-dark: #2c3e50;
            --text-muted: #6c757d;
            --border-color: #e9ecef;
            --shadow: 0 2px 10px rgba(0,0,0,0.1);
            --shadow-lg: 0 4px 20px rgba(0,0,0,0.15);
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .main-container {
            background: white;
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            margin: 20px;
            overflow: hidden;
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .page-header h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 700;
        }

        .page-header p {
            margin: 0.5rem 0 0 0;
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .stats-container {
            padding: 2rem;
            background: var(--secondary-color);
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            border: none;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
            color: white;
        }

        .stat-icon.doctors { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stat-icon.nurses { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .stat-icon.total { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .stat-icon.assigned { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
            color: var(--text-dark);
        }

        .stat-label {
            font-size: 1rem;
            color: var(--text-muted);
            margin: 0.5rem 0 0 0;
            font-weight: 500;
        }

        .content-section {
            padding: 2rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--text-dark);
            margin: 0;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .form-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .form-control {
            border: 2px solid var(--border-color);
            border-radius: 10px;
            padding: 12px 16px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(44, 90, 160, 0.25);
        }

        .staff-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .staff-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .staff-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary-color);
        }

        .staff-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .staff-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            margin-right: 1rem;
        }

        .staff-avatar.doctor { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .staff-avatar.nurse { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .staff-avatar.pharmacist { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .staff-avatar.technician { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }

        .staff-info h5 {
            margin: 0;
            color: var(--text-dark);
            font-weight: 600;
        }

        .staff-role {
            background: var(--primary-color);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .staff-details {
            margin: 1rem 0;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: var(--text-muted);
        }

        .detail-value {
            color: var(--text-dark);
        }

        .staff-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .action-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .action-btn.edit {
            background: #fff3e0;
            color: #f57c00;
        }

        .action-btn.edit:hover {
            background: #f57c00;
            color: white;
        }

        .action-btn.delete {
            background: #ffebee;
            color: #d32f2f;
        }

        .action-btn.delete:hover {
            background: #d32f2f;
            color: white;
        }

        .action-btn.view {
            background: #e3f2fd;
            color: #1976d2;
        }

        .action-btn.view:hover {
            background: #1976d2;
            color: white;
        }

        .filter-section {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }

        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            align-items: end;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: var(--border-color);
        }

        .alert-custom {
            border-radius: 10px;
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 1rem;
        }

        .modal-content {
            border-radius: 15px;
            border: none;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            color: white;
            border-radius: 15px 15px 0 0;
        }

        .btn-close {
            filter: invert(1);
        }

        @media (max-width: 768px) {
            .main-container {
                margin: 10px;
            }
            
            .page-header h1 {
                font-size: 2rem;
            }
            
            .staff-grid {
                grid-template-columns: 1fr;
            }
            
            .filter-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="fa fa-user-md"></i> Medical Staff Management</h1>
            <p>Manage doctors, nurses, and medical professionals across all pharmacies</p>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-container">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon total">
                            <i class="fa fa-users"></i>
                        </div>
                        <h3 class="stat-number"><?php echo $stats['total_staff']; ?></h3>
                        <p class="stat-label">Total Staff</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon doctors">
                            <i class="fa fa-stethoscope"></i>
                        </div>
                        <h3 class="stat-number"><?php echo $stats['doctors']; ?></h3>
                        <p class="stat-label">Doctors</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon nurses">
                            <i class="fa fa-heartbeat"></i>
                        </div>
                        <h3 class="stat-number"><?php echo $stats['nurses']; ?></h3>
                        <p class="stat-label">Nurses</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon assigned">
                            <i class="fa fa-hospital-o"></i>
                        </div>
                        <h3 class="stat-number"><?php echo $stats['assigned_staff']; ?></h3>
                        <p class="stat-label">Assigned</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Section -->
        <div class="content-section">
            <!-- Add New Staff Form -->
            <div class="section-header">
                <h2 class="section-title"><i class="fa fa-plus-circle"></i> Add New Medical Staff</h2>
                <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                    <i class="fa fa-plus"></i> Add Staff Member
                </button>
            </div>

            <!-- Filters -->
            <div class="filter-section">
                <h5 class="mb-3"><i class="fa fa-filter"></i> Filter Staff</h5>
                <div class="filter-row">
                    <div class="form-group">
                        <label class="form-label">Role</label>
                        <select class="form-control" id="roleFilter">
                            <option value="">All Roles</option>
                            <option value="doctor">Doctor</option>
                            <option value="nurse">Nurse</option>
                            <option value="pharmacist">Pharmacist</option>
                            <option value="technician">Technician</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Pharmacy</label>
                        <select class="form-control" id="pharmacyFilter">
                            <option value="">All Pharmacies</option>
                            <?php foreach ($pharmacies as $pharmacy): ?>
                                <option value="<?php echo $pharmacy['pharmacy_id']; ?>"><?php echo htmlspecialchars($pharmacy['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Search</label>
                        <input type="text" class="form-control" id="searchFilter" placeholder="Search by name or specialty">
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary-custom w-100" onclick="filterStaff()">
                            <i class="fa fa-search"></i> Apply Filters
                        </button>
                    </div>
                </div>
            </div>

            <!-- Staff Grid -->
            <div id="staffGrid">
                <?php if (!empty($existingStaff)): ?>
                    <div class="staff-grid">
                        <?php foreach ($existingStaff as $staff): ?>
                            <div class="staff-card" data-role="<?php echo $staff['role']; ?>" 
                                 data-pharmacy="<?php echo $staff['pharmacy_id']; ?>" 
                                 data-name="<?php echo strtolower($staff['full_name']); ?>"
                                 data-specialty="<?php echo strtolower($staff['specialty'] ?? ''); ?>">
                                <div class="staff-header">
                                    <div class="staff-avatar <?php echo $staff['role']; ?>">
                                        <i class="fa fa-<?php echo $staff['role'] === 'doctor' ? 'stethoscope' : ($staff['role'] === 'nurse' ? 'heartbeat' : ($staff['role'] === 'pharmacist' ? 'pills' : 'cog')); ?>"></i>
                                    </div>
                                    <div class="staff-info">
                                        <h5><?php echo htmlspecialchars($staff['full_name']); ?></h5>
                                        <span class="staff-role"><?php echo ucfirst($staff['role']); ?></span>
                                    </div>
                                </div>
                                
                                <div class="staff-details">
                                    <div class="detail-item">
                                        <span class="detail-label">ID:</span>
                                        <span class="detail-value">#<?php echo $staff['staff_id']; ?></span>
                                    </div>
                                    <?php if ($staff['license_number']): ?>
                                    <div class="detail-item">
                                        <span class="detail-label">License:</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($staff['license_number']); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($staff['specialty']): ?>
                                    <div class="detail-item">
                                        <span class="detail-label">Specialty:</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($staff['specialty']); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($staff['phone']): ?>
                                    <div class="detail-item">
                                        <span class="detail-label">Phone:</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($staff['phone']); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($staff['email']): ?>
                                    <div class="detail-item">
                                        <span class="detail-label">Email:</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($staff['email']); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <div class="detail-item">
                                        <span class="detail-label">Pharmacy:</span>
                                        <span class="detail-value"><?php echo $staff['pharmacy_name'] ? htmlspecialchars($staff['pharmacy_name']) : 'Not assigned'; ?></span>
                                    </div>
                                </div>
                                
                                <div class="staff-actions">
                                    <button class="action-btn view" onclick="viewStaffDetails(<?php echo $staff['staff_id']; ?>)">
                                        <i class="fa fa-eye"></i> View
                                    </button>
                                    <button class="action-btn edit" onclick="editStaff(<?php echo $staff['staff_id']; ?>)">
                                        <i class="fa fa-edit"></i> Edit
                                    </button>
                                    <button class="action-btn delete" onclick="deleteStaff(<?php echo $staff['staff_id']; ?>, '<?php echo htmlspecialchars($staff['full_name']); ?>')">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fa fa-user-md"></i>
                        <h4>No Medical Staff Found</h4>
                        <p>Start by adding your first medical staff member to the system.</p>
                        <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                            <i class="fa fa-plus"></i> Add First Staff Member
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Add Staff Modal -->
    <div class="modal fade" id="addStaffModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa fa-user-plus"></i> Add New Medical Staff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addStaffForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" name="full_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Role *</label>
                                    <select class="form-control" name="role" required>
                                        <option value="">Select Role</option>
                                        <option value="doctor">Doctor</option>
                                        <option value="nurse">Nurse</option>
                                        <option value="pharmacist">Pharmacist</option>
                                        <option value="technician">Technician</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">License Number</label>
                                    <input type="text" class="form-control" name="license_number">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Specialty</label>
                                    <input type="text" class="form-control" name="specialty" placeholder="e.g., Cardiology, Pediatrics">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Phone</label>
                                    <input type="tel" class="form-control" name="phone">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Assigned Pharmacy</label>
                                    <select class="form-control" name="pharmacy_id">
                                        <option value="">Select Pharmacy (Optional)</option>
                                        <?php foreach ($pharmacies as $pharmacy): ?>
                                            <option value="<?php echo $pharmacy['pharmacy_id']; ?>"><?php echo htmlspecialchars($pharmacy['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary-custom" onclick="addStaff()">
                        <i class="fa fa-plus"></i> Add Staff Member
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Container -->
    <div id="alertContainer"></div>

    <script>
        // Filter functionality
        function filterStaff() {
            const roleFilter = document.getElementById('roleFilter').value;
            const pharmacyFilter = document.getElementById('pharmacyFilter').value;
            const searchFilter = document.getElementById('searchFilter').value.toLowerCase();
            
            const staffCards = document.querySelectorAll('.staff-card');
            
            staffCards.forEach(card => {
                const role = card.dataset.role;
                const pharmacy = card.dataset.pharmacy;
                const name = card.dataset.name;
                const specialty = card.dataset.specialty;
                
                let show = true;
                
                if (roleFilter && role !== roleFilter) show = false;
                if (pharmacyFilter && pharmacy !== pharmacyFilter) show = false;
                if (searchFilter && !name.includes(searchFilter) && !specialty.includes(searchFilter)) show = false;
                
                card.style.display = show ? 'block' : 'none';
            });
        }

        // Add staff functionality
        function addStaff() {
            const form = document.getElementById('addStaffForm');
            const formData = new FormData(form);
            
            // Validate required fields
            if (!formData.get('full_name') || !formData.get('role')) {
                showAlert('Please fill in all required fields', 'error');
                return;
            }
            
            // Show loading
            const submitBtn = event.target;
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Adding...';
            
            // Submit form
            fetch('php_action/add_medical_staff.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Medical staff added successfully!', 'success');
                    $('#addStaffModal').modal('hide');
                    form.reset();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                showAlert('Network error. Please try again.', 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        }

        // View staff details
        function viewStaffDetails(staffId) {
            fetch(`php_action/get_medical_staff_details.php?staff_id=${staffId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showStaffDetailsModal(data.data);
                } else {
                    showAlert('Error loading staff details: ' + data.message, 'error');
                }
            })
            .catch(error => {
                showAlert('Network error. Please try again.', 'error');
            });
        }

        // Edit staff
        function editStaff(staffId) {
            window.location.href = `edit_medical_staff.php?id=${staffId}`;
        }

        // Delete staff
        function deleteStaff(staffId, staffName) {
            if (confirm(`Are you sure you want to delete "${staffName}"?\n\nThis action cannot be undone!`)) {
                fetch('php_action/delete_medical_staff.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ staff_id: staffId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('Medical staff deleted successfully!', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showAlert('Error: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    showAlert('Network error. Please try again.', 'error');
                });
            }
        }

        // Show staff details modal
        function showStaffDetailsModal(staffData) {
            const modal = `
                <div class="modal fade" id="staffDetailsModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><i class="fa fa-user-md"></i> Staff Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6><i class="fa fa-id-card"></i> Personal Information</h6>
                                        <div class="detail-item">
                                            <span class="detail-label">Staff ID:</span>
                                            <span class="detail-value">#${staffData.staff_id}</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">Full Name:</span>
                                            <span class="detail-value">${staffData.full_name}</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">Role:</span>
                                            <span class="detail-value"><span class="staff-role">${staffData.role.toUpperCase()}</span></span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">License Number:</span>
                                            <span class="detail-value">${staffData.license_number || 'Not provided'}</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">Specialty:</span>
                                            <span class="detail-value">${staffData.specialty || 'Not specified'}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6><i class="fa fa-phone"></i> Contact Information</h6>
                                        <div class="detail-item">
                                            <span class="detail-label">Phone:</span>
                                            <span class="detail-value">${staffData.phone || 'Not provided'}</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">Email:</span>
                                            <span class="detail-value">${staffData.email || 'Not provided'}</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">Assigned Pharmacy:</span>
                                            <span class="detail-value">${staffData.pharmacy_name || 'Not assigned'}</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">Status:</span>
                                            <span class="detail-value"><span class="badge bg-success">${staffData.status || 'Active'}</span></span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">Created:</span>
                                            <span class="detail-value">${new Date(staffData.created_at).toLocaleDateString()}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary-custom" onclick="editStaff(${staffData.staff_id})">
                                    <i class="fa fa-edit"></i> Edit Staff
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', modal);
            const modalElement = new bootstrap.Modal(document.getElementById('staffDetailsModal'));
            modalElement.show();
            
            document.getElementById('staffDetailsModal').addEventListener('hidden.bs.modal', function() {
                this.remove();
            });
        }

        // Show alert function
        function showAlert(message, type) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
            
            const alertHTML = `
                <div class="alert ${alertClass} alert-custom alert-dismissible fade show" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                    <i class="fa ${icon}"></i> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            document.getElementById('alertContainer').innerHTML = alertHTML;
            
            setTimeout(() => {
                const alert = document.querySelector('.alert');
                if (alert) {
                    alert.remove();
                }
            }, 5000);
        }

        // Search functionality
        document.getElementById('searchFilter').addEventListener('input', filterStaff);
    </script>
</body>
</html>
