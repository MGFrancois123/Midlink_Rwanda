<?php include('./constant/check.php'); ?>
<?php include('./constant/layout/head.php'); ?>
<?php include('./constant/layout/header.php'); ?>
<?php include('./constant/layout/sidebar.php'); ?>

<div class="page-wrapper">
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Failed Payments</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard_super.php">Home</a></li>
                    <li class="breadcrumb-item active">Payments & Billing</li>
                    <li class="breadcrumb-item active">Failed Payments</li>
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
                                <h3 class="text-white" id="totalFailedPayments">0</h3>
                                <h6 class="text-white">Total Failed Payments</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-times-circle fa-2x"></i>
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
                                <h3 class="text-white" id="retryPending">0</h3>
                                <h6 class="text-white">Retry Pending</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-refresh fa-2x"></i>
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
                                <h3 class="text-white" id="permanentlyFailed">0</h3>
                                <h6 class="text-white">Permanently Failed</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-ban fa-2x"></i>
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
                                <h3 class="text-white" id="resolvedPayments">0</h3>
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
                        <h4 class="card-title">Filter Failed Payments</h4>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control" id="statusFilter">
                                        <option value="">All Status</option>
                                        <option value="RETRY_PENDING">Retry Pending</option>
                                        <option value="PERMANENTLY_FAILED">Permanently Failed</option>
                                        <option value="RESOLVED">Resolved</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Payment Method</label>
                                    <select class="form-control" id="methodFilter">
                                        <option value="">All Methods</option>
                                        <option value="MOBILE_MONEY">Mobile Money</option>
                                        <option value="BANK_TRANSFER">Bank Transfer</option>
                                        <option value="CREDIT_CARD">Credit Card</option>
                                        <option value="CASH">Cash</option>
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
                        <button class="btn btn-primary" onclick="filterFailedPayments()">
                            <i class="fa fa-filter"></i> Apply Filters
                        </button>
                        <button class="btn btn-secondary" onclick="clearFilters()">
                            <i class="fa fa-refresh"></i> Clear Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Failed Payments Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Failed Payment Records</h4>
                        <div class="table-responsive">
                            <table id="failedPaymentsTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Payment ID</th>
                                        <th>Transaction ID</th>
                                        <th>Patient Name</th>
                                        <th>Patient Phone</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Retry Count</th>
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
    loadFailedPayments();
    loadFailedPaymentStatistics();
});

