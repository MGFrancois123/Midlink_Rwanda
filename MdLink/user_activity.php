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

        <!-- Compact Statistics Row -->
        <div class="row mb-2">
            <div class="col-lg-3 col-md-6 mb-1">
                <div class="card bg-gradient-primary text-white compact-card">
                    <div class="card-body py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="text-white mb-0" id="totalActivities">0</h5>
                                <small class="text-white-50">Total Activities</small>
                            </div>
                            <i class="fa fa-history fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-1">
                <div class="card bg-gradient-success text-white compact-card">
                    <div class="card-body py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="text-white mb-0" id="activeUsers">0</h5>
                                <small class="text-white-50">Active Users</small>
                            </div>
                            <i class="fa fa-users fa-2x opacity-75"></i>
                            </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-1">
                <div class="card bg-gradient-warning text-white compact-card">
                    <div class="card-body py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="text-white mb-0" id="todayActivities">0</h5>
                                <small class="text-white-50">Today's Activities</small>
                            </div>
                            <i class="fa fa-calendar-day fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-1">
                <div class="card bg-gradient-info text-white compact-card">
                    <div class="card-body py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="text-white mb-0" id="systemLogins">0</h5>
                                <small class="text-white-50">System Logins</small>
                            </div>
                            <i class="fa fa-sign-in-alt fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <!-- Compact Charts Row -->
        <div class="row mb-2">
            <div class="col-lg-6">
                <div class="card compact-chart">
                    <div class="card-body py-2">
                        <h6 class="card-title mb-2">
                            <i class="fa fa-chart-line"></i> Activity Trend
                        </h6>
                        <canvas id="activityChart" height="120"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card compact-chart">
                    <div class="card-body py-2">
                        <h6 class="card-title mb-2">
                            <i class="fa fa-chart-pie"></i> Activity Breakdown
                        </h6>
                        <canvas id="activityBreakdownChart" height="120"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Compact Filters -->
        <div class="row mb-2">
            <div class="col-12">
                <div class="card compact-filters">
                    <div class="card-body py-2">
                        <div class="row align-items-end">
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-1">
                                <label class="form-label small">User</label>
                                <select class="form-control form-control-sm" id="userFilter">
                                    <option value="">All Users</option>
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-1">
                                <label class="form-label small">Activity</label>
                                <select class="form-control form-control-sm" id="activityTypeFilter">
                                    <option value="">All Activities</option>
                                    <option value="LOGIN">Login</option>
                                    <option value="LOGOUT">Logout</option>
                                    <option value="CREATE">Create</option>
                                    <option value="UPDATE">Update</option>
                                    <option value="DELETE">Delete</option>
                                    <option value="VIEW">View</option>
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-1">
                                <label class="form-label small">Date From</label>
                                <input type="date" class="form-control form-control-sm" id="dateFrom">
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-1">
                                <label class="form-label small">Date To</label>
                                <input type="date" class="form-control form-control-sm" id="dateTo">
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-1">
                                <label class="form-label small">Records</label>
                                <select class="form-control form-control-sm" id="recordsPerPage">
                                    <option value="25">25 per page</option>
                                    <option value="50">50 per page</option>
                                    <option value="100">100 per page</option>
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-1">
                                <button class="btn btn-primary btn-sm" onclick="filterUserActivity()">
                                    <i class="fa fa-filter"></i> Apply
                                </button>
                                <button class="btn btn-outline-secondary btn-sm" onclick="clearFilters()">
                                    <i class="fa fa-times"></i> Clear
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Activity Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="card-title mb-0">
                                <i class="fa fa-list"></i> Activity Log
                            </h6>
                            <div class="d-flex align-items-center">
                                <span class="badge badge-info mr-2" id="totalRecords">0 records</span>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" onclick="refreshTable()">
                                        <i class="fa fa-sync"></i>
                                    </button>
                                    <button class="btn btn-outline-success" onclick="exportTable()">
                                        <i class="fa fa-file-excel"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table id="userActivityTable" class="table table-striped table-hover table-sm">
                                <thead class="thead-dark">
                                    <tr>
                                        <th width="5%">ID</th>
                                        <th width="12%">User</th>
                                        <th width="8%">Action</th>
                                        <th width="10%">Resource</th>
                                        <th width="8%">Record ID</th>
                                        <th width="20%">Description</th>
                                        <th width="10%">IP Address</th>
                                        <th width="12%">Timestamp</th>
                                        <th width="15%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be loaded via AJAX -->
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination Container -->
                        <div id="paginationContainer" class="mt-3">
                            <!-- Pagination will be inserted here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('./constant/layout/footer.php'); ?>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.bg-gradient-success {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}
