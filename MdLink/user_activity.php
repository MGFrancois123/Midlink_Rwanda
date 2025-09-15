<?php include('./constant/check.php'); ?>
<?php include('./constant/layout/head.php'); ?>
<?php include('./constant/layout/header.php'); ?>
<?php include('./constant/layout/sidebar.php'); ?>

<div class="page-wrapper">
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">User Activity</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard_super.php">Home</a></li>
                    <li class="breadcrumb-item active">User Management</li>
                    <li class="breadcrumb-item active">User Activity</li>
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
                                <h3 class="text-white" id="totalActivities">0</h3>
                                <h6 class="text-white">Total Activities</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-history fa-2x"></i>
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
                                <h3 class="text-white" id="activeUsers">0</h3>
                                <h6 class="text-white">Active Users</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-user fa-2x"></i>
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
                                <h3 class="text-white" id="todayActivities">0</h3>
                                <h6 class="text-white">Today's Activities</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-calendar fa-2x"></i>
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
                                <h3 class="text-white" id="systemLogins">0</h3>
                                <h6 class="text-white">System Logins</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-sign-in fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Chart -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">User Activity Trend (Last 30 Days)</h4>
                        <canvas id="activityChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Filter User Activity</h4>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>User</label>
                                    <select class="form-control" id="userFilter">
                                        <option value="">All Users</option>
                                        <!-- Options will be loaded via AJAX -->
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Activity Type</label>
                                    <select class="form-control" id="activityTypeFilter">
                                        <option value="">All Activities</option>
                                        <option value="LOGIN">Login</option>
                                        <option value="LOGOUT">Logout</option>
                                        <option value="CREATE">Create</option>
                                        <option value="UPDATE">Update</option>
                                        <option value="DELETE">Delete</option>
                                        <option value="VIEW">View</option>
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
                        <button class="btn btn-primary" onclick="filterUserActivity()">
                            <i class="fa fa-filter"></i> Apply Filters
                        </button>
                        <button class="btn btn-secondary" onclick="clearFilters()">
                            <i class="fa fa-refresh"></i> Clear Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Activity Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">User Activity Log</h4>
                        <div class="table-responsive">
                            <table id="userActivityTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Log ID</th>
                                        <th>User</th>
                                        <th>Activity Type</th>
                                        <th>Table/Resource</th>
                                        <th>Record ID</th>
                                        <th>Description</th>
                                        <th>IP Address</th>
                                        <th>User Agent</th>
                                        <th>Timestamp</th>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    loadUserActivity();
    loadUserActivityStatistics();
    loadUsers();
    loadActivityChart();
    
    // Set default date range (last 30 days)
    const today = new Date();
    const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));
    $('#dateFrom').val(thirtyDaysAgo.toISOString().split('T')[0]);
    $('#dateTo').val(today.toISOString().split('T')[0]);
});

