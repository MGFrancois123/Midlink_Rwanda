<?php
require_once __DIR__ . '/../constant/connect.php';
if (session_status() === PHP_SESSION_NONE) { if (session_status() === PHP_SESSION_NONE) {
    session_start();
} }

if (!isset($_SESSION['adminId'])) {
	echo '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Access Denied. Please log in.</div>';
	return;
}

// Sample audit logs
$auditLogs = [
	['id'=>'AL-0001','timestamp'=>'2024-01-15 09:12:33','actor'=>'super_admin','ip'=>'127.0.0.1','action'=>'LOGIN','object_type'=>'admin_users','object_id'=>1,'status'=>'SUCCESS','message'=>'User logged in'],
	['id'=>'AL-0002','timestamp'=>'2024-01-15 09:35:10','actor'=>'pharmacy_admin','ip'=>'127.0.0.1','action'=>'CREATE','object_type'=>'medicines','object_id'=>102,'status'=>'SUCCESS','message'=>'Added new medicine Paracetamol 500mg'],
	['id'=>'AL-0003','timestamp'=>'2024-01-15 10:01:05','actor'=>'finance_admin','ip'=>'127.0.0.1','action'=>'UPDATE','object_type'=>'stock_movements','object_id'=>554,'status'=>'SUCCESS','message'=>'Adjusted stock count'],
	['id'=>'AL-0004','timestamp'=>'2024-01-15 10:22:41','actor'=>'pharmacy_admin','ip'=>'127.0.0.1','action'=>'DELETE','object_type'=>'categories','object_id'=>14,'status'=>'FAILED','message'=>'Delete category failed - constraint'],
	['id'=>'AL-0005','timestamp'=>'2024-01-15 10:55:02','actor'=>'super_admin','ip'=>'127.0.0.1','action'=>'ASSIGN_ROLE','object_type'=>'admin_users','object_id'=>7,'status'=>'SUCCESS','message'=>'Assigned finance_admin role'],
	['id'=>'AL-0006','timestamp'=>'2024-01-15 11:10:22','actor'=>'finance_admin','ip'=>'127.0.0.1','action'=>'EXPORT','object_type'=>'reports','object_id'=>null,'status'=>'SUCCESS','message'=>'Exported daily revenue'],
];

$totalLogs = count($auditLogs);
$successCount = count(array_filter($auditLogs, fn($l)=>$l['status']==='SUCCESS'));
$failedCount = count(array_filter($auditLogs, fn($l)=>$l['status']==='FAILED'));
$uniqueActors = count(array_unique(array_column($auditLogs,'actor')));
?>

<!-- Hero -->
<div class="row mb-4">
	<div class="col-12">
		<div class="card bg-gradient-primary text-white">
			<div class="card-body">
				<div class="row align-items-center">
					<div class="col-md-8">
						<h2 class="mb-2"><i class="fa fa-clipboard"></i> Audit Logs</h2>
						<p class="mb-0">Track system activities, changes, and access events for security and compliance.</p>
					</div>
					<div class="col-md-4 text-right">
						<button class="btn btn-light btn-lg" id="exportAudit"><i class="fa fa-download"></i> Export</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- KPIs -->
<div class="row mb-4">
	<div class="col-md-3"><div class="card bg-primary text-white"><div class="card-body d-flex justify-content-between"><div><h4 class="mb-0"><?php echo $totalLogs; ?></h4><p class="mb-0">Total Logs</p></div><i class="fa fa-list fa-2x"></i></div></div></div>
	<div class="col-md-3"><div class="card bg-success text-white"><div class="card-body d-flex justify-content-between"><div><h4 class="mb-0"><?php echo $successCount; ?></h4><p class="mb-0">Success</p></div><i class="fa fa-check fa-2x"></i></div></div></div>
	<div class="col-md-3"><div class="card bg-danger text-white"><div class="card-body d-flex justify-content-between"><div><h4 class="mb-0"><?php echo $failedCount; ?></h4><p class="mb-0">Failed</p></div><i class="fa fa-times fa-2x"></i></div></div></div>
	<div class="col-md-3"><div class="card bg-info text-white"><div class="card-body d-flex justify-content-between"><div><h4 class="mb-0"><?php echo $uniqueActors; ?></h4><p class="mb-0">Unique Actors</p></div><i class="fa fa-users fa-2x"></i></div></div></div>
</div>

<!-- Filters -->
<div class="row mb-4">
	<div class="col-12">
		<div class="card"><div class="card-body">
			<div class="row">
				<div class="col-md-3"><div class="form-group"><label>Actor</label><input type="text" class="form-control" id="actorFilter" placeholder="e.g., super_admin"></div></div>
				<div class="col-md-3"><div class="form-group"><label>Action</label><select class="form-control" id="actionFilter"><option value="">All</option><option>LOGIN</option><option>CREATE</option><option>UPDATE</option><option>DELETE</option><option>ASSIGN_ROLE</option><option>EXPORT</option></select></div></div>
				<div class="col-md-3"><div class="form-group"><label>Status</label><select class="form-control" id="statusFilter"><option value="">All</option><option>SUCCESS</option><option>FAILED</option></select></div></div>
				<div class="col-md-3"><div class="form-group"><label>Search</label><input type="text" class="form-control" id="searchFilter" placeholder="Search message, object..."></div></div>
			</div>
			<div class="row">
				<div class="col-md-3"><button class="btn btn-primary btn-block" id="applyFilters"><i class="fa fa-filter"></i> Apply Filters</button></div>
				<div class="col-md-3"><button class="btn btn-secondary btn-block" id="clearFilters"><i class="fa fa-times"></i> Clear</button></div>
			</div>
		</div></div>
	</div>
