<?php include('./constant/check.php'); ?>
<?php include('./constant/layout/head.php'); ?>
<?php include('./constant/layout/header.php'); ?>
<?php include('./constant/layout/sidebar.php'); ?>

<div class="page-wrapper">
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Recall Alerts</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard_super.php">Home</a></li>
                    <li class="breadcrumb-item active">Notifications</li>
                    <li class="breadcrumb-item active">Recall Alerts</li>
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
                                <h3 class="text-white" id="totalRecalls">0</h3>
                                <h6 class="text-white">Total Recalls</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-exclamation-triangle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="p-10">
                                <h3 class="text-white" id="criticalRecalls">0</h3>
                                <h6 class="text-white">Critical</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-warning fa-2x"></i>
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
                                <h3 class="text-white" id="openRecalls">0</h3>
                                <h6 class="text-white">Open</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-clock-o fa-2x"></i>
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
                                <h3 class="text-white" id="resolvedRecalls">0</h3>
                                <h6 class="text-white">Resolved</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-check fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add New Recall Alert -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Add New Recall Alert</h4>
                        <form id="recallAlertForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Medicine Name *</label>
                                        <input type="text" class="form-control" id="medicineName" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Batch Number</label>
                                        <input type="text" class="form-control" id="batchNumber">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Category</label>
                                        <select class="form-control" id="categoryName">
                                            <option value="">Select Category</option>
                                            <!-- Options will be loaded via AJAX -->
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Pharmacy</label>
                                        <select class="form-control" id="pharmacyId">
                                            <option value="">All Pharmacies</option>
                                            <!-- Options will be loaded via AJAX -->
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Severity *</label>
                                        <select class="form-control" id="severity" required>
                                            <option value="">Select Severity</option>
                                            <option value="info">Info</option>
                                            <option value="warning">Warning</option>
                                            <option value="critical">Critical</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Announced Date *</label>
                                        <input type="date" class="form-control" id="announcedOn" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Reason for Recall *</label>
                                <textarea class="form-control" id="reason" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-plus"></i> Add Recall Alert
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
                        <h4 class="card-title">Filter Recall Alerts</h4>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control" id="statusFilter">
                                        <option value="">All Status</option>
                                        <option value="open">Open</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="resolved">Resolved</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Severity</label>
                                    <select class="form-control" id="severityFilter">
                                        <option value="">All Severities</option>
                                        <option value="info">Info</option>
                                        <option value="warning">Warning</option>
                                        <option value="critical">Critical</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date From</label>
                                    <input type="date" class="form-control" id="dateFrom">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date To</label>
                                    <input type="date" class="form-control" id="dateTo">
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary" onclick="filterRecallAlerts()">
                            <i class="fa fa-filter"></i> Apply Filters
                        </button>
                        <button class="btn btn-secondary" onclick="clearFilters()">
                            <i class="fa fa-refresh"></i> Clear Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recall Alerts Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Recall Alerts</h4>
                        <div class="table-responsive">
                            <table id="recallAlertsTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Recall ID</th>
                                        <th>Medicine Name</th>
                                        <th>Batch Number</th>
                                        <th>Category</th>
                                        <th>Pharmacy</th>
                                        <th>Severity</th>
                                        <th>Status</th>
                                        <th>Announced Date</th>
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
    loadRecallAlerts();
    loadRecallStatistics();
    loadCategories();
    loadPharmacies();
    
    // Set today's date as default
    $('#announcedOn').val(new Date().toISOString().split('T')[0]);
    
    // Form submission
    $('#recallAlertForm').on('submit', function(e) {
        e.preventDefault();
        addRecallAlert();
    });
});

function loadRecallAlerts() {
    $.ajax({
        url: 'php_action/get_recall_alerts.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#recallAlertsTable').DataTable({
                    data: response.data,
                    columns: [
                        { data: 'recall_id' },
                        { data: 'medicine_name' },
                        { data: 'batch_number' },
                        { data: 'category_name' },
                        { data: 'pharmacy_name' },
                        { 
                            data: 'severity',
                            render: function(data, type, row) {
                                let badgeClass = 'badge-secondary';
                                if (data === 'critical') badgeClass = 'badge-danger';
                                else if (data === 'warning') badgeClass = 'badge-warning';
                                else if (data === 'info') badgeClass = 'badge-info';
                                return '<span class="badge ' + badgeClass + '">' + data.toUpperCase() + '</span>';
                            }
                        },
                        { 
                            data: 'status',
                            render: function(data, type, row) {
                                let badgeClass = 'badge-secondary';
                                if (data === 'resolved') badgeClass = 'badge-success';
                                else if (data === 'in_progress') badgeClass = 'badge-warning';
                                else if (data === 'open') badgeClass = 'badge-danger';
                                return '<span class="badge ' + badgeClass + '">' + data.replace('_', ' ').toUpperCase() + '</span>';
                            }
                        },
                        { 
                            data: 'announced_on',
                            render: function(data, type, row) {
                                return new Date(data).toLocaleDateString();
                            }
                        },
                        {
                            data: null,
                            render: function(data, type, row) {
                                let actions = '<button class="btn btn-sm btn-info" onclick="viewRecallDetails(' + row.recall_id + ')">View</button>';
                                if (row.status === 'open') {
                                    actions += ' <button class="btn btn-sm btn-warning" onclick="updateRecallStatus(' + row.recall_id + ', \'in_progress\')">Start Progress</button>';
                                } else if (row.status === 'in_progress') {
                                    actions += ' <button class="btn btn-sm btn-success" onclick="updateRecallStatus(' + row.recall_id + ', \'resolved\')">Mark Resolved</button>';
                                }
                                return actions;
                            }
                        }
                    ],
                    order: [[7, 'desc']],
                    pageLength: 10
                });
            }
        }
    });
}

