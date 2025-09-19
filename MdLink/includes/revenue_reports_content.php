<?php
require_once __DIR__ . '/../constant/connect.php';
if (session_status() === PHP_SESSION_NONE) { if (session_status() === PHP_SESSION_NONE) {
    session_start();
} }

if (!isset($_SESSION['adminId'])) {
	echo '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Access Denied. Please log in.</div>';
	return;
}

// Sample report rows (daily aggregated)
$reports = [
	['date'=>'2024-01-15','transactions'=>45,'gross_sales'=>1250.75,'refunds'=>25.00,'net_revenue'=>1225.75,'cash'=>850.50,'mobile'=>300.25,'bank'=>100.00,'card'=>0.00],
	['date'=>'2024-01-14','transactions'=>32,'gross_sales'=>980.25,'refunds'=>15.25,'net_revenue'=>965.00,'cash'=>650.75,'mobile'=>250.50,'bank'=>79.00,'card'=>0.00],
	['date'=>'2024-01-13','transactions'=>58,'gross_sales'=>1450.00,'refunds'=>30.00,'net_revenue'=>1420.00,'cash'=>900.00,'mobile'=>400.00,'bank'=>150.00,'card'=>0.00],
	['date'=>'2024-01-12','transactions'=>52,'gross_sales'=>1350.50,'refunds'=>20.00,'net_revenue'=>1330.50,'cash'=>800.25,'mobile'=>350.25,'bank'=>200.00,'card'=>0.00],
	['date'=>'2024-01-11','transactions'=>38,'gross_sales'=>1100.75,'refunds'=>12.50,'net_revenue'=>1088.25,'cash'=>700.50,'mobile'=>300.25,'bank'=>100.00,'card'=>0.00],
	['date'=>'2024-01-10','transactions'=>42,'gross_sales'=>1200.00,'refunds'=>18.75,'net_revenue'=>1181.25,'cash'=>750.00,'mobile'=>350.00,'bank'=>100.00,'card'=>0.00],
	['date'=>'2024-01-09','transactions'=>35,'gross_sales'=>1050.25,'refunds'=>22.50,'net_revenue'=>1027.75,'cash'=>650.25,'mobile'=>300.00,'bank'=>100.00,'card'=>0.00],
];

$totalNet = array_sum(array_column($reports,'net_revenue'));
$totalGross = array_sum(array_column($reports,'gross_sales'));
$totalRefunds = array_sum(array_column($reports,'refunds'));
$totalTx = array_sum(array_column($reports,'transactions'));
$avgNet = $totalNet / count($reports);

$sumCash = array_sum(array_column($reports,'cash'));
$sumMobile = array_sum(array_column($reports,'mobile'));
$sumBank = array_sum(array_column($reports,'bank'));
$sumCard = array_sum(array_column($reports,'card'));
?>

<!-- Hero -->
<div class="row mb-4">
	<div class="col-12">
		<div class="card bg-gradient-success text-white">
			<div class="card-body">
				<div class="row align-items-center">
					<div class="col-md-8">
						<h2 class="mb-2"><i class="fa fa-file-text-o"></i> Revenue Reports</h2>
						<p class="mb-0">Generate, analyze, and export revenue reports with filters, charts, and summaries.</p>
					</div>
					<div class="col-md-4 text-right">
						<button class="btn btn-light btn-lg" data-toggle="modal" data-target="#exportReportModal"><i class="fa fa-download"></i> Export</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- KPIs -->
<div class="row mb-4">
	<div class="col-md-3"><div class="card bg-success text-white"><div class="card-body d-flex justify-content-between"><div><h4 class="mb-0"><?php echo number_format($totalNet,2); ?> RWF</h4><p class="mb-0">Total Net Revenue</p></div><i class="fa fa-money fa-2x"></i></div></div></div>
	<div class="col-md-3"><div class="card bg-primary text-white"><div class="card-body d-flex justify-content-between"><div><h4 class="mb-0"><?php echo number_format($totalGross,2); ?> RWF</h4><p class="mb-0">Total Gross Sales</p></div><i class="fa fa-line-chart fa-2x"></i></div></div></div>
	<div class="col-md-3"><div class="card bg-danger text-white"><div class="card-body d-flex justify-content-between"><div><h4 class="mb-0"><?php echo number_format($totalRefunds,2); ?> RWF</h4><p class="mb-0">Total Refunds</p></div><i class="fa fa-undo fa-2x"></i></div></div></div>
	<div class="col-md-3"><div class="card bg-info text-white"><div class="card-body d-flex justify-content-between"><div><h4 class="mb-0"><?php echo $totalTx; ?></h4><p class="mb-0">Total Transactions</p></div><i class="fa fa-list fa-2x"></i></div></div></div>
