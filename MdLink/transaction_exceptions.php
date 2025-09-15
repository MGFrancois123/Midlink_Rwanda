<?php include('./constant/check.php'); ?>
<?php include('./constant/layout/head.php'); ?>
<?php include('./constant/layout/header.php'); ?>
<?php include('./constant/layout/sidebar.php'); ?>

<div class="page-wrapper">
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Transaction Exceptions</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard_super.php">Home</a></li>
                    <li class="breadcrumb-item active">Reports & Analytics</li>
                    <li class="breadcrumb-item active">Transaction Exceptions</li>
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
                                <h3 class="text-white" id="totalExceptions">0</h3>
                                <h6 class="text-white">Total Exceptions</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-exclamation-circle fa-2x"></i>
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
                                <h3 class="text-white" id="criticalExceptions">0</h3>
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
                                <h3 class="text-white" id="openExceptions">0</h3>
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
                                <h3 class="text-white" id="resolvedExceptions">0</h3>
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

        <!-- Filters -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Filter Transaction Exceptions</h4>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Exception Type</label>
                                    <select class="form-control" id="exceptionTypeFilter">
                                        <option value="">All Types</option>
                                        <option value="AMOUNT_MISMATCH">Amount Mismatch</option>
                                        <option value="DUPLICATE_TRANSACTION">Duplicate Transaction</option>
                                        <option value="INVALID_MEDICINE">Invalid Medicine</option>
                                        <option value="SYSTEM_ERROR">System Error</option>
                                        <option value="MANUAL_REVIEW">Manual Review</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Severity</label>
                                    <select class="form-control" id="severityFilter">
                                        <option value="">All Severities</option>
                                        <option value="LOW">Low</option>
                                        <option value="MEDIUM">Medium</option>
                                        <option value="HIGH">High</option>
                                        <option value="CRITICAL">Critical</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control" id="statusFilter">
                                        <option value="">All Status</option>
                                        <option value="OPEN">Open</option>
                                        <option value="INVESTIGATING">Investigating</option>
                                        <option value="RESOLVED">Resolved</option>
                                        <option value="ESCALATED">Escalated</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date From</label>
                                    <input type="date" class="form-control" id="dateFrom">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date To</label>
                                    <input type="date" class="form-control" id="dateTo">
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label>&nbsp;</label><br>
                                    <button class="btn btn-primary" onclick="filterExceptions()">
                                        <i class="fa fa-filter"></i> Apply Filters
                                    </button>
                                    <button class="btn btn-secondary" onclick="clearFilters()">
                                        <i class="fa fa-refresh"></i> Clear Filters
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaction Exceptions Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Transaction Exceptions</h4>
                        <div class="table-responsive">
                            <table id="transactionExceptionsTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Exception ID</th>
                                        <th>Transaction ID</th>
                                        <th>Exception Type</th>
                                        <th>Severity</th>
                                        <th>Status</th>
                                        <th>Exposure Amount</th>
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
    loadTransactionExceptions();
    loadExceptionStatistics();
});

