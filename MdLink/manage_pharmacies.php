<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once './constant/connect.php';
require_once './constant/check.php';

// Log view activity
require_once 'activity_logger.php';
logView($_SESSION['adminId'], 'pharmacies', 'Viewed pharmacy management page');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include './constant/layout/head.php'; ?>
    <title>Manage Pharmacies - MdLink Rwanda</title>
    <style>
        /* Custom Management Page Styles */
        :root {
            --primary-color: #2f855a;
            --secondary-color: #f8f9fa;
            --accent-color: #e6f4ea;
            --text-dark: #2d2d2d;
            --text-muted: #6b7280;
            --border-color: #e5e7eb;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .management-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #276749 100%);
            color: white;
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
        }

        .management-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .management-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 2;
        }

        .management-header p {
            font-size: 1.1rem;
            opacity: 0.9;
            position: relative;
            z-index: 2;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            border-left: 4px solid var(--primary-color);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, var(--accent-color) 0%, transparent 70%);
            border-radius: 0 16px 0 100px;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .stat-card .icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 1rem;
            position: relative;
            z-index: 2;
        }

        .stat-card .icon.pharmacies { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stat-card .icon.admins { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .stat-card .icon.medicines { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .stat-card .icon.status { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }

        .stat-card h3 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
            position: relative;
            z-index: 2;
        }

        .stat-card p {
            color: var(--text-muted);
            margin: 0.5rem 0 0 0;
            font-weight: 500;
            position: relative;
            z-index: 2;
        }

        .management-toolbar {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .search-container {
            position: relative;
            flex: 1;
            min-width: 300px;
        }

        .search-input {
            width: 100%;
            padding: 12px 20px 12px 45px;
            border: 2px solid var(--border-color);
            border-radius: 25px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: var(--secondary-color);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(47, 133, 90, 0.1);
            background: white;
        }

        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 1.1rem;
        }

        .filter-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 8px 16px;
            border: 2px solid var(--border-color);
            background: white;
            color: var(--text-dark);
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .filter-btn:hover, .filter-btn.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .pharmacies-container {
            background: white;
            border-radius: 20px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .pharmacies-header {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--accent-color) 100%);
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .pharmacies-header h2 {
            margin: 0;
            color: var(--text-dark);
            font-size: 1.5rem;
            font-weight: 600;
        }

        .pharmacy-count {
            background: var(--primary-color);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .pharmacy-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 1.5rem;
            padding: 2rem;
        }

        .pharmacy-card {
            background: white;
            border: 2px solid var(--border-color);
            border-radius: 16px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .pharmacy-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color) 0%, #4facfe 100%);
        }

        .pharmacy-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary-color);
        }

        .pharmacy-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .pharmacy-avatar {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary-color) 0%, #4facfe 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            margin-right: 1rem;
        }

        .pharmacy-info h4 {
            margin: 0;
            color: var(--text-dark);
            font-size: 1.2rem;
            font-weight: 600;
        }

        .pharmacy-info p {
            margin: 0.25rem 0 0 0;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .pharmacy-details {
            margin-bottom: 1.5rem;
        }

        .detail-row {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
            font-size: 0.9rem;
        }

        .detail-row i {
            width: 20px;
            color: var(--primary-color);
            margin-right: 0.75rem;
        }

        .pharmacy-stats {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-badge {
            background: var(--accent-color);
            color: var(--primary-color);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .pharmacy-actions {
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
        }

        .action-btn {
            padding: 8px 12px;
            border: none;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }


        .action-btn.edit {
            background: #fff3e0;
            color: #f57c00;
        }

        .action-btn.delete {
            background: #ffebee;
            color: #d32f2f;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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

        .empty-state h3 {
            margin-bottom: 0.5rem;
            color: var(--text-dark);
        }

        .loading-spinner {
            display: none;
            text-align: center;
            padding: 2rem;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid var(--border-color);
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .modal-custom .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: var(--shadow-lg);
        }

        .modal-custom .modal-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #276749 100%);
            color: white;
            border-radius: 16px 16px 0 0;
            border: none;
        }

        .modal-custom .modal-body {
            padding: 2rem;
        }

        .detail-section {
            margin-bottom: 1.5rem;
        }

        .detail-section h6 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 500;
            color: var(--text-dark);
        }

        .detail-value {
            color: var(--text-muted);
        }

        @media (max-width: 768px) {
            .pharmacy-grid {
                grid-template-columns: 1fr;
                padding: 1rem;
            }
            
            .management-toolbar {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-container {
                min-width: auto;
            }
            
            .pharmacy-actions {
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<body>
    <?php include './constant/layout/header.php'; ?>
    <?php include './constant/layout/sidebar.php'; ?>

    <div class="page-wrapper">
        <div class="container-fluid">
            
            <!-- Management Header -->
            <div class="management-header">
                <h1><i class="fa fa-hospital-o"></i> Pharmacy Management</h1>
                <p>Comprehensive management system for all pharmacies in the MdLink Rwanda network</p>
                            </div>

            <?php
            // Display success/error messages
            if (isset($_GET['success'])) {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
                echo '<i class="fa fa-check-circle"></i> ' . htmlspecialchars($_GET['success']);
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
                echo '<span aria-hidden="true">&times;</span>';
                echo '</button>';
                echo '</div>';
            }
            
            if (isset($_GET['error'])) {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
                echo '<i class="fa fa-exclamation-triangle"></i> ' . htmlspecialchars($_GET['error']);
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
                echo '<span aria-hidden="true">&times;</span>';
                echo '</button>';
                echo '</div>';
            }
            ?>

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
                error_log("Database error in manage_pharmacies.php: " . $e->getMessage());
                
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
                echo '<li><a href="fix_database_schema.php" class="alert-link">Fix Database Schema</a></li>';
                echo '</ul>';
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
                echo '<span aria-hidden="true">&times;</span>';
                echo '</button>';
                echo '</div>';
            }
            ?>

            <!-- Statistics Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="icon pharmacies">
                        <i class="fa fa-hospital-o"></i>
                    </div>
                    <h3><?php echo $totalPharmacies; ?></h3>
                    <p>Total Pharmacies</p>
                            </div>
                <div class="stat-card">
                    <div class="icon admins">
                        <i class="fa fa-users"></i>
                                            </div>
                    <h3><?php echo $totalAdmins; ?></h3>
                    <p>Admin Accounts</p>
                                        </div>
                <div class="stat-card">
                    <div class="icon medicines">
                        <i class="fa fa-medkit"></i>
                                            </div>
                    <h3><?php echo $totalMedicines; ?></h3>
                    <p>Total Medicines</p>
                                        </div>
                <div class="stat-card">
                    <div class="icon status">
                        <i class="fa fa-check-circle"></i>
                                            </div>
                    <h3>Active</h3>
                    <p>System Status</p>
                                        </div>
                                    </div>

            <!-- Management Toolbar -->
            <div class="management-toolbar">
                <div class="search-container">
                    <i class="fa fa-search search-icon"></i>
                    <input type="text" class="search-input" id="searchInput" placeholder="Search pharmacies by name, location, or contact...">
                                            </div>
                <div class="filter-buttons">
                    <button class="filter-btn active" data-filter="all">All</button>
                    <button class="filter-btn" data-filter="active">Active</button>
                    <button class="filter-btn" data-filter="recent">Recent</button>
                    <button class="filter-btn" data-filter="high-medicines">High Medicines</button>
                                        </div>
                <a href="create_pharmacy.php" class="btn btn-primary">
                    <i class="fa fa-plus"></i> Add New Pharmacy
                </a>
                                                    </div>

            <!-- Pharmacies Container -->
            <div class="pharmacies-container">
                <div class="pharmacies-header">
                    <h2><i class="fa fa-list"></i> Pharmacy Directory</h2>
                    <span class="pharmacy-count" id="pharmacyCount"><?php echo count($existingPharmacies); ?> pharmacies</span>
                                                    </div>
                
                <div class="loading-spinner" id="loadingSpinner">
                    <div class="spinner"></div>
                    <p>Loading pharmacies...</p>
                                                    </div>

                <div class="pharmacy-grid" id="pharmacyGrid">
                    <?php if (!empty($existingPharmacies)): ?>
                        <?php foreach ($existingPharmacies as $pharmacy) { ?>
                        <div class="pharmacy-card" data-pharmacy-id="<?php echo $pharmacy['pharmacy_id']; ?>"
                             data-name="<?php echo strtolower(htmlspecialchars($pharmacy['name'])); ?>" 
                             data-location="<?php echo strtolower(htmlspecialchars($pharmacy['location'])); ?>"
                             data-medicines="<?php echo (int)$pharmacy['medicine_count']; ?>"
                             data-created="<?php echo strtotime($pharmacy['created_at']); ?>">
                            <div class="pharmacy-header">
                                <div class="pharmacy-avatar">
                                    <i class="fa fa-hospital-o"></i>
                                                    </div>
                                <div class="pharmacy-info">
                                    <h4><?php echo htmlspecialchars($pharmacy['name']); ?></h4>
                                    <p><?php echo htmlspecialchars($pharmacy['license_number']); ?></p>
                                                    </div>
                                                </div>
                            
                            <div class="pharmacy-details">
                                <div class="detail-row">
                                    <i class="fa fa-map-marker"></i>
                                    <span><?php echo htmlspecialchars($pharmacy['location'] ?: 'Location not specified'); ?></span>
                                </div>
                                <div class="detail-row">
                                    <i class="fa fa-user"></i>
                                    <span><?php echo htmlspecialchars($pharmacy['contact_person']); ?></span>
                                </div>
                                <div class="detail-row">
                                    <i class="fa fa-phone"></i>
                                    <span><?php echo htmlspecialchars($pharmacy['contact_phone']); ?></span>
                                </div>
                                <div class="detail-row">
                                    <i class="fa fa-calendar"></i>
                                    <span>Created <?php echo date('M j, Y', strtotime($pharmacy['created_at'])); ?></span>
                                            </div>
                                        </div>
                            
                            <div class="pharmacy-stats">
                                <div class="stat-badge">
                                    <i class="fa fa-users"></i>
                                    <?php echo (int)$pharmacy['admin_count']; ?> admins
                                    </div>
                                <div class="stat-badge">
                                    <i class="fa fa-medkit"></i>
                                    <?php echo (int)$pharmacy['medicine_count']; ?> medicines
                                    </div>
                            </div>
                            
                            <div class="pharmacy-actions">
                                <button class="action-btn edit" onclick="editPharmacy(<?php echo $pharmacy['pharmacy_id']; ?>)" title="Edit Pharmacy">
                                    <i class="fa fa-pencil"></i> Edit
                                </button>
                                <button class="action-btn delete" onclick="deletePharmacy(<?php echo $pharmacy['pharmacy_id']; ?>, '<?php echo htmlspecialchars($pharmacy['name']); ?>')" title="Delete Pharmacy">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                        <?php } ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fa fa-hospital-o"></i>
                            <h3>No Pharmacies Found</h3>
                            <p>Start building your pharmacy network by adding the first pharmacy.</p>
                            <a href="create_pharmacy.php" class="btn btn-primary mt-3">
                                <i class="fa fa-plus"></i> Add First Pharmacy
                            </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    <?php include './constant/layout/footer.php'; ?>

    <script>
    $(document).ready(function() {
        // Search functionality
        $('#searchInput').on('keyup', function() {
            const searchTerm = $(this).val().toLowerCase();
            filterPharmacies(searchTerm);
        });

        // Filter functionality
        $('.filter-btn').on('click', function() {
            $('.filter-btn').removeClass('active');
            $(this).addClass('active');
            
            const filter = $(this).data('filter');
            applyFilter(filter);
        });

        // Add hover effects
        $('.pharmacy-card').hover(
            function() {
                $(this).find('.pharmacy-actions').fadeIn(200);
            },
            function() {
                $(this).find('.pharmacy-actions').fadeOut(200);
            }
        );
    });

    function filterPharmacies(searchTerm) {
        $('.pharmacy-card').each(function() {
            const name = $(this).data('name');
            const location = $(this).data('location');
            const visible = name.includes(searchTerm) || location.includes(searchTerm);
            $(this).toggle(visible);
        });
        updateCount();
    }

    function applyFilter(filter) {
        const now = Date.now();
        const oneWeekAgo = now - (7 * 24 * 60 * 60 * 1000);
        
        $('.pharmacy-card').each(function() {
            let visible = true;
            
            switch(filter) {
                case 'all':
                    visible = true;
                    break;
                case 'active':
                    visible = true; // All pharmacies are considered active
                    break;
                case 'recent':
                    const created = $(this).data('created') * 1000;
                    visible = created > oneWeekAgo;
                    break;
                case 'high-medicines':
                    const medicines = $(this).data('medicines');
                    visible = medicines > 50; // Adjust threshold as needed
                    break;
            }
            
            $(this).toggle(visible);
        });
        updateCount();
    }

    function updateCount() {
        const visibleCount = $('.pharmacy-card:visible').length;
        $('#pharmacyCount').text(visibleCount + ' pharmacies');
    }

    // Pharmacy management functions

    function editPharmacy(id) {
        window.location.href = 'create_pharmacy.php?edit=' + id;
    }

    function deletePharmacy(id, name) {
        // Create a custom confirmation modal
        const confirmHTML = `
            <div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">
                                <i class="fa fa-exclamation-triangle"></i> Confirm Delete
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center mb-3">
                                <i class="fa fa-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                            </div>
                            <div class="alert alert-danger">
                                <h6><strong>Are you sure you want to delete "${name}"?</strong></h6>
                                <p class="mb-2">This will also delete:</p>
                                <ul class="mb-3">
                                    <li>All medicines in this pharmacy</li>
                                    <li>All admin accounts for this pharmacy</li>
                                </ul>
                                <p class="mb-0 text-danger"><strong>This action cannot be undone!</strong></p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fa fa-times"></i> Cancel
                            </button>
                            <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                                <i class="fa fa-trash"></i> Delete Pharmacy
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal if any
        $('#deleteConfirmModal').remove();
        
        // Add modal to body
        $('body').append(confirmHTML);
        
        // Show modal
        $('#deleteConfirmModal').modal('show');
        
        // Handle confirm delete button click
        $('#confirmDeleteBtn').off('click').on('click', function() {
            const btn = $(this);
            const originalText = btn.html();
            
            // Disable button and show loading
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Deleting...');
            
            // Close modal
            $('#deleteConfirmModal').modal('hide');
            
            // Show loading overlay
            showLoading();
            
            // Perform delete
            $.ajax({
                url: 'php_action/delete_pharmacy.php',
                type: 'POST',
                data: { pharmacy_id: id },
                dataType: 'json',
                success: function(response) {
                    hideLoading();
                    if (response.success) {
                        // Show success popup that auto-closes
                        showSuccessPopup(name, function() {
                            // Remove the pharmacy card with animation
                            const pharmacyCard = $(`[data-pharmacy-id="${id}"]`);
                            if (pharmacyCard.length) {
                                pharmacyCard.fadeOut(500, function() {
                                    $(this).remove();
                                    updatePharmacyCount();
                                    updateStatistics();
                                });
                            } else {
                                // Fallback: reload page
                                setTimeout(() => location.reload(), 1000);
                            }
                        });
                } else {
                        showAlert('Error deleting pharmacy: ' + response.message, 'error');
                        showNotification('Failed to delete pharmacy: ' + response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    hideLoading();
                    console.error('Delete error:', xhr, status, error);
                    showAlert('Network error. Please try again.', 'error');
                    showNotification('Network error. Please try again.', 'error');
                },
                complete: function() {
                    // Re-enable button
                    btn.prop('disabled', false).html(originalText);
                }
            });
        });

        // Handle modal close
        $('#deleteConfirmModal').on('hidden.bs.modal', function() {
            $(this).remove();
        });
    }


    function showLoading() {
        $('#loadingSpinner').show();
        $('#pharmacyGrid').hide();
    }

    function hideLoading() {
        $('#loadingSpinner').hide();
        $('#pharmacyGrid').show();
    }

    function showAlert(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
        
        const alertHTML = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="fa ${icon}"></i> ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;
        
        $('body').append(alertHTML);
        setTimeout(() => $('.alert').fadeOut(), 5000);
    }
    
    function showNotification(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
        
        const notification = $(`
            <div class="alert ${alertClass} alert-dismissible fade show" style="position: fixed; top: 80px; right: 20px; z-index: 9999; min-width: 300px; max-width: 500px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
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
    
    function updatePharmacyCount() {
        const count = $('.pharmacy-card').length;
        $('.pharmacy-count').text(`${count} Pharmacies`);
    }
    
    function showSuccessPopup(pharmacyName, callback) {
        const successHTML = `
            <div class="modal fade" id="successModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title">
                                <i class="fa fa-check-circle"></i> Success!
                            </h5>
                        </div>
                        <div class="modal-body text-center">
                            <div class="mb-3">
                                <i class="fa fa-check-circle text-success" style="font-size: 3rem;"></i>
                            </div>
                            <h5 class="text-success">Pharmacy Deleted Successfully!</h5>
                            <p class="text-muted">"${pharmacyName}" has been permanently deleted from the system.</p>
                            <div class="alert alert-info">
                                <small><i class="fa fa-info-circle"></i> All related medicines and admin accounts have also been removed.</small>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-success" id="okButton">
                                <i class="fa fa-check"></i> OK
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing success modal if any
        $('#successModal').remove();
        
        // Add modal to body
        $('body').append(successHTML);
        
        // Show modal
        $('#successModal').modal('show');
        
        // Handle OK button click
        $('#okButton').on('click', function() {
            $('#successModal').modal('hide');
            if (callback) callback();
        });
        
        // Auto-close after 3 seconds
        setTimeout(() => {
            if ($('#successModal').length) {
                $('#successModal').modal('hide');
                if (callback) callback();
            }
        }, 3000);
        
        // Handle modal close
        $('#successModal').on('hidden.bs.modal', function() {
            $(this).remove();
        });
    }
    
    function updateStatistics() {
        // Update the statistics cards without page refresh
        $.ajax({
            url: 'php_action/get_statistics.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    // Update pharmacy count
                    $('.pharmacy-count').text(data.pharmacy_count + ' Pharmacies');
                    
                    // Update other statistics if available
                    if (data.admin_count !== undefined) {
                        $('.admin-count').text(data.admin_count);
                    }
                    if (data.medicine_count !== undefined) {
                        $('.medicine-count').text(data.medicine_count);
                    }
                }
            },
            error: function() {
                console.log('Could not update statistics, but deletion was successful');
            }
        });
    }
    </script>

</body>
</html>