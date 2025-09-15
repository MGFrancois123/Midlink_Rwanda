<?php include('./constant/check.php'); ?>
<?php include('./constant/layout/head.php'); ?>
<?php include('./constant/layout/header.php'); ?>
<?php include('./constant/layout/sidebar.php'); ?>

<div class="page-wrapper">
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Security Logs</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard_super.php">Home</a></li>
                    <li class="breadcrumb-item active">Audit & Compliance</li>
                    <li class="breadcrumb-item active">Security Logs</li>
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
                                <h3 class="text-white" id="totalSecurityLogs">0</h3>
                                <h6 class="text-white">Total Security Events</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-shield fa-2x"></i>
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
                                <h3 class="text-white" id="criticalEvents">0</h3>
                                <h6 class="text-white">Critical Events</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-exclamation-triangle fa-2x"></i>
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
                                <h3 class="text-white" id="highEvents">0</h3>
                                <h6 class="text-white">High Severity</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-warning fa-2x"></i>
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
                                <h3 class="text-white" id="todayEvents">0</h3>
                                <h6 class="text-white">Today's Events</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-calendar fa-2x"></i>
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
                        <h4 class="card-title">Filter Security Events</h4>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Event Type</label>
                                    <select class="form-control" id="eventTypeFilter">
                                        <option value="">All Events</option>
                                        <option value="LOGIN">Login Attempts</option>
                                        <option value="LOGOUT">Logout Events</option>
                                        <option value="FAILED_LOGIN">Failed Login</option>
                                        <option value="PASSWORD_CHANGE">Password Change</option>
                                        <option value="PERMISSION_DENIED">Permission Denied</option>
                                        <option value="SUSPICIOUS_ACTIVITY">Suspicious Activity</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Severity</label>
                                    <select class="form-control" id="severityFilter">
                                        <option value="">All Severities</option>
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
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
                        <button class="btn btn-primary" onclick="filterSecurityLogs()">
                            <i class="fa fa-filter"></i> Apply Filters
                        </button>
                        <button class="btn btn-secondary" onclick="clearFilters()">
                            <i class="fa fa-refresh"></i> Clear Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Logs Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Security Events</h4>
                        <div class="table-responsive">
                            <table id="securityLogsTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Log ID</th>
                                        <th>User ID</th>
                                        <th>Event Type</th>
                                        <th>Severity</th>
                                        <th>Description</th>
                                        <th>User Agent</th>
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
    loadSecurityLogs();
    loadSecurityStatistics();
});

function loadSecurityLogs() {
    $.ajax({
        url: 'php_action/get_security_logs.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#securityLogsTable').DataTable({
                    data: response.data,
                    columns: [
                        { data: 'log_id' },
                        { data: 'user_id' },
                        { 
                            data: 'event_type',
                            render: function(data, type, row) {
                                return '<span class="badge badge-info">' + data + '</span>';
                            }
                        },
                        { 
                            data: 'severity',
                            render: function(data, type, row) {
                                let badgeClass = 'badge-secondary';
                                if (data === 'critical') badgeClass = 'badge-danger';
                                else if (data === 'high') badgeClass = 'badge-warning';
                                else if (data === 'medium') badgeClass = 'badge-info';
                                else if (data === 'low') badgeClass = 'badge-success';
                                return '<span class="badge ' + badgeClass + '">' + data.toUpperCase() + '</span>';
                            }
                        },
                        { data: 'description' },
                        { 
                            data: 'user_agent',
                            render: function(data, type, row) {
                                return data ? data.substring(0, 50) + '...' : 'N/A';
                            }
                        },
                        { 
                            data: 'created_at',
                            render: function(data, type, row) {
                                return new Date(data).toLocaleString();
                            }
                        },
                        {
                            data: null,
                            render: function(data, type, row) {
                                return '<button class="btn btn-sm btn-info" onclick="viewSecurityDetails(' + row.log_id + ')">View Details</button>';
                            }
                        }
                    ],
                    order: [[6, 'desc']],
                    pageLength: 10
                });
            }
        }
    });
}

function loadSecurityStatistics() {
    $.ajax({
        url: 'php_action/get_security_statistics.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#totalSecurityLogs').text(response.data.total_logs);
                $('#criticalEvents').text(response.data.critical_events);
                $('#highEvents').text(response.data.high_events);
                $('#todayEvents').text(response.data.today_events);
            }
        }
    });
}

function filterSecurityLogs() {
    const eventType = $('#eventTypeFilter').val();
    const severity = $('#severityFilter').val();
    const dateFrom = $('#dateFrom').val();
    const dateTo = $('#dateTo').val();
    
    // Reload table with filters
    $('#securityLogsTable').DataTable().destroy();
    loadSecurityLogs();
}

function clearFilters() {
    $('#eventTypeFilter').val('');
    $('#severityFilter').val('');
    $('#dateFrom').val('');
    $('#dateTo').val('');
    filterSecurityLogs();
}

function viewSecurityDetails(logId) {
    $.ajax({
        url: 'php_action/get_security_details.php',
        type: 'GET',
        data: { log_id: logId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showSecurityDetailsModal(response.data);
            }
        }
    });
}

function showSecurityDetailsModal(logData) {
    const modal = `
        <div class="modal fade" id="securityDetailsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Security Event Details</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Log ID:</strong> ${logData.log_id}<br>
                                <strong>User ID:</strong> ${logData.user_id || 'N/A'}<br>
                                <strong>Event Type:</strong> <span class="badge badge-info">${logData.event_type}</span><br>
                                <strong>Severity:</strong> <span class="badge badge-warning">${logData.severity.toUpperCase()}</span><br>
                                <strong>Created At:</strong> ${new Date(logData.created_at).toLocaleString()}
                            </div>
                            <div class="col-md-6">
                                <strong>Description:</strong><br>
                                <p class="bg-light p-2">${logData.description}</p>
                                <strong>User Agent:</strong><br>
                                <small class="text-muted">${logData.user_agent || 'N/A'}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('body').append(modal);
    $('#securityDetailsModal').modal('show');
    $('#securityDetailsModal').on('hidden.bs.modal', function() {
        $(this).remove();
    });
}
</script>
