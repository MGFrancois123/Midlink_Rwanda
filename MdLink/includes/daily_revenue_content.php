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
	// Fetch real daily revenue data
	$stmt = $connect->prepare("
		SELECT 
			revenue_date,
			total_sales,
			total_refunds,
			net_revenue,
			mobile_money,
			bank_transfer,
			credit_card,
			cash,
			transaction_count
		FROM daily_revenue
		WHERE pharmacy_id = ?
		ORDER BY revenue_date DESC
		LIMIT 30
	");
	$stmt->bind_param("i", $pharmacy_id);
	$stmt->execute();
	$result = $stmt->get_result();
	$revenueData = $result->fetch_all(MYSQLI_ASSOC);
	$stmt->close();

	// Calculate today's performance
	$today = date('Y-m-d');
	$todayData = array_filter($revenueData, function($r) use ($today) { return $r['revenue_date'] === $today; });
	$todayData = reset($todayData); // Get first (and should be only) match

	// Calculate weekly summary (last 7 days)
	$weeklyData = array_slice($revenueData, 0, 7);
	$weeklyTotal = array_sum(array_column($weeklyData, 'net_revenue'));
	$weeklyAvg = count($weeklyData) > 0 ? $weeklyTotal / count($weeklyData) : 0;

	// Calculate growth (comparing with previous period)
	$previousWeek = array_slice($revenueData, 7, 7);
	$previousWeekTotal = array_sum(array_column($previousWeek, 'net_revenue'));
	$growth = $previousWeekTotal > 0 ? (($weeklyTotal - $previousWeekTotal) / $previousWeekTotal) * 100 : 0;

} catch (Exception $e) {
	$revenueData = [];
	$todayData = null;
	$weeklyTotal = 0;
	$weeklyAvg = 0;
	$growth = 0;
}
?>

<!-- Hero Section -->
<div class="row mb-4">
	<div class="col-12">
		<div class="card bg-gradient-success text-white">
			<div class="card-body">
				<div class="row align-items-center">
					<div class="col-md-8">
						<h2 class="mb-2"><i class="fa fa-line-chart"></i> Daily Revenue</h2>
						<p class="mb-0">Track daily sales performance, revenue trends, and payment method analytics.</p>
					</div>
					<div class="col-md-4 text-right">
						<button class="btn btn-light btn-lg" id="exportRevenueBtn">
							<i class="fa fa-download"></i> Export Report
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Today's Performance -->
<div class="row mb-4">
	<div class="col-md-3">
		<div class="card bg-primary text-white">
			<div class="card-body">
				<div class="d-flex justify-content-between">
					<div>
						<h4 class="mb-0"><?php echo number_format($todayData['net_revenue'] ?? 0, 2); ?> RWF</h4>
						<p class="mb-0">Today's Revenue</p>
					</div>
					<div class="align-self-center">
						<i class="fa fa-calendar fa-2x"></i>
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
						<h4 class="mb-0"><?php echo $todayData['transaction_count'] ?? 0; ?></h4>
						<p class="mb-0">Today's Transactions</p>
					</div>
					<div class="align-self-center">
						<i class="fa fa-shopping-cart fa-2x"></i>
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
						<h4 class="mb-0"><?php echo number_format($todayData['total_refunds'] ?? 0, 2); ?> RWF</h4>
						<p class="mb-0">Today's Refunds</p>
					</div>
					<div class="align-self-center">
						<i class="fa fa-undo fa-2x"></i>
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
						<h4 class="mb-0"><?php echo number_format($todayData['total_sales'] ?? 0, 2); ?> RWF</h4>
						<p class="mb-0">Today's Sales</p>
					</div>
					<div class="align-self-center">
						<i class="fa fa-money fa-2x"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Weekly Summary -->
<div class="row mb-4">
	<div class="col-md-4">
		<div class="card bg-light">
			<div class="card-body text-center">
				<h3 class="text-primary"><?php echo number_format($weeklyTotal, 2); ?> RWF</h3>
				<p class="mb-0">Weekly Total</p>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="card bg-light">
			<div class="card-body text-center">
				<h3 class="text-info"><?php echo number_format($weeklyAvg, 2); ?> RWF</h3>
				<p class="mb-0">Daily Average</p>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="card bg-light">
			<div class="card-body text-center">
				<h3 class="<?php echo $growth >= 0 ? 'text-success' : 'text-danger'; ?>">
					<?php echo $growth >= 0 ? '+' : ''; ?><?php echo number_format($growth, 1); ?>%
				</h3>
				<p class="mb-0">Growth Rate</p>
			</div>
		</div>
	</div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
	<div class="col-md-8">
		<div class="card">
			<div class="card-header">
				<h4 class="mb-0"><i class="fa fa-line-chart"></i> Revenue Trend (Last 7 Days)</h4>
			</div>
			<div class="card-body">
				<canvas id="revenueChart" height="100"></canvas>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="card">
			<div class="card-header">
				<h4 class="mb-0"><i class="fa fa-pie-chart"></i> Payment Methods (Today)</h4>
			</div>
			<div class="card-body">
				<canvas id="paymentMethodsChart" height="200"></canvas>
			</div>
		</div>
	</div>
