<?php
require_once __DIR__ . '/../constant/connect.php';
if (session_status() === PHP_SESSION_NONE) {
	if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
}

// Enforce login
if (!isset($_SESSION['adminId'])) {
	echo '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Access Denied. Please log in.</div>';
	return;
}

// Sample branches
$branches = [
	['id' => 1, 'name' => 'Ineza Pharmacy - Main'],
	['id' => 2, 'name' => 'Ineza Pharmacy - Downtown'],
	['id' => 3, 'name' => 'Ineza Pharmacy - Airport'],
];

// Sample reconciliation rows
$reconRows = [
	[
		'ref' => 'REC-0001', 'date' => '2024-01-15 10:30:00', 'branch_from' => 1, 'branch_to' => 2,
		'items_count' => 12, 'system_total' => 850.50, 'reported_total' => 845.50, 'diff' => -5.00,
		'status' => 'Pending', 'created_by' => 'pharmacy_admin', 'notes' => 'Minor cash variance'
	],
	[
		'ref' => 'REC-0002', 'date' => '2024-01-14 17:45:00', 'branch_from' => 2, 'branch_to' => 1,
		'items_count' => 8, 'system_total' => 420.00, 'reported_total' => 420.00, 'diff' => 0.00,
		'status' => 'Matched', 'created_by' => 'finance_admin', 'notes' => 'All matched'
	],
	[
		'ref' => 'REC-0003', 'date' => '2024-01-13 12:10:00', 'branch_from' => 3, 'branch_to' => 1,
		'items_count' => 6, 'system_total' => 260.00, 'reported_total' => 270.00, 'diff' => 10.00,
		'status' => 'Investigating', 'created_by' => 'pharmacy_admin', 'notes' => 'Awaiting till report'
	],
];

// KPI calculations
$totalRecons = count($reconRows);
$matched = count(array_filter($reconRows, fn($r) => $r['status'] === 'Matched'));
$pending = count(array_filter($reconRows, fn($r) => $r['status'] === 'Pending'));
$investigating = count(array_filter($reconRows, fn($r) => $r['status'] === 'Investigating'));
$netVariance = array_sum(array_map(fn($r) => $r['diff'], $reconRows));

function branchNameById($branches, $id) {
	foreach ($branches as $b) if ($b['id'] === $id) return $b['name'];
	return 'Unknown';
}
?>

<!-- Hero -->
<div class="row mb-4">
	<div class="col-12">
		<div class="card bg-gradient-primary text-white">
			<div class="card-body">
				<div class="row align-items-center">
					<div class="col-md-8">
						<h2 class="mb-2"><i class="fa fa-exchange"></i> Branch Reconciliation</h2>
						<p class="mb-0">Compare sales, cash, and transfers between branches. Resolve variances and approve reconciliations.</p>
					</div>
					<div class="col-md-4 text-right">
						<button class="btn btn-light btn-lg" data-toggle="modal" data-target="#newReconModal"><i class="fa fa-plus"></i> New Reconciliation</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- KPIs -->
<div class="row mb-4">
	<div class="col-md-2">
		<div class="card bg-primary text-white"><div class="card-body d-flex justify-content-between"><div><h4 class="mb-0"><?php echo $totalRecons; ?></h4><p class="mb-0">Total</p></div><i class="fa fa-list fa-2x"></i></div></div>
	</div>
	<div class="col-md-2">
		<div class="card bg-success text-white"><div class="card-body d-flex justify-content-between"><div><h4 class="mb-0"><?php echo $matched; ?></h4><p class="mb-0">Matched</p></div><i class="fa fa-check-circle fa-2x"></i></div></div>
	</div>
	<div class="col-md-2">
		<div class="card bg-warning text-white"><div class="card-body d-flex justify-content-between"><div><h4 class="mb-0"><?php echo $pending; ?></h4><p class="mb-0">Pending</p></div><i class="fa fa-clock-o fa-2x"></i></div></div>
	</div>
	<div class="col-md-2">
		<div class="card bg-info text-white"><div class="card-body d-flex justify-content-between"><div><h4 class="mb-0"><?php echo $investigating; ?></h4><p class="mb-0">Investigating</p></div><i class="fa fa-search fa-2x"></i></div></div>
	</div>
	<div class="col-md-4">
		<div class="card bg-dark text-white"><div class="card-body d-flex justify-content-between"><div><h4 class="mb-0"><?php echo number_format($netVariance, 2); ?> RWF</h4><p class="mb-0">Net Variance</p></div><i class="fa fa-balance-scale fa-2x"></i></div></div>
	</div>
