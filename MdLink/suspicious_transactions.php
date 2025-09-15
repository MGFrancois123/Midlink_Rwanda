<?php include('./constant/check.php'); ?>
<?php include('./constant/layout/head.php'); ?>
<?php include('./constant/layout/header.php'); ?>
<?php include('./constant/layout/sidebar.php'); ?>

<div class="page-wrapper">
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Suspicious Transactions</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard_super.php">Home</a></li>
                    <li class="breadcrumb-item active">Reports & Analytics</li>
                    <li class="breadcrumb-item active">Suspicious Transactions</li>
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
                                <h3 class="text-white" id="totalSuspicious">0</h3>
                                <h6 class="text-white">Total Suspicious</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-eye fa-2x"></i>
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
                                <h3 class="text-white" id="highRisk">0</h3>
                                <h6 class="text-white">High Risk</h6>
                            </div>
                            <div class="align-self-center">
                                <i class="fa fa-exclamation-triangle fa-2x"></i>
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
                                <h3 class="text-white" id="openCases">0</h3>
                                <h6 class="text-white">Open Cases</h6>
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
                                <h3 class="text-white" id="resolvedCases">0</h3>
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
                        <h4 class="card-title">Filter Suspicious Transactions</h4>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Pattern Type</label>
                                    <select class="form-control" id="patternTypeFilter">
                                        <option value="">All Patterns</option>
                                        <option value="UNUSUAL_AMOUNT">Unusual Amount</option>
                                        <option value="FREQUENT_PURCHASES">Frequent Purchases</option>
                                        <option value="OFF_HOURS">Off Hours</option>
                                        <option value="SUSPICIOUS_MEDICINE">Suspicious Medicine</option>
                                        <option value="MULTIPLE_FAILURES">Multiple Failures</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Risk Score</label>
                                    <select class="form-control" id="riskScoreFilter">
                                        <option value="">All Risk Scores</option>
                                        <option value="0-25">Low (0-25)</option>
                                        <option value="26-50">Medium (26-50)</option>
                                        <option value="51-75">High (51-75)</option>
                                        <option value="76-100">Critical (76-100)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control" id="statusFilter">
                                        <option value="">All Status</option>
                                        <option value="OPEN">Open</option>
                                        <option value="INVESTIGATING">Investigating</option>
                                        <option value="RESOLVED">Resolved</option>
                                        <option value="ESCALATED">Escalated</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date From</label>
                                    <input type="date" class="form-control" id="dateFrom">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date To</label>
                                    <input type="date" class="form-control" id="dateTo">
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label>&nbsp;</label><br>
                                    <button class="btn btn-primary" onclick="filterSuspiciousTransactions()">
                                        <i class="fa fa-filter"></i> Apply Filters
                                    </button>
                                    <button class="btn btn-secondary" onclick="clearFilters()">
                                        <i class="fa fa-refresh"></i> Clear Filters
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Suspicious Transactions Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Suspicious Transaction Records</h4>
                        <div class="table-responsive">
                            <table id="suspiciousTransactionsTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Suspicious ID</th>
                                        <th>Transaction ID</th>
                                        <th>Pattern Type</th>
                                        <th>Risk Score</th>
                                        <th>Status</th>
                                        <th>Exposure Amount</th>
                                        <th>Admin</th>
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
    loadSuspiciousTransactions();
    loadSuspiciousStatistics();
});