</div>

<!-- Table -->
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header"><h4 class="mb-0"><i class="fa fa-table"></i> Audit Trail</h4></div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-striped table-hover" id="auditTable">
						<thead class="thead-dark">
							<tr>
								<th>ID</th>
								<th>Timestamp</th>
								<th>Actor</th>
								<th>IP</th>
								<th>Action</th>
								<th>Object</th>
								<th>Status</th>
								<th>Message</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($auditLogs as $l) { ?>
							<tr>
								<td><span class="badge badge-secondary"><?php echo htmlspecialchars($l['id']); ?></span></td>
								<td><div><?php echo date('M d, Y', strtotime($l['timestamp'])); ?></div><small class="text-muted"><?php echo date('H:i:s', strtotime($l['timestamp'])); ?></small></td>
								<td><span class="badge badge-info"><?php echo htmlspecialchars($l['actor']); ?></span></td>
								<td><small><?php echo htmlspecialchars($l['ip']); ?></small></td>
								<td><span class="badge badge-primary"><?php echo htmlspecialchars($l['action']); ?></span></td>
								<td><small><?php echo htmlspecialchars($l['object_type']); ?> <?php echo $l['object_id']!==null?('#'.$l['object_id']):''; ?></small></td>
								<td><?php if($l['status']==='SUCCESS'){ ?><span class="badge badge-success">SUCCESS</span><?php } else { ?><span class="badge badge-danger">FAILED</span><?php } ?></td>
								<td><?php echo htmlspecialchars($l['message']); ?></td>
								<td><button class="btn btn-sm btn-outline-primary" onclick="viewLog('<?php echo $l['id']; ?>')"><i class="fa fa-eye"></i></button></td>
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
<div class="modal fade" id="logDetailsModal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header bg-info text-white"><h5 class="modal-title"><i class="fa fa-eye"></i> Log Details</h5><button type="button" class="close text-white" data-dismiss="modal">&times;</button></div>
			<div class="modal-body" id="logDetailsContent"></div>
			<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button></div>
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
.modal-header.bg-info{background:linear-gradient(135deg,#17a2b8 0%,#138496 100%)!important}
.form-control:focus{border-color:#007bff;box-shadow:0 0 0 .2rem rgba(0,123,255,.25)}
</style>

<script>
(function(){
	$(document).ready(function(){
		$('#auditTable').DataTable({pageLength:10,order:[[1,'desc']],responsive:true,dom:'Bfrtip',buttons:['copy','csv','excel','pdf','print']});
	});

	function applyFilters(){
		const a=$('#actorFilter').val().toLowerCase(), act=$('#actionFilter').val(), st=$('#statusFilter').val(), q=$('#searchFilter').val().toLowerCase();
		$('#auditTable tbody tr').each(function(){
			let show=true; const row=$(this);
			if(a && !row.find('td:eq(2)').text().toLowerCase().includes(a)) show=false;
			if(act && !row.find('td:eq(4)').text().includes(act)) show=false;
			if(st && !row.find('td:eq(6)').text().includes(st)) show=false;
			if(q && !row.text().toLowerCase().includes(q)) show=false;
			row.toggle(show);
		});
	}
	$('#applyFilters').on('click', applyFilters);
	$('#actorFilter,#actionFilter,#statusFilter').on('change', applyFilters);
	$('#searchFilter').on('keyup', applyFilters);
	$('#clearFilters').on('click', function(){ $('#actorFilter').val(''); $('#actionFilter').val(''); $('#statusFilter').val(''); $('#searchFilter').val(''); applyFilters(); });

	window.viewLog = function(id){
		const l = <?php echo json_encode($auditLogs); ?>.find(x=>x.id===id);
		if(!l) return;
		const content = `
			<table class="table table-borderless">
				<tr><td><strong>ID:</strong></td><td>${l.id}</td></tr>
				<tr><td><strong>Timestamp:</strong></td><td>${new Date(l.timestamp).toLocaleString()}</td></tr>
				<tr><td><strong>Actor:</strong></td><td>${l.actor}</td></tr>
				<tr><td><strong>IP:</strong></td><td>${l.ip}</td></tr>
				<tr><td><strong>Action:</strong></td><td>${l.action}</td></tr>
				<tr><td><strong>Object:</strong></td><td>${l.object_type} ${l.object_id?('#'+l.object_id):''}</td></tr>
				<tr><td><strong>Status:</strong></td><td>${l.status}</td></tr>
				<tr><td><strong>Message:</strong></td><td>${l.message}</td></tr>
			</table>`;
		$('#logDetailsContent').html(content);
		$('#logDetailsModal').modal('show');
	}
})();
</script>
