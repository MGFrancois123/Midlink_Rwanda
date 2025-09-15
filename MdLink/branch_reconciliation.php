<?php include('./constant/check.php'); ?>
<?php include('./constant/layout/head.php'); ?>
<?php include('./constant/layout/header.php'); ?>
<?php include('./constant/layout/sidebar.php'); ?>

<div class="page-wrapper">
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Branch Reconciliation</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard_super.php">Home</a></li>
                    <li class="breadcrumb-item active">Payments & Billing</li>
                    <li class="breadcrumb-item active">Branch Reconciliation</li>
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
                                <h3 class="text-white" id="totalReconciliations">0</h3>
                                <h6 class="text-white">Total Reconciliations</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-random fa-2x"></i>
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
                                <h3 class="text-white" id="reconciledBranches">0</h3>
                                <h6 class="text-white">Reconciled</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-check fa-2x"></i>
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
                                <h3 class="text-white" id="pendingReconciliations">0</h3>
                                <h6 class="text-white">Pending</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-clock-o fa-2x"></i>
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
                                <h3 class="text-white" id="discrepancyBranches">0</h3>
                                <h6 class="text-white">Discrepancies</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-exclamation-triangle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add New Reconciliation -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Add New Reconciliation</h4>
                        <form id="reconciliationForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Reconciliation Date *</label>
                                        <input type="date" class="form-control" id="reconciliationDate" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Branch Name *</label>
                                        <select class="form-control" id="branchName" required>
                                            <option value="">Select Branch</option>
                                            <!-- Options will be loaded via AJAX -->
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Expected Amount *</label>
                                        <input type="number" class="form-control" id="expectedAmount" step="0.01" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Actual Amount *</label>
                                        <input type="number" class="form-control" id="actualAmount" step="0.01" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Notes</label>
                                <textarea class="form-control" id="notes" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-plus"></i> Add Reconciliation
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
                        <h4 class="card-title">Filter Reconciliations</h4>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control" id="statusFilter">
                                        <option value="">All Status</option>
                                        <option value="PENDING">Pending</option>
                                        <option value="RECONCILED">Reconciled</option>
                                        <option value="DISCREPANCY">Discrepancy</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Branch</label>
                                    <select class="form-control" id="branchFilter">
                                        <option value="">All Branches</option>
                                        <!-- Options will be loaded via AJAX -->
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
                        <button class="btn btn-primary" onclick="filterReconciliations()">
                            <i class="fa fa-filter"></i> Apply Filters
                        </button>
                        <button class="btn btn-secondary" onclick="clearFilters()">
                            <i class="fa fa-refresh"></i> Clear Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reconciliation Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Branch Reconciliation Records</h4>
                        <div class="table-responsive">
                            <table id="reconciliationTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Reconciliation ID</th>
                                        <th>Date</th>
                                        <th>Branch Name</th>
                                        <th>Expected Amount</th>
                                        <th>Actual Amount</th>
                                        <th>Variance</th>
                                        <th>Status</th>
                                        <th>Admin</th>
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
    loadReconciliations();
    loadReconciliationStatistics();
    loadPharmacies();
    
    // Set today's date as default
    $('#reconciliationDate').val(new Date().toISOString().split('T')[0]);
    
    // Form submission
    $('#reconciliationForm').on('submit', function(e) {
        e.preventDefault();
        addReconciliation();
    });
    
    // Auto-calculate variance
    $('#expectedAmount, #actualAmount').on('input', function() {
        const expected = parseFloat($('#expectedAmount').val()) || 0;
        const actual = parseFloat($('#actualAmount').val()) || 0;
        const variance = actual - expected;
        $('#variance').val(variance.toFixed(2));
    });
});