</div>

<!-- Filters -->
<div class="row mb-4">
	<div class="col-12">
		<div class="card"><div class="card-body">
			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						<label><i class="fa fa-building"></i> Branch</label>
						<select class="form-control" id="branchFilter">
							<option value="">All Branches</option>
							<?php foreach ($branches as $b) { ?>
							<option value="<?php echo $b['id']; ?>"><?php echo htmlspecialchars($b['name']); ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label><i class="fa fa-filter"></i> Status</label>
						<select class="form-control" id="statusFilter">
							<option value="">All</option>
							<option>Matched</option>
							<option>Pending</option>
							<option>Investigating</option>
						</select>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label><i class="fa fa-calendar"></i> Date Range</label>
						<input type="date" class="form-control" id="fromDate" value="<?php echo date('Y-m-d', strtotime('-7 days')); ?>">
						<small class="text-muted">From</small>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label>&nbsp;</label>
						<input type="date" class="form-control" id="toDate" value="<?php echo date('Y-m-d'); ?>">
						<small class="text-muted">To</small>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label><i class="fa fa-search"></i> Search</label>
						<input type="text" class="form-control" id="searchFilter" placeholder="Search reference, notes...">
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label>&nbsp;</label>
						<button class="btn btn-primary btn-block" id="applyFilters"><i class="fa fa-filter"></i> Apply Filters</button>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label>&nbsp;</label>
						<button class="btn btn-secondary btn-block" id="exportBtn"><i class="fa fa-download"></i> Export</button>
					</div>
				</div>
			</div>
		</div></div>
	</div>
</div>

<!-- Table -->
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h4 class="mb-0"><i class="fa fa-table"></i> Reconciliation Records</h4>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-striped table-hover" id="reconTable">
						<thead class="thead-dark">
							<tr>
								<th>Reference</th>
								<th>Date</th>
								<th>From</th>
								<th>To</th>
								<th>Items</th>
								<th>System Total</th>
								<th>Reported Total</th>
								<th>Variance</th>
								<th>Status</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($reconRows as $r) { ?>
							<tr>
								<td><span class="badge badge-secondary"><?php echo htmlspecialchars($r['ref']); ?></span></td>
								<td>
									<div><?php echo date('M d, Y', strtotime($r['date'])); ?></div>
									<small class="text-muted"><?php echo date('H:i', strtotime($r['date'])); ?></small>
								</td>
								<td><span class="badge badge-info"><?php echo htmlspecialchars(branchNameById($branches, $r['branch_from'])); ?></span></td>
								<td><span class="badge badge-info"><?php echo htmlspecialchars(branchNameById($branches, $r['branch_to'])); ?></span></td>
								<td><strong><?php echo number_format($r['items_count']); ?></strong></td>
								<td><?php echo number_format($r['system_total'], 2); ?> RWF</td>
								<td><?php echo number_format($r['reported_total'], 2); ?> RWF</td>
								<td><strong class="<?php echo $r['diff'] == 0 ? 'text-success' : ($r['diff'] > 0 ? 'text-danger' : 'text-warning'); ?>"><?php echo number_format($r['diff'], 2); ?> RWF</strong></td>
								<td>
									<?php if ($r['status'] === 'Matched') { ?>
										<span class="badge badge-success"><i class="fa fa-check"></i> Matched</span>
									<?php } elseif ($r['status'] === 'Pending') { ?>
										<span class="badge badge-warning"><i class="fa fa-clock-o"></i> Pending</span>
									<?php } else { ?>
										<span class="badge badge-info"><i class="fa fa-search"></i> Investigating</span>
									<?php } ?>
								</td>
								<td>
									<button class="btn btn-sm btn-outline-primary" onclick="viewRecon('<?php echo $r['ref']; ?>')" title="View Details"><i class="fa fa-eye"></i></button>
									<?php if ($r['status'] !== 'Matched') { ?>
									<button class="btn btn-sm btn-outline-success" onclick="markMatched('<?php echo $r['ref']; ?>')" title="Mark Matched"><i class="fa fa-check"></i></button>
									<button class="btn btn-sm btn-outline-danger" onclick="openVariance('<?php echo $r['ref']; ?>')" title="Open Variance"><i class="fa fa-balance-scale"></i></button>
									<?php } ?>
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

<!-- Modals -->
<div class="modal fade" id="newReconModal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h5 class="modal-title"><i class="fa fa-plus"></i> New Reconciliation</h5>
				<button type="button" class="close text-white" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div id="reconMsg"></div>
				<form id="newReconForm">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>From Branch *</label>
								<select class="form-control" name="branch_from" required>
									<option value="">Select</option>
									<?php foreach ($branches as $b) { ?>
									<option value="<?php echo $b['id']; ?>"><?php echo htmlspecialchars($b['name']); ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>To Branch *</label>
								<select class="form-control" name="branch_to" required>
									<option value="">Select</option>
									<?php foreach ($branches as $b) { ?>
									<option value="<?php echo $b['id']; ?>"><?php echo htmlspecialchars($b['name']); ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>System Total *</label>
								<input type="number" class="form-control" name="system_total" step="0.01" min="0" required>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>Reported Total *</label>
								<input type="number" class="form-control" name="reported_total" step="0.01" min="0" required>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label>Notes</label>
						<textarea class="form-control" name="notes" rows="3" placeholder="Add notes..."></textarea>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-primary" id="btnCreateRecon"><i class="fa fa-plus"></i> Create</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="reconDetailsModal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header bg-info text-white">
				<h5 class="modal-title"><i class="fa fa-eye"></i> Reconciliation Details</h5>
				<button type="button" class="close text-white" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body" id="reconDetailsContent"></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="varianceModal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header bg-danger text-white">
				<h5 class="modal-title"><i class="fa fa-balance-scale"></i> Open Variance Case</h5>
				<button type="button" class="close text-white" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div id="varianceMsg"></div>
				<form id="varianceForm">
					<div class="form-group">
						<label>Reference</label>
						<input type="text" class="form-control" id="varianceRef" readonly>
					</div>
					<div class="form-group">
						<label>Variance Notes *</label>
						<textarea class="form-control" name="variance_notes" rows="3" required placeholder="Describe investigation steps..."></textarea>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-danger" id="btnOpenVariance"><i class="fa fa-send"></i> Submit</button>
			</div>
		</div>
	</div>
</div>

<style>
.bg-gradient-primary{background:linear-gradient(135deg,#007bff 0%,#0056b3 100%)}
.card{border:none;box-shadow:0 0.125rem 0.25rem rgba(0,0,0,.075);transition:all .3s ease}
.card:hover{box-shadow:0 .5rem 1rem rgba(0,0,0,.15);transform:translateY(-2px)}
.table th{border-top:none;font-weight:600}
.badge{font-size:.75rem;padding:.375rem .75rem}
.table-hover tbody tr:hover{background-color:rgba(0,123,255,.08);cursor:pointer}
.modal-header.bg-primary{background:linear-gradient(135deg,#007bff 0%,#0056b3 100%)!important}
.modal-header.bg-info{background:linear-gradient(135deg,#17a2b8 0%,#138496 100%)!important}
.modal-header.bg-danger{background:linear-gradient(135deg,#dc3545 0%,#c82333 100%)!important}
.form-control:focus{border-color:#007bff;box-shadow:0 0 0 .2rem rgba(0,123,255,.25)}
</style>

<script>
(function(){
	$(document).ready(function(){
		$('#reconTable').DataTable({pageLength:10,order:[[1,'desc']],responsive:true,dom:'Bfrtip',buttons:['copy','csv','excel','pdf','print']});
	});

	function applyFilters(){
		const branch = $('#branchFilter').val();
		const status = $('#statusFilter').val();
		const from = $('#fromDate').val();
		const to = $('#toDate').val();
		const search = $('#searchFilter').val().toLowerCase();
		$('#reconTable tbody tr').each(function(){
			let show = true; const row = $(this);
			if (status && !row.find('td:eq(8)').text().includes(status)) show = false;
			if (branch){
				const fromText = row.find('td:eq(2)').text();
				const toText = row.find('td:eq(3)').text();
				const branchId = parseInt(branch,10);
				// Simple contains check by branch name text since we show names
				const name = <?php echo json_encode(array_column($branches,'name','id')); ?>[branchId];
				if (!fromText.includes(name) && !toText.includes(name)) show = false;
			}
			if (from || to){
				const d = new Date(row.find('td:eq(1)').text());
				if (from && d < new Date(from)) show = false;
				if (to && d > new Date(to)) show = false;
			}
			if (search && !row.text().toLowerCase().includes(search)) show = false;
			row.toggle(show);
		});
	}
	$('#applyFilters').on('click', applyFilters);
	$('#statusFilter,#branchFilter,#fromDate,#toDate').on('change', applyFilters);
	$('#searchFilter').on('keyup', applyFilters);

	// Export
	$('#exportBtn').on('click', function(){ $('#reconTable').DataTable().button('.buttons-excel').trigger(); });

	// Actions
	window.viewRecon = function(ref){
		const r = <?php echo json_encode($reconRows); ?>.find(x=>x.ref===ref);
		if (!r) return;
		const content = `
			<div class="row">
				<div class="col-md-6">
					<h6 class="text-primary">Reconciliation Summary</h6>
					<table class="table table-borderless">
						<tr><td><strong>Reference:</strong></td><td>${r.ref}</td></tr>
						<tr><td><strong>Date:</strong></td><td>${new Date(r.date).toLocaleString()}</td></tr>
						<tr><td><strong>From:</strong></td><td>${r.branch_from}</td></tr>
						<tr><td><strong>To:</strong></td><td>${r.branch_to}</td></tr>
						<tr><td><strong>Items:</strong></td><td>${r.items_count}</td></tr>
					</table>
				</div>
				<div class="col-md-6">
					<h6 class="text-primary">Totals</h6>
					<table class="table table-borderless">
						<tr><td><strong>System Total:</strong></td><td>${r.system_total} RWF</td></tr>
						<tr><td><strong>Reported Total:</strong></td><td>${r.reported_total} RWF</td></tr>
						<tr><td><strong>Variance:</strong></td><td><strong class="${r.diff==0?'text-success':(r.diff>0?'text-danger':'text-warning')}">${r.diff} RWF</strong></td></tr>
						<tr><td><strong>Status:</strong></td><td>${r.status}</td></tr>
					</table>
				</div>
			</div>
			<div class="row mt-3"><div class="col-md-12"><h6 class="text-primary">Notes</h6><p>${r.notes||'No notes'}</p></div></div>`;
		$('#reconDetailsContent').html(content);
		$('#reconDetailsModal').modal('show');
	}
	window.markMatched = function(ref){
		alert('Marking '+ref+' as Matched (simulate API).');
	}
	window.openVariance = function(ref){
		$('#varianceRef').val(ref);
		$('#varianceModal').modal('show');
	}
	$('#btnOpenVariance').on('click', function(){
		if (!$('#varianceForm')[0].checkValidity()){ $('#varianceForm')[0].reportValidity(); return; }
		$('#varianceMsg').html('<div class="alert alert-success"><i class="fa fa-check-circle"></i> Variance case submitted successfully!</div>');
		setTimeout(()=>{$('#varianceModal').modal('hide');},1200);
	});
	$('#btnCreateRecon').on('click', function(){
		if (!$('#newReconForm')[0].checkValidity()){ $('#newReconForm')[0].reportValidity(); return; }
		$('#reconMsg').html('<div class="alert alert-success"><i class="fa fa-check-circle"></i> Reconciliation created successfully!</div>');
		setTimeout(()=>{$('#newReconModal').modal('hide'); location.reload();},1200);
	});
})();
</script>