function loadRecallStatistics() {
    $.ajax({
        url: 'php_action/get_recall_statistics.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#totalRecalls').text(response.data.total_recalls);
                $('#criticalRecalls').text(response.data.critical_recalls);
                $('#openRecalls').text(response.data.open_recalls);
                $('#resolvedRecalls').text(response.data.resolved_recalls);
            }
        }
    });
}

function loadCategories() {
    $.ajax({
        url: 'php_action/get_categories.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                let options = '<option value="">Select Category</option>';
                response.data.forEach(function(category) {
                    options += '<option value="' + category.category_name + '">' + category.category_name + '</option>';
                });
                $('#categoryName').html(options);
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
                let options = '<option value="">All Pharmacies</option>';
                response.data.forEach(function(pharmacy) {
                    options += '<option value="' + pharmacy.pharmacy_id + '">' + pharmacy.name + '</option>';
                });
                $('#pharmacyId').html(options);
            }
        }
    });
}

function addRecallAlert() {
    const formData = {
        medicine_name: $('#medicineName').val(),
        batch_number: $('#batchNumber').val(),
        category_name: $('#categoryName').val(),
        pharmacy_id: $('#pharmacyId').val(),
        severity: $('#severity').val(),
        announced_on: $('#announcedOn').val(),
        reason: $('#reason').val()
    };
    
    $.ajax({
        url: 'php_action/add_recall_alert.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Recall alert added successfully!');
                $('#recallAlertForm')[0].reset();
                $('#announcedOn').val(new Date().toISOString().split('T')[0]);
                loadRecallAlerts();
                loadRecallStatistics();
            } else {
                alert('Error: ' + response.message);
            }
        }
    });
}

function filterRecallAlerts() {
    const status = $('#statusFilter').val();
    const severity = $('#severityFilter').val();
    const dateFrom = $('#dateFrom').val();
    const dateTo = $('#dateTo').val();
    
    // Reload table with filters
    $('#recallAlertsTable').DataTable().destroy();
    loadRecallAlerts();
}

function clearFilters() {
    $('#statusFilter').val('');
    $('#severityFilter').val('');
    $('#dateFrom').val('');
    $('#dateTo').val('');
    filterRecallAlerts();
}

function viewRecallDetails(recallId) {
    $.ajax({
        url: 'php_action/get_recall_details.php',
        type: 'GET',
        data: { recall_id: recallId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showRecallDetailsModal(response.data);
            }
        }
    });
}

function updateRecallStatus(recallId, newStatus) {
    const action = newStatus === 'resolved' ? 'resolve' : 'start progress';
    if (confirm('Are you sure you want to ' + action + ' this recall alert?')) {
        $.ajax({
            url: 'php_action/update_recall_status.php',
            type: 'POST',
            data: { 
                recall_id: recallId,
                status: newStatus
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Recall alert status updated successfully!');
                    loadRecallAlerts();
                    loadRecallStatistics();
                } else {
                    alert('Error: ' + response.message);
                }
            }
        });
    }
}

function showRecallDetailsModal(recallData) {
    const modal = `
        <div class="modal fade" id="recallDetailsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Recall Alert Details</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Recall ID:</strong> ${recallData.recall_id}<br>
                                <strong>Medicine Name:</strong> ${recallData.medicine_name}<br>
                                <strong>Batch Number:</strong> ${recallData.batch_number || 'N/A'}<br>
                                <strong>Category:</strong> ${recallData.category_name || 'N/A'}<br>
                                <strong>Pharmacy:</strong> ${recallData.pharmacy_name || 'All Pharmacies'}<br>
                                <strong>Severity:</strong> <span class="badge badge-warning">${recallData.severity.toUpperCase()}</span><br>
                                <strong>Status:</strong> <span class="badge badge-info">${recallData.status.replace('_', ' ').toUpperCase()}</span><br>
                                <strong>Announced Date:</strong> ${new Date(recallData.announced_on).toLocaleDateString()}<br>
                                <strong>Created At:</strong> ${new Date(recallData.created_at).toLocaleString()}
                            </div>
                            <div class="col-md-6">
                                <strong>Reason for Recall:</strong><br>
                                <p class="bg-light p-2">${recallData.reason}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('body').append(modal);
    $('#recallDetailsModal').modal('show');
    $('#recallDetailsModal').on('hidden.bs.modal', function() {
        $(this).remove();
    });
}
</script>
