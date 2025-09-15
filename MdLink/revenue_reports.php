<?php include('./constant/check.php'); ?>
<?php include('./constant/layout/head.php'); ?>
<?php include('./constant/layout/header.php'); ?>
<?php include('./constant/layout/sidebar.php'); ?>

<div class="page-wrapper">
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Revenue Reports</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard_super.php">Home</a></li>
                    <li class="breadcrumb-item active">Reports & Analytics</li>
                    <li class="breadcrumb-item active">Revenue Reports</li>
                </ol>
            </div>
        </div>

        <!-- Report Configuration -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Generate Revenue Report</h4>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Report Type</label>
                                    <select class="form-control" id="reportType">
                                        <option value="daily">Daily Report</option>
                                        <option value="weekly">Weekly Report</option>
                                        <option value="monthly">Monthly Report</option>
                                        <option value="yearly">Yearly Report</option>
                                        <option value="custom">Custom Period</option>
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
                        <button class="btn btn-primary" onclick="generateRevenueReport()">
                            <i class="fa fa-chart-bar"></i> Generate Report
                        </button>
                        <button class="btn btn-success" onclick="exportReport()">
                            <i class="fa fa-download"></i> Export PDF
                        </button>
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
                                <h3 class="text-white" id="totalRevenue">RWF 0</h3>
                                <h6 class="text-white">Total Revenue</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-money fa-2x"></i>
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
                                <h3 class="text-white" id="totalSales">RWF 0</h3>
                                <h6 class="text-white">Total Sales</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-shopping-cart fa-2x"></i>
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
                                <h3 class="text-white" id="totalRefunds">RWF 0</h3>
                                <h6 class="text-white">Total Refunds</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-undo fa-2x"></i>
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
                                <h3 class="text-white" id="averageDaily">RWF 0</h3>
                                <h6 class="text-white">Average Daily</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-calendar fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Charts -->
        <div class="row" id="chartsSection" style="display: none;">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Revenue Trend</h4>
                        <canvas id="revenueTrendChart" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Payment Methods</h4>
                        <canvas id="paymentMethodsChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pharmacy Performance -->
        <div class="row" id="pharmacyPerformance" style="display: none;">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Pharmacy Performance</h4>
                        <canvas id="pharmacyPerformanceChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Table -->
        <div class="row" id="tableSection" style="display: none;">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Detailed Revenue Data</h4>
                        <div class="table-responsive">
                            <table id="revenueReportTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Pharmacy</th>
                                        <th>Total Sales</th>
                                        <th>Total Refunds</th>
                                        <th>Net Revenue</th>
                                        <th>Mobile Money</th>
                                        <th>Bank Transfer</th>
                                        <th>Credit Card</th>
                                        <th>Cash</th>
                                        <th>Transactions</th>
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
    loadPharmacies();
    
    // Set default date range (last 30 days)
    const today = new Date();
    const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));
    $('#dateFrom').val(thirtyDaysAgo.toISOString().split('T')[0]);
    $('#dateTo').val(today.toISOString().split('T')[0]);
});

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
                $('#pharmacyFilter').html(options);
            }
        }
    });
}

function generateRevenueReport() {
    const reportType = $('#reportType').val();
    const pharmacy = $('#pharmacyFilter').val();
    const dateFrom = $('#dateFrom').val();
    const dateTo = $('#dateTo').val();
    
    if (!dateFrom || !dateTo) {
        alert('Please select date range.');
        return;
    }
    
    // Show sections
    $('#summaryCards').show();
    $('#chartsSection').show();
    $('#pharmacyPerformance').show();
    $('#tableSection').show();
    
    // Load data
    loadRevenueSummary(reportType, pharmacy, dateFrom, dateTo);
    loadRevenueTrendChart(reportType, pharmacy, dateFrom, dateTo);
    loadPaymentMethodsChart(reportType, pharmacy, dateFrom, dateTo);
    loadPharmacyPerformanceChart(reportType, pharmacy, dateFrom, dateTo);
    loadRevenueTable(reportType, pharmacy, dateFrom, dateTo);
}

function loadRevenueSummary(reportType, pharmacy, dateFrom, dateTo) {
    $.ajax({
        url: 'php_action/get_revenue_summary.php',
        type: 'GET',
        data: {
            report_type: reportType,
            pharmacy: pharmacy,
            date_from: dateFrom,
            date_to: dateTo
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#totalRevenue').text('RWF ' + parseFloat(response.data.total_revenue).toLocaleString());
                $('#totalSales').text('RWF ' + parseFloat(response.data.total_sales).toLocaleString());
                $('#totalRefunds').text('RWF ' + parseFloat(response.data.total_refunds).toLocaleString());
                $('#averageDaily').text('RWF ' + parseFloat(response.data.average_daily).toLocaleString());
            }
        }
    });
}

