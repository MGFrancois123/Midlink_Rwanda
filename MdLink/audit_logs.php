<?php include('./constant/check.php'); ?>
<?php include('./constant/layout/head.php'); ?>
<?php include('./constant/layout/header.php'); ?>
<?php include('./constant/layout/sidebar.php'); ?>

<div class="page-wrapper">
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Audit Logs</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard_super.php">Home</a></li>
                    <li class="breadcrumb-item active">Audit & Compliance</li>
                    <li class="breadcrumb-item active">Audit Logs</li>
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
                                <h3 class="text-white" id="totalLogs">0</h3>
                                <h6 class="text-white">Total Logs</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-list-alt fa-2x"></i>
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
                                <h3 class="text-white" id="todayLogs">0</h3>
                                <h6 class="text-white">Today's Logs</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-calendar fa-2x"></i>
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
                                <h3 class="text-white" id="createLogs">0</h3>
                                <h6 class="text-white">Create Actions</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-plus fa-2x"></i>
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
                                <h3 class="text-white" id="deleteLogs">0</h3>
                                <h6 class="text-white">Delete Actions</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-trash fa-2x"></i>
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
                        <h4 class="card-title">Filter Audit Logs</h4>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Action Type</label>
                                    <select class="form-control" id="actionFilter">
                                        <option value="">All Actions</option>
                                        <option value="CREATE">CREATE</option>
                                        <option value="UPDATE">UPDATE</option>
                                        <option value="DELETE">DELETE</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Table Name</label>
                                    <select class="form-control" id="tableFilter">
                                        <option value="">All Tables</option>
                                        <option value="admin_users">Admin Users</option>
                                        <option value="medicines">Medicines</option>
                                        <option value="pharmacies">Pharmacies</option>
                                        <option value="payments">Payments</option>
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
                        <button class="btn btn-primary" onclick="filterLogs()">
                            <i class="fa fa-filter"></i> Apply Filters
                        </button>
                        <button class="btn btn-secondary" onclick="clearFilters()">
                            <i class="fa fa-refresh"></i> Clear Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Audit Logs Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Audit Logs</h4>
                        <div class="table-responsive">
                            <table id="auditLogsTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Log ID</th>
                                        <th>Admin</th>
                                        <th>Table</th>
                                        <th>Action</th>
                                        <th>Record ID</th>
                                        <th>Action Time</th>
                                        <th>Details</th>
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
    loadAuditLogs();
    loadStatistics();
});

function loadAuditLogs() {
    $.ajax({
        url: 'php_action/get_audit_logs.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#auditLogsTable').DataTable({
                    data: response.data,
                    columns: [
                        { data: 'log_id' },
                        { data: 'admin_name' },
                        { data: 'table_name' },
                        { 
                            data: 'action',
                            render: function(data, type, row) {
                                let badgeClass = 'badge-secondary';
                                if (data === 'CREATE') badgeClass = 'badge-success';
                                else if (data === 'UPDATE') badgeClass = 'badge-warning';
                                else if (data === 'DELETE') badgeClass = 'badge-danger';
                                return '<span class="badge ' + badgeClass + '">' + data + '</span>';
                            }
                        },
                        { data: 'record_id' },
                        { 
                            data: 'action_time',
                            render: function(data, type, row) {
                                return new Date(data).toLocaleString();
                            }
                        },
                        {
                            data: null,
                            render: function(data, type, row) {
                                return '<button class="btn btn-sm btn-info" onclick="viewLogDetails(' + row.log_id + ')">View Details</button>';
                            }
                        }
                    ],
                    order: [[5, 'desc']],
                    pageLength: 10
                });
            }
        }
    });
}

function loadStatistics() {
    $.ajax({
        url: 'php_action/get_audit_statistics.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#totalLogs').text(response.data.total_logs);
                $('#todayLogs').text(response.data.today_logs);
                $('#createLogs').text(response.data.create_logs);
                $('#deleteLogs').text(response.data.delete_logs);
            }
        }
    });
}

function filterLogs() {
    const action = $('#actionFilter').val();
    const table = $('#tableFilter').val();
    const dateFrom = $('#dateFrom').val();
    const dateTo = $('#dateTo').val();
    
    // Reload table with filters
    $('#auditLogsTable').DataTable().destroy();
    loadAuditLogs();
}

function clearFilters() {
    $('#actionFilter').val('');
    $('#tableFilter').val('');
    $('#dateFrom').val('');
    $('#dateTo').val('');
    filterLogs();
}

function viewLogDetails(logId) {
    // Open modal with log details
    $.ajax({
        url: 'php_action/get_log_details.php',
        type: 'GET',
        data: { log_id: logId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Show modal with log details
                showLogDetailsModal(response.data);
            }
        }
    });
}

function showLogDetailsModal(logData) {
    const modal = `
        <div class="modal fade" id="logDetailsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Audit Log Details</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Log ID:</strong> ${logData.log_id}<br>
                                <strong>Admin:</strong> ${logData.admin_name}<br>
                                <strong>Table:</strong> ${logData.table_name}<br>
                                <strong>Action:</strong> <span class="badge badge-primary">${logData.action}</span><br>
                                <strong>Record ID:</strong> ${logData.record_id}<br>
                                <strong>Action Time:</strong> ${new Date(logData.action_time).toLocaleString()}
                            </div>
                            <div class="col-md-6">
                                <strong>Old Data:</strong><br>
                                <pre class="bg-light p-2">${logData.old_data || 'N/A'}</pre>
                                <strong>New Data:</strong><br>
                                <pre class="bg-light p-2">${logData.new_data || 'N/A'}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('body').append(modal);
    $('#logDetailsModal').modal('show');
    $('#logDetailsModal').on('hidden.bs.modal', function() {
        $(this).remove();
    });
}
</script>