.bg-gradient-warning {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}
.bg-gradient-info {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.badge {
    font-size: 0.75em;
}

.table th {
    border-top: none;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 0.5px;
}

.table td {
    vertical-align: middle;
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.activity-badge {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}

.user-info {
    display: flex;
    flex-direction: column;
}

.user-name {
    font-weight: 600;
    color: #2c3e50;
}

.user-role {
    font-size: 0.75rem;
    color: #6c757d;
}

.timestamp {
    font-size: 0.8rem;
    color: #6c757d;
}

.description {
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.top-user-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #eee;
}

.top-user-item:last-child {
    border-bottom: none;
}

.user-activity-count {
    background: #e3f2fd;
    color: #1976d2;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

/* Performance optimizations */
.table-responsive {
    max-height: 70vh;
    overflow-y: auto;
}

#userActivityTable {
    table-layout: fixed;
    width: 100%;
}

#userActivityTable th,
#userActivityTable td {
    word-wrap: break-word;
    overflow: hidden;
    text-overflow: ellipsis;
}

#userActivityTable .description {
    max-width: 200px;
}

#userActivityTable .timestamp {
    min-width: 120px;
}

/* Pagination styling */
.pagination {
    margin-bottom: 0;
}

.page-link {
    color: #2f855a;
    border-color: #dee2e6;
}

.page-link:hover {
    color: #1e6b47;
    background-color: #e9ecef;
    border-color: #dee2e6;
}

.page-item.active .page-link {
    background-color: #2f855a;
    border-color: #2f855a;
}

/* Loading state improvements */
.loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #2f855a;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

/* Table performance */
.table tbody tr {
    transition: background-color 0.15s ease-in-out;
}

.table tbody tr:hover {
    background-color: rgba(47, 133, 90, 0.05);
}

/* Compact layout styles */
.compact-card {
    margin-bottom: 0.5rem;
}

.compact-card .card-body {
    padding: 0.75rem;
}

.compact-chart {
    margin-bottom: 0.5rem;
}

.compact-chart .card-body {
    padding: 0.75rem;
}

.compact-filters {
    margin-bottom: 0.5rem;
}

.compact-filters .card-body {
    padding: 0.75rem;
}

.compact-table {
    margin-bottom: 0.5rem;
}

.compact-table .card-body {
    padding: 0.75rem;
}

/* Reduce spacing throughout */
.mb-3 {
    margin-bottom: 0.75rem !important;
}

.mb-2 {
    margin-bottom: 0.5rem !important;
}

.mb-1 {
    margin-bottom: 0.25rem !important;
}

.py-2 {
    padding-top: 0.5rem !important;
    padding-bottom: 0.5rem !important;
}

/* Smaller form controls */
.form-control-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

/* Compact table */
.table-sm th,
.table-sm td {
    padding: 0.3rem;
}

/* Chart heights */
canvas {
    max-height: 120px;
}

/* Chart styling */
.compact-chart .card-body {
    padding: 0.75rem;
}

.compact-chart h6 {
    font-size: 1rem;
    margin-bottom: 0.5rem;
}

/* Make chart containers larger */
.compact-chart {
    height: auto;
    min-height: 150px;
}

/* Ultra-compact padding */
.py-1 {
    padding-top: 0.25rem !important;
    padding-bottom: 0.25rem !important;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let activityTable;
let activityChart, breakdownChart, hourlyChart;

$(document).ready(function() {
    initializePage();
});

function initializePage() {
    // Set default date range (last 30 days)
    const today = new Date();
    const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));
    $('#dateFrom').val(thirtyDaysAgo.toISOString().split('T')[0]);
    $('#dateTo').val(today.toISOString().split('T')[0]);
    
    // Load all data
    loadUserActivityStatistics();
    loadUsers();
    loadActivityCharts();
    loadUserActivity();
    loadTopUsers();
}

