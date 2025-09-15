<?php include('./constant/check.php'); ?>
<?php include('./constant/layout/head.php'); ?>
<?php include('./constant/layout/header.php'); ?>
<?php include('./constant/layout/sidebar.php'); ?>

<div class="page-wrapper">
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">All Transactions</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard_super.php">Home</a></li>
                    <li class="breadcrumb-item active">Payments & Billing</li>
                    <li class="breadcrumb-item active">All Transactions</li>
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
                                <h3 class="text-white" id="totalTransactions">0</h3>
                                <h6 class="text-white">Total Transactions</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-list fa-2x"></i>
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
                                <h3 class="text-white" id="completedTransactions">0</h3>
                                <h6 class="text-white">Completed</h6>
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
                                <h3 class="text-white" id="pendingTransactions">0</h3>
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
                                <h3 class="text-white" id="failedTransactions">0</h3>
                                <h6 class="text-white">Failed</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-times fa-2x"></i>
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
                        <h4 class="card-title">Filter Transactions</h4>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Payment Method</label>
                                    <select class="form-control" id="methodFilter">
                                        <option value="">All Methods</option>
                                        <option value="mobile_money">Mobile Money</option>
                                        <option value="card">Credit Card</option>
                                        <option value="cash">Cash</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control" id="statusFilter">
                                        <option value="">All Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="completed">Completed</option>
                                        <option value="failed">Failed</option>
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
                        <button class="btn btn-primary" onclick="filterTransactions()">
                            <i class="fa fa-filter"></i> Apply Filters
                        </button>
                        <button class="btn btn-secondary" onclick="clearFilters()">
                            <i class="fa fa-refresh"></i> Clear Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Transaction History</h4>
                        <div class="table-responsive">
                            <table id="transactionsTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Payment ID</th>
                                        <th>Admin</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Paid At</th>
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
    loadTransactions();
    loadTransactionStatistics();
});

function loadTransactions() {
    $.ajax({
        url: 'php_action/get_all_transactions.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#transactionsTable').DataTable({
                    data: response.data,
                    columns: [
                        { data: 'payment_id' },
                        { data: 'admin_name' },
                        { 
                            data: 'amount',
                            render: function(data, type, row) {
                                return 'RWF ' + parseFloat(data).toLocaleString();
                            }
                        },
                        { 
                            data: 'method',
                            render: function(data, type, row) {
                                let badgeClass = 'badge-secondary';
                                if (data === 'mobile_money') badgeClass = 'badge-primary';
                                else if (data === 'card') badgeClass = 'badge-info';
                                else if (data === 'cash') badgeClass = 'badge-success';
                                return '<span class="badge ' + badgeClass + '">' + data.replace('_', ' ').toUpperCase() + '</span>';
                            }
                        },
                        { 
                            data: 'status',
                            render: function(data, type, row) {
                                let badgeClass = 'badge-secondary';
                                if (data === 'completed') badgeClass = 'badge-success';
                                else if (data === 'pending') badgeClass = 'badge-warning';
                                else if (data === 'failed') badgeClass = 'badge-danger';
                                return '<span class="badge ' + badgeClass + '">' + data.toUpperCase() + '</span>';
                            }
                        },
                        { 
                            data: 'paid_at',
                            render: function(data, type, row) {
                                return new Date(data).toLocaleString();
                            }
                        },
                        {
                            data: null,
                            render: function(data, type, row) {
                                return '<button class="btn btn-sm btn-info" onclick="viewTransactionDetails(' + row.payment_id + ')">View Details</button>';
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

function loadTransactionStatistics() {
    $.ajax({
        url: 'php_action/get_transaction_statistics.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#totalTransactions').text(response.data.total_transactions);
                $('#completedTransactions').text(response.data.completed_transactions);
                $('#pendingTransactions').text(response.data.pending_transactions);
                $('#failedTransactions').text(response.data.failed_transactions);
            }
        }
    });
}

function filterTransactions() {
    const method = $('#methodFilter').val();
    const status = $('#statusFilter').val();
    const dateFrom = $('#dateFrom').val();
    const dateTo = $('#dateTo').val();
    
    // Reload table with filters
    $('#transactionsTable').DataTable().destroy();
    loadTransactions();
}

function clearFilters() {
    $('#methodFilter').val('');
    $('#statusFilter').val('');
    $('#dateFrom').val('');
    $('#dateTo').val('');
    filterTransactions();
}

function viewTransactionDetails(paymentId) {
    $.ajax({
        url: 'php_action/get_transaction_details.php',
        type: 'GET',
        data: { payment_id: paymentId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showTransactionDetailsModal(response.data);
            }
        }
    });
}

function showTransactionDetailsModal(transactionData) {
    const modal = `
        <div class="modal fade" id="transactionDetailsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Transaction Details</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Payment ID:</strong> ${transactionData.payment_id}<br>
                                <strong>Admin:</strong> ${transactionData.admin_name}<br>
                                <strong>Amount:</strong> RWF ${parseFloat(transactionData.amount).toLocaleString()}<br>
                                <strong>Method:</strong> <span class="badge badge-primary">${transactionData.method.replace('_', ' ').toUpperCase()}</span><br>
                                <strong>Status:</strong> <span class="badge badge-success">${transactionData.status.toUpperCase()}</span><br>
                                <strong>Paid At:</strong> ${new Date(transactionData.paid_at).toLocaleString()}
                            </div>
                            <div class="col-md-6">
                                <strong>Transaction Details:</strong><br>
                                <p class="bg-light p-2">Payment processed successfully through ${transactionData.method.replace('_', ' ')} payment gateway.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('body').append(modal);
    $('#transactionDetailsModal').modal('show');
    $('#transactionDetailsModal').on('hidden.bs.modal', function() {
        $(this).remove();
    });
}
</script>
