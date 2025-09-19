<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once './constant/connect.php';
require_once './constant/check.php';

// Check if we're in edit mode
$edit_mode = false;
$pharmacy_data = null;
$edit_pharmacy_id = null;

if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit_pharmacy_id = (int)$_GET['edit'];
    $edit_mode = true;
    
    try {
        // Fetch pharmacy data for editing
        $edit_query = "SELECT * FROM pharmacies WHERE pharmacy_id = ?";
        $edit_stmt = $connect->prepare($edit_query);
        $edit_stmt->bind_param("i", $edit_pharmacy_id);
        $edit_stmt->execute();
        $edit_result = $edit_stmt->get_result();
        
        if ($edit_result && $edit_result->num_rows > 0) {
            $pharmacy_data = $edit_result->fetch_assoc();
        } else {
            // Pharmacy not found, redirect to manage page
            header("Location: manage_pharmacies.php?error=Pharmacy not found");
            exit();
        }
    } catch (Exception $e) {
        error_log("Error fetching pharmacy data: " . $e->getMessage());
        header("Location: manage_pharmacies.php?error=Error loading pharmacy data");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include './constant/layout/head.php'; ?>
    <title><?php echo $edit_mode ? 'Edit Pharmacy' : 'Create Pharmacy'; ?> - MdLink Rwanda</title>
    <style>
        /* Clean Minimal Design System */
        :root {
            --primary-color: #276749;
            --primary-light: #e6f4ea;
            --text-color: #333333;
            --text-muted: #666666;
            --border-color: #e0e0e0;
            --background-light: #f8f9fa;
            --white: #ffffff;
            --border-radius: 6px;
            --box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            --transition: all 0.2s ease;
        }

        /* Main Layout - Bold and Prominent */
        .bg-gradient-primary {
            background: var(--primary-color);
            border: 3px solid var(--primary-color);
        }
        
        .card {
            border: 2px solid var(--primary-color);
            border-radius: var(--border-radius);
            box-shadow: 0 4px 16px rgba(39, 103, 73, 0.15);
            transition: var(--transition);
            margin-bottom: 1.5rem;
            background: var(--white);
        }
        
        .card:hover {
            box-shadow: 0 8px 24px rgba(39, 103, 73, 0.25);
            transform: translateY(-2px);
        }
        
        .card-header {
            background: var(--primary-color);
            color: white;
            border-bottom: 3px solid var(--primary-color);
            border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
            padding: 1.25rem 1.5rem;
        }

        .card-header h4 {
            color: white;
            font-weight: 700;
            margin: 0;
            font-size: 1.2rem;
        }
        
        /* Form Styling - Clean and Minimal */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            font-weight: 500;
            color: var(--text-color);
            margin-bottom: 0.5rem;
            display: block;
            font-size: 0.9rem;
        }

        .form-control {
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 0.75rem 1rem;
            transition: var(--transition);
            font-size: 0.9rem;
            background: var(--white);
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(39, 103, 73, 0.1);
            outline: none;
        }

        .form-control.is-valid {
            border-color: #28a745;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 0.8rem;
            margin-top: 0.25rem;
        }

        .valid-feedback {
            display: block;
            color: #28a745;
            font-size: 0.8rem;
            margin-top: 0.25rem;
        }

        /* Statistics Cards - Bold and Prominent */
        .stats-card {
            background: var(--white);
            border: 2px solid var(--primary-color);
            border-left: 6px solid var(--primary-color);
            border-radius: var(--border-radius);
            padding: 1.75rem;
            text-align: center;
            transition: var(--transition);
            box-shadow: 0 4px 16px rgba(39, 103, 73, 0.1);
        }

        .stats-card:hover {
            box-shadow: 0 8px 24px rgba(39, 103, 73, 0.2);
            transform: translateY(-3px);
        }

        .stats-card h4 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0;
            line-height: 1;
            text-shadow: 0 2px 4px rgba(39, 103, 73, 0.1);
        }
        
        .stats-card p {
            color: var(--text-muted);
            margin: 0.5rem 0 0 0;
            font-size: 0.9rem;
        }

        .stats-card i {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 0.75rem;
            text-shadow: 0 2px 4px rgba(39, 103, 73, 0.1);
        }

        /* Modal - Bold Design */
        .modal-content {
            border: 3px solid var(--primary-color);
            border-radius: var(--border-radius);
            box-shadow: 0 12px 48px rgba(39, 103, 73, 0.25);
        }

        .modal-header {
            background: var(--primary-color);
            color: white;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            border-bottom: 3px solid var(--primary-color);
            padding: 1.5rem 2rem;
        }

        .modal-header .modal-title {
            font-weight: 700;
            font-size: 1.3rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .modal-header .close {
            color: white;
            opacity: 0.8;
            font-size: 1.25rem;
        }

        .modal-header .close:hover {
            opacity: 1;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            border-top: 1px solid var(--border-color);
            padding: 1.25rem 1.5rem;
            background: var(--primary-light);
            border-radius: 0 0 var(--border-radius) var(--border-radius);
        }

        /* Buttons - Bold and Prominent */
        .btn {
            border-radius: var(--border-radius);
            font-weight: 600;
            padding: 0.875rem 1.5rem;
            transition: var(--transition);
            border: 2px solid transparent;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            box-shadow: 0 4px 12px rgba(39, 103, 73, 0.3);
        }

        .btn-primary:hover {
            background: #1e4d36;
            border-color: #1e4d36;
            box-shadow: 0 6px 16px rgba(39, 103, 73, 0.4);
            transform: translateY(-1px);
        }

        .btn-success {
            background: #28a745;
            border-color: #28a745;
            color: white;
        }

        .btn-success:hover {
            background: #218838;
            border-color: #1e7e34;
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
            background: transparent;
            border-width: 2px;
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            box-shadow: 0 4px 12px rgba(39, 103, 73, 0.3);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: var(--text-muted);
            border-color: var(--text-muted);
            color: white;
        }

        .btn-secondary:hover {
            background: #555555;
            border-color: #555555;
        }

        /* Table - Clean and Simple */
        .table {
            border-radius: var(--border-radius);
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        .table thead th {
            background: var(--primary-light);
            color: var(--primary-color);
            font-weight: 600;
            border: none;
            padding: 1rem;
            font-size: 0.9rem;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-color: var(--border-color);
            color: var(--text-color);
        }

        .table tbody tr:hover {
            background-color: var(--background-light);
        }

        /* Badges - Simple and Clean */
        .badge {
            font-size: 0.75rem;
            padding: 0.4rem 0.75rem;
            border-radius: 12px;
            font-weight: 500;
        }

        .badge-primary {
            background: var(--primary-color);
            color: white;
        }

        .badge-success {
            background: #28a745;
            color: white;
        }

        .badge-info {
            background: #17a2b8;
            color: white;
        }

        /* Alerts - Clean Design */
        .alert {
            border: 1px solid transparent;
            border-radius: var(--border-radius);
            padding: 1rem 1.25rem;
            margin-bottom: 1.25rem;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }

        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border-color: #bee5eb;
        }

        /* Loading States */
        .btn-loading {
            position: relative;
            color: transparent !important;
        }

        .btn-loading::after {
            content: "";
            position: absolute;
            width: 16px;
            height: 16px;
            top: 50%;
            left: 50%;
            margin-left: -8px;
            margin-top: -8px;
            border: 2px solid transparent;
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Progress Indicator - Clean Design */
        .progress-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            padding: 0 1rem;
        }

        .progress-step {
            flex: 1;
            text-align: center;
            position: relative;
        }

        .progress-step::after {
            content: '';
            position: absolute;
            top: 15px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: var(--border-color);
            z-index: 1;
        }

        .progress-step:last-child::after {
            display: none;
        }

        .progress-step.active::after {
            background: var(--primary-color);
        }

        .progress-circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--border-color);
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.5rem;
            font-weight: 600;
            position: relative;
            z-index: 2;
            font-size: 0.8rem;
        }

        .progress-step.active .progress-circle {
            background: var(--primary-color);
            color: white;
        }

        .progress-step.completed .progress-circle {
            background: #28a745;
            color: white;
        }

        /* Form Section Styling - Clean */
        .form-section {
            background: var(--white);
            border: 2px solid var(--primary-color);
            border-radius: var(--border-radius);
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 16px rgba(39, 103, 73, 0.1);
        }

        .form-section h6 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 3px solid var(--primary-color);
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Compact clearer selects in grid */
        #createPharmacyModal .form-group label { font-weight: 600; }
        #createPharmacyModal select.form-control { background-color: #fff; }
        #createPharmacyModal .form-section .row + .row { margin-top: .25rem; }
        #createPharmacyModal .form-text { color: var(--text-muted); }

        /* Loading state for selects */
        .is-loading { position: relative; }
        .is-loading::after {
            content: '\f110';
            font-family: FontAwesome;
            position: absolute;
            right: 12px; top: 50%; transform: translateY(-50%);
            animation: spin 1s linear infinite;
            color: var(--primary-color);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .modal-dialog {
                margin: 0.5rem;
            }
            
            .modal-body {
                padding: 1rem;
            }
            
            .stats-card h4 {
                font-size: 1.5rem;
            }
            
            .progress-indicator {
                margin-bottom: 1.5rem;
            }
        }

        /* Animation Classes - Subtle */
        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Input Group Styling */
        .input-group .form-control {
            border-right: none;
        }

        .input-group-append .btn {
            border-left: none;
        }

        /* Text Colors - Consistent */
        .text-primary {
            color: var(--primary-color) !important;
        }

        .text-muted {
            color: var(--text-muted) !important;
        }

        /* Remove excessive colors from existing elements */
        .card.bg-primary,
        .card.bg-success,
        .card.bg-info,
        .card.bg-warning { 
            background: var(--white) !important; 
            color: var(--text-color) !important; 
            border: 1px solid var(--border-color) !important;
        }

        .card.bg-primary i,
        .card.bg-success i,
        .card.bg-info i,
        .card.bg-warning i { 
            color: var(--primary-color) !important; 
        }

        .card.bg-primary h4,
        .card.bg-success h4,
        .card.bg-info h4,
        .card.bg-warning h4 { 
            color: var(--primary-color) !important; 
        }

        .card.bg-primary p,
        .card.bg-success p,
        .card.bg-info p,
        .card.bg-warning p { 
            color: var(--text-muted) !important; 
        }

        /* Table header cleanup */
        .thead-dark th { 
            background: var(--background-light) !important; 
            color: var(--text-color) !important; 
        }

        /* Badge cleanup */
        .badge-primary,
        .badge-info,
        .badge-success,
        .badge-warning { 
            background: var(--primary-color) !important; 
            color: white !important; 
        }

        /* Button cleanup */
        .btn-outline-primary,
        .btn-outline-warning,
        .btn-outline-danger { 
            color: var(--primary-color); 
            border-color: var(--primary-color); 
        }
        
        .btn-outline-primary:hover,
        .btn-outline-warning:hover,
        .btn-outline-danger:hover { 
            background: var(--primary-color); 
            color: white; 
        }

        /* Text color cleanup */
        .text-primary, .text-info, .text-success, .text-danger { 
            color: var(--text-color) !important; 
        }

        /* Header/labels cleanup */
        .card-header h4, .card-header, .card-body, h6, label { 
            color: var(--text-color); 
        }
    </style>
