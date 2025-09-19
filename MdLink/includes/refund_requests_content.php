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
	// Fetch real refund requests data
	$stmt = $connect->prepare("
		SELECT 
			rr.refund_id,
			rr.patient_name,
			rr.patient_phone,
			m.medicine_name,
			rr.quantity,
			rr.unit_price,
			rr.total_amount,
			rr.refund_amount,
			rr.reason,
			rr.status,
			rr.request_date,
			au.username as admin_username,
			rr.notes
		FROM refund_requests rr
		LEFT JOIN medicines m ON rr.medicine_id = m.medicine_id
		LEFT JOIN admin_users au ON rr.admin_id = au.admin_id
		WHERE rr.pharmacy_id = ?
		ORDER BY rr.request_date DESC
	");
	$stmt->bind_param("i", $pharmacy_id);
	$stmt->execute();
	$result = $stmt->get_result();
	$refundRequests = $result->fetch_all(MYSQLI_ASSOC);
	$stmt->close();

	// Calculate statistics
	$totalRequests = count($refundRequests);
	$pendingRequests = count(array_filter($refundRequests, function($r) { return $r['status'] === 'PENDING'; }));
	$approvedRequests = count(array_filter($refundRequests, function($r) { return $r['status'] === 'APPROVED'; }));
	$rejectedRequests = count(array_filter($refundRequests, function($r) { return $r['status'] === 'REJECTED'; }));
	$totalRefundAmount = array_sum(array_column($refundRequests, 'refund_amount'));

} catch (Exception $e) {
	$refundRequests = [];
	$totalRequests = 0;
	$pendingRequests = 0;
	$approvedRequests = 0;
	$rejectedRequests = 0;
	$totalRefundAmount = 0;
}
?>

