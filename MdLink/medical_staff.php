<?php include('./constant/check.php'); ?>
<?php include('./constant/layout/head.php'); ?>
<?php include('./constant/layout/header.php'); ?>
<?php include('./constant/layout/sidebar.php'); ?>

<div class="page-wrapper">
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Medical Staff</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard_super.php">Home</a></li>
                    <li class="breadcrumb-item active">User Management</li>
                    <li class="breadcrumb-item active">Medical Staff</li>
                </ol>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="p-10">
                                <h3 class="text-white" id="totalStaff">0</h3>
                                <h6 class="text-white">Total Staff</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-user-md fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="p-10">
                                <h3 class="text-white" id="doctors">0</h3>
                                <h6 class="text-white">Doctors</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-stethoscope fa-2x"></i>
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
                                <h3 class="text-white" id="nurses">0</h3>
                                <h6 class="text-white">Nurses</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-heartbeat fa-2x"></i>
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
                                <h3 class="text-white" id="assignedStaff">0</h3>
                                <h6 class="text-white">Assigned</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-hospital-o fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add New Medical Staff -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Add New Medical Staff</h4>
                        <form id="medicalStaffForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Full Name *</label>
                                        <input type="text" class="form-control" id="fullName" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Role *</label>
                                        <select class="form-control" id="role" required>
                                            <option value="">Select Role</option>
                                            <option value="doctor">Doctor</option>
                                            <option value="nurse">Nurse</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>License Number</label>
                                        <input type="text" class="form-control" id="licenseNumber">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Specialty</label>
                                        <input type="text" class="form-control" id="specialty">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Phone</label>
                                        <input type="tel" class="form-control" id="phone">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" class="form-control" id="email">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Assigned Pharmacy</label>
                                        <select class="form-control" id="assignedPharmacy">
                                            <option value="">Select Pharmacy (Optional)</option>
                                            <!-- Options will be loaded via AJAX -->
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-plus"></i> Add Medical Staff
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Filter Medical Staff</h4>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Role</label>
                                    <select class="form-control" id="roleFilter">
                                        <option value="">All Roles</option>
                                        <option value="doctor">Doctor</option>
                                        <option value="nurse">Nurse</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Pharmacy</label>
                                    <select class="form-control" id="pharmacyFilter">
                                        <option value="">All Pharmacies</option>
                                        <!-- Options will be loaded via AJAX -->
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Specialty</label>
                                    <input type="text" class="form-control" id="specialtyFilter" placeholder="Search by specialty">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label><br>
                                    <button class="btn btn-primary" onclick="filterMedicalStaff()">
                                        <i class="fa fa-filter"></i> Apply Filters
                                    </button>
                                    <button class="btn btn-secondary" onclick="clearFilters()">
                                        <i class="fa fa-refresh"></i> Clear
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Medical Staff Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Medical Staff Directory</h4>
                        <div class="table-responsive">
                            <table id="medicalStaffTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Staff ID</th>
                                        <th>Full Name</th>
                                        <th>Role</th>
                                        <th>License Number</th>
                                        <th>Specialty</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Assigned Pharmacy</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be loaded via AJAX -->
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
    loadMedicalStaff();
    loadMedicalStaffStatistics();
    loadPharmacies();
    
    // Form submission
    $('#medicalStaffForm').on('submit', function(e) {
        e.preventDefault();
        addMedicalStaff();
    });
});

function loadMedicalStaff() {
    $.ajax({
        url: 'php_action/get_medical_staff.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#medicalStaffTable').DataTable({
                    data: response.data,
                    columns: [
                        { data: 'staff_id' },
                        { data: 'full_name' },
                        { 
                            data: 'role',
                            render: function(data, type, row) {
                                let badgeClass = data === 'doctor' ? 'badge-success' : 'badge-info';
                                return '<span class="badge ' + badgeClass + '">' + data.toUpperCase() + '</span>';
                            }
                        },
                        { data: 'license_number' },
                        { data: 'specialty' },
                        { data: 'phone' },
                        { data: 'email' },
                        { data: 'pharmacy_name' },
                        { 
                            data: 'created_at',
                            render: function(data, type, row) {
                                return new Date(data).toLocaleDateString();
                            }
                        },
                        {
                            data: null,
                            render: function(data, type, row) {
                                return '<button class="btn btn-sm btn-info" onclick="viewStaffDetails(' + row.staff_id + ')">View</button> ' +
                                       '<button class="btn btn-sm btn-warning" onclick="editStaff(' + row.staff_id + ')">Edit</button> ' +
                                       '<button class="btn btn-sm btn-danger" onclick="deleteStaff(' + row.staff_id + ')">Delete</button>';
                            }
                        }
                    ],
                    order: [[8, 'desc']],
                    pageLength: 10
                });
            }
        }
    });
}