function loadUserActivity() {
    $.ajax({
        url: 'php_action/get_user_activity.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#userActivityTable').DataTable({
                    data: response.data,
                    columns: [
                        { data: 'log_id' },
                        { data: 'username' },
                        { 
                            data: 'action',
                            render: function(data, type, row) {
                                let badgeClass = 'badge-secondary';
                                if (data === 'LOGIN') badgeClass = 'badge-success';
                                else if (data === 'LOGOUT') badgeClass = 'badge-warning';
                                else if (data === 'CREATE') badgeClass = 'badge-primary';
                                else if (data === 'UPDATE') badgeClass = 'badge-info';
                                else if (data === 'DELETE') badgeClass = 'badge-danger';
                                return '<span class="badge ' + badgeClass + '">' + data + '</span>';
                            }
                        },
                        { data: 'table_name' },
                        { data: 'record_id' },
                        { data: 'description' },
                        { data: 'ip_address' },
                        { 
                            data: 'user_agent',
                            render: function(data, type, row) {
                                return data ? data.substring(0, 50) + '...' : 'N/A';
                            }
                        },
                        { 
                            data: 'action_time',
                            render: function(data, type, row) {
                                return new Date(data).toLocaleString();
                            }
                        },
                        {
                            data: null,
                            render: function(data, type, row) {
                                return '<button class="btn btn-sm btn-info" onclick="viewActivityDetails(' + row.log_id + ')">View Details</button>';
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

function loadUserActivityStatistics() {
    $.ajax({
        url: 'php_action/get_user_activity_statistics.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#totalActivities').text(response.data.total_activities);
                $('#activeUsers').text(response.data.active_users);
                $('#todayActivities').text(response.data.today_activities);
                $('#systemLogins').text(response.data.system_logins);
            }
        }
    });
}

function loadUsers() {
    $.ajax({
        url: 'php_action/get_admin_users.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                let options = '<option value="">All Users</option>';
                response.data.forEach(function(user) {
                    options += '<option value="' + user.admin_id + '">' + user.username + ' (' + user.role + ')</option>';
                });
                $('#userFilter').html(options);
            }
        }
    });
}

function loadActivityChart() {
    $.ajax({
        url: 'php_action/get_activity_chart_data.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const ctx = document.getElementById('activityChart').getContext('2d');
                
                if (window.activityChart) {
                    window.activityChart.destroy();
                }
                
                window.activityChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: response.data.labels,
                        datasets: [{
                            label: 'Daily Activities',
                            data: response.data.activities,
                            borderColor: 'rgb(75, 192, 192)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            tension: 0.1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        }
    });
}

function filterUserActivity() {
    const user = $('#userFilter').val();
    const activityType = $('#activityTypeFilter').val();
    const dateFrom = $('#dateFrom').val();
    const dateTo = $('#dateTo').val();
    
    // Reload table with filters
    $('#userActivityTable').DataTable().destroy();
    loadUserActivity();
}

function clearFilters() {
    $('#userFilter').val('');
    $('#activityTypeFilter').val('');
    $('#dateFrom').val('');
    $('#dateTo').val('');
    filterUserActivity();
}

function viewActivityDetails(logId) {
    $.ajax({
        url: 'php_action/get_activity_details.php',
        type: 'GET',
        data: { log_id: logId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showActivityDetailsModal(response.data);
            }
        }
    });
}

function showActivityDetailsModal(activityData) {
    const modal = `
        <div class="modal fade" id="activityDetailsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Activity Details</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Log ID:</strong> ${activityData.log_id}<br>
                                <strong>User:</strong> ${activityData.username} (${activityData.role})<br>
                                <strong>Activity Type:</strong> <span class="badge badge-info">${activityData.action}</span><br>
                                <strong>Table/Resource:</strong> ${activityData.table_name}<br>
                                <strong>Record ID:</strong> ${activityData.record_id || 'N/A'}<br>
                                <strong>IP Address:</strong> ${activityData.ip_address}<br>
                                <strong>Timestamp:</strong> ${new Date(activityData.action_time).toLocaleString()}
                            </div>
                            <div class="col-md-6">
                                <strong>Description:</strong><br>
                                <p class="bg-light p-2">${activityData.description}</p>
                                <strong>User Agent:</strong><br>
                                <p class="bg-light p-2 small">${activityData.user_agent || 'Not available'}</p>
                            </div>
                        </div>
                        ${activityData.old_data || activityData.new_data ? `
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <strong>Old Data:</strong><br>
                                <pre class="bg-light p-2 small">${activityData.old_data ? JSON.stringify(JSON.parse(activityData.old_data), null, 2) : 'N/A'}</pre>
                            </div>
                            <div class="col-md-6">
                                <strong>New Data:</strong><br>
                                <pre class="bg-light p-2 small">${activityData.new_data ? JSON.stringify(JSON.parse(activityData.new_data), null, 2) : 'N/A'}</pre>
                            </div>
                        </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('body').append(modal);
    $('#activityDetailsModal').modal('show');
    $('#activityDetailsModal').on('hidden.bs.modal', function() {
        $(this).remove();
    });
}
</script>
