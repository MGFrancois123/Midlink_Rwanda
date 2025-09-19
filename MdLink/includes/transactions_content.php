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
	// Fetch real transactions data
	$stmt = $connect->prepare("
		SELECT 
			sm.movement_id as transaction_id,
			sm.movement_type as type,
			m.medicine_name,
			m.generic_name,
			c.category_name,
			sm.quantity,
			sm.previous_stock,
			sm.new_stock,
			sm.reference_number as reference,
			au.username as admin_username,
			sm.movement_date as transaction_date,
			sm.notes
		FROM stock_movements sm
		LEFT JOIN medicines m ON sm.medicine_id = m.medicine_id
		LEFT JOIN categories c ON m.category_id = c.category_id
		LEFT JOIN admin_users au ON sm.admin_id = au.admin_id
		WHERE m.pharmacy_id = ?
		ORDER BY sm.movement_date DESC
		LIMIT 100
	");
	$stmt->bind_param("i", $pharmacy_id);
	$stmt->execute();
	$result = $stmt->get_result();
	$transactions = $result->fetch_all(MYSQLI_ASSOC);
	$stmt->close();

	// Calculate statistics
	$totalTransactions = count($transactions);
	$stockInCount = count(array_filter($transactions, function($t) { return $t['type'] === 'IN'; }));
	$stockOutCount = count(array_filter($transactions, function($t) { return $t['type'] === 'OUT'; }));
	$adjustmentCount = count(array_filter($transactions, function($t) { return $t['type'] === 'ADJUSTMENT'; }));
	
	// Calculate net value (simplified)
	$netValue = 0;

} catch (Exception $e) {
	$transactions = [];
	$totalTransactions = 0;
	$stockInCount = 0;
	$stockOutCount = 0;
	$adjustmentCount = 0;
	$netValue = 0;
}
?>