function loadMedicalStaffStatistics() {
    $.ajax({
        url: 'php_action/get_medical_staff_statistics.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#totalStaff').text(response.data.total_staff);
                $('#doctors').text(response.data.doctors);
                $('#nurses').text(response.data.nurses);
                $('#assignedStaff').text(response.data.assigned_staff);
            }
        }
    });
}

function loadPharmacies() {
    $.ajax({
        url: 'php_action/get_pharmacies.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                let options = '<option value="">Select Pharmacy (Optional)</option>';
                let filterOptions = '<option value="">All Pharmacies</option>';
                response.data.forEach(function(pharmacy) {
                    options += '<option value="' + pharmacy.pharmacy_id + '">' + pharmacy.name + '</option>';
                    filterOptions += '<option value="' + pharmacy.pharmacy_id + '">' + pharmacy.name + '</option>';
                });
                $('#assignedPharmacy').html(options);
                $('#pharmacyFilter').html(filterOptions);
            }
        }
    });
}

function addMedicalStaff() {
    const formData = {
        full_name: $('#fullName').val(),
        role: $('#role').val(),
        license_number: $('#licenseNumber').val(),
        specialty: $('#specialty').val(),
        phone: $('#phone').val(),
        email: $('#email').val(),
        assigned_pharmacy_id: $('#assignedPharmacy').val()
    };
    
    $.ajax({
        url: 'php_action/add_medical_staff.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Medical staff added successfully!');
                $('#medicalStaffForm')[0].reset();
                loadMedicalStaff();
                loadMedicalStaffStatistics();
            } else {
                alert('Error: ' + response.message);
            }
        }
    });
}

function filterMedicalStaff() {
    const role = $('#roleFilter').val();
    const pharmacy = $('#pharmacyFilter').val();
    const specialty = $('#specialtyFilter').val();
    
    // Reload table with filters
    $('#medicalStaffTable').DataTable().destroy();
    loadMedicalStaff();
}

function clearFilters() {
    $('#roleFilter').val('');
    $('#pharmacyFilter').val('');
    $('#specialtyFilter').val('');
    filterMedicalStaff();
}

function viewStaffDetails(staffId) {
    $.ajax({
        url: 'php_action/get_medical_staff_details.php',
        type: 'GET',
        data: { staff_id: staffId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showStaffDetailsModal(response.data);
            }
        }
    });
}

function editStaff(staffId) {
    // Redirect to edit page or open modal
    window.location.href = 'edit_medical_staff.php?id=' + staffId;
}

function deleteStaff(staffId) {
    if (confirm('Are you sure you want to delete this medical staff member?')) {
        $.ajax({
            url: 'php_action/delete_medical_staff.php',
            type: 'POST',
            data: { staff_id: staffId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Medical staff deleted successfully!');
                    loadMedicalStaff();
                    loadMedicalStaffStatistics();
                } else {
                    alert('Error: ' + response.message);
                }
            }
        });
    }
}

function showStaffDetailsModal(staffData) {
    const modal = `
        <div class="modal fade" id="staffDetailsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Medical Staff Details</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Staff ID:</strong> ${staffData.staff_id}<br>
                                <strong>Full Name:</strong> ${staffData.full_name}<br>
                                <strong>Role:</strong> <span class="badge badge-success">${staffData.role.toUpperCase()}</span><br>
                                <strong>License Number:</strong> ${staffData.license_number || 'Not provided'}<br>
                                <strong>Specialty:</strong> ${staffData.specialty || 'Not specified'}<br>
                                <strong>Phone:</strong> ${staffData.phone || 'Not provided'}<br>
                                <strong>Email:</strong> ${staffData.email || 'Not provided'}<br>
                                <strong>Assigned Pharmacy:</strong> ${staffData.pharmacy_name || 'Not assigned'}<br>
                                <strong>Created At:</strong> ${new Date(staffData.created_at).toLocaleString()}
                            </div>
                            <div class="col-md-6">
                                <strong>Additional Information:</strong><br>
                                <div class="bg-light p-2">
                                    <p>This medical staff member is ${staffData.pharmacy_name ? 'assigned to ' + staffData.pharmacy_name : 'not currently assigned to any pharmacy'}.</p>
                                    <p>Role: ${staffData.role === 'doctor' ? 'Medical Doctor' : 'Registered Nurse'}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('body').append(modal);
    $('#staffDetailsModal').modal('show');
    $('#staffDetailsModal').on('hidden.bs.modal', function() {
        $(this).remove();
    });
}
</script>
