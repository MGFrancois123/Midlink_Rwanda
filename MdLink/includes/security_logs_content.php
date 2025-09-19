<?php
require_once __DIR__ . '/../constant/connect.php';
if (session_status() === PHP_SESSION_NONE) { if (session_status() === PHP_SESSION_NONE) {
    session_start();
} }

if (!isset($_SESSION['adminId'])) {
	echo '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Access Denied. Please log in.</div>';
	return;
}

// Sample security logs
$securityLogs = [
	['id'=>'SEC-0001','timestamp'=>'2024-01-15 08:45:12','event_type'=>'LOGIN_SUCCESS','user'=>'pharmacy_admin','ip'=>'192.168.1.100','location'=>'Kigali, Rwanda','severity'=>'INFO','description'=>'Successful login from trusted IP'],
	['id'=>'SEC-0002','timestamp'=>'2024-01-15 09:15:33','event_type'=>'FAILED_LOGIN','user'=>'unknown_user','ip'=>'203.45.67.89','location'=>'Unknown','severity'=>'WARNING','description'=>'Multiple failed login attempts from suspicious IP'],
	['id'=>'SEC-0003','timestamp'=>'2024-01-15 10:22:45','event_type'=>'PASSWORD_CHANGE','user'=>'finance_admin','ip'=>'192.168.1.100','location'=>'Kigali, Rwanda','severity'=>'INFO','description'=>'Password changed successfully'],
	['id'=>'SEC-0004','timestamp'=>'2024-01-15 11:05:18','event_type'=>'SUSPICIOUS_ACTIVITY','user'=>'pharmacy_admin','ip'=>'45.78.123.45','location'=>'Unknown','severity'=>'HIGH','description'=>'Unusual data access pattern detected'],
	['id'=>'SEC-0005','timestamp'=>'2024-01-15 12:30:22','event_type'=>'ROLE_ASSIGNMENT','user'=>'super_admin','ip'=>'192.168.1.50','location'=>'Kigali, Rwanda','severity'=>'INFO','description'=>'Role assigned to new user'],
	['id'=>'SEC-0006','timestamp'=>'2024-01-15 14:15:07','event_type'=>'DATA_EXPORT','user'=>'finance_admin','ip'=>'192.168.1.100','location'=>'Kigali, Rwanda','severity'=>'MEDIUM','description'=>'Sensitive data exported to CSV'],
	['id'=>'SEC-0007','timestamp'=>'2024-01-15 15:45:33','event_type'=>'SESSION_TIMEOUT','user'=>'pharmacy_admin','ip'=>'192.168.1.100','location'=>'Kigali, Rwanda','severity'=>'INFO','description'=>'Session expired due to inactivity'],
	['id'=>'SEC-0008','timestamp'=>'2024-01-15 16:20:15','event_type'=>'UNAUTHORIZED_ACCESS','user'=>'hacker_attempt','ip'=>'185.220.101.42','location'=>'Unknown','severity'=>'CRITICAL','description'=>'Attempted access to restricted admin functions'],
];

$totalLogs = count($securityLogs);
$criticalCount = count(array_filter($securityLogs, fn($l)=>$l['severity']==='CRITICAL'));
$warningCount = count(array_filter($securityLogs, fn($l)=>$l['severity']==='WARNING' || $l['severity']==='HIGH'));
$uniqueIPs = count(array_unique(array_column($securityLogs,'ip')));
?>

<!-- Hero -->
<div class="row mb-4">
	<div class="col-12">
		<div class="card bg-gradient-danger text-white">
			<div class="card-body">
				<div class="row align-items-center">
					<div class="col-md-8">
						<h2 class="mb-2"><i class="fa fa-shield"></i> Security Logs</h2>
						<p class="mb-0">Monitor security events, failed logins, suspicious activities, and system access patterns.</p>
					</div>
					<div class="col-md-4 text-right">
						<button class="btn btn-light btn-lg" id="exportSecurity"><i class="fa fa-download"></i> Export</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- KPIs -->