<!-- Hero Section -->
<div class="row mb-4">
	<div class="col-12">
		<div class="card bg-gradient-primary text-white">
			<div class="card-body">
				<div class="row align-items-center">
					<div class="col-md-8">
						<h2 class="mb-2"><i class="fa fa-exchange"></i> All Transactions</h2>
						<p class="mb-0">Track all stock movements, sales, and inventory adjustments in real-time.</p>
					</div>
					<div class="col-md-4 text-right">
						<button class="btn btn-light btn-lg" id="addTransactionBtn">
							<i class="fa fa-plus"></i> Add Transaction
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
						<h4 class="mb-0"><?php echo $totalTransactions; ?></h4>
						<p class="mb-0">Total Transactions</p>
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
				<div class="d-flex justify-content-between">
					<div>
						<h4 class="mb-0"><?php echo $stockInCount; ?></h4>
						<p class="mb-0">Stock In</p>
					</div>
					<div class="align-self-center">
						<i class="fa fa-arrow-down fa-2x"></i>
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
						<h4 class="mb-0"><?php echo $stockOutCount; ?></h4>
						<p class="mb-0">Stock Out</p>
					</div>
					<div class="align-self-center">
						<i class="fa fa-arrow-up fa-2x"></i>
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
						<h4 class="mb-0"><?php echo $adjustmentCount; ?></h4>
						<p class="mb-0">Adjustments</p>
					</div>
					<div class="align-self-center">
						<i class="fa fa-edit fa-2x"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Transactions Table -->
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h4 class="mb-0"><i class="fa fa-table"></i> Transaction History</h4>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-striped table-hover" id="transactionsTable">
						<thead class="thead-dark">
							<tr>
								<th>Transaction ID</th>
								<th>Medicine</th>
								<th>Category</th>
								<th>Type</th>
								<th>Quantity</th>
								<th>Stock Before</th>
								<th>Stock After</th>
								<th>Reference</th>
								<th>Admin</th>
								<th>Date</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($transactions as $transaction) { ?>
							<tr>
								<td>
									<span class="badge badge-secondary"><?php echo htmlspecialchars($transaction['transaction_id']); ?></span>
								</td>
								<td>
									<div>
										<strong><?php echo htmlspecialchars($transaction['medicine_name']); ?></strong>
										<br><small class="text-muted"><?php echo htmlspecialchars($transaction['generic_name']); ?></small>
									</div>
								</td>
								<td>
									<span class="badge badge-info"><?php echo htmlspecialchars($transaction['category_name']); ?></span>
								</td>
								<td>
									<?php 
									$typeClass = '';
									$typeIcon = '';
									switch($transaction['type']) {
										case 'IN': $typeClass = 'badge-success'; $typeIcon = 'fa-arrow-down'; break;
										case 'OUT': $typeClass = 'badge-danger'; $typeIcon = 'fa-arrow-up'; break;
										case 'ADJUSTMENT': $typeClass = 'badge-warning'; $typeIcon = 'fa-edit'; break;
										case 'EXPIRED': $typeClass = 'badge-secondary'; $typeIcon = 'fa-times'; break;
									}
									?>
									<span class="badge <?php echo $typeClass; ?>">
										<i class="fa <?php echo $typeIcon; ?>"></i> <?php echo htmlspecialchars($transaction['type']); ?>
									</span>
								</td>
								<td>
									<span class="<?php echo $transaction['type'] === 'IN' ? 'text-success' : ($transaction['type'] === 'OUT' ? 'text-danger' : 'text-warning'); ?>">
										<?php echo $transaction['type'] === 'IN' ? '+' : ($transaction['type'] === 'OUT' ? '-' : ''); ?><?php echo abs($transaction['quantity']); ?>
									</span>
								</td>
								<td><?php echo $transaction['previous_stock']; ?></td>
								<td><?php echo $transaction['new_stock']; ?></td>
								<td>
									<code><?php echo htmlspecialchars($transaction['reference']); ?></code>
								</td>
								<td>
									<span class="badge badge-primary"><?php echo htmlspecialchars($transaction['admin_username']); ?></span>
								</td>
								<td>
									<div>
										<div><?php echo date('M d, Y', strtotime($transaction['transaction_date'])); ?></div>
										<small class="text-muted"><?php echo date('H:i', strtotime($transaction['transaction_date'])); ?></small>
									</div>
								</td>
								<td>
									<button class="btn btn-sm btn-outline-primary" title="View Details">
										<i class="fa fa-eye"></i>
									</button>
									<button class="btn btn-sm btn-outline-warning" title="Edit">
										<i class="fa fa-pencil"></i>
									</button>
									<button class="btn btn-sm btn-outline-danger" title="Delete">
										<i class="fa fa-trash"></i>
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
.bg-gradient-primary {
	background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
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
	background-color: rgba(0, 123, 255, 0.1);
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

.text-warning {
	color: #ffc107 !important;
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
	$('#transactionsTable').DataTable({
		pageLength: 10,
		order: [[9, 'desc']],
		responsive: true,
		dom: 'Bfrtip',
		buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
	});
});

// Add transaction button functionality
document.getElementById('addTransactionBtn').addEventListener('click', function() {
	alert('Add Transaction functionality would be implemented here with a form for creating new transactions.');
});

// Add row hover effects
document.querySelectorAll('tbody tr').forEach(row => {
	row.addEventListener('mouseenter', function() {
		this.style.backgroundColor = '#f8f9fa';
	});
	row.addEventListener('mouseleave', function() {
		this.style.backgroundColor = '';
	});
});

// Add click functionality to action buttons
document.querySelectorAll('.btn-outline-primary').forEach(btn => {
	btn.addEventListener('click', function() {
		alert('View transaction details functionality would be implemented here.');
	});
});

document.querySelectorAll('.btn-outline-warning').forEach(btn => {
	btn.addEventListener('click', function() {
		alert('Edit transaction functionality would be implemented here.');
	});
});

document.querySelectorAll('.btn-outline-danger').forEach(btn => {
	btn.addEventListener('click', function() {
		if (confirm('Are you sure you want to delete this transaction?')) {
			alert('Delete transaction functionality would be implemented here.');
		}
	});
});
</script>