function loadReconciliations() {
    $.ajax({
        url: 'php_action/get_branch_reconciliations.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#reconciliationTable').DataTable({
                    data: response.data,
                    columns: [
                        { data: 'reconciliation_id' },
                        { 
                            data: 'reconciliation_date',
                            render: function(data, type, row) {
                                return new Date(data).toLocaleDateString();
                            }
                        },
                        { data: 'branch_name' },
                        { 
                            data: 'expected_amount',
                            render: function(data, type, row) {
                                return 'RWF ' + parseFloat(data).toLocaleString();
                            }
                        },
                        { 
                            data: 'actual_amount',
                            render: function(data, type, row) {
                                return 'RWF ' + parseFloat(data).toLocaleString();
                            }
                        },
                        { 
                            data: 'variance',
                            render: function(data, type, row) {
                                const variance = parseFloat(data);
                                const badgeClass = variance === 0 ? 'badge-success' : (variance > 0 ? 'badge-info' : 'badge-danger');
                                return '<span class="badge ' + badgeClass + '">RWF ' + variance.toLocaleString() + '</span>';
                            }
                        },
                        { 
                            data: 'status',
                            render: function(data, type, row) {
                                let badgeClass = 'badge-secondary';
                                if (data === 'RECONCILED') badgeClass = 'badge-success';
                                else if (data === 'PENDING') badgeClass = 'badge-warning';
                                else if (data === 'DISCREPANCY') badgeClass = 'badge-danger';
                                return '<span class="badge ' + badgeClass + '">' + data + '</span>';
                            }
                        },
                        { data: 'admin_name' },
                        { 
                            data: 'created_at',
                            render: function(data, type, row) {
                                return new Date(data).toLocaleString();
                            }
                        },
                        {
                            data: null,
                            render: function(data, type, row) {
                                let actions = '<button class="btn btn-sm btn-info" onclick="viewReconciliationDetails(' + row.reconciliation_id + ')">View</button>';
                                if (row.status === 'PENDING') {
                                    actions += ' <button class="btn btn-sm btn-success" onclick="markAsReconciled(' + row.reconciliation_id + ')">Mark Reconciled</button>';
                                    actions += ' <button class="btn btn-sm btn-warning" onclick="markAsDiscrepancy(' + row.reconciliation_id + ')">Mark Discrepancy</button>';
                                }
                                return actions;
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

function loadReconciliationStatistics() {
    $.ajax({
        url: 'php_action/get_reconciliation_statistics.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#totalReconciliations').text(response.data.total_reconciliations);
                $('#reconciledBranches').text(response.data.reconciled_branches);
                $('#pendingReconciliations').text(response.data.pending_reconciliations);
                $('#discrepancyBranches').text(response.data.discrepancy_branches);
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
                let options = '<option value="">Select Branch</option>';
                let filterOptions = '<option value="">All Branches</option>';
                response.data.forEach(function(pharmacy) {
                    options += '<option value="' + pharmacy.name + '">' + pharmacy.name + '</option>';
                    filterOptions += '<option value="' + pharmacy.name + '">' + pharmacy.name + '</option>';
                });
                $('#branchName').html(options);
                $('#branchFilter').html(filterOptions);
            }
        }
    });
}

function addReconciliation() {
    const formData = {
        reconciliation_date: $('#reconciliationDate').val(),
        branch_name: $('#branchName').val(),
        expected_amount: $('#expectedAmount').val(),
        actual_amount: $('#actualAmount').val(),
        notes: $('#notes').val()
    };
    
    $.ajax({
        url: 'php_action/add_branch_reconciliation.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Reconciliation added successfully!');
                $('#reconciliationForm')[0].reset();
                $('#reconciliationDate').val(new Date().toISOString().split('T')[0]);
                loadReconciliations();
                loadReconciliationStatistics();
            } else {
                alert('Error: ' + response.message);
            }
        }
    });
}

function filterReconciliations() {
    const status = $('#statusFilter').val();
    const branch = $('#branchFilter').val();
    const dateFrom = $('#dateFrom').val();
    const dateTo = $('#dateTo').val();
    
    // Reload table with filters
    $('#reconciliationTable').DataTable().destroy();
    loadReconciliations();
}

function clearFilters() {
    $('#statusFilter').val('');
    $('#branchFilter').val('');
    $('#dateFrom').val('');
    $('#dateTo').val('');
    filterReconciliations();
}

function viewReconciliationDetails(reconciliationId) {
    $.ajax({
        url: 'php_action/get_reconciliation_details.php',
        type: 'GET',
        data: { reconciliation_id: reconciliationId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showReconciliationDetailsModal(response.data);
            }
        }
    });
}

function markAsReconciled(reconciliationId) {
    if (confirm('Are you sure you want to mark this reconciliation as reconciled?')) {
        $.ajax({
            url: 'php_action/update_reconciliation_status.php',
            type: 'POST',
            data: { 
                reconciliation_id: reconciliationId,
                status: 'RECONCILED'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Reconciliation marked as reconciled successfully!');
                    loadReconciliations();
                    loadReconciliationStatistics();
                } else {
                    alert('Error: ' + response.message);
                }
            }
        });
    }
}

function markAsDiscrepancy(reconciliationId) {
    const notes = prompt('Please provide notes for the discrepancy:');
    if (notes) {
        $.ajax({
            url: 'php_action/update_reconciliation_status.php',
            type: 'POST',
            data: { 
                reconciliation_id: reconciliationId,
                status: 'DISCREPANCY',
                notes: notes
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Reconciliation marked as discrepancy successfully!');
                    loadReconciliations();
                    loadReconciliationStatistics();
                } else {
                    alert('Error: ' + response.message);
                }
            }
        });
    }
}

function showReconciliationDetailsModal(reconciliationData) {
    const modal = `
        <div class="modal fade" id="reconciliationDetailsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reconciliation Details</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Reconciliation ID:</strong> ${reconciliationData.reconciliation_id}<br>
                                <strong>Date:</strong> ${new Date(reconciliationData.reconciliation_date).toLocaleDateString()}<br>
                                <strong>Branch Name:</strong> ${reconciliationData.branch_name}<br>
                                <strong>Expected Amount:</strong> RWF ${parseFloat(reconciliationData.expected_amount).toLocaleString()}<br>
                                <strong>Actual Amount:</strong> RWF ${parseFloat(reconciliationData.actual_amount).toLocaleString()}<br>
                                <strong>Variance:</strong> RWF ${parseFloat(reconciliationData.variance).toLocaleString()}<br>
                                <strong>Status:</strong> <span class="badge badge-warning">${reconciliationData.status}</span><br>
                                <strong>Admin:</strong> ${reconciliationData.admin_name}<br>
                                <strong>Created At:</strong> ${new Date(reconciliationData.created_at).toLocaleString()}
                            </div>
                            <div class="col-md-6">
                                <strong>Notes:</strong><br>
                                <p class="bg-light p-2">${reconciliationData.notes || 'No notes available'}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('body').append(modal);
    $('#reconciliationDetailsModal').modal('show');
    $('#reconciliationDetailsModal').on('hidden.bs.modal', function() {
        $(this).remove();
    });
}
</script>
