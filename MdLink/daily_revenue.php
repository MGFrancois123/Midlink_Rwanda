<?php include('./constant/check.php'); ?>
<?php include('./constant/layout/head.php'); ?>
<?php include('./constant/layout/header.php'); ?>
<?php include('./constant/layout/sidebar.php'); ?>

<div class="page-wrapper">
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Daily Revenue</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard_super.php">Home</a></li>
                    <li class="breadcrumb-item active">Payments & Billing</li>
                    <li class="breadcrumb-item active">Daily Revenue</li>
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
                                <h3 class="text-white" id="todayRevenue">RWF 0</h3>
                                <h6 class="text-white">Today's Revenue</h6>
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

        <!-- Payment Method Breakdown -->
        <div class="row">
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

        <!-- Filters -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Filter Daily Revenue</h4>
                        <div class="row">
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
                                    <input type="date" class="form-control" id="dateFrom" value="<?php echo date('Y-m-d'); ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date To</label>
                                    <input type="date" class="form-control" id="dateTo" value="<?php echo date('Y-m-d'); ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label><br>
                                    <button class="btn btn-primary" onclick="filterDailyRevenue()">
                                        <i class="fa fa-filter"></i> Apply Filters
                                    </button>
                                    <button class="btn btn-secondary" onclick="clearFilters()">
                                        <i class="fa fa-refresh"></i> Clear
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daily Revenue Chart -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Revenue Trend</h4>
                        <canvas id="revenueChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daily Revenue Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Daily Revenue Records</h4>
                        <div class="table-responsive">
                            <table id="dailyRevenueTable" class="table table-striped table-bordered">
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
    loadDailyRevenue();
    loadDailyRevenueStatistics();
    loadPharmacies();
    loadRevenueChart();
});

function loadDailyRevenue() {
    $.ajax({
        url: 'php_action/get_daily_revenue.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#dailyRevenueTable').DataTable({
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

function loadDailyRevenueStatistics() {
    $.ajax({
        url: 'php_action/get_daily_revenue_statistics.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#todayRevenue').text('RWF ' + parseFloat(response.data.today_revenue).toLocaleString());
                $('#totalSales').text('RWF ' + parseFloat(response.data.total_sales).toLocaleString());
                $('#totalRefunds').text('RWF ' + parseFloat(response.data.total_refunds).toLocaleString());
                $('#transactionCount').text(response.data.transaction_count);
                $('#mobileMoneyRevenue').text('RWF ' + parseFloat(response.data.mobile_money).toLocaleString());
                $('#bankTransferRevenue').text('RWF ' + parseFloat(response.data.bank_transfer).toLocaleString());
                $('#creditCardRevenue').text('RWF ' + parseFloat(response.data.credit_card).toLocaleString());
                $('#cashRevenue').text('RWF ' + parseFloat(response.data.cash).toLocaleString());
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
                $('#pharmacyFilter').html(options);
            }
        }
    });
}

function loadRevenueChart() {
    $.ajax({
        url: 'php_action/get_revenue_chart_data.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const ctx = document.getElementById('revenueChart').getContext('2d');
                new Chart(ctx, {
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

function filterDailyRevenue() {
    const pharmacy = $('#pharmacyFilter').val();
    const dateFrom = $('#dateFrom').val();
    const dateTo = $('#dateTo').val();
    
    // Reload table with filters
    $('#dailyRevenueTable').DataTable().destroy();
    loadDailyRevenue();
}

function clearFilters() {
    $('#pharmacyFilter').val('');
    $('#dateFrom').val('<?php echo date('Y-m-d'); ?>');
    $('#dateTo').val('<?php echo date('Y-m-d'); ?>');
    filterDailyRevenue();
}
</script>
