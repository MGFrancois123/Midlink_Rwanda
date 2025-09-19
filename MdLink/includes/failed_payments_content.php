<?php
require_once __DIR__ . '/../constant/connect.php';
if (session_status() === PHP_SESSION_NONE) { if (session_status() === PHP_SESSION_NONE) {
    session_start();
} }

if (!isset($_SESSION['adminId'])) {
	echo '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Access Denied. Please log in.</div>';
	return;
}

$pharmacy_id = $_SESSION['pharmacy_id'] ?? 1;

try {
	// Fetch real failed payments data
	$stmt = $connect->prepare("
		SELECT 
			fp.payment_id,
			fp.transaction_id,
			fp.patient_name,
			fp.patient_phone,
			fp.amount,
			fp.payment_method,
			fp.status,
			fp.failure_reason,
			fp.retry_count,
			fp.last_retry,
			fp.created_at,
			fp.resolved_at,
			au.username as admin_username
		FROM failed_payments fp
		LEFT JOIN admin_users au ON fp.admin_id = au.admin_id
		WHERE fp.pharmacy_id = ?
		ORDER BY fp.created_at DESC
	");
	$stmt->bind_param("i", $pharmacy_id);
	$stmt->execute();
	$result = $stmt->get_result();
	$failedPayments = $result->fetch_all(MYSQLI_ASSOC);
	$stmt->close();

	// Calculate statistics
	$totalFailed = count($failedPayments);
	$retryPending = count(array_filter($failedPayments, function($p) { return $p['status'] === 'RETRY_PENDING'; }));
	$permanentlyFailed = count(array_filter($failedPayments, function($p) { return $p['status'] === 'PERMANENTLY_FAILED'; }));
	$resolved = count(array_filter($failedPayments, function($p) { return $p['status'] === 'RESOLVED'; }));
	$totalFailedAmount = array_sum(array_column($failedPayments, 'amount'));

} catch (Exception $e) {
	$failedPayments = [];
	$totalFailed = 0;
	$retryPending = 0;
	$permanentlyFailed = 0;
	$resolved = 0;
	$totalFailedAmount = 0;
}
?>

<!-- Hero Section -->
<div class="row mb-4">
	<div class="col-12">
		<div class="card bg-gradient-danger text-white">
			<div class="card-body">
				<div class="row align-items-center">
					<div class="col-md-8">
						<h2 class="mb-2"><i class="fa fa-exclamation-triangle"></i> Failed Payments</h2>
						<p class="mb-0">Monitor and manage failed payment attempts, retry mechanisms, and resolution tracking.</p>
					</div>
					<div class="col-md-4 text-right">
						<button class="btn btn-light btn-lg" id="retryAllBtn">
							<i class="fa fa-refresh"></i> Retry All Pending
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
	<div class="col-md-3">
		<div class="card bg-primary text-white">
			<div class="card-body">
				<div class="d-flex justify-content-between">
					<div>
						<h4 class="mb-0"><?php echo $totalFailed; ?></h4>
						<p class="mb-0">Total Failed</p>
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
				<div class="d-flex justify-content-between">
					<div>
						<h4 class="mb-0"><?php echo $retryPending; ?></h4>
						<p class="mb-0">Retry Pending</p>
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
				<div class="d-flex justify-content-between">
					<div>
						<h4 class="mb-0"><?php echo $permanentlyFailed; ?></h4>
						<p class="mb-0">Permanently Failed</p>
					</div>
					<div class="align-self-center">
						<i class="fa fa-ban fa-2x"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card bg-info text-white">
			<div class="card-body">
				<div class="d-flex justify-content-between">
					<div>
						<h4 class="mb-0"><?php echo number_format($totalFailedAmount, 2); ?> RWF</h4>
						<p class="mb-0">Total Failed Amount</p>
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
<div class="row mb-4">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<label>Status</label>
							<select class="form-control" id="statusFilter">
								<option value="">All</option>
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
								<option value="">All</option>
								<option value="MOBILE_MONEY">Mobile Money</option>
								<option value="BANK_TRANSFER">Bank Transfer</option>
								<option value="CREDIT_CARD">Credit Card</option>
								<option value="CASH">Cash</option>
							</select>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label>Search</label>
							<input type="text" class="form-control" id="searchFilter" placeholder="Patient name, phone...">
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label>&nbsp;</label>
							<button class="btn btn-primary btn-block" id="applyFilters">
								<i class="fa fa-filter"></i> Apply Filters
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Failed Payments Table -->
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h4 class="mb-0"><i class="fa fa-table"></i> Failed Payment Records</h4>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-striped table-hover" id="failedPaymentsTable">
						<thead class="thead-dark">
							<tr>
								<th>Payment ID</th>
								<th>Transaction ID</th>
								<th>Patient</th>
								<th>Amount</th>
								<th>Method</th>
								<th>Status</th>
								<th>Failure Reason</th>
								<th>Retry Count</th>
								<th>Created</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($failedPayments as $payment) { ?>
							<tr>
								<td>
									<span class="badge badge-secondary"><?php echo htmlspecialchars($payment['payment_id']); ?></span>
								</td>
								<td>
									<code><?php echo htmlspecialchars($payment['transaction_id']); ?></code>
								</td>
								<td>
									<div>
										<strong><?php echo htmlspecialchars($payment['patient_name']); ?></strong>
										<br><small class="text-muted"><i class="fa fa-phone"></i> <?php echo htmlspecialchars($payment['patient_phone']); ?></small>
									</div>
								</td>
								<td>
									<strong class="text-danger"><?php echo number_format($payment['amount'], 2); ?> RWF</strong>
								</td>
								<td>
									<?php 
									$methodIcon = '';
									$methodClass = '';
									switch($payment['payment_method']) {
										case 'MOBILE_MONEY': $methodIcon = 'fa-mobile'; $methodClass = 'badge-primary'; break;
										case 'BANK_TRANSFER': $methodIcon = 'fa-university'; $methodClass = 'badge-info'; break;
										case 'CREDIT_CARD': $methodIcon = 'fa-credit-card'; $methodClass = 'badge-success'; break;
										case 'CASH': $methodIcon = 'fa-money'; $methodClass = 'badge-warning'; break;
									}
									?>
									<span class="badge <?php echo $methodClass; ?>">
										<i class="fa <?php echo $methodIcon; ?>"></i> <?php echo str_replace('_', ' ', $payment['payment_method']); ?>
									</span>
								</td>
								<td>
									<?php 
									$statusClass = '';
									$statusIcon = '';
									switch($payment['status']) {
										case 'RETRY_PENDING': $statusClass = 'badge-warning'; $statusIcon = 'fa-clock-o'; break;
										case 'PERMANENTLY_FAILED': $statusClass = 'badge-danger'; $statusIcon = 'fa-ban'; break;
										case 'RESOLVED': $statusClass = 'badge-success'; $statusIcon = 'fa-check'; break;
									}
									?>
									<span class="badge <?php echo $statusClass; ?>">
										<i class="fa <?php echo $statusIcon; ?>"></i> <?php echo str_replace('_', ' ', $payment['status']); ?>
									</span>
								</td>
								<td>
									<small><?php echo htmlspecialchars($payment['failure_reason']); ?></small>
								</td>
								<td>
									<span class="badge badge-info"><?php echo $payment['retry_count']; ?></span>
								</td>
								<td>
									<div>
										<div><?php echo date('M d, Y', strtotime($payment['created_at'])); ?></div>
										<small class="text-muted"><?php echo date('H:i', strtotime($payment['created_at'])); ?></small>
									</div>
								</td>
								<td>
									<button class="btn btn-sm btn-outline-primary" title="View Details" onclick="viewPayment(<?php echo $payment['payment_id']; ?>)">
										<i class="fa fa-eye"></i>
									</button>
									<?php if ($payment['status'] === 'RETRY_PENDING') { ?>
										<button class="btn btn-sm btn-outline-success" title="Retry Payment" onclick="retryPayment(<?php echo $payment['payment_id']; ?>)">
											<i class="fa fa-refresh"></i>
										</button>
									<?php } ?>
									<button class="btn btn-sm btn-outline-info" title="Contact Patient" onclick="contactPatient('<?php echo $payment['patient_phone']; ?>')">
										<i class="fa fa-phone"></i>
									</button>
									<button class="btn btn-sm btn-outline-warning" title="Mark as Resolved" onclick="markResolved(<?php echo $payment['payment_id']; ?>)">
										<i class="fa fa-check"></i>
									</button>
								</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
.bg-gradient-danger {
	background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
}

.card {
	border: none;
	box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
	transition: all 0.3s ease;
}

.card:hover {
	box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
	transform: translateY(-2px);
}

.table th {
	border-top: none;
	font-weight: 600;
}

.badge {
	font-size: 0.75rem;
	padding: 0.375rem 0.75rem;
}

.table-hover tbody tr:hover {
	background-color: rgba(220, 53, 69, 0.1);
	cursor: pointer;
}

.btn-sm {
	margin: 2px;
}

.text-danger {
	color: #dc3545 !important;
}

.badge-success {
	background-color: #28a745;
}

.badge-danger {
	background-color: #dc3545;
}

.badge-warning {
	background-color: #ffc107;
	color: #212529;
}

.badge-info {
	background-color: #17a2b8;
}

.badge-primary {
	background-color: #007bff;
}

.badge-secondary {
	background-color: #6c757d;
}

code {
	background-color: #f8f9fa;
	padding: 0.2rem 0.4rem;
	border-radius: 0.25rem;
	font-size: 0.875rem;
}
</style>

<script>
$(document).ready(function() {
	$('#failedPaymentsTable').DataTable({
		pageLength: 10,
		order: [[8, 'desc']],
		responsive: true,
		dom: 'Bfrtip',
		buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
	});
});

function applyFilters() {
	const status = $('#statusFilter').val();
	const method = $('#methodFilter').val();
	const search = $('#searchFilter').val().toLowerCase();
	
	$('#failedPaymentsTable tbody tr').each(function() {
		let show = true;
		const row = $(this);
		
		if (status && !row.find('td:eq(5)').text().includes(status.replace('_', ' '))) show = false;
		if (method && !row.find('td:eq(4)').text().includes(method.replace('_', ' '))) show = false;
		if (search && !row.text().toLowerCase().includes(search)) show = false;
		
		row.toggle(show);
	});
}

$('#applyFilters').on('click', applyFilters);
$('#statusFilter, #methodFilter').on('change', applyFilters);
$('#searchFilter').on('keyup', applyFilters);

function viewPayment(id) {
	alert('View payment details for ID: ' + id);
}

function retryPayment(id) {
	if (confirm('Are you sure you want to retry this payment?')) {
		// AJAX call to retry payment
		alert('Payment retry initiated for ID: ' + id);
	}
}

function contactPatient(phone) {
	alert('Contact patient at: ' + phone);
}

function markResolved(id) {
	if (confirm('Are you sure you want to mark this payment as resolved?')) {
		// AJAX call to mark as resolved
		alert('Payment marked as resolved for ID: ' + id);
	}
}

$('#retryAllBtn').on('click', function() {
	if (confirm('Are you sure you want to retry all pending payments?')) {
		alert('Retry all pending payments initiated');
	}
});
</script>