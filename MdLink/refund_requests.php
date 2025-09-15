<?php include('./constant/check.php'); ?>
<?php include('./constant/layout/head.php'); ?>
<?php include('./constant/layout/header.php'); ?>
<?php include('./constant/layout/sidebar.php'); ?>

<div class="page-wrapper">
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Refund Requests</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard_super.php">Home</a></li>
                    <li class="breadcrumb-item active">Payments & Billing</li>
                    <li class="breadcrumb-item active">Refund Requests</li>
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
                                <h3 class="text-white" id="totalRefunds">0</h3>
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
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="p-10">
                                <h3 class="text-white" id="pendingRefunds">0</h3>
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
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="p-10">
                                <h3 class="text-white" id="approvedRefunds">0</h3>
                                <h6 class="text-white">Approved</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-check fa-2x"></i>
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
                                <h3 class="text-white" id="rejectedRefunds">0</h3>
                                <h6 class="text-white">Rejected</h6>
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
                        <h4 class="card-title">Filter Refund Requests</h4>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control" id="statusFilter">
                                        <option value="">All Status</option>
                                        <option value="PENDING">Pending</option>
                                        <option value="APPROVED">Approved</option>
                                        <option value="REJECTED">Rejected</option>
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
                        <button class="btn btn-primary" onclick="filterRefunds()">
                            <i class="fa fa-filter"></i> Apply Filters
                        </button>
                        <button class="btn btn-secondary" onclick="clearFilters()">
                            <i class="fa fa-refresh"></i> Clear Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Refund Requests Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Refund Requests</h4>
                        <div class="table-responsive">
                            <table id="refundRequestsTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Refund ID</th>
                                        <th>Patient Name</th>
                                        <th>Patient Phone</th>
                                        <th>Medicine</th>
                                        <th>Quantity</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Request Date</th>
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
    loadRefundRequests();
    loadRefundStatistics();
    loadPharmacies();
});

function loadRefundRequests() {
    $.ajax({
        url: 'php_action/get_refund_requests.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#refundRequestsTable').DataTable({
                    data: response.data,
                    columns: [
                        { data: 'refund_id' },
                        { data: 'patient_name' },
                        { data: 'patient_phone' },
                        { data: 'medicine_name' },
                        { data: 'quantity' },
                        { 
                            data: 'refund_amount',
                            render: function(data, type, row) {
                                return 'RWF ' + parseFloat(data).toLocaleString();
                            }
                        },
                        { 
                            data: 'status',
                            render: function(data, type, row) {
                                let badgeClass = 'badge-secondary';
                                if (data === 'APPROVED') badgeClass = 'badge-success';
                                else if (data === 'PENDING') badgeClass = 'badge-warning';
                                else if (data === 'REJECTED') badgeClass = 'badge-danger';
                                return '<span class="badge ' + badgeClass + '">' + data + '</span>';
                            }
                        },
                        { 
                            data: 'request_date',
                            render: function(data, type, row) {
                                return new Date(data).toLocaleString();
                            }
                        },
                        {
                            data: null,
                            render: function(data, type, row) {
                                let actions = '<button class="btn btn-sm btn-info" onclick="viewRefundDetails(' + row.refund_id + ')">View</button>';
                                if (row.status === 'PENDING') {
                                    actions += ' <button class="btn btn-sm btn-success" onclick="approveRefund(' + row.refund_id + ')">Approve</button>';
                                    actions += ' <button class="btn btn-sm btn-danger" onclick="rejectRefund(' + row.refund_id + ')">Reject</button>';
                                }
                                return actions;
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

function loadRefundStatistics() {
    $.ajax({
        url: 'php_action/get_refund_statistics.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#totalRefunds').text(response.data.total_refunds);
                $('#pendingRefunds').text(response.data.pending_refunds);
                $('#approvedRefunds').text(response.data.approved_refunds);
                $('#rejectedRefunds').text(response.data.rejected_refunds);
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

function filterRefunds() {
    const status = $('#statusFilter').val();
    const pharmacy = $('#pharmacyFilter').val();
    const dateFrom = $('#dateFrom').val();
    const dateTo = $('#dateTo').val();
    
    // Reload table with filters
    $('#refundRequestsTable').DataTable().destroy();
    loadRefundRequests();
}

function clearFilters() {
    $('#statusFilter').val('');
    $('#pharmacyFilter').val('');
    $('#dateFrom').val('');
    $('#dateTo').val('');
    filterRefunds();
}

function viewRefundDetails(refundId) {
    $.ajax({
        url: 'php_action/get_refund_details.php',
        type: 'GET',
        data: { refund_id: refundId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showRefundDetailsModal(response.data);
            }
        }
    });
}

function approveRefund(refundId) {
    if (confirm('Are you sure you want to approve this refund request?')) {
        $.ajax({
            url: 'php_action/process_refund.php',
            type: 'POST',
            data: { 
                refund_id: refundId,
                action: 'approve'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Refund request approved successfully!');
                    loadRefundRequests();
                    loadRefundStatistics();
                } else {
                    alert('Error: ' + response.message);
                }
            }
        });
    }
}

function rejectRefund(refundId) {
    const reason = prompt('Please provide a reason for rejection:');
    if (reason) {
        $.ajax({
            url: 'php_action/process_refund.php',
            type: 'POST',
            data: { 
                refund_id: refundId,
                action: 'reject',
                reason: reason
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Refund request rejected successfully!');
                    loadRefundRequests();
                    loadRefundStatistics();
                } else {
                    alert('Error: ' + response.message);
                }
            }
        });
    }
}

function showRefundDetailsModal(refundData) {
    const modal = `
        <div class="modal fade" id="refundDetailsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Refund Request Details</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Refund ID:</strong> ${refundData.refund_id}<br>
                                <strong>Patient Name:</strong> ${refundData.patient_name}<br>
                                <strong>Patient Phone:</strong> ${refundData.patient_phone}<br>
                                <strong>Medicine:</strong> ${refundData.medicine_name}<br>
                                <strong>Quantity:</strong> ${refundData.quantity}<br>
                                <strong>Unit Price:</strong> RWF ${parseFloat(refundData.unit_price).toLocaleString()}<br>
                                <strong>Total Amount:</strong> RWF ${parseFloat(refundData.total_amount).toLocaleString()}<br>
                                <strong>Refund Amount:</strong> RWF ${parseFloat(refundData.refund_amount).toLocaleString()}<br>
                                <strong>Status:</strong> <span class="badge badge-warning">${refundData.status}</span><br>
                                <strong>Request Date:</strong> ${new Date(refundData.request_date).toLocaleString()}
                            </div>
                            <div class="col-md-6">
                                <strong>Reason:</strong><br>
                                <p class="bg-light p-2">${refundData.reason}</p>
                                <strong>Notes:</strong><br>
                                <p class="bg-light p-2">${refundData.notes || 'No notes available'}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('body').append(modal);
    $('#refundDetailsModal').modal('show');
    $('#refundDetailsModal').on('hidden.bs.modal', function() {
        $(this).remove();
    });
}
</script>