function loadSuspiciousTransactions() {
    $.ajax({
        url: 'php_action/get_suspicious_transactions.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#suspiciousTransactionsTable').DataTable({
                    data: response.data,
                    columns: [
                        { data: 'suspicious_id' },
                        { data: 'transaction_id' },
                        { 
                            data: 'pattern_type',
                            render: function(data, type, row) {
                                return '<span class="badge badge-info">' + data.replace('_', ' ').toUpperCase() + '</span>';
                            }
                        },
                        { 
                            data: 'risk_score',
                            render: function(data, type, row) {
                                let badgeClass = 'badge-success';
                                if (data >= 76) badgeClass = 'badge-danger';
                                else if (data >= 51) badgeClass = 'badge-warning';
                                else if (data >= 26) badgeClass = 'badge-info';
                                return '<span class="badge ' + badgeClass + '">' + data + '</span>';
                            }
                        },
                        { 
                            data: 'status',
                            render: function(data, type, row) {
                                let badgeClass = 'badge-secondary';
                                if (data === 'RESOLVED') badgeClass = 'badge-success';
                                else if (data === 'INVESTIGATING') badgeClass = 'badge-warning';
                                else if (data === 'ESCALATED') badgeClass = 'badge-danger';
                                else if (data === 'OPEN') badgeClass = 'badge-info';
                                return '<span class="badge ' + badgeClass + '">' + data + '</span>';
                            }
                        },
                        { 
                            data: 'exposure_amount',
                            render: function(data, type, row) {
                                return 'RWF ' + parseFloat(data).toLocaleString();
                            }
                        },
                        { data: 'admin_name' },
                        { 
                            data: 'created_at',
                            render: function(data, type, row) {
                                return new Date(data).toLocaleString();
                            }
                        },
                        {
                            data: null,
                            render: function(data, type, row) {
                                let actions = '<button class="btn btn-sm btn-info" onclick="viewSuspiciousDetails(' + row.suspicious_id + ')">View</button>';
                                if (row.status === 'OPEN') {
                                    actions += ' <button class="btn btn-sm btn-warning" onclick="startInvestigation(' + row.suspicious_id + ')">Start Investigation</button>';
                                    actions += ' <button class="btn btn-sm btn-success" onclick="resolveSuspicious(' + row.suspicious_id + ')">Resolve</button>';
                                } else if (row.status === 'INVESTIGATING') {
                                    actions += ' <button class="btn btn-sm btn-success" onclick="resolveSuspicious(' + row.suspicious_id + ')">Resolve</button>';
                                    actions += ' <button class="btn btn-sm btn-danger" onclick="escalateSuspicious(' + row.suspicious_id + ')">Escalate</button>';
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

function loadSuspiciousStatistics() {
    $.ajax({
        url: 'php_action/get_suspicious_statistics.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#totalSuspicious').text(response.data.total_suspicious);
                $('#highRisk').text(response.data.high_risk);
                $('#openCases').text(response.data.open_cases);
                $('#resolvedCases').text(response.data.resolved_cases);
            }
        }
    });
}

function filterSuspiciousTransactions() {
    const patternType = $('#patternTypeFilter').val();
    const riskScore = $('#riskScoreFilter').val();
    const status = $('#statusFilter').val();
    const dateFrom = $('#dateFrom').val();
    const dateTo = $('#dateTo').val();
    
    // Reload table with filters
    $('#suspiciousTransactionsTable').DataTable().destroy();
    loadSuspiciousTransactions();
}

function clearFilters() {
    $('#patternTypeFilter').val('');
    $('#riskScoreFilter').val('');
    $('#statusFilter').val('');
    $('#dateFrom').val('');
    $('#dateTo').val('');
    filterSuspiciousTransactions();
}

function viewSuspiciousDetails(suspiciousId) {
    $.ajax({
        url: 'php_action/get_suspicious_details.php',
        type: 'GET',
        data: { suspicious_id: suspiciousId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showSuspiciousDetailsModal(response.data);
            }
        }
    });
}

function startInvestigation(suspiciousId) {
    if (confirm('Are you sure you want to start investigation for this suspicious transaction?')) {
        $.ajax({
            url: 'php_action/update_suspicious_status.php',
            type: 'POST',
            data: { 
                suspicious_id: suspiciousId,
                status: 'INVESTIGATING'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Investigation started successfully!');
                    loadSuspiciousTransactions();
                    loadSuspiciousStatistics();
                } else {
                    alert('Error: ' + response.message);
                }
            }
        });
    }
}

function resolveSuspicious(suspiciousId) {
    const notes = prompt('Please provide resolution notes:');
    if (notes) {
        $.ajax({
            url: 'php_action/update_suspicious_status.php',
            type: 'POST',
            data: { 
                suspicious_id: suspiciousId,
                status: 'RESOLVED',
                notes: notes
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Suspicious transaction resolved successfully!');
                    loadSuspiciousTransactions();
                    loadSuspiciousStatistics();
                } else {
                    alert('Error: ' + response.message);
                }
            }
        });
    }
}

function escalateSuspicious(suspiciousId) {
    const reason = prompt('Please provide escalation reason:');
    if (reason) {
        $.ajax({
            url: 'php_action/update_suspicious_status.php',
            type: 'POST',
            data: { 
                suspicious_id: suspiciousId,
                status: 'ESCALATED',
                notes: reason
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Suspicious transaction escalated successfully!');
                    loadSuspiciousTransactions();
                    loadSuspiciousStatistics();
                } else {
                    alert('Error: ' + response.message);
                }
            }
        });
    }
}

function showSuspiciousDetailsModal(suspiciousData) {
    const modal = `
        <div class="modal fade" id="suspiciousDetailsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Suspicious Transaction Details</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Suspicious ID:</strong> ${suspiciousData.suspicious_id}<br>
                                <strong>Transaction ID:</strong> ${suspiciousData.transaction_id}<br>
                                <strong>Pattern Type:</strong> <span class="badge badge-info">${suspiciousData.pattern_type.replace('_', ' ').toUpperCase()}</span><br>
                                <strong>Risk Score:</strong> <span class="badge badge-warning">${suspiciousData.risk_score}</span><br>
                                <strong>Status:</strong> <span class="badge badge-info">${suspiciousData.status}</span><br>
                                <strong>Exposure Amount:</strong> RWF ${parseFloat(suspiciousData.exposure_amount).toLocaleString()}<br>
                                <strong>Admin:</strong> ${suspiciousData.admin_name}<br>
                                <strong>Created At:</strong> ${new Date(suspiciousData.created_at).toLocaleString()}<br>
                                <strong>Resolved At:</strong> ${suspiciousData.resolved_at ? new Date(suspiciousData.resolved_at).toLocaleString() : 'Not resolved'}
                            </div>
                            <div class="col-md-6">
                                <strong>Description:</strong><br>
                                <p class="bg-light p-2">${suspiciousData.description}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('body').append(modal);
    $('#suspiciousDetailsModal').modal('show');
    $('#suspiciousDetailsModal').on('hidden.bs.modal', function() {
        $(this).remove();
    });
}
</script>