<div class="row mb-4">
	<div class="col-md-3"><div class="card bg-primary text-white"><div class="card-body d-flex justify-content-between"><div><h4 class="mb-0"><?php echo $totalLogs; ?></h4><p class="mb-0">Total Events</p></div><i class="fa fa-shield fa-2x"></i></div></div></div>
	<div class="col-md-3"><div class="card bg-danger text-white"><div class="card-body d-flex justify-content-between"><div><h4 class="mb-0"><?php echo $criticalCount; ?></h4><p class="mb-0">Critical</p></div><i class="fa fa-exclamation-triangle fa-2x"></i></div></div></div>
	<div class="col-md-3"><div class="card bg-warning text-white"><div class="card-body d-flex justify-content-between"><div><h4 class="mb-0"><?php echo $warningCount; ?></h4><p class="mb-0">Warnings</p></div><i class="fa fa-warning fa-2x"></i></div></div></div>
	<div class="col-md-3"><div class="card bg-info text-white"><div class="card-body d-flex justify-content-between"><div><h4 class="mb-0"><?php echo $uniqueIPs; ?></h4><p class="mb-0">Unique IPs</p></div><i class="fa fa-globe fa-2x"></i></div></div></div>
</div>

<!-- Filters -->
<div class="row mb-4">
	<div class="col-12">
		<div class="card"><div class="card-body">
			<div class="row">
				<div class="col-md-3"><div class="form-group"><label>Event Type</label><select class="form-control" id="eventFilter"><option value="">All</option><option>LOGIN_SUCCESS</option><option>FAILED_LOGIN</option><option>PASSWORD_CHANGE</option><option>SUSPICIOUS_ACTIVITY</option><option>ROLE_ASSIGNMENT</option><option>DATA_EXPORT</option><option>SESSION_TIMEOUT</option><option>UNAUTHORIZED_ACCESS</option></select></div></div>
				<div class="col-md-3"><div class="form-group"><label>Severity</label><select class="form-control" id="severityFilter"><option value="">All</option><option>INFO</option><option>WARNING</option><option>MEDIUM</option><option>HIGH</option><option>CRITICAL</option></select></div></div>
				<div class="col-md-3"><div class="form-group"><label>User</label><input type="text" class="form-control" id="userFilter" placeholder="e.g., pharmacy_admin"></div></div>
				<div class="col-md-3"><div class="form-group"><label>Search</label><input type="text" class="form-control" id="searchFilter" placeholder="Search description, IP..."></div></div>
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
			<div class="card-header"><h4 class="mb-0"><i class="fa fa-table"></i> Security Events</h4></div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-striped table-hover" id="securityTable">
						<thead class="thead-dark">
							<tr>
								<th>ID</th>
								<th>Timestamp</th>
								<th>Event Type</th>
								<th>User</th>
								<th>IP Address</th>
								<th>Location</th>
								<th>Severity</th>
								<th>Description</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($securityLogs as $l) { ?>
							<tr>
								<td><span class="badge badge-secondary"><?php echo htmlspecialchars($l['id']); ?></span></td>
								<td><div><?php echo date('M d, Y', strtotime($l['timestamp'])); ?></div><small class="text-muted"><?php echo date('H:i:s', strtotime($l['timestamp'])); ?></small></td>
								<td><span class="badge badge-primary"><?php echo htmlspecialchars($l['event_type']); ?></span></td>
								<td><span class="badge badge-info"><?php echo htmlspecialchars($l['user']); ?></span></td>
								<td><code><?php echo htmlspecialchars($l['ip']); ?></code></td>
								<td><small><?php echo htmlspecialchars($l['location']); ?></small></td>
								<td><?php 
									$severityClass = '';
									switch($l['severity']) {
										case 'CRITICAL': $severityClass = 'badge-danger'; break;
										case 'HIGH': $severityClass = 'badge-warning'; break;
										case 'MEDIUM': $severityClass = 'badge-info'; break;
										case 'WARNING': $severityClass = 'badge-warning'; break;
										case 'INFO': $severityClass = 'badge-success'; break;
									}
								?><span class="badge <?php echo $severityClass; ?>"><?php echo htmlspecialchars($l['severity']); ?></span></td>
								<td><?php echo htmlspecialchars($l['description']); ?></td>
								<td><button class="btn btn-sm btn-outline-primary" onclick="viewSecurityLog('<?php echo $l['id']; ?>')"><i class="fa fa-eye"></i></button></td>
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
<div class="modal fade" id="securityDetailsModal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header bg-danger text-white"><h5 class="modal-title"><i class="fa fa-shield"></i> Security Event Details</h5><button type="button" class="close text-white" data-dismiss="modal">&times;</button></div>
			<div class="modal-body" id="securityDetailsContent"></div>
			<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button></div>
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
.modal-header.bg-danger{background:linear-gradient(135deg,#dc3545 0%,#c82333 100%)!important}
.form-control:focus{border-color:#dc3545;box-shadow:0 0 0 .2rem rgba(220,53,69,.25)}
code{background-color:#f8f9fa;padding:.2rem .4rem;border-radius:.25rem;font-size:.875rem}
.badge-danger{background-color:#dc3545}
.badge-warning{background-color:#ffc107;color:#212529}
.badge-info{background-color:#17a2b8}
.badge-success{background-color:#28a745}
.badge-primary{background-color:#007bff}
.badge-secondary{background-color:#6c757d}
</style>

<script>
(function(){
	$(document).ready(function(){
		$('#securityTable').DataTable({pageLength:10,order:[[1,'desc']],responsive:true,dom:'Bfrtip',buttons:['copy','csv','excel','pdf','print']});
	});

	function applyFilters(){
		const event=$('#eventFilter').val(), severity=$('#severityFilter').val(), user=$('#userFilter').val().toLowerCase(), q=$('#searchFilter').val().toLowerCase();
		$('#securityTable tbody tr').each(function(){
			let show=true; const row=$(this);
			if(event && !row.find('td:eq(2)').text().includes(event)) show=false;
			if(severity && !row.find('td:eq(6)').text().includes(severity)) show=false;
			if(user && !row.find('td:eq(3)').text().toLowerCase().includes(user)) show=false;
			if(q && !row.text().toLowerCase().includes(q)) show=false;
			row.toggle(show);
		});
	}
	$('#applyFilters').on('click', applyFilters);
	$('#eventFilter,#severityFilter').on('change', applyFilters);
	$('#userFilter,#searchFilter').on('keyup', applyFilters);
	$('#clearFilters').on('click', function(){ $('#eventFilter').val(''); $('#severityFilter').val(''); $('#userFilter').val(''); $('#searchFilter').val(''); applyFilters(); });

	window.viewSecurityLog = function(id){
		const l = <?php echo json_encode($securityLogs); ?>.find(x=>x.id===id);
		if(!l) return;
		const content = `
			<table class="table table-borderless">
				<tr><td><strong>ID:</strong></td><td>${l.id}</td></tr>
				<tr><td><strong>Timestamp:</strong></td><td>${new Date(l.timestamp).toLocaleString()}</td></tr>
				<tr><td><strong>Event Type:</strong></td><td>${l.event_type}</td></tr>
				<tr><td><strong>User:</strong></td><td>${l.user}</td></tr>
				<tr><td><strong>IP Address:</strong></td><td><code>${l.ip}</code></td></tr>
				<tr><td><strong>Location:</strong></td><td>${l.location}</td></tr>
				<tr><td><strong>Severity:</strong></td><td>${l.severity}</td></tr>
				<tr><td><strong>Description:</strong></td><td>${l.description}</td></tr>
			</table>`;
		$('#securityDetailsContent').html(content);
		$('#securityDetailsModal').modal('show');
	}
})();
</script>
