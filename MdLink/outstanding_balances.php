<?php include('./constant/check.php'); ?>
<?php include('./constant/layout/head.php'); ?>
<?php include('./constant/layout/header.php'); ?>
<?php include('./constant/layout/sidebar.php'); ?>

<div class="page-wrapper">
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Outstanding Balances</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard_super.php">Home</a></li>
                    <li class="breadcrumb-item active">Payments & Billing</li>
                    <li class="breadcrumb-item active">Outstanding Balances</li>
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
                                <h3 class="text-white" id="totalOutstanding">RWF 0</h3>
                                <h6 class="text-white">Total Outstanding</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-balance-scale fa-2x"></i>
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
                                <h3 class="text-white" id="currentBalances">0</h3>
                                <h6 class="text-white">Current</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-clock-o fa-2x"></i>
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
                                <h3 class="text-white" id="overdueBalances">0</h3>
                                <h6 class="text-white">Overdue</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-exclamation-triangle fa-2x"></i>
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
                                <h3 class="text-white" id="paidBalances">0</h3>
                                <h6 class="text-white">Paid</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-check fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add New Outstanding Balance -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Add New Outstanding Balance</h4>
                        <form id="outstandingBalanceForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Patient Name *</label>
                                        <input type="text" class="form-control" id="patientName" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Patient Phone *</label>
                                        <input type="tel" class="form-control" id="patientPhone" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Total Amount *</label>
                                        <input type="number" class="form-control" id="totalAmount" step="0.01" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Paid Amount</label>
                                        <input type="number" class="form-control" id="paidAmount" step="0.01" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Due Date *</label>
                                        <input type="date" class="form-control" id="dueDate" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Pharmacy</label>
                                        <select class="form-control" id="pharmacyId">
                                            <option value="">Select Pharmacy</option>
                                            <!-- Options will be loaded via AJAX -->
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-plus"></i> Add Outstanding Balance
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Filter Outstanding Balances</h4>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control" id="statusFilter">
                                        <option value="">All Status</option>
                                        <option value="CURRENT">Current</option>
                                        <option value="OVERDUE">Overdue</option>
                                        <option value="PAID">Paid</option>
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
                        <button class="btn btn-primary" onclick="filterOutstandingBalances()">
                            <i class="fa fa-filter"></i> Apply Filters
                        </button>
                        <button class="btn btn-secondary" onclick="clearFilters()">
                            <i class="fa fa-refresh"></i> Clear Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Outstanding Balances Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Outstanding Balances</h4>
                        <div class="table-responsive">
                            <table id="outstandingBalancesTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Balance ID</th>
                                        <th>Patient Name</th>
                                        <th>Patient Phone</th>
                                        <th>Total Amount</th>
                                        <th>Paid Amount</th>
                                        <th>Outstanding Amount</th>
                                        <th>Status</th>
                                        <th>Due Date</th>
                                        <th>Last Payment</th>
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
    loadOutstandingBalances();
    loadOutstandingStatistics();
    loadPharmacies();
    
    // Form submission
    $('#outstandingBalanceForm').on('submit', function(e) {
        e.preventDefault();
        addOutstandingBalance();
    });
    
    // Auto-calculate outstanding amount
    $('#totalAmount, #paidAmount').on('input', function() {
        const total = parseFloat($('#totalAmount').val()) || 0;
        const paid = parseFloat($('#paidAmount').val()) || 0;
        const outstanding = total - paid;
        $('#outstandingAmount').val(outstanding.toFixed(2));
    });
});

function loadOutstandingBalances() {
    $.ajax({
        url: 'php_action/get_outstanding_balances.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#outstandingBalancesTable').DataTable({
                    data: response.data,
                    columns: [
                        { data: 'balance_id' },
                        { data: 'patient_name' },
                        { data: 'patient_phone' },
                        { 
                            data: 'total_amount',
                            render: function(data, type, row) {
                                return 'RWF ' + parseFloat(data).toLocaleString();
                            }
                        },
                        { 
                            data: 'paid_amount',
                            render: function(data, type, row) {
                                return 'RWF ' + parseFloat(data).toLocaleString();
                            }
                        },
                        { 
                            data: 'outstanding_amount',
                            render: function(data, type, row) {
                                return 'RWF ' + parseFloat(data).toLocaleString();
                            }
                        },
                        { 
                            data: 'status',
                            render: function(data, type, row) {
                                let badgeClass = 'badge-secondary';
                                if (data === 'CURRENT') badgeClass = 'badge-success';
                                else if (data === 'OVERDUE') badgeClass = 'badge-danger';
                                else if (data === 'PAID') badgeClass = 'badge-info';
                                return '<span class="badge ' + badgeClass + '">' + data + '</span>';
                            }
                        },
                        { 
                            data: 'due_date',
                            render: function(data, type, row) {
                                return new Date(data).toLocaleDateString();
                            }
                        },
                        { 
                            data: 'last_payment_date',
                            render: function(data, type, row) {
                                return data ? new Date(data).toLocaleDateString() : 'Never';
                            }
                        },
                        {
                            data: null,
                            render: function(data, type, row) {
                                let actions = '<button class="btn btn-sm btn-info" onclick="viewBalanceDetails(' + row.balance_id + ')">View</button>';
                                if (row.status !== 'PAID') {
                                    actions += ' <button class="btn btn-sm btn-success" onclick="recordPayment(' + row.balance_id + ')">Record Payment</button>';
                                }
                                return actions;
                            }
                        }
                    ],
                    order: [[7, 'asc']],
                    pageLength: 10
                });
            }
        }
    });
}