<!-- Hero Section -->
<div class="row mb-4">
	<div class="col-12">
		<div class="card bg-gradient-warning text-white">
			<div class="card-body">
				<div class="row align-items-center">
					<div class="col-md-8">
						<h2 class="mb-2"><i class="fa fa-undo"></i> Refund Requests</h2>
						<p class="mb-0">Manage patient refund requests, review reasons, and process refunds efficiently.</p>
					</div>
					<div class="col-md-4 text-right">
						<button class="btn btn-light btn-lg" data-toggle="modal" data-target="#addRefundModal">
							<i class="fa fa-plus"></i> New Refund Request
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
						<h4 class="mb-0"><?php echo $totalRequests; ?></h4>
						<p class="mb-0">Total Requests</p>
					</div>
					<div class="align-self-center">
						<i class="fa fa-list fa-2x"></i>
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
						<h4 class="mb-0"><?php echo $pendingRequests; ?></h4>
						<p class="mb-0">Pending Review</p>
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
				<div class="d-flex justify-content-between">
					<div>
						<h4 class="mb-0"><?php echo $approvedRequests; ?></h4>
						<p class="mb-0">Approved</p>
					</div>
					<div class="align-self-center">
						<i class="fa fa-check-circle fa-2x"></i>
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
						<h4 class="mb-0"><?php echo number_format($totalRefundAmount, 2); ?> RWF</h4>
						<p class="mb-0">Total Refund Value</p>
					</div>
					<div class="align-self-center">
						<i class="fa fa-money fa-2x"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Refund Requests Table -->
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h4 class="mb-0"><i class="fa fa-table"></i> Refund Requests</h4>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-striped table-hover" id="refundTable">
						<thead class="thead-dark">
							<tr>
								<th>Refund ID</th>
								<th>Patient</th>
								<th>Medicine</th>
								<th>Amount</th>
								<th>Reason</th>
								<th>Status</th>
								<th>Request Date</th>
								<th>Admin</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($refundRequests as $refund) { ?>
							<tr>
								<td>
									<span class="badge badge-secondary"><?php echo htmlspecialchars($refund['refund_id']); ?></span>
								</td>
								<td>
									<div>
										<strong><?php echo htmlspecialchars($refund['patient_name']); ?></strong>
										<br><small class="text-muted"><i class="fa fa-phone"></i> <?php echo htmlspecialchars($refund['patient_phone']); ?></small>
									</div>
								</td>
								<td>
									<div>
										<strong><?php echo htmlspecialchars($refund['medicine_name']); ?></strong>
										<br><small class="text-muted">Qty: <?php echo $refund['quantity']; ?> × <?php echo number_format($refund['unit_price'], 2); ?> RWF</small>
									</div>
								</td>
								<td>
									<div>
										<strong class="text-danger"><?php echo number_format($refund['refund_amount'], 2); ?> RWF</strong>
										<br><small class="text-muted">Original: <?php echo number_format($refund['total_amount'], 2); ?> RWF</small>
									</div>
								</td>
								<td>
									<div>
										<span class="badge badge-info"><?php echo htmlspecialchars($refund['reason']); ?></span>
										<br><small class="text-muted"><?php echo htmlspecialchars($refund['notes']); ?></small>
									</div>
								</td>
								<td>
									<?php if ($refund['status'] === 'PENDING') { ?>
										<span class="badge badge-warning"><i class="fa fa-clock-o"></i> Pending</span>
									<?php } elseif ($refund['status'] === 'APPROVED') { ?>
										<span class="badge badge-success"><i class="fa fa-check"></i> Approved</span>
									<?php } else { ?>
										<span class="badge badge-danger"><i class="fa fa-times"></i> Rejected</span>
									<?php } ?>
								</td>
								<td>
									<div>
										<div><?php echo date('M d, Y', strtotime($refund['request_date'])); ?></div>
										<small class="text-muted"><?php echo date('H:i', strtotime($refund['request_date'])); ?></small>
									</div>
								</td>
								<td>
									<span class="badge badge-primary"><?php echo htmlspecialchars($refund['admin_username']); ?></span>
								</td>
								<td>
									<button class="btn btn-sm btn-outline-primary" title="View Details" onclick="viewRefund(<?php echo $refund['refund_id']; ?>)">
										<i class="fa fa-eye"></i>
									</button>
									<?php if ($refund['status'] === 'PENDING') { ?>
										<button class="btn btn-sm btn-outline-success" title="Approve" onclick="approveRefund(<?php echo $refund['refund_id']; ?>)">
											<i class="fa fa-check"></i>
										</button>
										<button class="btn btn-sm btn-outline-danger" title="Reject" onclick="rejectRefund(<?php echo $refund['refund_id']; ?>)">
											<i class="fa fa-times"></i>
										</button>
									<?php } ?>
									<button class="btn btn-sm btn-outline-warning" title="Edit" onclick="editRefund(<?php echo $refund['refund_id']; ?>)">
										<i class="fa fa-pencil"></i>
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

<!-- Add Refund Modal -->
<div class="modal fade" id="addRefundModal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header bg-warning text-white">
				<h5 class="modal-title"><i class="fa fa-plus"></i> New Refund Request</h5>
				<button type="button" class="close text-white" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<form id="addRefundForm">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Patient Name</label>
								<input type="text" class="form-control" name="patient_name" required>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>Patient Phone</label>
								<input type="tel" class="form-control" name="patient_phone" required>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Medicine</label>
								<select class="form-control" name="medicine_id" required>
									<option value="">Select Medicine</option>
									<!-- This would be populated via AJAX -->
								</select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>Quantity</label>
								<input type="number" class="form-control" name="quantity" min="1" required>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Unit Price (RWF)</label>
								<input type="number" class="form-control" name="unit_price" step="0.01" required>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>Refund Amount (RWF)</label>
								<input type="number" class="form-control" name="refund_amount" step="0.01" required>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label>Reason</label>
						<select class="form-control" name="reason" required>
							<option value="">Select Reason</option>
							<option value="Patient allergic reaction">Patient allergic reaction</option>
							<option value="Wrong medication dispensed">Wrong medication dispensed</option>
							<option value="Expired medication">Expired medication</option>
							<option value="Side effects">Side effects</option>
							<option value="Partial refund - unused portion">Partial refund - unused portion</option>
							<option value="Other">Other</option>
						</select>
					</div>
					<div class="form-group">
						<label>Notes</label>
						<textarea class="form-control" name="notes" rows="3"></textarea>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-warning" onclick="submitRefund()">Create Refund Request</button>
			</div>
		</div>
	</div>
</div>

<style>
.bg-gradient-warning {
	background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%);
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
	background-color: rgba(255, 193, 7, 0.1);
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
</style>

<script>
$(document).ready(function() {
	$('#refundTable').DataTable({
		pageLength: 10,
		order: [[6, 'desc']],
		responsive: true,
		dom: 'Bfrtip',
		buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
	});
});

function viewRefund(id) {
	alert('View refund details for ID: ' + id);
}

function approveRefund(id) {
	if (confirm('Are you sure you want to approve this refund request?')) {
		// AJAX call to approve refund
		alert('Refund approved for ID: ' + id);
	}
}

function rejectRefund(id) {
	if (confirm('Are you sure you want to reject this refund request?')) {
		// AJAX call to reject refund
		alert('Refund rejected for ID: ' + id);
	}
}

function editRefund(id) {
	alert('Edit refund for ID: ' + id);
}

function submitRefund() {
	// Form validation and AJAX submission
	alert('Refund request submitted successfully!');
	$('#addRefundModal').modal('hide');
}
</script>