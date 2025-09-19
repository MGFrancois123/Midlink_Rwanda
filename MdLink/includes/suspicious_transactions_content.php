<?php
require_once __DIR__ . '/../constant/connect.php';
if (session_status() === PHP_SESSION_NONE) { if (session_status() === PHP_SESSION_NONE) {
    session_start();
} }

if (!isset($_SESSION['adminId'])) {
	echo '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Access Denied. Please log in.</div>';
	return;
}

// Sample suspicious transactions
$suspicious = [
	['id'=>'SUS-001','date'=>'2024-01-15 08:40:00','pattern'=>'High-Value Cash','risk_score'=>92,'amount'=>550.00,'payment_method'=>'Cash','status'=>'Open','reference'=>'TXN-1201','reason'=>'Cash sale above threshold','reported_by'=>'pharmacy_admin'],
	['id'=>'SUS-002','date'=>'2024-01-14 19:05:00','pattern'=>'After-hours Sale','risk_score'=>78,'amount'=>85.00,'payment_method'=>'Mobile Money','status'=>'Investigating','reference'=>'TXN-1188','reason'=>'Transaction outside business hours','reported_by'=>'finance_admin'],
	['id'=>'SUS-003','date'=>'2024-01-13 10:25:00','pattern'=>'Rapid Refund','risk_score'=>83,'amount'=>45.00,'payment_method'=>'Cash','status'=>'Resolved','reference'=>'REF-004','reason'=>'Refund issued within 5 minutes of sale','reported_by'=>'pharmacy_admin'],
	['id'=>'SUS-004','date'=>'2024-01-12 12:11:00','pattern'=>'Split Payments','risk_score'=>69,'amount'=>120.00,'payment_method'=>'Mobile Money','status'=>'Open','reference'=>'TXN-1107','reason'=>'Same patient split into multiple payments','reported_by'=>'pharmacy_admin'],
	['id'=>'SUS-005','date'=>'2024-01-12 16:59:00','pattern'=>'Unusual Quantity','risk_score'=>74,'amount'=>32.50,'payment_method'=>'Bank Transfer','status'=>'Investigating','reference'=>'TXN-1099','reason'=>'High quantity of controlled medicine','reported_by'=>'finance_admin'],
];

$total = count($suspicious);
$open = count(array_filter($suspicious, fn($s)=>$s['status']==='Open'));
$investigating = count(array_filter($suspicious, fn($s)=>$s['status']==='Investigating'));
$resolved = count(array_filter($suspicious, fn($s)=>$s['status']==='Resolved'));
$avgRisk = round(array_sum(array_column($suspicious,'risk_score'))/max(1,$total),1);
$exposure = array_sum(array_column($suspicious,'amount'));
?>

<!-- Hero -->
<div class="row mb-4">
	<div class="col-12">
		<div class="card bg-gradient-danger text-white">
			<div class="card-body">
				<div class="row align-items-center">
					<div class="col-md-8">
						<h2 class="mb-2"><i class="fa fa-shield"></i> Suspicious Transactions</h2>
						<p class="mb-0">Detect and investigate potentially fraudulent or risky transactions.</p>
					</div>
					<div class="col-md-4 text-right">
						<button class="btn btn-light btn-lg" data-toggle="modal" data-target="#newSusModal"><i class="fa fa-flag"></i> Flag Transaction</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- KPIs -->
<div class="row mb-4">
	<div class="col-md-2"><div class="card bg-danger text-white"><div class="card-body d-flex justify-content-between"><div><h4 class="mb-0"><?php echo $total; ?></h4><p class="mb-0">Total Flags</p></div><i class="fa fa-list fa-2x"></i></div></div></div>
	<div class="col-md-2"><div class="card bg-warning text-white"><div class="card-body d-flex justify-content-between"><div><h4 class="mb-0"><?php echo $open; ?></h4><p class="mb-0">Open</p></div><i class="fa fa-exclamation-triangle fa-2x"></i></div></div></div>
	<div class="col-md-2"><div class="card bg-info text-white"><div class="card-body d-flex justify-content-between"><div><h4 class="mb-0"><?php echo $investigating; ?></h4><p class="mb-0">Investigating</p></div><i class="fa fa-search fa-2x"></i></div></div></div>
	<div class="col-md-2"><div class="card bg-success text-white"><div class="card-body d-flex justify-content-between"><div><h4 class="mb-0"><?php echo $resolved; ?></h4><p class="mb-0">Resolved</p></div><i class="fa fa-check fa-2x"></i></div></div></div>
	<div class="col-md-2"><div class="card bg-dark text-white"><div class="card-body d-flex justify-content-between"><div><h4 class="mb-0"><?php echo number_format($exposure,2); ?> RWF</h4><p class="mb-0">Exposure</p></div><i class="fa fa-money fa-2x"></i></div></div></div>
	<div class="col-md-2"><div class="card bg-primary text-white"><div class="card-body d-flex justify-content-between"><div><h4 class="mb-0"><?php echo $avgRisk; ?>%</h4><p class="mb-0">Avg. Risk</p></div><i class="fa fa-tachometer fa-2x"></i></div></div></div>
