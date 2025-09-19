<?php
require_once __DIR__ . '/../constant/connect.php';
if (session_status() === PHP_SESSION_NONE) { if (session_status() === PHP_SESSION_NONE) {
    session_start();
} }

if (!isset($_SESSION['adminId'])) {
	echo '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Access Denied. Please log in.</div>';
	return;
}

$exceptions = [
	['id'=>'EXC-001','date'=>'2024-01-15 09:40:00','type'=>'Duplicate Sale','severity'=>'High','amount'=>120.50,'payment_method'=>'Mobile Money','status'=>'Open','reference'=>'TXN-1001','notes'=>'Same prescription billed twice','reported_by'=>'pharmacy_admin'],
	['id'=>'EXC-002','date'=>'2024-01-14 13:22:00','type'=>'Negative Stock','severity'=>'Medium','amount'=>0.00,'payment_method'=>'N/A','status'=>'Investigating','reference'=>'STK-208','notes'=>'Stock went below zero after adjustment','reported_by'=>'finance_admin'],
	['id'=>'EXC-003','date'=>'2024-01-13 18:05:00','type'=>'Refund Mismatch','severity'=>'High','amount'=>45.00,'payment_method'=>'Cash','status'=>'Resolved','reference'=>'REF-004','notes'=>'Refund not matching original sale','reported_by'=>'pharmacy_admin'],
	['id'=>'EXC-004','date'=>'2024-01-12 11:10:00','type'=>'Unposted Payment','severity'=>'Low','amount'=>15.00,'payment_method'=>'Bank Transfer','status'=>'Open','reference'=>'PAY-221','notes'=>'Payment received but not posted','reported_by'=>'finance_admin'],
	['id'=>'EXC-005','date'=>'2024-01-12 16:31:00','type'=>'Pricing Error','severity'=>'Medium','amount'=>8.75,'payment_method'=>'Cash','status'=>'Resolved','reference'=>'TXN-0977','notes'=>'Wrong unit price applied','reported_by'=>'pharmacy_admin'],
];

$totalExceptions = count($exceptions);
$openCount = count(array_filter($exceptions, fn($e)=>$e['status']==='Open'));
$investigatingCount = count(array_filter($exceptions, fn($e)=>$e['status']==='Investigating'));
$resolvedCount = count(array_filter($exceptions, fn($e)=>$e['status']==='Resolved'));
$totalExposure = array_sum(array_map(fn($e)=>$e['amount'], $exceptions));
?>

<!-- Hero -->
<div class="row mb-4">
	<div class="col-12">
		<div class="card bg-gradient-danger text-white">
			<div class="card-body">
				<div class="row align-items-center">
					<div class="col-md-8">
						<h2 class="mb-2"><i class="fa fa-exclamation-circle"></i> Transaction Exceptions</h2>
						<p class="mb-0">Monitor and resolve anomalous transactions: duplicates, mismatches, negative stock, and unposted payments.</p>
					</div>
					<div class="col-md-4 text-right">
						<button class="btn btn-light btn-lg" data-toggle="modal" data-target="#newExceptionModal"><i class="fa fa-plus"></i> Log Exception</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- KPIs -->
<div class="row mb-4">
	<div class="col-md-2"><div class="card bg-danger text-white"><div class="card-body d-flex justify-content-between"><div><h4 class="mb-0"><?php echo $totalExceptions; ?></h4><p class="mb-0">Total</p></div><i class="fa fa-list fa-2x"></i></div></div></div>
	<div class="col-md-2"><div class="card bg-warning text-white"><div class="card-body d-flex justify-content-between"><div><h4 class="mb-0"><?php echo $openCount; ?></h4><p class="mb-0">Open</p></div><i class="fa fa-exclamation-triangle fa-2x"></i></div></div></div>
	<div class="col-md-3"><div class="card bg-info text-white"><div class="card-body d-flex justify-content-between"><div><h4 class="mb-0"><?php echo $investigatingCount; ?></h4><p class="mb-0">Investigating</p></div><i class="fa fa-search fa-2x"></i></div></div></div>
	<div class="col-md-2"><div class="card bg-success text-white"><div class="card-body d-flex justify-content-between"><div><h4 class="mb-0"><?php echo $resolvedCount; ?></h4><p class="mb-0">Resolved</p></div><i class="fa fa-check fa-2x"></i></div></div></div>
	<div class="col-md-3"><div class="card bg-dark text-white"><div class="card-body d-flex justify-content-between"><div><h4 class="mb-0"><?php echo number_format($totalExposure,2); ?> RWF</h4><p class="mb-0">Total Exposure</p></div><i class="fa fa-money fa-2x"></i></div></div></div>
</div>