</div>

<!-- Filters -->
<div class="row mb-4">
	<div class="col-12">
		<div class="card"><div class="card-body">
			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						<label><i class="fa fa-calendar"></i> Date Range</label>
						<input type="date" class="form-control" id="startDate" value="<?php echo date('Y-m-d', strtotime('-7 days')); ?>">
						<small class="text-muted">Start</small>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label>&nbsp;</label>
						<input type="date" class="form-control" id="endDate" value="<?php echo date('Y-m-d'); ?>">
						<small class="text-muted">End</small>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label><i class="fa fa-credit-card"></i> Payment Method</label>
						<select class="form-control" id="methodFilter">
							<option value="">All</option>
							<option>Cash</option>
							<option>Mobile Money</option>
							<option>Bank Transfer</option>
							<option>Credit Card</option>
						</select>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label><i class="fa fa-search"></i> Search</label>
						<input type="text" class="form-control" id="searchFilter" placeholder="Search by date...">
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

<!-- Charts -->
<div class="row mb-4">
	<div class="col-md-8">
		<div class="card">
			<div class="card-header"><h4 class="mb-0"><i class="fa fa-line-chart"></i> Net Revenue Trend</h4></div>
			<div class="card-body"><canvas id="netRevenueChart" height="100"></canvas></div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="card">
			<div class="card-header"><h4 class="mb-0"><i class="fa fa-pie-chart"></i> Payment Methods</h4></div>
			<div class="card-body"><canvas id="methodsChart" height="200"></canvas></div>
		</div>
	</div>
</div>