</div>

<!-- Filters -->
<div class="row mb-4">
	<div class="col-12">
		<div class="card"><div class="card-body">
			<div class="row">
				<div class="col-md-3"><div class="form-group"><label>Pattern</label><select class="form-control" id="patternFilter"><option value="">All</option><option>High-Value Cash</option><option>After-hours Sale</option><option>Rapid Refund</option><option>Split Payments</option><option>Unusual Quantity</option></select></div></div>
				<div class="col-md-3"><div class="form-group"><label>Status</label><select class="form-control" id="statusFilter"><option value="">All</option><option>Open</option><option>Investigating</option><option>Resolved</option></select></div></div>
				<div class="col-md-3"><div class="form-group"><label>Min Risk</label><input type="number" class="form-control" id="minRisk" min="0" max="100" placeholder="e.g., 70"></div></div>
				<div class="col-md-3"><div class="form-group"><label>Search</label><input type="text" class="form-control" id="searchFilter" placeholder="Search ID, ref, reason..."></div></div>
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
			<div class="card-header"><h4 class="mb-0"><i class="fa fa-table"></i> Suspicious Transactions</h4></div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-striped table-hover" id="susTable">
						<thead class="thead-dark">
							<tr>
								<th>ID</th>
								<th>Date</th>
								<th>Pattern</th>
								<th>Risk</th>
								<th>Amount</th>
								<th>Method</th>
								<th>Status</th>
								<th>Reference</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($suspicious as $s) { ?>
							<tr>
								<td><span class="badge badge-secondary"><?php echo htmlspecialchars($s['id']); ?></span></td>
								<td><div><?php echo date('M d, Y', strtotime($s['date'])); ?></div><small class="text-muted"><?php echo date('H:i', strtotime($s['date'])); ?></small></td>
								<td><span class="badge badge-info"><?php echo htmlspecialchars($s['pattern']); ?></span></td>
								<td><span class="badge <?php echo $s['risk_score']>=85?'badge-danger':($s['risk_score']>=70?'badge-warning':'badge-success'); ?>"><?php echo $s['risk_score']; ?>%</span></td>
								<td><strong class="<?php echo $s['amount']>0?'text-danger':'text-muted'; ?>"><?php echo number_format($s['amount'],2); ?> RWF</strong></td>
								<td><?php echo htmlspecialchars($s['payment_method']); ?></td>
								<td><?php if($s['status']==='Resolved'){ ?><span class="badge badge-success"><i class="fa fa-check"></i> Resolved</span><?php } elseif($s['status']==='Open'){ ?><span class="badge badge-danger"><i class="fa fa-exclamation"></i> Open</span><?php } else { ?><span class="badge badge-warning"><i class="fa fa-search"></i> Investigating</span><?php } ?></td>
								<td><small class="text-muted"><?php echo htmlspecialchars($s['reference']); ?></small></td>
								<td>
									<button class="btn btn-sm btn-outline-primary" onclick="viewSus('<?php echo $s['id']; ?>')"><i class="fa fa-eye"></i></button>
									<?php if($s['status']!=='Resolved'){ ?>
									<button class="btn btn-sm btn-outline-success" onclick="resolveSus('<?php echo $s['id']; ?>')"><i class="fa fa-check"></i></button>
									<button class="btn btn-sm btn-outline-danger" onclick="escalateSus('<?php echo $s['id']; ?>')"><i class="fa fa-level-up"></i></button>
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
<div class="modal fade" id="susDetailsModal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header bg-info text-white"><h5 class="modal-title"><i class="fa fa-eye"></i> Suspicious Transaction Details</h5><button type="button" class="close text-white" data-dismiss="modal">&times;</button></div>
			<div class="modal-body" id="susDetailsContent"></div>
			<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button></div>
		</div>
	</div>
</div>

<!-- Flag Modal -->
<div class="modal fade" id="newSusModal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header bg-danger text-white"><h5 class="modal-title"><i class="fa fa-flag"></i> Flag Suspicious Transaction</h5><button type="button" class="close text-white" data-dismiss="modal">&times;</button></div>
			<div class="modal-body">
				<div id="flagMsg"></div>
				<form id="flagForm">
					<div class="form-group"><label>Pattern *</label><select class="form-control" name="pattern" required><option value="">Select</option><option>High-Value Cash</option><option>After-hours Sale</option><option>Rapid Refund</option><option>Split Payments</option><option>Unusual Quantity</option></select></div>
					<div class="form-group"><label>Risk Score *</label><input type="number" class="form-control" name="risk" min="0" max="100" required></div>
					<div class="form-group"><label>Amount (RWF)</label><input type="number" class="form-control" name="amount" step="0.01" min="0"></div>
					<div class="form-group"><label>Reference *</label><input type="text" class="form-control" name="reference" required placeholder="e.g., TXN-1201"></div>
					<div class="form-group"><label>Reason</label><textarea class="form-control" name="reason" rows="3" placeholder="Why is this suspicious?"></textarea></div>
				</form>
			</div>
			<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button><button type="button" class="btn btn-danger" id="btnFlag"><i class="fa fa-flag"></i> Flag</button></div>
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
		$('#susTable').DataTable({pageLength:10,order:[[1,'desc']],responsive:true,dom:'Bfrtip',buttons:['copy','csv','excel','pdf','print']});
	});

	function applyFilters(){
		const p=$('#patternFilter').val(), s=$('#statusFilter').val(), r=parseInt($('#minRisk').val()||'0',10), q=$('#searchFilter').val().toLowerCase();
		$('#susTable tbody tr').each(function(){
			let show=true; const row=$(this);
			if(p && !row.find('td:eq(2)').text().includes(p)) show=false;
			if(s && !row.find('td:eq(6)').text().includes(s)) show=false;
			const riskText=row.find('td:eq(3)').text().replace(/[^0-9]/g,'');
			const risk=parseInt(riskText||'0',10);
			if(r && risk<r) show=false;
			if(q && !row.text().toLowerCase().includes(q)) show=false;
			row.toggle(show);
		});
	}
	$('#applyFilters').on('click', applyFilters);
	$('#patternFilter,#statusFilter,#minRisk').on('change', applyFilters);
	$('#searchFilter').on('keyup', applyFilters);

	$('#exportBtn').on('click', function(){ $('#susTable').DataTable().button('.buttons-excel').trigger(); });

	window.viewSus = function(id){
		const s = <?php echo json_encode($suspicious); ?>.find(x=>x.id===id);
		if(!s) return;
		const content = `
			<table class="table table-borderless">
				<tr><td><strong>ID:</strong></td><td>${s.id}</td></tr>
				<tr><td><strong>Date:</strong></td><td>${new Date(s.date).toLocaleString()}</td></tr>
				<tr><td><strong>Pattern:</strong></td><td>${s.pattern}</td></tr>
				<tr><td><strong>Risk:</strong></td><td>${s.risk_score}%</td></tr>
				<tr><td><strong>Amount:</strong></td><td>${s.amount} RWF</td></tr>
				<tr><td><strong>Payment Method:</strong></td><td>${s.payment_method}</td></tr>
				<tr><td><strong>Status:</strong></td><td>${s.status}</td></tr>
				<tr><td><strong>Reference:</strong></td><td>${s.reference}</td></tr>
				<tr><td><strong>Reason:</strong></td><td>${s.reason||'N/A'}</td></tr>
				<tr><td><strong>Reported By:</strong></td><td>${s.reported_by}</td></tr>
			</table>`;
		$('#susDetailsContent').html(content);
		$('#susDetailsModal').modal('show');
	}
	window.resolveSus = function(id){ alert('Resolve '+id+' (simulate API)'); };
	window.escalateSus = function(id){ alert('Escalate '+id+' (simulate API)'); };

	// Guarded flag button click handler
	$(document).on('click', '#btnFlag', function(e){
		e.preventDefault(); // Prevent default form submission
		if (!$('#flagForm')[0].checkValidity()){
			$('#flagForm')[0].reportValidity();
			return;
		}

		const formData = new FormData($('#flagForm')[0]);
		const url = 'includes/flag_transaction.php'; // Assuming this is the correct path

		$.ajax({
			url: url,
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			success: function(response) {
				$('#flagMsg').html('<div class="alert alert-success"><i class="fa fa-check-circle"></i> Suspicious transaction flagged successfully!</div>');
				setTimeout(()=>{$('#newSusModal').modal('hide'); location.reload();},1200);
			},
			error: function(xhr, status, error) {
				let errorMsg = 'Error flagging suspicious transaction.';
				if (xhr.responseJSON && xhr.responseJSON.message) {
					errorMsg = xhr.responseJSON.message;
				} else if (xhr.responseText) {
					errorMsg = xhr.responseText;
				}
				$('#flagMsg').html('<div class="alert alert-danger"><i class="fa fa-times-circle"></i> ' + errorMsg + '</div>');
			}
		});
	});
})();
</script>
