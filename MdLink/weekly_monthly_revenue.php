<?php include('./constant/check.php'); ?>
<?php include('./constant/layout/head.php'); ?>
<?php include('./constant/layout/header.php'); ?>
<?php include('./constant/layout/sidebar.php'); ?>

<div class="page-wrapper">
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Weekly / Monthly Revenue</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard_super.php">Home</a></li>
                    <li class="breadcrumb-item active">Payments & Billing</li>
                    <li class="breadcrumb-item active">Weekly / Monthly Revenue</li>
                </ol>
            </div>
        </div>

        <!-- Period Selection -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Select Reporting Period</h4>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Report Type</label>
                                    <select class="form-control" id="reportType" onchange="togglePeriodInputs()">
                                        <option value="weekly">Weekly Report</option>
                                        <option value="monthly">Monthly Report</option>
                                        <option value="yearly">Yearly Report</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3" id="weekInput">
                                <div class="form-group">
                                    <label>Select Week</label>
                                    <input type="week" class="form-control" id="selectedWeek">
                                </div>
                            </div>
                            <div class="col-md-3" id="monthInput" style="display: none;">
                                <div class="form-group">
                                    <label>Select Month</label>
                                    <input type="month" class="form-control" id="selectedMonth">
                                </div>
                            </div>
                            <div class="col-md-3" id="yearInput" style="display: none;">
                                <div class="form-group">
                                    <label>Select Year</label>
                                    <select class="form-control" id="selectedYear">
                                        <option value="2024">2024</option>
                                        <option value="2025" selected>2025</option>
                                        <option value="2026">2026</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label><br>
                                    <button class="btn btn-primary" onclick="generateReport()">
                                        <i class="fa fa-chart-bar"></i> Generate Report
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
                                <h3 class="text-white" id="transactionCount">0</h3>
                                <h6 class="text-white">Transactions</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-list fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Chart -->
        <div class="row" id="chartSection" style="display: none;">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Revenue Trend</h4>
                        <canvas id="revenueChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Method Breakdown -->
        <div class="row" id="paymentBreakdown" style="display: none;">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="p-10">
                                <h3 class="text-white" id="mobileMoneyRevenue">RWF 0</h3>
                                <h6 class="text-white">Mobile Money</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-mobile fa-2x"></i>
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
                                <h3 class="text-white" id="bankTransferRevenue">RWF 0</h3>
                                <h6 class="text-white">Bank Transfer</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-university fa-2x"></i>
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
                                <h3 class="text-white" id="creditCardRevenue">RWF 0</h3>
                                <h6 class="text-white">Credit Card</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-credit-card fa-2x"></i>
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
                                <h3 class="text-white" id="cashRevenue">RWF 0</h3>
                                <h6 class="text-white">Cash</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-money fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Table -->
        <div class="row" id="tableSection" style="display: none;">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Detailed Revenue Report</h4>
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
    // Set default values
    const today = new Date();
    $('#selectedWeek').val(getWeekString(today));
    $('#selectedMonth').val(today.toISOString().slice(0, 7));
});

function togglePeriodInputs() {
    const reportType = $('#reportType').val();
    
    // Hide all inputs
    $('#weekInput').hide();
    $('#monthInput').hide();
    $('#yearInput').hide();
    
    // Show relevant input
    if (reportType === 'weekly') {
        $('#weekInput').show();
    } else if (reportType === 'monthly') {
        $('#monthInput').show();
    } else if (reportType === 'yearly') {
        $('#yearInput').show();
    }
}

function getWeekString(date) {
    const year = date.getFullYear();
    const week = getWeekNumber(date);
    return `${year}-W${week.toString().padStart(2, '0')}`;
}

function getWeekNumber(date) {
    const firstDayOfYear = new Date(date.getFullYear(), 0, 1);
    const pastDaysOfYear = (date - firstDayOfYear) / 86400000;
    return Math.ceil((pastDaysOfYear + firstDayOfYear.getDay() + 1) / 7);
}

function generateReport() {
    const reportType = $('#reportType').val();
    let period = '';
    
    if (reportType === 'weekly') {
        period = $('#selectedWeek').val();
    } else if (reportType === 'monthly') {
        period = $('#selectedMonth').val();
    } else if (reportType === 'yearly') {
        period = $('#selectedYear').val();
    }
    
    if (!period) {
        alert('Please select a period for the report.');
        return;
    }
    
    // Show sections
    $('#summaryCards').show();
    $('#chartSection').show();
    $('#paymentBreakdown').show();
    $('#tableSection').show();
    
    // Load data
    loadRevenueReport(reportType, period);
    loadRevenueChart(reportType, period);
}

function loadRevenueReport(reportType, period) {
    $.ajax({
        url: 'php_action/get_period_revenue_report.php',
        type: 'GET',
        data: {
            report_type: reportType,
            period: period
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Update summary cards
                $('#totalRevenue').text('RWF ' + parseFloat(response.data.total_revenue).toLocaleString());
                $('#totalSales').text('RWF ' + parseFloat(response.data.total_sales).toLocaleString());
                $('#totalRefunds').text('RWF ' + parseFloat(response.data.total_refunds).toLocaleString());
                $('#transactionCount').text(response.data.transaction_count);
                
                // Update payment method breakdown
                $('#mobileMoneyRevenue').text('RWF ' + parseFloat(response.data.mobile_money).toLocaleString());
                $('#bankTransferRevenue').text('RWF ' + parseFloat(response.data.bank_transfer).toLocaleString());
                $('#creditCardRevenue').text('RWF ' + parseFloat(response.data.credit_card).toLocaleString());
                $('#cashRevenue').text('RWF ' + parseFloat(response.data.cash).toLocaleString());
                
                // Load table data
                if ($.fn.DataTable.isDataTable('#revenueReportTable')) {
                    $('#revenueReportTable').DataTable().destroy();
                }
                
                $('#revenueReportTable').DataTable({
                    data: response.data.daily_data,
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

function loadRevenueChart(reportType, period) {
    $.ajax({
        url: 'php_action/get_period_revenue_chart.php',
        type: 'GET',
        data: {
            report_type: reportType,
            period: period
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const ctx = document.getElementById('revenueChart').getContext('2d');
                
                // Destroy existing chart if it exists
                if (window.revenueChartInstance) {
                    window.revenueChartInstance.destroy();
                }
                
                window.revenueChartInstance = new Chart(ctx, {
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
</script>
