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
	// Fetch real outstanding balances data
	$stmt = $connect->prepare("
		SELECT 
			ob.balance_id,
			ob.patient_name,
			ob.patient_phone,
			ob.total_amount,
			ob.paid_amount,
			ob.outstanding_amount,
			ob.status,
			ob.due_date,
			ob.last_payment_date,
			ob.created_at,
			au.username as admin_username
		FROM outstanding_balances ob
		LEFT JOIN admin_users au ON ob.admin_id = au.admin_id
		WHERE ob.pharmacy_id = ?
		ORDER BY ob.created_at DESC
	");
	$stmt->bind_param("i", $pharmacy_id);
	$stmt->execute();
	$result = $stmt->get_result();
	$outstandingBalances = $result->fetch_all(MYSQLI_ASSOC);
	$stmt->close();

	// Calculate statistics
	$totalOutstanding = count($outstandingBalances);
	$overdueAccounts = count(array_filter($outstandingBalances, function($b) { 
		return $b['status'] === 'OVERDUE'; 
	}));
	$overdueAmount = array_sum(array_column(array_filter($outstandingBalances, function($b) { 
		return $b['status'] === 'OVERDUE'; 
	}), 'outstanding_amount'));
	$currentAccounts = count(array_filter($outstandingBalances, function($b) { 
		return $b['status'] === 'CURRENT'; 
	}));
	$paidAccounts = count(array_filter($outstandingBalances, function($b) { 
		return $b['status'] === 'PAID'; 
	}));
	$totalOutstandingAmount = array_sum(array_column($outstandingBalances, 'outstanding_amount'));
	$collectionRate = $totalOutstanding > 0 ? (($totalOutstanding - $overdueAccounts) / $totalOutstanding) * 100 : 0;

} catch (Exception $e) {
	$outstandingBalances = [];
	$totalOutstanding = 0;
	$overdueAccounts = 0;
	$overdueAmount = 0;
	$currentAccounts = 0;
	$paidAccounts = 0;
	$totalOutstandingAmount = 0;
	$collectionRate = 0;
}
?>

<!-- Hero Section -->
<div class="row mb-4">
	<div class="col-12">
		<div class="card bg-gradient-info text-white">
			<div class="card-body">
				<div class="row align-items-center">
					<div class="col-md-8">
						<h2 class="mb-2"><i class="fa fa-credit-card"></i> Outstanding Balances</h2>
						<p class="mb-0">Track patient outstanding balances, overdue accounts, and payment collection progress.</p>
					</div>
					<div class="col-md-4 text-right">
						<button class="btn btn-light btn-lg" id="addBalanceBtn">
							<i class="fa fa-plus"></i> Add Balance
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
						<h4 class="mb-0"><?php echo $totalOutstanding; ?></h4>
						<p class="mb-0">Total Outstanding</p>
					</div>
					<div class="align-self-center">
						<i class="fa fa-list fa-2x"></i>
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
						<h4 class="mb-0"><?php echo $overdueAccounts; ?></h4>
						<p class="mb-0">Overdue Accounts</p>
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
				<div class="d-flex justify-content-between">
					<div>
						<h4 class="mb-0"><?php echo number_format($overdueAmount, 2); ?> RWF</h4>
						<p class="mb-0">Overdue Amount</p>
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
				<div class="d-flex justify-content-between">
					<div>
						<h4 class="mb-0"><?php echo number_format($collectionRate, 1); ?>%</h4>
						<p class="mb-0">Collection Rate</p>
					</div>
					<div class="align-self-center">
						<i class="fa fa-percent fa-2x"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Status Summary -->
<div class="row mb-4">
	<div class="col-md-4">
		<div class="card bg-light">
			<div class="card-body text-center">
				<h3 class="text-primary"><?php echo $currentAccounts; ?></h3>
				<p class="mb-0">Current Accounts</p>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="card bg-light">
			<div class="card-body text-center">
				<h3 class="text-success"><?php echo $paidAccounts; ?></h3>
				<p class="mb-0">Paid Accounts</p>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="card bg-light">
			<div class="card-body text-center">
				<h3 class="text-info"><?php echo number_format($totalOutstandingAmount, 2); ?> RWF</h3>
				<p class="mb-0">Total Outstanding Amount</p>
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
								<option value="CURRENT">Current</option>
								<option value="OVERDUE">Overdue</option>
								<option value="PAID">Paid</option>
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
							<label>Due Date From</label>
							<input type="date" class="form-control" id="dueDateFrom">
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