function loadUserActivity(page = 1) {
    const filters = getFilterParams();
    filters.page = page;
    filters.limit = parseInt($('#recordsPerPage').val()) || 25;
    
    $.ajax({
        url: 'php_action/get_user_activity.php',
        type: 'GET',
        data: filters,
        dataType: 'json',
        beforeSend: function() {
            $('#userActivityTable tbody').html('<tr><td colspan="9" class="text-center"><div class="loading-spinner"></div> Loading...</td></tr>');
            $('#paginationContainer').html('');
        },
        success: function(response) {
            if (response.success) {
                if (activityTable) {
                    activityTable.destroy();
                }
                
                // Update pagination info
                const startRecord = ((response.page - 1) * response.limit) + 1;
                const endRecord = Math.min(response.page * response.limit, response.total);
                $('#totalRecords').text(`Showing ${startRecord}-${endRecord} of ${response.total} records`);
                
                // Create simple table without DataTables for better performance
                let tableHtml = '';
                if (response.data.length > 0) {
                    response.data.forEach(function(row) {
                        const date = new Date(row.action_time);
                        const badgeClass = getActionBadgeClass(row.action);
                        const icon = getActionIcon(row.action);
                        
                        tableHtml += `
                            <tr>
                                <td class="text-center">${row.log_id}</td>
                                <td>
                                    <div class="user-info">
                                        <div class="user-name">${row.username || 'Unknown'}</div>
                                        <div class="user-role">${row.role || 'N/A'}</div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge ${badgeClass} activity-badge">
                                        <i class="fa ${icon}"></i> ${row.action}
                                    </span>
                                </td>
                                <td>${row.table_name ? `<code>${row.table_name}</code>` : 'N/A'}</td>
                                <td class="text-center">${row.record_id ? `<span class="badge badge-light">${row.record_id}</span>` : 'N/A'}</td>
                                <td class="description">${row.description || 'No description'}</td>
                                <td>${row.ip_address ? `<code>${row.ip_address}</code>` : 'N/A'}</td>
                                <td class="timestamp">
                                    <div>
                                        <div>${date.toLocaleDateString()}</div>
                                        <small>${date.toLocaleTimeString()}</small>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-info" onclick="viewActivityDetails(${row.log_id})" title="View Details">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-secondary" onclick="copyActivityInfo(${row.log_id})" title="Copy Info">
                                            <i class="fa fa-copy"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    tableHtml = '<tr><td colspan="9" class="text-center text-muted">No activity data available</td></tr>';
                }
                
                $('#userActivityTable tbody').html(tableHtml);
                
                // Create pagination
                createPagination(response.page, response.total_pages, response.total);
                
            } else {
                $('#userActivityTable tbody').html('<tr><td colspan="9" class="text-center text-danger">Error loading data: ' + response.message + '</td></tr>');
            }
        },
        error: function() {
            $('#userActivityTable tbody').html('<tr><td colspan="9" class="text-center text-danger">Error loading data</td></tr>');
        }
    });
}

function getActionBadgeClass(action) {
    switch(action) {
        case 'LOGIN': return 'badge-success';
        case 'LOGOUT': return 'badge-warning';
        case 'CREATE': return 'badge-primary';
        case 'UPDATE': return 'badge-info';
        case 'DELETE': return 'badge-danger';
        case 'VIEW': return 'badge-secondary';
        default: return 'badge-secondary';
    }
}

function getActionIcon(action) {
    switch(action) {
        case 'LOGIN': return 'fa-sign-in-alt';
        case 'LOGOUT': return 'fa-sign-out-alt';
        case 'CREATE': return 'fa-plus';
        case 'UPDATE': return 'fa-edit';
        case 'DELETE': return 'fa-trash';
        case 'VIEW': return 'fa-eye';
        default: return 'fa-circle';
    }
}

function createPagination(currentPage, totalPages, totalRecords) {
    if (totalPages <= 1) return;
    
    let paginationHtml = '<nav aria-label="Activity pagination"><ul class="pagination justify-content-center">';
    
    // Previous button
    if (currentPage > 1) {
        paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="loadUserActivity(${currentPage - 1}); return false;">Previous</a></li>`;
    } else {
        paginationHtml += '<li class="page-item disabled"><span class="page-link">Previous</span></li>';
    }
    
    // Page numbers
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);
    
    if (startPage > 1) {
        paginationHtml += '<li class="page-item"><a class="page-link" href="#" onclick="loadUserActivity(1); return false;">1</a></li>';
        if (startPage > 2) {
            paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }
    
    for (let i = startPage; i <= endPage; i++) {
        if (i === currentPage) {
            paginationHtml += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
        } else {
            paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="loadUserActivity(${i}); return false;">${i}</a></li>`;
        }
    }
    
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="loadUserActivity(${totalPages}); return false;">${totalPages}</a></li>`;
    }
    
    // Next button
    if (currentPage < totalPages) {
        paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="loadUserActivity(${currentPage + 1}); return false;">Next</a></li>`;
    } else {
        paginationHtml += '<li class="page-item disabled"><span class="page-link">Next</span></li>';
    }
    
    paginationHtml += '</ul></nav>';
    
    $('#paginationContainer').html(paginationHtml);
}

function loadUserActivityStatistics() {
    console.log('Loading user activity statistics...');
    $.ajax({
        url: 'php_action/get_user_activity_statistics.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Statistics response:', response);
            if (response.success) {
                $('#totalActivities').text(response.data.total_activities.toLocaleString());
                $('#activeUsers').text(response.data.active_users.toLocaleString());
                $('#todayActivities').text(response.data.today_activities.toLocaleString());
                $('#systemLogins').text(response.data.system_logins.toLocaleString());
                console.log('Statistics updated successfully');
            } else {
                console.error('Statistics API error:', response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Statistics AJAX error:', error);
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
                    options += `<option value="${user.admin_id}">${user.username} (${user.role})</option>`;
                });
                $('#userFilter').html(options);
            }
        }
    });
}

function loadActivityCharts() {
    console.log('Loading activity charts...');
    $.ajax({
        url: 'php_action/get_activity_chart_data.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Chart data response:', response);
            if (response.success) {
                loadMainChart(response.data);
                loadBreakdownChart(response.data);
                loadHourlyChart(response.data);
                console.log('Charts loaded successfully');
            } else {
                console.error('Chart data API error:', response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Chart data AJAX error:', error);
        }
    });
}

function loadMainChart(data) {
                const ctx = document.getElementById('activityChart').getContext('2d');
                
    if (activityChart) {
        activityChart.destroy();
                }
                
    activityChart = new Chart(ctx, {
                    type: 'line',
                    data: {
            labels: data.labels,
                        datasets: [{
                            label: 'Daily Activities',
                data: data.activities,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

function loadBreakdownChart(data) {
    const ctx = document.getElementById('activityBreakdownChart').getContext('2d');
    
    if (breakdownChart) {
        breakdownChart.destroy();
    }
    
    const colors = ['#667eea', '#f093fb', '#4facfe', '#43e97b', '#f5576c', '#764ba2'];
    
    breakdownChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.breakdown.map(item => item.action),
            datasets: [{
                data: data.breakdown.map(item => item.count),
                backgroundColor: colors.slice(0, data.breakdown.length),
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function loadHourlyChart(data) {
    const ctx = document.getElementById('hourlyChart').getContext('2d');
    
    if (hourlyChart) {
        hourlyChart.destroy();
    }
    
    const hourlyLabels = Array.from({length: 24}, (_, i) => i + ':00');
    
    hourlyChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: hourlyLabels,
            datasets: [{
                label: 'Activities',
                data: data.hourly,
                backgroundColor: 'rgba(102, 126, 234, 0.8)',
                borderColor: '#667eea',
                borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
            maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
            },
            plugins: {
                legend: {
                    display: false
                }
                        }
                    }
                });
}

function loadTopUsers() {
    $.ajax({
        url: 'php_action/get_user_activity_statistics.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data.top_users) {
                let html = '';
                response.data.top_users.forEach((user, index) => {
                    html += `
                        <div class="top-user-item">
                            <div>
                                <div class="user-name">${user.username}</div>
                                <div class="user-role">${user.role}</div>
                            </div>
                            <div class="user-activity-count">${user.activity_count}</div>
                        </div>
                    `;
                });
                $('#topUsersList').html(html);
            }
        }
    });
}

function getFilterParams() {
    return {
        user_id: $('#userFilter').val(),
        activity_type: $('#activityTypeFilter').val(),
        date_from: $('#dateFrom').val(),
        date_to: $('#dateTo').val()
    };
}

function filterUserActivity() {
    loadUserActivity(1); // Reset to first page when filtering
}

function clearFilters() {
    $('#userFilter').val('');
    $('#activityTypeFilter').val('');
    $('#dateFrom').val('');
    $('#dateTo').val('');
    $('#ipFilter').val('');
    $('#tableFilter').val('');
    $('#quickFilter').val('');
    loadUserActivity(1); // Reset to first page
}

function applyQuickFilter() {
    const quickFilter = $('#quickFilter').val();
    const today = new Date();
    
    switch(quickFilter) {
        case 'today':
            $('#dateFrom').val(today.toISOString().split('T')[0]);
            $('#dateTo').val(today.toISOString().split('T')[0]);
            break;
        case 'yesterday':
            const yesterday = new Date(today.getTime() - 24 * 60 * 60 * 1000);
            $('#dateFrom').val(yesterday.toISOString().split('T')[0]);
            $('#dateTo').val(yesterday.toISOString().split('T')[0]);
            break;
        case 'week':
            const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
            $('#dateFrom').val(weekAgo.toISOString().split('T')[0]);
            $('#dateTo').val(today.toISOString().split('T')[0]);
            break;
        case 'month':
            const monthAgo = new Date(today.getTime() - 30 * 24 * 60 * 60 * 1000);
            $('#dateFrom').val(monthAgo.toISOString().split('T')[0]);
            $('#dateTo').val(today.toISOString().split('T')[0]);
            break;
    }
    
    if (quickFilter !== 'custom') {
    filterUserActivity();
    }
}

function toggleAdvancedFilters() {
    $('#advancedOptions').slideToggle();
}

function refreshData() {
    loadUserActivityStatistics();
    loadActivityCharts();
    loadUserActivity();
    loadTopUsers();
}

function refreshTable() {
    loadUserActivity(1); // Reset to first page
}

function exportActivity() {
    // Implementation for exporting activity data
    alert('Export functionality will be implemented');
}

function exportTable() {
    // Implementation for exporting table data
    alert('Table export functionality will be implemented');
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

function copyActivityInfo(logId) {
    // Implementation for copying activity info
    alert('Copy functionality will be implemented');
}

function showActivityDetailsModal(activityData) {
    const modal = `
        <div class="modal fade" id="activityDetailsModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fa fa-info-circle"></i> Activity Details
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary">Basic Information</h6>
                                <table class="table table-sm">
                                    <tr><td><strong>Log ID:</strong></td><td>${activityData.log_id}</td></tr>
                                    <tr><td><strong>User:</strong></td><td>${activityData.username} (${activityData.role})</td></tr>
                                    <tr><td><strong>Email:</strong></td><td>${activityData.email || 'N/A'}</td></tr>
                                    <tr><td><strong>Activity Type:</strong></td><td><span class="badge badge-info">${activityData.action}</span></td></tr>
                                    <tr><td><strong>Table/Resource:</strong></td><td><code>${activityData.table_name}</code></td></tr>
                                    <tr><td><strong>Record ID:</strong></td><td>${activityData.record_id || 'N/A'}</td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary">Technical Details</h6>
                                <table class="table table-sm">
                                    <tr><td><strong>IP Address:</strong></td><td><code>${activityData.ip_address}</code></td></tr>
                                    <tr><td><strong>Session ID:</strong></td><td><code>${activityData.session_id || 'N/A'}</code></td></tr>
                                    <tr><td><strong>Timestamp:</strong></td><td>${new Date(activityData.action_time).toLocaleString()}</td></tr>
                                </table>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-12">
                                <h6 class="text-primary">Description</h6>
                                <div class="bg-light p-3 rounded">
                                    ${activityData.description || 'No description available'}
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-12">
                                <h6 class="text-primary">User Agent</h6>
                                <div class="bg-light p-3 rounded small">
                                    ${activityData.user_agent || 'Not available'}
                                </div>
                            </div>
                        </div>
                        
                        ${activityData.old_data || activityData.new_data ? `
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <h6 class="text-primary">Old Data</h6>
                                <pre class="bg-light p-3 rounded small">${activityData.old_data ? JSON.stringify(JSON.parse(activityData.old_data), null, 2) : 'N/A'}</pre>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary">New Data</h6>
                                <pre class="bg-light p-3 rounded small">${activityData.new_data ? JSON.stringify(JSON.parse(activityData.new_data), null, 2) : 'N/A'}</pre>
                            </div>
                        </div>
                        ` : ''}
                        
                        ${activityData.related_activities && activityData.related_activities.length > 0 ? `
                        <div class="row mt-3">
                            <div class="col-12">
                                <h6 class="text-primary">Related Activities (Same Day)</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Time</th>
                                                <th>Action</th>
                                                <th>Resource</th>
                                                <th>Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${activityData.related_activities.map(activity => `
                                                <tr>
                                                    <td>${new Date(activity.action_time).toLocaleTimeString()}</td>
                                                    <td><span class="badge badge-secondary">${activity.action}</span></td>
                                                    <td><code>${activity.table_name}</code></td>
                                                    <td>${activity.description}</td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        ` : ''}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="copyActivityInfo(${activityData.log_id})">
                            <i class="fa fa-copy"></i> Copy Details
                        </button>
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