<!-- Filters -->
<div class="row mb-4">
	<div class="col-12">
		<div class="card"><div class="card-body">
			<div class="row">
				<div class="col-md-3">
					<div class="form-group"><label>Type</label>
						<select class="form-control" id="typeFilter">
							<option value="">All</option>
							<option>Duplicate Sale</option>
							<option>Refund Mismatch</option>
							<option>Negative Stock</option>
							<option>Unposted Payment</option>
							<option>Pricing Error</option>
						</select>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group"><label>Status</label>
						<select class="form-control" id="statusFilter">
							<option value="">All</option>
							<option>Open</option>
							<option>Investigating</option>
							<option>Resolved</option>
						</select>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group"><label>Severity</label>
						<select class="form-control" id="sevFilter">
							<option value="">All</option>
							<option>High</option>
							<option>Medium</option>
							<option>Low</option>
						</select>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group"><label>Search</label>
						<input type="text" class="form-control" id="searchFilter" placeholder="Search ID, ref, notes...">
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-3"><button class="btn btn-primary btn-block" id="applyFilters"><i class="fa fa-filter"></i> Apply Filters</button></div>
				<div class="col-md-3"><button class="btn btn-secondary btn-block" id="exportBtn"><i class="fa fa-download"></i> Export</button></div>
			</div>
		</div></div>
	</div>
</div>