<!-- Outstanding Balances Table -->
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h4 class="mb-0"><i class="fa fa-table"></i> Outstanding Balances</h4>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-striped table-hover" id="balancesTable">
						<thead class="thead-dark">
							<tr>
								<th>Balance ID</th>
								<th>Patient</th>
								<th>Total Amount</th>
								<th>Paid Amount</th>
								<th>Outstanding</th>
								<th>Status</th>
								<th>Due Date</th>
								<th>Last Payment</th>
								<th>Created</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($outstandingBalances as $balance) { ?>
							<tr>
								<td>
									<span class="badge badge-secondary"><?php echo htmlspecialchars($balance['balance_id']); ?></span>
								</td>
								<td>
									<div>
										<strong><?php echo htmlspecialchars($balance['patient_name']); ?></strong>
										<br><small class="text-muted"><i class="fa fa-phone"></i> <?php echo htmlspecialchars($balance['patient_phone']); ?></small>
									</div>
								</td>
								<td>
									<strong><?php echo number_format($balance['total_amount'], 2); ?> RWF</strong>
								</td>
								<td>
									<span class="text-success"><?php echo number_format($balance['paid_amount'], 2); ?> RWF</span>
								</td>
								<td>
									<strong class="text-danger"><?php echo number_format($balance['outstanding_amount'], 2); ?> RWF</strong>
								</td>
								<td>
									<?php 
									$statusClass = '';
									$statusIcon = '';
									switch($balance['status']) {
										case 'CURRENT': $statusClass = 'badge-primary'; $statusIcon = 'fa-clock-o'; break;
										case 'OVERDUE': $statusClass = 'badge-danger'; $statusIcon = 'fa-exclamation-triangle'; break;
										case 'PAID': $statusClass = 'badge-success'; $statusIcon = 'fa-check'; break;
									}
									?>
									<span class="badge <?php echo $statusClass; ?>">
										<i class="fa <?php echo $statusIcon; ?>"></i> <?php echo htmlspecialchars($balance['status']); ?>
									</span>
								</td>
								<td>
									<div>
										<div><?php echo date('M d, Y', strtotime($balance['due_date'])); ?></div>
										<small class="text-muted">
											<?php 
											$daysDiff = (strtotime($balance['due_date']) - time()) / (60 * 60 * 24);
											if ($daysDiff < 0) {
												echo abs(floor($daysDiff)) . ' days overdue';
											} else {
												echo floor($daysDiff) . ' days remaining';
											}
											?>
										</small>
									</div>
								</td>
								<td>
									<?php if ($balance['last_payment_date']) { ?>
										<div>
											<div><?php echo date('M d, Y', strtotime($balance['last_payment_date'])); ?></div>
											<small class="text-muted"><?php echo date('H:i', strtotime($balance['last_payment_date'])); ?></small>
										</div>
									<?php } else { ?>
										<span class="text-muted">No payments</span>
									<?php } ?>
								</td>
								<td>
									<div>
										<div><?php echo date('M d, Y', strtotime($balance['created_at'])); ?></div>
										<small class="text-muted"><?php echo date('H:i', strtotime($balance['created_at'])); ?></small>
									</div>
								</td>
								<td>
									<button class="btn btn-sm btn-outline-primary" title="View Details" onclick="viewBalance(<?php echo $balance['balance_id']; ?>)">
										<i class="fa fa-eye"></i>
									</button>
									<button class="btn btn-sm btn-outline-success" title="Record Payment" onclick="recordPayment(<?php echo $balance['balance_id']; ?>)">
										<i class="fa fa-plus"></i>
									</button>
									<button class="btn btn-sm btn-outline-warning" title="Send Reminder" onclick="sendReminder('<?php echo $balance['patient_phone']; ?>')">
										<i class="fa fa-bell"></i>
									</button>
									<button class="btn btn-sm btn-outline-info" title="Contact Patient" onclick="contactPatient('<?php echo $balance['patient_phone']; ?>')">
										<i class="fa fa-phone"></i>
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
.bg-gradient-info {
	background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
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
	background-color: rgba(23, 162, 184, 0.1);
	cursor: pointer;
}

.btn-sm {
	margin: 2px;
}

.text-success {
	color: #28a745 !important;
}

.text-danger {
	color: #dc3545 !important;
}

.text-primary {
	color: #007bff !important;
}

.text-info {
	color: #17a2b8 !important;
}

.badge-success {
	background-color: #28a745;
}

.badge-danger {
	background-color: #dc3545;
}

.badge-primary {
	background-color: #007bff;
}

.badge-secondary {
	background-color: #6c757d;
}
</style>

<script>
$(document).ready(function() {
	$('#balancesTable').DataTable({
		pageLength: 10,
		order: [[8, 'desc']],
		responsive: true,
		dom: 'Bfrtip',
		buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
	});
});

function applyFilters() {
	const status = $('#statusFilter').val();
	const search = $('#searchFilter').val().toLowerCase();
	const dueDateFrom = $('#dueDateFrom').val();
	
	$('#balancesTable tbody tr').each(function() {
		let show = true;
		const row = $(this);
		
		if (status && !row.find('td:eq(5)').text().includes(status)) show = false;
		if (search && !row.text().toLowerCase().includes(search)) show = false;
		if (dueDateFrom) {
			const dueDate = row.find('td:eq(6)').text();
			// Simple date comparison - you might want to improve this
		}
		
		row.toggle(show);
	});
}

$('#applyFilters').on('click', applyFilters);
$('#statusFilter').on('change', applyFilters);
$('#searchFilter').on('keyup', applyFilters);

function viewBalance(id) {
	alert('View balance details for ID: ' + id);
}

function recordPayment(id) {
	alert('Record payment for balance ID: ' + id);
}

function sendReminder(phone) {
	alert('Send reminder to: ' + phone);
}

function contactPatient(phone) {
	alert('Contact patient at: ' + phone);
}

$('#addBalanceBtn').on('click', function() {
	alert('Add balance functionality would be implemented here.');
});
</script>