function loadOutstandingStatistics() {
    $.ajax({
        url: 'php_action/get_outstanding_statistics.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#totalOutstanding').text('RWF ' + parseFloat(response.data.total_outstanding).toLocaleString());
                $('#currentBalances').text(response.data.current_balances);
                $('#overdueBalances').text(response.data.overdue_balances);
                $('#paidBalances').text(response.data.paid_balances);
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
                let options = '<option value="">Select Pharmacy</option>';
                let filterOptions = '<option value="">All Pharmacies</option>';
                response.data.forEach(function(pharmacy) {
                    options += '<option value="' + pharmacy.pharmacy_id + '">' + pharmacy.name + '</option>';
                    filterOptions += '<option value="' + pharmacy.pharmacy_id + '">' + pharmacy.name + '</option>';
                });
                $('#pharmacyId').html(options);
                $('#pharmacyFilter').html(filterOptions);
            }
        }
    });
}

function addOutstandingBalance() {
    const formData = {
        patient_name: $('#patientName').val(),
        patient_phone: $('#patientPhone').val(),
        total_amount: $('#totalAmount').val(),
        paid_amount: $('#paidAmount').val(),
        due_date: $('#dueDate').val(),
        pharmacy_id: $('#pharmacyId').val()
    };
    
    $.ajax({
        url: 'php_action/add_outstanding_balance.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Outstanding balance added successfully!');
                $('#outstandingBalanceForm')[0].reset();
                loadOutstandingBalances();
                loadOutstandingStatistics();
            } else {
                alert('Error: ' + response.message);
            }
        }
    });
}

function filterOutstandingBalances() {
    const status = $('#statusFilter').val();
    const pharmacy = $('#pharmacyFilter').val();
    const dateFrom = $('#dateFrom').val();
    const dateTo = $('#dateTo').val();
    
    // Reload table with filters
    $('#outstandingBalancesTable').DataTable().destroy();
    loadOutstandingBalances();
}

function clearFilters() {
    $('#statusFilter').val('');
    $('#pharmacyFilter').val('');
    $('#dateFrom').val('');
    $('#dateTo').val('');
    filterOutstandingBalances();
}

function viewBalanceDetails(balanceId) {
    $.ajax({
        url: 'php_action/get_balance_details.php',
        type: 'GET',
        data: { balance_id: balanceId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showBalanceDetailsModal(response.data);
            }
        }
    });
}

function recordPayment(balanceId) {
    const amount = prompt('Enter payment amount:');
    if (amount && parseFloat(amount) > 0) {
        $.ajax({
            url: 'php_action/record_payment.php',
            type: 'POST',
            data: { 
                balance_id: balanceId,
                payment_amount: amount
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Payment recorded successfully!');
                    loadOutstandingBalances();
                    loadOutstandingStatistics();
                } else {
                    alert('Error: ' + response.message);
                }
            }
        });
    }
}

function showBalanceDetailsModal(balanceData) {
    const modal = `
        <div class="modal fade" id="balanceDetailsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Outstanding Balance Details</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Balance ID:</strong> ${balanceData.balance_id}<br>
                                <strong>Patient Name:</strong> ${balanceData.patient_name}<br>
                                <strong>Patient Phone:</strong> ${balanceData.patient_phone}<br>
                                <strong>Total Amount:</strong> RWF ${parseFloat(balanceData.total_amount).toLocaleString()}<br>
                                <strong>Paid Amount:</strong> RWF ${parseFloat(balanceData.paid_amount).toLocaleString()}<br>
                                <strong>Outstanding Amount:</strong> RWF ${parseFloat(balanceData.outstanding_amount).toLocaleString()}<br>
                                <strong>Status:</strong> <span class="badge badge-warning">${balanceData.status}</span><br>
                                <strong>Due Date:</strong> ${new Date(balanceData.due_date).toLocaleDateString()}<br>
                                <strong>Last Payment:</strong> ${balanceData.last_payment_date ? new Date(balanceData.last_payment_date).toLocaleDateString() : 'Never'}<br>
                                <strong>Created At:</strong> ${new Date(balanceData.created_at).toLocaleString()}
                            </div>
                            <div class="col-md-6">
                                <strong>Payment History:</strong><br>
                                <div class="bg-light p-2" style="max-height: 200px; overflow-y: auto;">
                                    ${balanceData.payment_history || 'No payment history available'}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('body').append(modal);
    $('#balanceDetailsModal').modal('show');
    $('#balanceDetailsModal').on('hidden.bs.modal', function() {
        $(this).remove();
    });
}
</script>