</div>

<!-- Daily Revenue Table -->
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h4 class="mb-0"><i class="fa fa-table"></i> Daily Revenue History</h4>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-striped table-hover" id="revenueTable">
						<thead class="thead-dark">
							<tr>
								<th>Date</th>
								<th>Total Sales</th>
								<th>Refunds</th>
								<th>Net Revenue</th>
								<th>Mobile Money</th>
								<th>Bank Transfer</th>
								<th>Credit Card</th>
								<th>Cash</th>
								<th>Transactions</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($revenueData as $revenue) { ?>
							<tr>
								<td>
									<div>
										<strong><?php echo date('M d, Y', strtotime($revenue['revenue_date'])); ?></strong>
										<br><small class="text-muted"><?php echo date('l', strtotime($revenue['revenue_date'])); ?></small>
									</div>
								</td>
								<td>
									<strong class="text-success"><?php echo number_format($revenue['total_sales'], 2); ?> RWF</strong>
								</td>
								<td>
									<span class="text-danger"><?php echo number_format($revenue['total_refunds'], 2); ?> RWF</span>
								</td>
								<td>
									<strong class="text-primary"><?php echo number_format($revenue['net_revenue'], 2); ?> RWF</strong>
								</td>
								<td><?php echo number_format($revenue['mobile_money'], 2); ?> RWF</td>
								<td><?php echo number_format($revenue['bank_transfer'], 2); ?> RWF</td>
								<td><?php echo number_format($revenue['credit_card'], 2); ?> RWF</td>
								<td><?php echo number_format($revenue['cash'], 2); ?> RWF</td>
								<td>
									<span class="badge badge-info"><?php echo $revenue['transaction_count']; ?></span>
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
.bg-gradient-success {
	background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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
	background-color: rgba(40, 167, 69, 0.1);
	cursor: pointer;
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

.badge-info {
	background-color: #17a2b8;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
	$('#revenueTable').DataTable({
		pageLength: 10,
		order: [[0, 'desc']],
		responsive: true,
		dom: 'Bfrtip',
		buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
	});

	// Revenue Trend Chart
	const revenueData = <?php echo json_encode(array_reverse(array_slice($revenueData, 0, 7))); ?>;
	const ctx1 = document.getElementById('revenueChart').getContext('2d');
	new Chart(ctx1, {
		type: 'line',
		data: {
			labels: revenueData.map(r => new Date(r.revenue_date).toLocaleDateString()),
			datasets: [{
				label: 'Net Revenue (RWF)',
				data: revenueData.map(r => r.net_revenue),
				borderColor: '#007bff',
				backgroundColor: 'rgba(0, 123, 255, 0.1)',
				tension: 0.4,
				fill: true
			}]
		},
		options: {
			responsive: true,
			maintainAspectRatio: false,
			scales: {
				y: {
					beginAtZero: true,
					ticks: {
						callback: function(value) {
							return value.toLocaleString() + ' RWF';
						}
					}
				}
			}
		}
	});

	// Payment Methods Chart
	const todayData = <?php echo json_encode($todayData); ?>;
	if (todayData) {
		const ctx2 = document.getElementById('paymentMethodsChart').getContext('2d');
		new Chart(ctx2, {
			type: 'doughnut',
			data: {
				labels: ['Mobile Money', 'Bank Transfer', 'Credit Card', 'Cash'],
				datasets: [{
					data: [
						todayData.mobile_money,
						todayData.bank_transfer,
						todayData.credit_card,
						todayData.cash
					],
					backgroundColor: ['#007bff', '#17a2b8', '#28a745', '#ffc107']
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				plugins: {
					legend: {
						position: 'bottom'
					}
				}
			}
		});
	}
});

$('#exportRevenueBtn').on('click', function() {
	alert('Export revenue report functionality would be implemented here.');
});
</script>