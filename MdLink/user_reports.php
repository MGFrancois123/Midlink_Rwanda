<?php include('./constant/check.php'); ?>
<?php include('./constant/layout/head.php'); ?>
<?php include('./constant/layout/header.php'); ?>
<?php include('./constant/layout/sidebar.php'); ?>

<div class="page-wrapper">
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">User Reports</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard_super.php">Home</a></li>
                    <li class="breadcrumb-item active">User Management</li>
                    <li class="breadcrumb-item active">User Reports</li>
                </ol>
            </div>
        </div>

        <!-- Report Configuration -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Generate User Report</h4>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Report Type</label>
                                    <select class="form-control" id="reportType">
                                        <option value="user_summary">User Summary</option>
                                        <option value="activity_summary">Activity Summary</option>
                                        <option value="role_analysis">Role Analysis</option>
                                        <option value="pharmacy_staff">Pharmacy Staff</option>
                                        <option value="patient_demographics">Patient Demographics</option>
                                        <option value="security_audit">Security Audit</option>
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label><br>
                                    <button class="btn btn-primary" onclick="generateUserReport()">
                                        <i class="fa fa-chart-bar"></i> Generate Report
                                    </button>
                                    <button class="btn btn-success" onclick="exportReport()">
                                        <i class="fa fa-download"></i> Export PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row" id="summaryCards" style="display: none;">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="p-10">
                                <h3 class="text-white" id="totalUsers">0</h3>
                                <h6 class="text-white">Total Users</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-users fa-2x"></i>
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
                                <i class="fa fa-user-check fa-2x"></i>
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
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="p-10">
                                <h3 class="text-white" id="avgActivity">0</h3>
                                <h6 class="text-white">Avg Daily Activity</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-chart-line fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row" id="chartsSection" style="display: none;">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">User Distribution by Role</h4>
                        <canvas id="roleDistributionChart" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Activity Trends</h4>
                        <canvas id="activityTrendsChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Table -->
        <div class="row" id="tableSection" style="display: none;">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Detailed Report Data</h4>
                        <div class="table-responsive">
                            <table id="userReportTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>User ID</th>
                                        <th>Username</th>
                                        <th>Role</th>
                                        <th>Pharmacy</th>
                                        <th>Status</th>
                                        <th>Last Login</th>
                                        <th>Total Activities</th>
                                        <th>Created At</th>
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
    // Set default date range (last 30 days)
    const today = new Date();
    const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));
    $('#dateFrom').val(thirtyDaysAgo.toISOString().split('T')[0]);
    $('#dateTo').val(today.toISOString().split('T')[0]);
});

function generateUserReport() {
    const reportType = $('#reportType').val();
    const dateFrom = $('#dateFrom').val();
    const dateTo = $('#dateTo').val();
    
    if (!dateFrom || !dateTo) {
        alert('Please select date range.');
        return;
    }
    
    // Show sections
    $('#summaryCards').show();
    $('#chartsSection').show();
    $('#tableSection').show();
    
    // Load data based on report type
    loadUserReportData(reportType, dateFrom, dateTo);
    loadRoleDistributionChart(reportType, dateFrom, dateTo);
    loadActivityTrendsChart(reportType, dateFrom, dateTo);
}

function loadUserReportData(reportType, dateFrom, dateTo) {
    $.ajax({
        url: 'php_action/get_user_report_data.php',
        type: 'GET',
        data: {
            report_type: reportType,
            date_from: dateFrom,
            date_to: dateTo
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Update summary cards
                $('#totalUsers').text(response.data.total_users);
                $('#activeUsers').text(response.data.active_users);
                $('#totalActivities').text(response.data.total_activities);
                $('#avgActivity').text(response.data.avg_daily_activity);
                
                // Load table data
                if ($.fn.DataTable.isDataTable('#userReportTable')) {
                    $('#userReportTable').DataTable().destroy();
                }
                
                $('#userReportTable').DataTable({
                    data: response.data.users,
                    columns: [
                        { data: 'admin_id' },
                        { data: 'username' },
                        { 
                            data: 'role',
                            render: function(data, type, row) {
                                let badgeClass = 'badge-secondary';
                                if (data === 'super_admin') badgeClass = 'badge-danger';
                                else if (data === 'pharmacy_admin') badgeClass = 'badge-primary';
                                else if (data === 'finance_admin') badgeClass = 'badge-success';
                                return '<span class="badge ' + badgeClass + '">' + data.replace('_', ' ').toUpperCase() + '</span>';
                            }
                        },
                        { data: 'pharmacy_name' },
                        { 
                            data: 'status',
                            render: function(data, type, row) {
                                let badgeClass = data === 'active' ? 'badge-success' : 'badge-danger';
                                return '<span class="badge ' + badgeClass + '">' + data.toUpperCase() + '</span>';
                            }
                        },
                        { 
                            data: 'last_login',
                            render: function(data, type, row) {
                                return data ? new Date(data).toLocaleString() : 'Never';
                            }
                        },
                        { data: 'total_activities' },
                        { 
                            data: 'created_at',
                            render: function(data, type, row) {
                                return new Date(data).toLocaleDateString();
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

function loadRoleDistributionChart(reportType, dateFrom, dateTo) {
    $.ajax({
        url: 'php_action/get_role_distribution_chart.php',
        type: 'GET',
        data: {
            report_type: reportType,
            date_from: dateFrom,
            date_to: dateTo
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const ctx = document.getElementById('roleDistributionChart').getContext('2d');
                
                if (window.roleDistributionChart) {
                    window.roleDistributionChart.destroy();
                }
                
                window.roleDistributionChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: response.data.labels,
                        datasets: [{
                            data: response.data.data,
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.8)',
                                'rgba(54, 162, 235, 0.8)',
                                'rgba(255, 205, 86, 0.8)',
                                'rgba(75, 192, 192, 0.8)'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }
        }
    });
}

function loadActivityTrendsChart(reportType, dateFrom, dateTo) {
    $.ajax({
        url: 'php_action/get_activity_trends_chart.php',
        type: 'GET',
        data: {
            report_type: reportType,
            date_from: dateFrom,
            date_to: dateTo
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const ctx = document.getElementById('activityTrendsChart').getContext('2d');
                
                if (window.activityTrendsChart) {
                    window.activityTrendsChart.destroy();
                }
                
                window.activityTrendsChart = new Chart(ctx, {
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

function exportReport() {
    const reportType = $('#reportType').val();
    const dateFrom = $('#dateFrom').val();
    const dateTo = $('#dateTo').val();
    
    if (!dateFrom || !dateTo) {
        alert('Please select date range.');
        return;
    }
    
    const params = new URLSearchParams({
        report_type: reportType,
        date_from: dateFrom,
        date_to: dateTo
    });
    
    window.open('php_action/export_user_report.php?' + params.toString(), '_blank');
}
</script>