<!-- Table -->
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header"><h4 class="mb-0"><i class="fa fa-table"></i> Exceptions</h4></div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-striped table-hover" id="excTable">
						<thead class="thead-dark">
							<tr>
								<th>ID</th>
								<th>Date</th>
								<th>Type</th>
								<th>Severity</th>
								<th>Amount</th>
								<th>Payment Method</th>
								<th>Status</th>
								<th>Reference</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($exceptions as $e) { ?>
							<tr>
								<td><span class="badge badge-secondary"><?php echo htmlspecialchars($e['id']); ?></span></td>
								<td><div><?php echo date('M d, Y', strtotime($e['date'])); ?></div><small class="text-muted"><?php echo date('H:i', strtotime($e['date'])); ?></small></td>
								<td><span class="badge badge-info"><?php echo htmlspecialchars($e['type']); ?></span></td>
								<td><span class="badge <?php echo $e['severity']==='High'?'badge-danger':($e['severity']==='Medium'?'badge-warning':'badge-success'); ?>"><?php echo $e['severity']; ?></span></td>
								<td><strong class="<?php echo $e['amount']>0?'text-danger':'text-muted'; ?>"><?php echo number_format($e['amount'],2); ?> RWF</strong></td>
								<td><?php echo htmlspecialchars($e['payment_method']); ?></td>
								<td>
									<?php if ($e['status']==='Resolved') { ?><span class="badge badge-success"><i class="fa fa-check"></i> Resolved</span><?php } elseif ($e['status']==='Open') { ?><span class="badge badge-danger"><i class="fa fa-exclamation"></i> Open</span><?php } else { ?><span class="badge badge-warning"><i class="fa fa-search"></i> Investigating</span><?php } ?>
								</td>
								<td><small class="text-muted"><?php echo htmlspecialchars($e['reference']); ?></small></td>
								<td>
									<button class="btn btn-sm btn-outline-primary" onclick="viewExc('<?php echo $e['id']; ?>')"><i class="fa fa-eye"></i></button>
									<?php if ($e['status']!=='Resolved') { ?>
									<button class="btn btn-sm btn-outline-success" onclick="resolveExc('<?php echo $e['id']; ?>')"><i class="fa fa-check"></i></button>
									<button class="btn btn-sm btn-outline-danger" onclick="escalateExc('<?php echo $e['id']; ?>')"><i class="fa fa-level-up"></i></button>
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

<!-- Details Modal -->
<div class="modal fade" id="excDetailsModal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header bg-info text-white"><h5 class="modal-title"><i class="fa fa-eye"></i> Exception Details</h5><button type="button" class="close text-white" data-dismiss="modal">&times;</button></div>
			<div class="modal-body" id="excDetailsContent"></div>
			<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button></div>
		</div>
	</div>
</div>

<!-- Log Exception Modal -->
<div class="modal fade" id="newExceptionModal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header bg-danger text-white"><h5 class="modal-title"><i class="fa fa-plus"></i> Log Exception</h5><button type="button" class="close text-white" data-dismiss="modal">&times;</button></div>
			<div class="modal-body">
				<div id="logMsg"></div>
				<form id="logForm">
					<div class="form-group"><label>Type *</label><select class="form-control" name="type" required><option value="">Select</option><option>Duplicate Sale</option><option>Refund Mismatch</option><option>Negative Stock</option><option>Unposted Payment</option><option>Pricing Error</option></select></div>
					<div class="form-group"><label>Severity *</label><select class="form-control" name="severity" required><option value="">Select</option><option>High</option><option>Medium</option><option>Low</option></select></div>
					<div class="form-group"><label>Amount (RWF)</label><input type="number" class="form-control" name="amount" step="0.01" min="0"></div>
					<div class="form-group"><label>Reference *</label><input type="text" class="form-control" name="reference" required placeholder="e.g., TXN-1234"></div>
					<div class="form-group"><label>Notes</label><textarea class="form-control" name="notes" rows="3" placeholder="Describe the exception..."></textarea></div>
				</form>
			</div>
			<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button><button type="button" class="btn btn-danger" id="btnLog"><i class="fa fa-plus"></i> Log</button></div>
		</div>
	</div>
</div>

<style>
.bg-gradient-danger{background:linear-gradient(135deg,#dc3545 0%,#c82333 100%)}
.card{border:none;box-shadow:0 0.125rem 0.25rem rgba(0,0,0,.075);transition:all .3s ease}
.card:hover{box-shadow:0 .5rem 1rem rgba(0,0,0,.15);transform:translateY(-2px)}
.table th{border-top:none;font-weight:600}
.badge{font-size:.75rem;padding:.375rem .75rem}
.table-hover tbody tr:hover{background-color:rgba(220,53,69,.08);cursor:pointer}
.modal-header.bg-info{background:linear-gradient(135deg,#17a2b8 0%,#138496 100%)!important}
.modal-header.bg-danger{background:linear-gradient(135deg,#dc3545 0%,#c82333 100%)!important}
.form-control:focus{border-color:#dc3545;box-shadow:0 0 0 .2rem rgba(220,53,69,.25)}
</style>

<script>
(function(){
	$(document).ready(function(){
		$('#excTable').DataTable({pageLength:10,order:[[1,'desc']],responsive:true,dom:'Bfrtip',buttons:['copy','csv','excel','pdf','print']});
	});

	function applyFilters(){
		const t=$('#typeFilter').val(), s=$('#statusFilter').val(), v=$('#sevFilter').val(), q=$('#searchFilter').val().toLowerCase();
		$('#excTable tbody tr').each(function(){
			let show=true; const row=$(this);
			if(t && !row.find('td:eq(2)').text().includes(t)) show=false;
			if(s && !row.find('td:eq(6)').text().includes(s)) show=false;
			if(v && !row.find('td:eq(3)').text().includes(v)) show=false;
			if(q && !row.text().toLowerCase().includes(q)) show=false;
			row.toggle(show);
		});
	}
	$('#applyFilters').on('click', applyFilters);
	$('#typeFilter,#statusFilter,#sevFilter').on('change', applyFilters);
	$('#searchFilter').on('keyup', applyFilters);

	$('#exportBtn').on('click', function(){ $('#excTable').DataTable().button('.buttons-excel').trigger(); });

	window.viewExc = function(id){
		const e = <?php echo json_encode($exceptions); ?>.find(x=>x.id===id);
		if(!e) return;
		const content = `
			<table class="table table-borderless">
				<tr><td><strong>ID:</strong></td><td>${e.id}</td></tr>
				<tr><td><strong>Date:</strong></td><td>${new Date(e.date).toLocaleString()}</td></tr>
				<tr><td><strong>Type:</strong></td><td>${e.type}</td></tr>
				<tr><td><strong>Severity:</strong></td><td>${e.severity}</td></tr>
				<tr><td><strong>Amount:</strong></td><td>${e.amount} RWF</td></tr>
				<tr><td><strong>Payment Method:</strong></td><td>${e.payment_method}</td></tr>
				<tr><td><strong>Status:</strong></td><td>${e.status}</td></tr>
				<tr><td><strong>Reference:</strong></td><td>${e.reference}</td></tr>
				<tr><td><strong>Notes:</strong></td><td>${e.notes||'N/A'}</td></tr>
				<tr><td><strong>Reported By:</strong></td><td>${e.reported_by}</td></tr>
			</table>`;
		$('#excDetailsContent').html(content);
		$('#excDetailsModal').modal('show');
	}
	window.resolveExc = function(id){ alert('Resolve '+id+' (simulate API)'); };
	window.escalateExc = function(id){ alert('Escalate '+id+' (simulate API)'); };
	$('#btnLog').on('click', function(){
		if (!$('#logForm')[0].checkValidity()){ $('#logForm')[0].reportValidity(); return; }
		$('#logMsg').html('<div class="alert alert-success"><i class="fa fa-check-circle"></i> Exception logged successfully!</div>');
		setTimeout(()=>{$('#newExceptionModal').modal('hide'); location.reload();},1200);
	});
})();
</script>
