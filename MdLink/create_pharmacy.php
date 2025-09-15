<?php
session_start();
require_once './constant/connect.php';
require_once './constant/check.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include './constant/layout/head.php'; ?>
    <title>Create Pharmacy - MdLink Rwanda</title>
    <style>
        /* Minimal palette: primary green + grayscale */
        .bg-gradient-primary {
            background: #2f855a; /* primary */
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
        
        .avatar-sm {
            width: 40px;
            height: 40px;
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
            background: #2f855a !important;
        }
        
        .form-group label {
            font-weight: 600;
            color: #495057;
        }
        
        .form-control:focus {
            border-color: #2f855a;
            box-shadow: 0 0 0 0.2rem rgba(47, 133, 90, 0.25);
        }
        
        .stats-card {
            background: #f8f9fa;
            color: #2d2d2d;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #2f855a;
        }
        
        .stats-card h3 {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 0;
        }
        
        .stats-card p {
            margin: 5px 0 0 0;
            opacity: 0.9;
        }
        
        .pharmacy-card {
            border-left: 4px solid #2f855a;
            transition: all 0.3s ease;
        }
        
        .pharmacy-card:hover {
            border-left-color: #276749;
            transform: translateX(5px);
        }

        /* Make summary cards neutral with green accent */
        .card.bg-primary,
        .card.bg-success,
        .card.bg-info,
        .card.bg-warning { 
            background: #f8f9fa !important; 
            color: #2d2d2d !important; 
        }
        .card.bg-primary i,
        .card.bg-success i,
        .card.bg-info i,
        .card.bg-warning i { color: #2f855a !important; }

        /* Some cards include text-white utility; force readable color */
        .card.bg-primary.text-white,
        .card.bg-success.text-white,
        .card.bg-info.text-white,
        .card.bg-warning.text-white { color: #2d2d2d !important; }
        .card.bg-primary.text-white .mb-0,
        .card.bg-success.text-white .mb-0,
        .card.bg-info.text-white .mb-0,
        .card.bg-warning.text-white .mb-0 { color: #2d2d2d !important; }

        /* Table header minimal */
        .thead-dark th { background: #f1f3f5 !important; color: #2d2d2d !important; }
        .table tbody td { color: #2d2d2d; }

        /* Badges unified to soft green */
        .badge-primary,
        .badge-info,
        .badge-success,
        .badge-warning { 
            background: #e6f4ea !important; 
            color: #2f855a !important; 
            font-weight: 600;
        }

        /* Outline buttons unified */
        .btn-outline-primary,
        .btn-outline-warning,
        .btn-outline-danger { 
            color: #2f855a; border-color: #2f855a; 
        }
        .btn-outline-primary:hover,
        .btn-outline-warning:hover,
        .btn-outline-danger:hover { 
            background: #2f855a; color: #fff; 
        }

        /* De-emphasize colored text utilities inside this page */
        .text-primary, .text-info, .text-success, .text-danger { color: #2d2d2d !important; }
        .text-muted { color: #6b7280 !important; }

        /* Ensure header/labels are readable */
        .card-header h4, .card-header, .card-body, h6, label { color: #2d2d2d; }
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
                    <div class="card bg-gradient-primary text-white">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h2 class="mb-2"><i class="fa fa-hospital-o"></i> Pharmacy Management</h2>
                                    <p class="mb-0">Create new pharmacies and manage their basic information.</p>
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

            <?php
            // Fetch existing pharmacies for display
            $existingPharmacies = [];
            $totalPharmacies = 0;
            $totalAdmins = 0;
            $totalMedicines = 0;
            
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
                        $totalPharmacies++;
                        $totalAdmins += (int)$row['admin_count'];
                        $totalMedicines += (int)$row['medicine_count'];
                    }
                }
            } catch (Exception $e) {
                // Use sample data if database query fails
                $existingPharmacies = [
                    [
                        'name' => 'Ineza Pharmacy',
                        'license_number' => 'RL-2024-001',
                        'location' => 'Kigali, Gasabo District',
                        'contact_person' => 'Dr. Jean Bosco',
                        'contact_phone' => '+250 788 123 456',
                        'admin_count' => 2,
                        'medicine_count' => 156,
                        'status' => 'active'
                    ],
                    [
                        'name' => 'Keza Pharmacy',
                        'license_number' => 'RL-2024-002',
                        'location' => 'Kigali, Kicukiro District',
                        'contact_person' => 'Dr. Marie Claire',
                        'contact_phone' => '+250 789 987 654',
                        'admin_count' => 2,
                        'medicine_count' => 89,
                        'status' => 'active'
                    ]
                ];
                $totalPharmacies = count($existingPharmacies);
                $totalAdmins = 4;
                $totalMedicines = 245;
            }
            ?>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0"><?php echo $totalPharmacies; ?></h4>
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
                                    <h4 class="mb-0"><?php echo $totalAdmins; ?></h4>
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
                                    <h4 class="mb-0"><?php echo $totalMedicines; ?></h4>
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
                                            <input type="text" class="form-control" id="license_number" name="license_number" placeholder="MdLink-12345" required readonly>
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
                                
                                <!-- Location Selection -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <h6 class="text-info mb-3"><i class="fa fa-map-marker"></i> Location Selection</h6>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><i class="fa fa-map"></i> Province *</label>
                                            <select class="form-control" id="province" name="province" required>
                                                <option value="">Select Province</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><i class="fa fa-building"></i> District *</label>
                                            <select class="form-control" id="district" name="district" required disabled>
                                                <option value="">Select District</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><i class="fa fa-map-pin"></i> Sector *</label>
                                            <select class="form-control" id="sector" name="sector" required disabled>
                                                <option value="">Select Sector</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><i class="fa fa-home"></i> Cell *</label>
                                            <select class="form-control" id="cell" name="cell" required disabled>
                                                <option value="">Select Cell</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><i class="fa fa-map-signs"></i> Village *</label>
                                            <select class="form-control" id="village" name="village" required disabled>
                                                <option value="">Select Village</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><i class="fa fa-map-marker"></i> Additional Address Details</label>
                                            <input type="text" class="form-control" name="address_details" placeholder="Street name, building number, etc.">
                                            <small class="form-text text-muted">Optional: Add specific street or building details</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Hidden field to store complete location -->
                                <input type="hidden" id="complete_location" name="location" required>
                                
                                
                                
                            </form>
                        </div>
                        <div class="modal-footer">
                            <a href="http://localhost/Final_year_project/MdLink%20Rwanda/MdLink/create_pharmacy.php"><button type="button" class="btn btn-danger">Cancel</button></a>
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
        // Initialize location dropdowns
        initializeLocationDropdowns();
        // Initialize modal plugin explicitly (Bootstrap)
        if (typeof $('#createPharmacyModal').modal === 'function') {
            $('#createPharmacyModal').modal({backdrop: true, keyboard: true, show: false});
        }
        // Generate license number each time modal opens
        $('#createPharmacyModal').on('show.bs.modal', function(){
            $('#license_number').val('MdLink-' + Math.floor(10000 + Math.random()*90000));
        });

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
            const formData = new FormData(form);
            const btn = this;
            
            // Validate form
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            // Disable button and show loading
            $(btn).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Creating...');
            
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
                        $('#pharmacyMessage').html(
                            '<div class="alert alert-success"><i class="fa fa-check-circle"></i> Pharmacy created successfully!</div>'
                        );
                        
                        // Reset form
                        form.reset();
                        
                        // Close modal after 2 seconds
                        setTimeout(() => {
                            $('#createPharmacyModal').modal('hide');
                            location.reload(); // Refresh page to show new pharmacy
                        }, 2000);
                    } else {
                        // Show error message
                        $('#pharmacyMessage').html(
                            '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> ' + 
                            (response.message || 'Failed to create pharmacy') + '</div>'
                        );
                    }
                },
                error: function(xhr, status, error) {
                    // Show error message
                    $('#pharmacyMessage').html(
                        '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> ' + 
                        'Network error. Please try again.</div>'
                    );
                },
                complete: function() {
                    // Re-enable button
                    $(btn).prop('disabled', false).html('<i class="fa fa-plus"></i> Create Pharmacy');
                }
            });
        });
        
        // Clear messages when modal is closed
        $('#createPharmacyModal').on('hidden.bs.modal', function () {
            $('#pharmacyMessage').html('');
            $('#createPharmacyForm')[0].reset();
            // Reset location dropdowns
            resetLocationDropdowns();
            $('#license_number').val('');
        });
        
        // Add some interactivity to the table rows
        $('tbody tr').on('mouseenter', function() {
            $(this).css('background-color', '#f8f9fa');
        }).on('mouseleave', function() {
            $(this).css('background-color', '');
        });
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
            updateCompleteLocation();
            
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
            updateCompleteLocation();
            
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
            updateCompleteLocation();
            
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
            updateCompleteLocation();
            
            if (province && district && sector && cell) {
                loadVillages(province, district, sector, cell);
            }
        });
        
        // Village change handler
        villageSelect.on('change', function() {
            updateCompleteLocation();
        });
        
        // Additional address details change handler
        $('input[name="address_details"]').on('input', function() {
            updateCompleteLocation();
        });
    }
    
    function resetSelect(select, placeholder) {
        select.html(`<option value="">${placeholder}</option>`).prop('disabled', true);
    }
    
    function loadProvinces() {
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
            }
        });
    }
    
    function loadDistricts(province) {
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
            }
        });
    }
    
    function loadSectors(province, district) {
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
            }
        });
    }
    
    function loadCells(province, district, sector) {
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
            }
        });
    }
    
    function loadVillages(province, district, sector, cell) {
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
        $('input[name="address_details"]').val('');
        $('#complete_location').val('');
    }
    </script>

</body>
</html>