function loadFailedPayments() {
    $.ajax({
        url: 'php_action/get_failed_payments.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#failedPaymentsTable').DataTable({
                    data: response.data,
                    columns: [
                        { data: 'payment_id' },
                        { data: 'transaction_id' },
                        { data: 'patient_name' },
                        { data: 'patient_phone' },
                        { 
                            data: 'amount',
                            render: function(data, type, row) {
                                return 'RWF ' + parseFloat(data).toLocaleString();
                            }
                        },
                        { 
                            data: 'payment_method',
                            render: function(data, type, row) {
                                let badgeClass = 'badge-secondary';
                                if (data === 'MOBILE_MONEY') badgeClass = 'badge-primary';
                                else if (data === 'BANK_TRANSFER') badgeClass = 'badge-info';
                                else if (data === 'CREDIT_CARD') badgeClass = 'badge-warning';
                                else if (data === 'CASH') badgeClass = 'badge-success';
                                return '<span class="badge ' + badgeClass + '">' + data.replace('_', ' ').toUpperCase() + '</span>';
                            }
                        },
                        { 
                            data: 'status',
                            render: function(data, type, row) {
                                let badgeClass = 'badge-secondary';
                                if (data === 'RETRY_PENDING') badgeClass = 'badge-warning';
                                else if (data === 'PERMANENTLY_FAILED') badgeClass = 'badge-danger';
                                else if (data === 'RESOLVED') badgeClass = 'badge-success';
                                return '<span class="badge ' + badgeClass + '">' + data.replace('_', ' ').toUpperCase() + '</span>';
                            }
                        },
                        { data: 'retry_count' },
                        { 
                            data: 'created_at',
                            render: function(data, type, row) {
                                return new Date(data).toLocaleString();
                            }
                        },
                        {
                            data: null,
                            render: function(data, type, row) {
                                let actions = '<button class="btn btn-sm btn-info" onclick="viewFailedPaymentDetails(' + row.payment_id + ')">View</button>';
                                if (row.status === 'RETRY_PENDING') {
                                    actions += ' <button class="btn btn-sm btn-warning" onclick="retryPayment(' + row.payment_id + ')">Retry</button>';
                                    actions += ' <button class="btn btn-sm btn-success" onclick="markAsResolved(' + row.payment_id + ')">Mark Resolved</button>';
                                }
                                return actions;
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

function loadFailedPaymentStatistics() {
    $.ajax({
        url: 'php_action/get_failed_payment_statistics.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#totalFailedPayments').text(response.data.total_failed_payments);
                $('#retryPending').text(response.data.retry_pending);
                $('#permanentlyFailed').text(response.data.permanently_failed);
                $('#resolvedPayments').text(response.data.resolved_payments);
            }
        }
    });
}

function filterFailedPayments() {
    const status = $('#statusFilter').val();
    const method = $('#methodFilter').val();
    const dateFrom = $('#dateFrom').val();
    const dateTo = $('#dateTo').val();
    
    // Reload table with filters
    $('#failedPaymentsTable').DataTable().destroy();
    loadFailedPayments();
}

function clearFilters() {
    $('#statusFilter').val('');
    $('#methodFilter').val('');
    $('#dateFrom').val('');
    $('#dateTo').val('');
    filterFailedPayments();
}

function viewFailedPaymentDetails(paymentId) {
    $.ajax({
        url: 'php_action/get_failed_payment_details.php',
        type: 'GET',
        data: { payment_id: paymentId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showFailedPaymentDetailsModal(response.data);
            }
        }
    });
}

function retryPayment(paymentId) {
    if (confirm('Are you sure you want to retry this payment?')) {
        $.ajax({
            url: 'php_action/retry_payment.php',
            type: 'POST',
            data: { payment_id: paymentId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Payment retry initiated successfully!');
                    loadFailedPayments();
                    loadFailedPaymentStatistics();
                } else {
                    alert('Error: ' + response.message);
                }
            }
        });
    }
}

function markAsResolved(paymentId) {
    const notes = prompt('Please provide resolution notes:');
    if (notes) {
        $.ajax({
            url: 'php_action/mark_payment_resolved.php',
            type: 'POST',
            data: { 
                payment_id: paymentId,
                notes: notes
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Payment marked as resolved successfully!');
                    loadFailedPayments();
                    loadFailedPaymentStatistics();
                } else {
                    alert('Error: ' + response.message);
                }
            }
        });
    }
}

function showFailedPaymentDetailsModal(paymentData) {
    const modal = `
        <div class="modal fade" id="failedPaymentDetailsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Failed Payment Details</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Payment ID:</strong> ${paymentData.payment_id}<br>
                                <strong>Transaction ID:</strong> ${paymentData.transaction_id}<br>
                                <strong>Patient Name:</strong> ${paymentData.patient_name}<br>
                                <strong>Patient Phone:</strong> ${paymentData.patient_phone}<br>
                                <strong>Amount:</strong> RWF ${parseFloat(paymentData.amount).toLocaleString()}<br>
                                <strong>Method:</strong> <span class="badge badge-primary">${paymentData.payment_method.replace('_', ' ').toUpperCase()}</span><br>
                                <strong>Status:</strong> <span class="badge badge-warning">${paymentData.status.replace('_', ' ').toUpperCase()}</span><br>
                                <strong>Retry Count:</strong> ${paymentData.retry_count}<br>
                                <strong>Created At:</strong> ${new Date(paymentData.created_at).toLocaleString()}
                            </div>
                            <div class="col-md-6">
                                <strong>Failure Reason:</strong><br>
                                <p class="bg-light p-2">${paymentData.failure_reason}</p>
                                <strong>Last Retry:</strong><br>
                                <p class="bg-light p-2">${paymentData.last_retry ? new Date(paymentData.last_retry).toLocaleString() : 'Never'}</p>
                                <strong>Resolved At:</strong><br>
                                <p class="bg-light p-2">${paymentData.resolved_at ? new Date(paymentData.resolved_at).toLocaleString() : 'Not resolved'}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('body').append(modal);
    $('#failedPaymentDetailsModal').modal('show');
    $('#failedPaymentDetailsModal').on('hidden.bs.modal', function() {
        $(this).remove();
    });
}
</script>