<!-- Table -->
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header"><h4 class="mb-0"><i class="fa fa-table"></i> Revenue Report (Daily)</h4></div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-striped table-hover" id="reportsTable">
						<thead class="thead-dark">
							<tr>
								<th>Date</th>
								<th>Transactions</th>
								<th>Gross Sales</th>
								<th>Refunds</th>
								<th>Net Revenue</th>
								<th>Cash</th>
								<th>Mobile Money</th>
								<th>Bank Transfer</th>
								<th>Credit Card</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($reports as $row) { ?>
							<tr>
								<td><strong><?php echo date('M d, Y', strtotime($row['date'])); ?></strong></td>
								<td><span class="badge badge-primary"><?php echo $row['transactions']; ?></span></td>
								<td><?php echo number_format($row['gross_sales'],2); ?> RWF</td>
								<td><span class="text-danger"><?php echo number_format($row['refunds'],2); ?> RWF</span></td>
								<td><strong class="text-success"><?php echo number_format($row['net_revenue'],2); ?> RWF</strong></td>
								<td><?php echo number_format($row['cash'],2); ?> RWF</td>
								<td><?php echo number_format($row['mobile'],2); ?> RWF</td>
								<td><?php echo number_format($row['bank'],2); ?> RWF</td>
								<td><?php echo number_format($row['card'],2); ?> RWF</td>
								<td>
									<button class="btn btn-sm btn-outline-primary" onclick="viewDay('<?php echo $row['date']; ?>')"><i class="fa fa-eye"></i></button>
									<button class="btn btn-sm btn-outline-success" onclick="exportDay('<?php echo $row['date']; ?>')"><i class="fa fa-download"></i></button>
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

<!-- Export Modal -->
<div class="modal fade" id="exportReportModal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header bg-success text-white"><h5 class="modal-title"><i class="fa fa-download"></i> Export Revenue Report</h5><button type="button" class="close text-white" data-dismiss="modal">&times;</button></div>
			<div class="modal-body">
				<div class="form-group"><label>Date Range</label><div class="row"><div class="col-md-6"><input type="date" class="form-control" id="expStart"></div><div class="col-md-6"><input type="date" class="form-control" id="expEnd"></div></div></div>
				<div class="form-group"><label>Format</label><select class="form-control" id="expFormat"><option value="excel">Excel (.xlsx)</option><option value="pdf">PDF</option><option value="csv">CSV</option></select></div>
			</div>
			<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button><button type="button" class="btn btn-success" id="btnExport"><i class="fa fa-download"></i> Export</button></div>
		</div>
	</div>
</div>

<style>
.bg-gradient-success{background:linear-gradient(135deg,#28a745 0%,#20c997 100%)}
.card{border:none;box-shadow:0 0.125rem 0.25rem rgba(0,0,0,.075);transition:all .3s ease}
.card:hover{box-shadow:0 .5rem 1rem rgba(0,0,0,.15);transform:translateY(-2px)}
.table th{border-top:none;font-weight:600}
.badge{font-size:.75rem;padding:.375rem .75rem}
.table-hover tbody tr:hover{background-color:rgba(40,167,69,.08);cursor:pointer}
.modal-header.bg-success{background:linear-gradient(135deg,#28a745 0%,#1e7e34 100%)!important}
.form-control:focus{border-color:#28a745;box-shadow:0 0 0 .2rem rgba(40,167,69,.25)}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function(){
	$(document).ready(function(){
		$('#reportsTable').DataTable({pageLength:10,order:[[0,'desc']],responsive:true,dom:'Bfrtip',buttons:['copy','csv','excel','pdf','print']});
		initCharts();
	});

	function applyFilters(){
		const start = $('#startDate').val();
		const end = $('#endDate').val();
		const method = $('#methodFilter').val();
		const search = $('#searchFilter').val().toLowerCase();
		$('#reportsTable tbody tr').each(function(){
			let show = true; const row = $(this);
			if (start || end){
				const d = new Date(row.find('td:eq(0)').text());
				if (start && d < new Date(start)) show = false;
				if (end && d > new Date(end)) show = false;
			}
			if (method){
				const idx = method==='Cash'?5:method==='Mobile Money'?6:method==='Bank Transfer'?7:8;
				const val = parseFloat((row.find('td:eq('+idx+')').text()||'0').replace(/[^0-9.]/g,''))||0;
				if (!val) show = false;
			}
			if (search && !row.text().toLowerCase().includes(search)) show = false;
			row.toggle(show);
		});
	}
	$('#applyFilters').on('click', applyFilters);
	$('#startDate,#endDate,#methodFilter').on('change', applyFilters);
	$('#searchFilter').on('keyup', applyFilters);

	$('#exportBtn').on('click', function(){ $('#reportsTable').DataTable().button('.buttons-excel').trigger(); });
	$('#btnExport').on('click', function(){
		const s=$('#expStart').val(), e=$('#expEnd').val(), f=$('#expFormat').val();
		if (!s||!e){ alert('Select start and end dates'); return; }
		alert(`Exporting revenue report from ${s} to ${e} as ${f.toUpperCase()}`);
		$('#exportReportModal').modal('hide');
	});

	function initCharts(){
		const labels = <?php echo json_encode(array_map(fn($r)=>date('M d',strtotime($r['date'])),$reports)); ?>.reverse();
		const data = <?php echo json_encode(array_column($reports,'net_revenue')); ?>.reverse();
		const ctx = document.getElementById('netRevenueChart').getContext('2d');
		new Chart(ctx,{type:'line',data:{labels:labels,datasets:[{label:'Net Revenue (RWF)',data:data,borderColor:'#28a745',backgroundColor:'rgba(40,167,69,.1)',borderWidth:3,fill:true,tension:.4}]},options:{responsive:true,maintainAspectRatio:false,scales:{y:{beginAtZero:true,ticks:{callback:v=>v.toLocaleString()+' RWF'}}}}});
		const mctx = document.getElementById('methodsChart').getContext('2d');
		new Chart(mctx,{type:'doughnut',data:{labels:['Cash','Mobile Money','Bank Transfer','Credit Card'],datasets:[{data:[<?php echo $sumCash; ?>,<?php echo $sumMobile; ?>,<?php echo $sumBank; ?>,<?php echo $sumCard; ?>],backgroundColor:['#28a745','#17a2b8','#ffc107','#dc3545'],borderColor:'#fff',borderWidth:2}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom'}}}});
	}

	window.viewDay = function(date){ alert('View details for '+date); };
	window.exportDay = function(date){ alert('Export day '+date); };
})();
</script>