</head>
<body>
    <?php include './constant/layout/header.php'; ?>
    <?php include './constant/layout/sidebar.php'; ?>
    
    <div class="page-wrapper">
        <div class="container-fluid">
            
            <!-- Hero Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card text-white" style="background-color: #276749;">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h2 class="mb-2"><i class="fa fa-hospital-o"></i> <?php echo $edit_mode ? 'Edit Pharmacy' : 'Pharmacy Management'; ?></h2>
                                    <p class="mb-0" style="font-weight: bold; color: white;"><?php echo $edit_mode ? 'Update pharmacy information and settings.' : 'Create new pharmacies and manage their basic information.'; ?></p>
                                </div>
                                <div class="col-md-4 text-right">
                                    <?php if ($edit_mode): ?>
                                        <a href="manage_pharmacies.php" class="btn btn-light btn-lg">
                                            <i class="fa fa-arrow-left"></i> Back to Manage
                                        </a>
                                    <?php else: ?>
                                    <button id="openCreatePharmacy" class="btn btn-light btn-lg" data-toggle="modal" data-target="createPharmacyModal" data-bs-toggle="modal" data-bs-target="#createPharmacyModal" type="button">
                                        <i class="fa fa-plus"></i> Create New Pharmacy
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            // Fetch existing pharmacies for display
            $existingPharmacies = [];
            $totalPharmacies = 0;
            $totalAdmins = 0;
            $totalMedicines = 0;
            $databaseError = false;
            
            try {
                // Check if tables exist first
                $tables_check = $connect->query("SHOW TABLES LIKE 'pharmacies'");
                if ($tables_check->num_rows == 0) {
                    throw new Exception("Pharmacies table does not exist");
                }
                
                // First check if admin_users table has pharmacy_id column
                $admin_columns = $connect->query("SHOW COLUMNS FROM admin_users LIKE 'pharmacy_id'");
                $has_pharmacy_id = $admin_columns && $admin_columns->num_rows > 0;
                
                if ($has_pharmacy_id) {
                    // Use pharmacy_id column if it exists
                $query = "
                    SELECT p.*, 
                               COALESCE(COUNT(DISTINCT au.admin_id), 0) as admin_count,
                               COALESCE(COUNT(DISTINCT m.medicine_id), 0) as medicine_count
                    FROM pharmacies p
                    LEFT JOIN admin_users au ON p.pharmacy_id = au.pharmacy_id
                    LEFT JOIN medicines m ON p.pharmacy_id = m.pharmacy_id
                        GROUP BY p.pharmacy_id, p.name, p.license_number, p.contact_person, p.contact_phone, p.location, p.created_at
                    ORDER BY p.created_at DESC
                    LIMIT 10
                ";
                } else {
                    // Fallback query without pharmacy_id join
                    $query = "
                        SELECT p.*, 
                               0 as admin_count,
                               COALESCE(COUNT(DISTINCT m.medicine_id), 0) as medicine_count
                        FROM pharmacies p
                        LEFT JOIN medicines m ON p.pharmacy_id = m.pharmacy_id
                        GROUP BY p.pharmacy_id, p.name, p.license_number, p.contact_person, p.contact_phone, p.location, p.created_at
                        ORDER BY p.created_at DESC
                        LIMIT 10
                    ";
                }
                
                $result = $connect->query($query);
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $existingPharmacies[] = $row;
                        $totalPharmacies++;
                        $totalAdmins += (int)$row['admin_count'];
                        $totalMedicines += (int)$row['medicine_count'];
                    }
                } else {
                    // No pharmacies found, but database is working
                    $existingPharmacies = [];
                }
                
                // Get total counts for statistics
                $total_query = "
                    SELECT 
                        (SELECT COUNT(*) FROM pharmacies) as total_pharmacies,
                        (SELECT COUNT(*) FROM admin_users) as total_admins,
                        (SELECT COUNT(*) FROM medicines) as total_medicines
                ";
                $total_result = $connect->query($total_query);
                if ($total_result && $total_result->num_rows > 0) {
                    $total_row = $total_result->fetch_assoc();
                    $totalPharmacies = (int)$total_row['total_pharmacies'];
                    $totalAdmins = (int)$total_row['total_admins'];
                    $totalMedicines = (int)$total_row['total_medicines'];
                }
                
            } catch (Exception $e) {
                $databaseError = true;
                error_log("Database error in create_pharmacy.php: " . $e->getMessage());
                
                // Show detailed error message with solutions
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
                echo '<i class="fa fa-exclamation-triangle"></i> <strong>Database Connection Issue:</strong> ';
                echo 'Unable to fetch pharmacy data from database. ';
                echo '<br><strong>Error:</strong> ' . htmlspecialchars($e->getMessage());
                echo '<br><strong>Quick Fixes:</strong>';
                echo '<ul class="mb-0 mt-2">';
                echo '<li>Make sure XAMPP MySQL is running</li>';
                echo '<li>Check if database "mdlink2" exists</li>';
                echo '<li><a href="setup_database.php" class="alert-link">Run Database Setup</a></li>';
                echo '<li><a href="diagnose_database.php" class="alert-link">Run Database Diagnostic</a></li>';
                echo '</ul>';
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
                echo '<span aria-hidden="true">&times;</span>';
                echo '</button>';
                echo '</div>';
            }
            ?>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stats-card">
                        <i class="fa fa-hospital-o"></i>
                        <h4><?php echo $totalPharmacies; ?></h4>
                        <p>Total Pharmacies</p>
                                </div>
                                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <i class="fa fa-users"></i>
                        <h4><?php echo $totalAdmins; ?></h4>
                        <p>Admin Accounts</p>
                            </div>
                        </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <i class="fa fa-medkit"></i>
                        <h4><?php echo $totalMedicines; ?></h4>
                        <p>Total Medicines</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <i class="fa fa-check-circle"></i>
                        <h4>Active</h4>
                        <p>System Status</p>
                    </div>
                </div>
            </div>

            <?php if ($edit_mode): ?>
            <!-- Edit Pharmacy Form -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0"><i class="fa fa-edit"></i> Edit Pharmacy: <?php echo htmlspecialchars($pharmacy_data['name']); ?></h4>
                        </div>
                        <div class="card-body">
                            <div id="editPharmacyMessage"></div>
                            
                            <form id="editPharmacyForm">
                                <input type="hidden" name="pharmacy_id" value="<?php echo $pharmacy_data['pharmacy_id']; ?>">
                                
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
                                            <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($pharmacy_data['name']); ?>" required>
                            </div>
                        </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><i class="fa fa-id-badge"></i> License Number *</label>
                                            <input type="text" class="form-control" name="license_number" value="<?php echo htmlspecialchars($pharmacy_data['license_number']); ?>" required>
                    </div>
                </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><i class="fa fa-user-md"></i> Contact Person *</label>
                                            <input type="text" class="form-control" name="contact_person" value="<?php echo htmlspecialchars($pharmacy_data['contact_person']); ?>" required>
                                </div>
                            </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><i class="fa fa-phone"></i> Contact Phone *</label>
                                            <input type="tel" class="form-control" name="contact_phone" value="<?php echo htmlspecialchars($pharmacy_data['contact_phone']); ?>" required>
                        </div>
                    </div>
                </div>
                                
                                <!-- Location Information -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <h6 class="text-info mb-3"><i class="fa fa-map-marker"></i> Location Information</h6>
                                </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label><i class="fa fa-map-marker"></i> Current Location</label>
                                            <input type="text" class="form-control" name="location" value="<?php echo htmlspecialchars($pharmacy_data['location']); ?>" required>
                                            <small class="form-text text-muted">Full address of the pharmacy</small>
                            </div>
                        </div>
                    </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label><i class="fa fa-map-signs"></i> Additional Address Details</label>
                                            <input type="text" class="form-control" name="address_details" placeholder="Street name, building number, etc.">
                                            <small class="form-text text-muted">Optional: Add specific street or building details</small>
                </div>
            </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-6">
                                    <a href="manage_pharmacies.php" class="btn btn-secondary">
                                        <i class="fa fa-arrow-left"></i> Cancel
                                    </a>
                                </div>
                                <div class="col-md-6 text-right">
                                    <button type="button" class="btn btn-primary" id="btnUpdatePharmacy">
                                        <i class="fa fa-save"></i> Update Pharmacy
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Existing Pharmacies Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0" style="color: white;"><i class="fa fa-list"></i> Recent Pharmacies</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
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
                                        <?php if (!empty($existingPharmacies)): ?>
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
                                                    <i class="fa fa-map-marker"></i>
                                                    <?php echo htmlspecialchars($pharmacy['location'] ?: 'Location not specified'); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <div><i class="fa fa-user"></i> <?php echo htmlspecialchars($pharmacy['contact_person']); ?></div>
                                                    <div><i class="fa fa-phone"></i> <?php echo htmlspecialchars($pharmacy['contact_phone']); ?></div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-primary"><?php echo (int)$pharmacy['admin_count']; ?> accounts</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-primary"><?php echo (int)$pharmacy['medicine_count']; ?> medicines</span>
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
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4">
                                                    <i class="fa fa-hospital-o fa-3x mb-3"></i>
                                                    <h5>No Pharmacies Found</h5>
                                                    <p>Start by creating your first pharmacy using the "Create New Pharmacy" button above.</p>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Create Pharmacy Modal -->
            <div class="modal fade" id="createPharmacyModal" tabindex="-1" role="dialog" aria-labelledby="createPharmacyModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createPharmacyModalLabel">
                                <i class="fa fa-plus-circle"></i> Create New Pharmacy
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div id="pharmacyMessage"></div>
                            
                            
                            <form id="createPharmacyForm" novalidate>
                                <!-- Pharmacy Information Section -->
                                <div class="form-section">
                                    <h6><i class="fa fa-hospital-o"></i> Pharmacy Information</h6>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                                <label for="pharmacy_name">
                                                    <i class="fa fa-hospital-o"></i> Pharmacy Name *
                                                </label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="pharmacy_name" 
                                                       name="name" 
                                                       placeholder="e.g., Ineza Pharmacy" 
                                                       required
                                                       minlength="3"
                                                       maxlength="100">
                                                <div class="invalid-feedback">
                                                    Please enter a valid pharmacy name (3-100 characters).
                                                </div>
                                                <div class="valid-feedback">
                                                    Looks good!
                                                </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                                <label for="license_number">
                                                    <i class="fa fa-id-badge"></i> License Number *
                                                </label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="license_number" 
                                                       name="license_number" 
                                                       placeholder="e.g., RL-2024-XXX" 
                                                       required>
                                                <div class="invalid-feedback">
                                                    Please enter a valid license number.
                                                </div>
                                                <div class="valid-feedback">
                                                    Looks good!
                                                </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                                <label for="contact_person">
                                                    <i class="fa fa-user"></i> Contact Person *
                                                </label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="contact_person" 
                                                       name="contact_person" 
                                                       placeholder="e.g., Dr. Jean Bosco" 
                                                       required
                                                       minlength="3"
                                                       maxlength="100">
                                                <div class="invalid-feedback">
                                                    Please enter a valid contact person name (3-100 characters).
                                                </div>
                                                <div class="valid-feedback">
                                                    Looks good!
                                                </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                                <label for="contact_phone">
                                                    <i class="fa fa-phone"></i> Contact Phone *
                                                </label>
                                                <input type="tel" 
                                                       class="form-control" 
                                                       id="contact_phone" 
                                                       name="contact_phone" 
                                                       placeholder="+250 788 XXX XXX" 
                                                       required
                                                       pattern="^[\+]?[0-9\s\-\(\)]{10,}$">
                                                <div class="invalid-feedback">
                                                    Please enter a valid phone number (minimum 10 digits).
                                        </div>
                                                <div class="valid-feedback">
                                                    Looks good!
                                    </div>
                                </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Rwanda Location Section -->
                                <div class="form-section">
                                    <h6><i class="fa fa-map-marker"></i> Rwanda Location</h6>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                                <label for="province">
                                                    <i class="fa fa-map"></i> Province *
                                                </label>
                                            <select class="form-control" id="province" name="province" required>
                                                <option value="">Select Province</option>
                                            </select>
                                                <div class="invalid-feedback">
                                                    Please select a province.
                                                </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                                <label for="district">
                                                    <i class="fa fa-building"></i> District *
                                                </label>
                                            <select class="form-control" id="district" name="district" required disabled>
                                                <option value="">Select District</option>
                                            </select>
                                                <div class="invalid-feedback">
                                                    Please select a district.
                                                </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                                <label for="sector">
                                                    <i class="fa fa-map-pin"></i> Sector *
                                                </label>
                                            <select class="form-control" id="sector" name="sector" required disabled>
                                                <option value="">Select Sector</option>
                                            </select>
                                                <div class="invalid-feedback">
                                                    Please select a sector.
                                                </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                                <label for="cell">
                                                    <i class="fa fa-home"></i> Cell *
                                                </label>
                                            <select class="form-control" id="cell" name="cell" required disabled>
                                                <option value="">Select Cell</option>
                                            </select>
                                                <div class="invalid-feedback">
                                                    Please select a cell.
                                                </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                                <label for="village">
                                                    <i class="fa fa-map-signs"></i> Village *
                                                </label>
                                            <select class="form-control" id="village" name="village" required disabled>
                                                <option value="">Select Village</option>
                                            </select>
                                                <div class="invalid-feedback">
                                                    Please select a village.
                                                </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                                <label for="full_location">
                                                    <i class="fa fa-map"></i> Full Location (auto-filled)
                                                </label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="full_location" 
                                                       name="full_location" 
                                                       placeholder="Province / District / Sector / Cell / Village"
                                                       readonly
                                                       style="background-color: #f8f9fa;">
                                                <small class="form-text text-muted">
                                                    This field is automatically filled based on your selections above
                                                </small>
                                            </div>
                                        </div>
                                </div>
                                </div>
                                
                                <!-- Manager Account Section -->
                                <div class="form-section">
                                    <h6 style="color: #28a745;"><i class="fa fa-user"></i> Manager Account</h6>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="manager_email">
                                                    <i class="fa fa-envelope"></i> Manager Email *
                                                </label>
                                                <input type="email" 
                                                       class="form-control" 
                                                       id="manager_email" 
                                                       name="manager_email" 
                                                       placeholder="manager@pharmacy.com" 
                                                       required>
                                                <div class="invalid-feedback">
                                                    Please enter a valid email address.
                                                </div>
                                                <div class="valid-feedback">
                                                    Looks good!
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="manager_password">
                                                    <i class="fa fa-lock"></i> Manager Password *
                                                </label>
                                                <input type="password" 
                                                       class="form-control" 
                                                       id="manager_password" 
                                                       name="manager_password" 
                                                       placeholder="Minimum 8 characters" 
                                                       required
                                                       minlength="8">
                                                <div class="invalid-feedback">
                                                    Password must be at least 8 characters long.
                                                </div>
                                                <div class="valid-feedback">
                                                    Looks good!
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Hidden field to store complete location -->
                                <input type="hidden" id="complete_location" name="location" required>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Cancel
                            </button>
                            <button type="button" class="btn btn-primary" id="btnCreatePharmacy">
                                <i class="fa fa-plus"></i> Create Pharmacy
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php include './constant/layout/footer.php'; ?>

    <script>
    $(document).ready(function() {
        // Global variables
        
        // Initialize location dropdowns
        initializeLocationDropdowns();
        
        // Initialize modal plugin explicitly (Bootstrap 3/4/5 compatible)
        var $modal = $('#createPharmacyModal');
        if (typeof $modal.modal === 'function') {
            $modal.modal({backdrop: true, keyboard: true, show: false});
        }

        // Robust opener: works across Bootstrap versions and even if plugin fails
        function forceOpenCreatePharmacyModal(){
            var $modal = $('#createPharmacyModal');
            // ensure modal is attached to body
            if (!$modal.parent().is('body')) { $modal.appendTo('body'); }
            try {
                if (typeof $modal.modal === 'function') {
                    $modal.modal('show');
                } else {
                    $modal.addClass('show').attr({'aria-hidden':'false','style':'display:block'});
                    if ($('.modal-backdrop').length === 0) {
                        $('body').append('<div class="modal-backdrop fade show"></div>');
                    }
                    $('body').addClass('modal-open');
                }
            } catch(err) {
                $modal.addClass('show').attr({'aria-hidden':'false','style':'display:block'});
                if ($('.modal-backdrop').length === 0) {
                    $('body').append('<div class="modal-backdrop fade show"></div>');
                }
                $('body').addClass('modal-open');
            }
        }

        $(document).on('click', '#openCreatePharmacy,[data-target="#createPharmacyModal"],[data-bs-target="#createPharmacyModal"]', function(e){
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            forceOpenCreatePharmacyModal();
            return false;
        });
        
        // Debug: Check if we're in edit mode
        console.log('Page loaded. Edit mode:', <?php echo $edit_mode ? 'true' : 'false'; ?>);
        console.log('Update button exists:', $('#btnUpdatePharmacy').length > 0);
        
        // Clear form when modal opens
        $('#createPharmacyModal').on('show.bs.modal shown.bs.modal', function(){
            resetForm();
        });

        // Update full location field
        function updateFullLocation() {
            const province = $('#province').val();
            const district = $('#district').val();
            const sector = $('#sector').val();
            const cell = $('#cell').val();
            const village = $('#village').val();
            
            let locationText = '';
            const locationParts = [];
            
            if (village) locationParts.push(village);
            if (cell) locationParts.push(cell);
            if (sector) locationParts.push(sector);
            if (district) locationParts.push(district);
            if (province) locationParts.push(province);
            
            if (locationParts.length > 0) {
                locationText = locationParts.join(' / ');
            } else {
                locationText = 'Province / District / Sector / Cell / Village';
            }
            
            $('#full_location').val(locationText);
            $('#complete_location').val(locationText);
        }

        // Reset form
        function resetForm() {
            $('#createPharmacyForm')[0].reset();
            $('#pharmacyMessage').html('');
            resetLocationDropdowns();
            updateFullLocation();
        }

        // Show alert
        function showAlert(message, type) {
            const alertClass = type === 'danger' ? 'alert-danger' : 'alert-success';
            const icon = type === 'danger' ? 'fa-exclamation-triangle' : 'fa-check-circle';
            
            $('#pharmacyMessage').html(`
                <div class="alert ${alertClass} alert-dismissible fade show">
                    <i class="fa ${icon}"></i> ${message}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `);
        }

        // Ensure cancel always closes (supports Bootstrap 3/4/5)
        $('#btnCancelCreatePharmacy').on('click', function(e){
            e.preventDefault();
            e.stopPropagation();
            var $modal = $('#createPharmacyModal');
            if (typeof $modal.modal === 'function') {
                $modal.modal('hide');
            } else {
                $modal.removeClass('show').attr('aria-hidden','true').hide();
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css('padding-right','');
            }
            return false;
        });

        // Catch-all: any element with data-dismiss/data-bs-dismiss will close any open modal
        $(document).on('click','[data-dismiss="modal"],[data-bs-dismiss="modal"]', function(ev){
            var $m = $(this).closest('.modal');
            if ($m.length && typeof $m.modal === 'function') {
                $m.modal('hide');
            } else {
                $('.modal.show').removeClass('show').attr('aria-hidden','true').hide();
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css('padding-right','');
            }
        });
        
        // Handle form submission
        $('#btnCreatePharmacy').on('click', function() {
            const form = document.getElementById('createPharmacyForm');
            const btn = this;
            
            // Validate form
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            // Prepare form data
            const formData = new FormData(form);
            
            // Disable button and show loading
            $(btn).addClass('btn-loading').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Creating Pharmacy...');
            
            // Submit form
            $.ajax({
                url: 'php_action/create_pharmacy.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        showAlert('Pharmacy created successfully! Redirecting...', 'success');
                        
                        // Show success notification
                        showNotification('Pharmacy created successfully!', 'success');
                        
                        // Close modal after 2 seconds and reload page
                        setTimeout(() => {
                            $('#createPharmacyModal').modal('hide');
                            location.reload();
                        }, 2000);
                    } else {
                        // Show error message
                        showAlert(response.message || 'Failed to create pharmacy. Please try again.', 'danger');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', xhr.responseText);
                    let errorMessage = 'Network error. Please try again.';
                    
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMessage = response.message;
                        }
                    } catch (e) {
                        // Use default error message
                    }
                    
                    showAlert(errorMessage, 'danger');
                },
                complete: function() {
                    // Re-enable button
                    $(btn).removeClass('btn-loading').prop('disabled', false).html('<i class="fa fa-plus"></i> Create Pharmacy');
                }
            });
        });

        // Show notification function
        function showNotification(message, type) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
            
            const notification = $(`
                <div class="alert ${alertClass} alert-dismissible fade show" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 500px;">
                    <i class="fa ${icon}"></i> ${message}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `);
            
            $('body').append(notification);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                notification.fadeOut(() => notification.remove());
            }, 5000);
        }
        
        // Real-time validation
        $('#pharmacy_name, #contact_person, #contact_phone').on('input blur', function() {
            const field = $(this);
            const value = field.val().trim();
            
            if (field.attr('id') === 'contact_phone') {
                const phonePattern = /^[\+]?[0-9\s\-\(\)]{10,}$/;
                if (value && phonePattern.test(value)) {
                    field.removeClass('is-invalid').addClass('is-valid');
                } else if (value) {
                    field.removeClass('is-valid').addClass('is-invalid');
                } else {
                    field.removeClass('is-valid is-invalid');
                }
            } else {
                if (value && value.length >= 3) {
                    field.removeClass('is-invalid').addClass('is-valid');
                } else if (value) {
                    field.removeClass('is-valid').addClass('is-invalid');
                } else {
                    field.removeClass('is-valid is-invalid');
                }
            }
        });
        
        // Clear messages when modal is closed
        $('#createPharmacyModal').on('hidden.bs.modal', function () {
            $('#pharmacyMessage').html('');
            $('#createPharmacyForm')[0].reset();
            // Reset location dropdowns
            resetLocationDropdowns();
            $('#license_number').val('');
            // Reset form state
            resetForm();
        });
        
        // Add some interactivity to the table rows
        $('tbody tr').on('mouseenter', function() {
            $(this).css('background-color', '#f8f9fa');
        }).on('mouseleave', function() {
            $(this).css('background-color', '');
        });
        
        // Handle edit pharmacy form submission - Multiple approaches for reliability
        function handleUpdatePharmacy() {
            console.log('Update button clicked!');
            
            const form = document.getElementById('editPharmacyForm');
            if (!form) {
                console.error('Edit form not found!');
                alert('Edit form not found!');
                return false;
            }
            
            // Validate form
            if (!form.checkValidity()) {
                console.log('Form validation failed');
                form.reportValidity();
                return false;
            }
            
            console.log('Form validation passed, submitting...');
            
            const btn = document.getElementById('btnUpdatePharmacy');
            const originalText = btn.innerHTML;
            
            // Disable button and show loading
            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Updating...';
            
            // Collect form data
            const formData = new FormData(form);
            
            // Log form data for debugging
            console.log('Form data:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }
            
            // Submit form using fetch API for better error handling
            fetch('php_action/update_pharmacy.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.text();
            })
            .then(data => {
                console.log('Raw response:', data);
                
                try {
                    const response = JSON.parse(data);
                    console.log('Parsed response:', response);
                    
                    if (response.success) {
                        // Show success message
                        $('#editPharmacyMessage').html(
                            '<div class="alert alert-success alert-dismissible fade show">' +
                            '<i class="fa fa-check-circle"></i> <strong>Success!</strong> Pharmacy updated successfully!' +
                            '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                            '</div>'
                        );
                        
                        // Show success notification
                        showNotification('Pharmacy updated successfully!', 'success');
                        
                        // Redirect to manage page after 3 seconds
                        setTimeout(() => {
                            window.location.href = 'manage_pharmacies.php?success=Pharmacy updated successfully';
                        }, 3000);
                    } else {
                        // Show error message
                        $('#editPharmacyMessage').html(
                            '<div class="alert alert-danger alert-dismissible fade show">' +
                            '<i class="fa fa-exclamation-triangle"></i> <strong>Error!</strong> ' + 
                            (response.message || 'Failed to update pharmacy') +
                            '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                            '</div>'
                        );
                        showNotification('Update failed: ' + (response.message || 'Unknown error'), 'error');
                    }
                } catch (e) {
                    console.error('JSON parse error:', e);
                    $('#editPharmacyMessage').html(
                        '<div class="alert alert-danger alert-dismissible fade show">' +
                        '<i class="fa fa-exclamation-triangle"></i> <strong>Error!</strong> Invalid response from server: ' + data +
                        '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                        '</div>'
                    );
                    showNotification('Update failed: Invalid server response', 'error');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                $('#editPharmacyMessage').html(
                    '<div class="alert alert-danger alert-dismissible fade show">' +
                    '<i class="fa fa-exclamation-triangle"></i> <strong>Error!</strong> Network error: ' + error.message +
                    '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                    '</div>'
                );
                showNotification('Update failed: Network error', 'error');
            })
            .finally(() => {
                // Re-enable button
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
            
            return false;
        }
        
        // Attach event handlers
        $(document).on('click', '#btnUpdatePharmacy', function(e) {
            e.preventDefault();
            e.stopPropagation();
            return handleUpdatePharmacy();
        });
        
        // Also attach to form submit
        $(document).on('submit', '#editPharmacyForm', function(e) {
            e.preventDefault();
            e.stopPropagation();
            return handleUpdatePharmacy();
        });
        
        // Function to show notifications
        function showNotification(message, type) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
            
            const notification = $(`
                <div class="alert ${alertClass} alert-dismissible fade show" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 500px;">
                    <i class="fa ${icon}"></i> ${message}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `);
            
            $('body').append(notification);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                notification.fadeOut(() => notification.remove());
            }, 5000);
        }
    });
    
    // Location dropdowns functionality
    function initializeLocationDropdowns() {
        const provinceSelect = $('#province');
        const districtSelect = $('#district');
        const sectorSelect = $('#sector');
        const cellSelect = $('#cell');
        const villageSelect = $('#village');
        const completeLocationInput = $('#complete_location');
        
        // Load provinces on page load
        loadProvinces();
        
        // Province change handler
        provinceSelect.on('change', function() {
            const province = $(this).val();
            resetSelect(districtSelect, 'Select District');
            resetSelect(sectorSelect, 'Select Sector');
            resetSelect(cellSelect, 'Select Cell');
            resetSelect(villageSelect, 'Select Village');
            updateFullLocation();
            
            if (province) {
                loadDistricts(province);
            }
        });
        
        // District change handler
        districtSelect.on('change', function() {
            const province = provinceSelect.val();
            const district = $(this).val();
            resetSelect(sectorSelect, 'Select Sector');
            resetSelect(cellSelect, 'Select Cell');
            resetSelect(villageSelect, 'Select Village');
            updateFullLocation();
            
            if (province && district) {
                loadSectors(province, district);
            }
        });
        
        // Sector change handler
        sectorSelect.on('change', function() {
            const province = provinceSelect.val();
            const district = districtSelect.val();
            const sector = $(this).val();
            resetSelect(cellSelect, 'Select Cell');
            resetSelect(villageSelect, 'Select Village');
            updateFullLocation();
            
            if (province && district && sector) {
                loadCells(province, district, sector);
            }
        });
        
        // Cell change handler
        cellSelect.on('change', function() {
            const province = provinceSelect.val();
            const district = districtSelect.val();
            const sector = sectorSelect.val();
            const cell = $(this).val();
            resetSelect(villageSelect, 'Select Village');
            updateFullLocation();
            
            if (province && district && sector && cell) {
                loadVillages(province, district, sector, cell);
            }
        });
        
        // Village change handler
        villageSelect.on('change', function() {
            updateFullLocation();
        });
    }
    
    function resetSelect(select, placeholder) {
        select.html(`<option value="">${placeholder}</option>`).prop('disabled', true);
    }
    
    function loadProvinces() {
        var select = $('#province');
        select.addClass('is-loading');
        $.ajax({
            url: 'php_action/get_locations.php?action=provinces',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const provinceSelect = $('#province');
                    provinceSelect.html('<option value="">Select Province</option>');
                    
                    response.data.forEach(function(province) {
                        provinceSelect.append(`<option value="${province.name}">${province.name}</option>`);
                    });
                }
            },
            error: function() {
                console.error('Failed to load provinces');
            },
            complete: function(){
                select.removeClass('is-loading');
            }
        });
    }
    
    function loadDistricts(province) {
        var select = $('#district');
        select.addClass('is-loading');
        $.ajax({
            url: `php_action/get_locations.php?action=districts&province=${encodeURIComponent(province)}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const districtSelect = $('#district');
                    districtSelect.html('<option value="">Select District</option>');
                    
                    response.data.forEach(function(district) {
                        districtSelect.append(`<option value="${district.name}">${district.name}</option>`);
                    });
                    
                    districtSelect.prop('disabled', false);
                }
            },
            error: function() {
                console.error('Failed to load districts');
            },
            complete: function(){
                select.removeClass('is-loading');
            }
        });
    }
    
    function loadSectors(province, district) {
        var select = $('#sector');
        select.addClass('is-loading');
        $.ajax({
            url: `php_action/get_locations.php?action=sectors&province=${encodeURIComponent(province)}&district=${encodeURIComponent(district)}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const sectorSelect = $('#sector');
                    sectorSelect.html('<option value="">Select Sector</option>');
                    
                    response.data.forEach(function(sector) {
                        sectorSelect.append(`<option value="${sector.name}">${sector.name}</option>`);
                    });
                    
                    sectorSelect.prop('disabled', false);
                }
            },
            error: function() {
                console.error('Failed to load sectors');
            },
            complete: function(){
                select.removeClass('is-loading');
            }
        });
    }
    
    function loadCells(province, district, sector) {
        var select = $('#cell');
        select.addClass('is-loading');
        $.ajax({
            url: `php_action/get_locations.php?action=cells&province=${encodeURIComponent(province)}&district=${encodeURIComponent(district)}&sector=${encodeURIComponent(sector)}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const cellSelect = $('#cell');
                    cellSelect.html('<option value="">Select Cell</option>');
                    
                    response.data.forEach(function(cell) {
                        cellSelect.append(`<option value="${cell.name}">${cell.name}</option>`);
                    });
                    
                    cellSelect.prop('disabled', false);
                }
            },
            error: function() {
                console.error('Failed to load cells');
            },
            complete: function(){
                select.removeClass('is-loading');
            }
        });
    }
    
    function loadVillages(province, district, sector, cell) {
        var select = $('#village');
        select.addClass('is-loading');
        $.ajax({
            url: `php_action/get_locations.php?action=villages&province=${encodeURIComponent(province)}&district=${encodeURIComponent(district)}&sector=${encodeURIComponent(sector)}&cell=${encodeURIComponent(cell)}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const villageSelect = $('#village');
                    villageSelect.html('<option value="">Select Village</option>');
                    
                    response.data.forEach(function(village) {
                        const villageName = typeof village === 'string' ? village : village.name;
                        villageSelect.append(`<option value="${villageName}">${villageName}</option>`);
                    });
                    
                    villageSelect.prop('disabled', false);
                }
            },
            error: function() {
                console.error('Failed to load villages');
            },
            complete: function(){
                select.removeClass('is-loading');
            }
        });
    }
    
    function updateCompleteLocation() {
        const province = $('#province').val();
        const district = $('#district').val();
        const sector = $('#sector').val();
        const cell = $('#cell').val();
        const village = $('#village').val();
        const addressDetails = $('input[name="address_details"]').val();
        
        let location = '';
        const locationParts = [];
        
        if (village) locationParts.push(village);
        if (cell) locationParts.push(cell);
        if (sector) locationParts.push(sector);
        if (district) locationParts.push(district);
        if (province) locationParts.push(province);
        
        if (locationParts.length > 0) {
            location = locationParts.join(', ');
            if (addressDetails) {
                location += ', ' + addressDetails;
            }
        }
        
        $('#complete_location').val(location);
    }
    
    function resetLocationDropdowns() {
        $('#province').val('').trigger('change');
        $('#district').html('<option value="">Select District</option>').prop('disabled', true);
        $('#sector').html('<option value="">Select Sector</option>').prop('disabled', true);
        $('#cell').html('<option value="">Select Cell</option>').prop('disabled', true);
        $('#village').html('<option value="">Select Village</option>').prop('disabled', true);
        $('#complete_location').val('');
        updateFullLocation();
    }
    </script>

</body>
</html>