function loadTransactionExceptions() {
    $.ajax({
        url: 'php_action/get_transaction_exceptions.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#transactionExceptionsTable').DataTable({
                    data: response.data,
                    columns: [
                        { data: 'exception_id' },
                        { data: 'transaction_id' },
                        { 
                            data: 'exception_type',
                            render: function(data, type, row) {
                                return '<span class="badge badge-info">' + data.replace('_', ' ').toUpperCase() + '</span>';
                            }
                        },
                        { 
                            data: 'severity',
                            render: function(data, type, row) {
                                let badgeClass = 'badge-secondary';
                                if (data === 'CRITICAL') badgeClass = 'badge-danger';
                                else if (data === 'HIGH') badgeClass = 'badge-warning';
                                else if (data === 'MEDIUM') badgeClass = 'badge-info';
                                else if (data === 'LOW') badgeClass = 'badge-success';
                                return '<span class="badge ' + badgeClass + '">' + data + '</span>';
                            }
                        },
                        { 
                            data: 'status',
                            render: function(data, type, row) {
                                let badgeClass = 'badge-secondary';
                                if (data === 'RESOLVED') badgeClass = 'badge-success';
                                else if (data === 'INVESTIGATING') badgeClass = 'badge-warning';
                                else if (data === 'ESCALATED') badgeClass = 'badge-danger';
                                else if (data === 'OPEN') badgeClass = 'badge-info';
                                return '<span class="badge ' + badgeClass + '">' + data + '</span>';
                            }
                        },
                        { 
                            data: 'exposure_amount',
                            render: function(data, type, row) {
                                return 'RWF ' + parseFloat(data).toLocaleString();
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
                                let actions = '<button class="btn btn-sm btn-info" onclick="viewExceptionDetails(' + row.exception_id + ')">View</button>';
                                if (row.status === 'OPEN') {
                                    actions += ' <button class="btn btn-sm btn-warning" onclick="startInvestigation(' + row.exception_id + ')">Start Investigation</button>';
                                    actions += ' <button class="btn btn-sm btn-success" onclick="resolveException(' + row.exception_id + ')">Resolve</button>';
                                } else if (row.status === 'INVESTIGATING') {
                                    actions += ' <button class="btn btn-sm btn-success" onclick="resolveException(' + row.exception_id + ')">Resolve</button>';
                                    actions += ' <button class="btn btn-sm btn-danger" onclick="escalateException(' + row.exception_id + ')">Escalate</button>';
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

function loadExceptionStatistics() {
    $.ajax({
        url: 'php_action/get_exception_statistics.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#totalExceptions').text(response.data.total_exceptions);
                $('#criticalExceptions').text(response.data.critical_exceptions);
                $('#openExceptions').text(response.data.open_exceptions);
                $('#resolvedExceptions').text(response.data.resolved_exceptions);
            }
        }
    });
}

function filterExceptions() {
    const exceptionType = $('#exceptionTypeFilter').val();
    const severity = $('#severityFilter').val();
    const status = $('#statusFilter').val();
    const dateFrom = $('#dateFrom').val();
    const dateTo = $('#dateTo').val();
    
    // Reload table with filters
    $('#transactionExceptionsTable').DataTable().destroy();
    loadTransactionExceptions();
}

function clearFilters() {
    $('#exceptionTypeFilter').val('');
    $('#severityFilter').val('');
    $('#statusFilter').val('');
    $('#dateFrom').val('');
    $('#dateTo').val('');
    filterExceptions();
}

function viewExceptionDetails(exceptionId) {
    $.ajax({
        url: 'php_action/get_exception_details.php',
        type: 'GET',
        data: { exception_id: exceptionId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showExceptionDetailsModal(response.data);
            }
        }
    });
}

function startInvestigation(exceptionId) {
    if (confirm('Are you sure you want to start investigation for this exception?')) {
        $.ajax({
            url: 'php_action/update_exception_status.php',
            type: 'POST',
            data: { 
                exception_id: exceptionId,
                status: 'INVESTIGATING'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Investigation started successfully!');
                    loadTransactionExceptions();
                    loadExceptionStatistics();
                } else {
                    alert('Error: ' + response.message);
                }
            }
        });
    }
}

function resolveException(exceptionId) {
    const notes = prompt('Please provide resolution notes:');
    if (notes) {
        $.ajax({
            url: 'php_action/update_exception_status.php',
            type: 'POST',
            data: { 
                exception_id: exceptionId,
                status: 'RESOLVED',
                notes: notes
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Exception resolved successfully!');
                    loadTransactionExceptions();
                    loadExceptionStatistics();
                } else {
                    alert('Error: ' + response.message);
                }
            }
        });
    }
}

function escalateException(exceptionId) {
    const reason = prompt('Please provide escalation reason:');
    if (reason) {
        $.ajax({
            url: 'php_action/update_exception_status.php',
            type: 'POST',
            data: { 
                exception_id: exceptionId,
                status: 'ESCALATED',
                notes: reason
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Exception escalated successfully!');
                    loadTransactionExceptions();
                    loadExceptionStatistics();
                } else {
                    alert('Error: ' + response.message);
                }
            }
        });
    }
}

function showExceptionDetailsModal(exceptionData) {
    const modal = `
        <div class="modal fade" id="exceptionDetailsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Transaction Exception Details</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Exception ID:</strong> ${exceptionData.exception_id}<br>
                                <strong>Transaction ID:</strong> ${exceptionData.transaction_id}<br>
                                <strong>Exception Type:</strong> <span class="badge badge-info">${exceptionData.exception_type.replace('_', ' ').toUpperCase()}</span><br>
                                <strong>Severity:</strong> <span class="badge badge-warning">${exceptionData.severity}</span><br>
                                <strong>Status:</strong> <span class="badge badge-info">${exceptionData.status}</span><br>
                                <strong>Exposure Amount:</strong> RWF ${parseFloat(exceptionData.exposure_amount).toLocaleString()}<br>
                                <strong>Admin:</strong> ${exceptionData.admin_name}<br>
                                <strong>Created At:</strong> ${new Date(exceptionData.created_at).toLocaleString()}<br>
                                <strong>Resolved At:</strong> ${exceptionData.resolved_at ? new Date(exceptionData.resolved_at).toLocaleString() : 'Not resolved'}
                            </div>
                            <div class="col-md-6">
                                <strong>Description:</strong><br>
                                <p class="bg-light p-2">${exceptionData.description}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('body').append(modal);
    $('#exceptionDetailsModal').modal('show');
    $('#exceptionDetailsModal').on('hidden.bs.modal', function() {
        $(this).remove();
    });
}
</script>