function loadRevenueTrendChart(reportType, pharmacy, dateFrom, dateTo) {
    $.ajax({
        url: 'php_action/get_revenue_trend_chart.php',
        type: 'GET',
        data: {
            report_type: reportType,
            pharmacy: pharmacy,
            date_from: dateFrom,
            date_to: dateTo
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const ctx = document.getElementById('revenueTrendChart').getContext('2d');
                
                if (window.revenueTrendChart) {
                    window.revenueTrendChart.destroy();
                }
                
                window.revenueTrendChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: response.data.labels,
                        datasets: [{
                            label: 'Net Revenue',
                            data: response.data.revenue,
                            borderColor: 'rgb(75, 192, 192)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            tension: 0.1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'RWF ' + value.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }
    });
}

function loadPaymentMethodsChart(reportType, pharmacy, dateFrom, dateTo) {
    $.ajax({
        url: 'php_action/get_payment_methods_chart.php',
        type: 'GET',
        data: {
            report_type: reportType,
            pharmacy: pharmacy,
            date_from: dateFrom,
            date_to: dateTo
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const ctx = document.getElementById('paymentMethodsChart').getContext('2d');
                
                if (window.paymentMethodsChart) {
                    window.paymentMethodsChart.destroy();
                }
                
                window.paymentMethodsChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: response.data.labels,
                        datasets: [{
                            data: response.data.data,
                            backgroundColor: [
                                'rgba(54, 162, 235, 0.8)',
                                'rgba(255, 99, 132, 0.8)',
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

function loadPharmacyPerformanceChart(reportType, pharmacy, dateFrom, dateTo) {
    $.ajax({
        url: 'php_action/get_pharmacy_performance_chart.php',
        type: 'GET',
        data: {
            report_type: reportType,
            pharmacy: pharmacy,
            date_from: dateFrom,
            date_to: dateTo
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const ctx = document.getElementById('pharmacyPerformanceChart').getContext('2d');
                
                if (window.pharmacyPerformanceChart) {
                    window.pharmacyPerformanceChart.destroy();
                }
                
                window.pharmacyPerformanceChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: response.data.labels,
                        datasets: [{
                            label: 'Revenue',
                            data: response.data.revenue,
                            backgroundColor: 'rgba(54, 162, 235, 0.8)'
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'RWF ' + value.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }
    });
}

function loadRevenueTable(reportType, pharmacy, dateFrom, dateTo) {
    $.ajax({
        url: 'php_action/get_revenue_table_data.php',
        type: 'GET',
        data: {
            report_type: reportType,
            pharmacy: pharmacy,
            date_from: dateFrom,
            date_to: dateTo
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                if ($.fn.DataTable.isDataTable('#revenueReportTable')) {
                    $('#revenueReportTable').DataTable().destroy();
                }
                
                $('#revenueReportTable').DataTable({
                    data: response.data,
                    columns: [
                        { 
                            data: 'revenue_date',
                            render: function(data, type, row) {
                                return new Date(data).toLocaleDateString();
                            }
                        },
                        { data: 'pharmacy_name' },
                        { 
                            data: 'total_sales',
                            render: function(data, type, row) {
                                return 'RWF ' + parseFloat(data).toLocaleString();
                            }
                        },
                        { 
                            data: 'total_refunds',
                            render: function(data, type, row) {
                                return 'RWF ' + parseFloat(data).toLocaleString();
                            }
                        },
                        { 
                            data: 'net_revenue',
                            render: function(data, type, row) {
                                return 'RWF ' + parseFloat(data).toLocaleString();
                            }
                        },
                        { 
                            data: 'mobile_money',
                            render: function(data, type, row) {
                                return 'RWF ' + parseFloat(data).toLocaleString();
                            }
                        },
                        { 
                            data: 'bank_transfer',
                            render: function(data, type, row) {
                                return 'RWF ' + parseFloat(data).toLocaleString();
                            }
                        },
                        { 
                            data: 'credit_card',
                            render: function(data, type, row) {
                                return 'RWF ' + parseFloat(data).toLocaleString();
                            }
                        },
                        { 
                            data: 'cash',
                            render: function(data, type, row) {
                                return 'RWF ' + parseFloat(data).toLocaleString();
                            }
                        },
                        { data: 'transaction_count' }
                    ],
                    order: [[0, 'desc']],
                    pageLength: 10
                });
            }
        }
    });
}

function exportReport() {
    const reportType = $('#reportType').val();
    const pharmacy = $('#pharmacyFilter').val();
    const dateFrom = $('#dateFrom').val();
    const dateTo = $('#dateTo').val();
    
    if (!dateFrom || !dateTo) {
        alert('Please select date range.');
        return;
    }
    
    const params = new URLSearchParams({
        report_type: reportType,
        pharmacy: pharmacy,
        date_from: dateFrom,
        date_to: dateTo
    });
    
    window.open('php_action/export_revenue_report.php?' + params.toString(), '_blank');
}
</script>
