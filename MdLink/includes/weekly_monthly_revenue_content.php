<?php
require_once __DIR__ . '/../constant/connect.php';
if (session_status() === PHP_SESSION_NONE) { 
    if (session_status() === PHP_SESSION_NONE) {
    session_start();
} 
}

// Check if user is logged in and has access
if (!isset($_SESSION['adminId'])) {
    echo '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Access Denied. Please log in.</div>';
    return;
}

// Sample weekly revenue data
$weeklyRevenue = [
    [
        'week_start' => '2024-01-08',
        'week_end' => '2024-01-14',
        'week_number' => 'Week 2',
        'total_revenue' => 8237.50,
        'total_transactions' => 302,
        'average_transaction' => 27.28,
        'cash_sales' => 5600.25,
        'mobile_money_sales' => 1950.75,
        'bank_transfer_sales' => 686.50,
        'credit_card_sales' => 0.00,
        'refunds' => 143.00,
        'net_revenue' => 8094.50,
        'growth_percentage' => 8.5
    ],
    [
        'week_start' => '2024-01-01',
        'week_end' => '2024-01-07',
        'week_number' => 'Week 1',
        'total_revenue' => 7456.25,
        'total_transactions' => 275,
        'average_transaction' => 27.11,
        'cash_sales' => 5100.75,
        'mobile_money_sales' => 1756.50,
        'bank_transfer_sales' => 599.00,
        'credit_card_sales' => 0.00,
        'refunds' => 125.25,
        'net_revenue' => 7331.00,
        'growth_percentage' => -3.2
    ],
    [
        'week_start' => '2023-12-25',
        'week_end' => '2023-12-31',
        'week_number' => 'Week 52',
        'total_revenue' => 7890.75,
        'total_transactions' => 285,
        'average_transaction' => 27.69,
        'cash_sales' => 5400.25,
        'mobile_money_sales' => 1890.50,
        'bank_transfer_sales' => 600.00,
        'credit_card_sales' => 0.00,
        'refunds' => 98.50,
        'net_revenue' => 7792.25,
        'growth_percentage' => 12.8
    ],
    [
        'week_start' => '2023-12-18',
        'week_end' => '2023-12-24',
        'week_number' => 'Week 51',
        'total_revenue' => 6912.50,
        'total_transactions' => 248,
        'average_transaction' => 27.87,
        'cash_sales' => 4700.75,
        'mobile_money_sales' => 1611.75,
        'bank_transfer_sales' => 600.00,
        'credit_card_sales' => 0.00,
        'refunds' => 87.25,
        'net_revenue' => 6825.25,
        'growth_percentage' => 5.7
    ],
    [
        'week_start' => '2023-12-11',
        'week_end' => '2023-12-17',
        'week_number' => 'Week 50',
        'total_revenue' => 6456.25,
        'total_transactions' => 232,
        'average_transaction' => 27.83,
        'cash_sales' => 4400.50,
        'mobile_money_sales' => 1455.75,
        'bank_transfer_sales' => 600.00,
        'credit_card_sales' => 0.00,
        'refunds' => 75.00,
        'net_revenue' => 6381.25,
        'growth_percentage' => -2.1
    ]
];

// Sample monthly revenue data
$monthlyRevenue = [
    [
        'month' => '2024-01',
        'month_name' => 'January 2024',
        'total_revenue' => 32500.75,
        'total_transactions' => 1185,
        'average_transaction' => 27.43,
        'cash_sales' => 22100.50,
        'mobile_money_sales' => 7800.25,
        'bank_transfer_sales' => 2600.00,
        'credit_card_sales' => 0.00,
        'refunds' => 450.25,
        'net_revenue' => 32050.50,
        'growth_percentage' => 15.8,
        'days_in_month' => 31,
        'average_daily_revenue' => 1034.21
    ],
    [
        'month' => '2023-12',
        'month_name' => 'December 2023',
        'total_revenue' => 28075.25,
        'total_transactions' => 1025,
        'average_transaction' => 27.39,
        'cash_sales' => 19100.75,
        'mobile_money_sales' => 6774.50,
        'bank_transfer_sales' => 2200.00,
        'credit_card_sales' => 0.00,
        'refunds' => 325.50,
        'net_revenue' => 27749.75,
        'growth_percentage' => 8.2,
        'days_in_month' => 31,
        'average_daily_revenue' => 895.15
    ],
    [
        'month' => '2023-11',
        'month_name' => 'November 2023',
        'total_revenue' => 25950.50,
        'total_transactions' => 945,
        'average_transaction' => 27.46,
        'cash_sales' => 17650.25,
        'mobile_money_sales' => 6300.25,
        'bank_transfer_sales' => 2000.00,
        'credit_card_sales' => 0.00,
        'refunds' => 275.75,
        'net_revenue' => 25674.75,
        'growth_percentage' => -5.3,
        'days_in_month' => 30,
        'average_daily_revenue' => 855.83
    ],
    [
        'month' => '2023-10',
        'month_name' => 'October 2023',
        'total_revenue' => 27400.75,
        'total_transactions' => 995,
        'average_transaction' => 27.54,
        'cash_sales' => 18650.50,
        'mobile_money_sales' => 6650.25,
        'bank_transfer_sales' => 2100.00,
        'credit_card_sales' => 0.00,
        'refunds' => 300.25,
        'net_revenue' => 27100.50,
        'growth_percentage' => 12.1,
        'days_in_month' => 31,
        'average_daily_revenue' => 874.21
    ],
    [
        'month' => '2023-09',
        'month_name' => 'September 2023',
        'total_revenue' => 24450.25,
        'total_transactions' => 890,
        'average_transaction' => 27.47,
        'cash_sales' => 16650.75,
        'mobile_money_sales' => 5900.50,
        'bank_transfer_sales' => 1899.00,
        'credit_card_sales' => 0.00,
        'refunds' => 250.00,
        'net_revenue' => 24200.25,
        'growth_percentage' => -3.7,
        'days_in_month' => 30,
        'average_daily_revenue' => 806.68
    ]
];

// Calculate totals and averages
$totalWeeklyRevenue = array_sum(array_column($weeklyRevenue, 'net_revenue'));
$totalMonthlyRevenue = array_sum(array_column($monthlyRevenue, 'net_revenue'));
$averageWeeklyRevenue = $totalWeeklyRevenue / count($weeklyRevenue);
$averageMonthlyRevenue = $totalMonthlyRevenue / count($monthlyRevenue);

// Get current period data
$currentWeek = $weeklyRevenue[0];
$currentMonth = $monthlyRevenue[0];
?>

<!-- Hero Section -->
<div class="row mb-4">
  <div class="col-12">
    <div class="card bg-gradient-primary text-white">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col-md-8">
            <h2 class="mb-2"><i class="fa fa-bar-chart"></i> Weekly / Monthly Revenue</h2>
            <p class="mb-0">Comprehensive revenue analytics with weekly and monthly trends, performance metrics, and detailed financial insights.</p>
          </div>
          <div class="col-md-4 text-right">
            <div class="btn-group" role="group">
              <button type="button" class="btn btn-light active" id="weeklyView">
                <i class="fa fa-calendar-week"></i> Weekly
              </button>
              <button type="button" class="btn btn-light" id="monthlyView">
                <i class="fa fa-calendar"></i> Monthly
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Current Period Performance Cards -->
<div class="row mb-4" id="currentPeriodCards">
  <div class="col-md-3">
    <div class="card bg-success text-white">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <h4 class="mb-0" id="currentRevenue"><?php echo number_format($currentWeek['net_revenue'], 2); ?> RWF</h4>
            <p class="mb-0" id="currentPeriodLabel">This Week's Revenue</p>
          </div>
          <div class="align-self-center">
            <i class="fa fa-money fa-2x"></i>
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
            <h4 class="mb-0" id="currentTransactions"><?php echo $currentWeek['total_transactions']; ?></h4>
            <p class="mb-0" id="currentTransactionsLabel">This Week's Transactions</p>
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
            <h4 class="mb-0" id="currentAverage"><?php echo number_format($currentWeek['average_transaction'], 2); ?> RWF</h4>
            <p class="mb-0" id="currentAverageLabel">Avg Transaction</p>
          </div>
          <div class="align-self-center">
            <i class="fa fa-calculator fa-2x"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card <?php echo $currentWeek['growth_percentage'] >= 0 ? 'bg-success' : 'bg-danger'; ?> text-white">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <h4 class="mb-0" id="currentGrowth"><?php echo $currentWeek['growth_percentage'] >= 0 ? '+' : ''; ?><?php echo number_format($currentWeek['growth_percentage'], 1); ?>%</h4>
            <p class="mb-0" id="currentGrowthLabel">vs Previous Week</p>
          </div>
          <div class="align-self-center">
            <i class="fa <?php echo $currentWeek['growth_percentage'] >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'; ?> fa-2x"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
  <div class="col-md-3">
    <div class="card bg-primary text-white">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <h4 class="mb-0"><?php echo number_format($totalWeeklyRevenue, 2); ?> RWF</h4>
            <p class="mb-0">Total Weekly Revenue</p>
          </div>
          <div class="align-self-center">
            <i class="fa fa-calendar-week fa-2x"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card bg-secondary text-white">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <h4 class="mb-0"><?php echo number_format($totalMonthlyRevenue, 2); ?> RWF</h4>
            <p class="mb-0">Total Monthly Revenue</p>
          </div>
          <div class="align-self-center">
            <i class="fa fa-calendar fa-2x"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card bg-dark text-white">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <h4 class="mb-0"><?php echo number_format($averageWeeklyRevenue, 2); ?> RWF</h4>
            <p class="mb-0">Average Weekly</p>
          </div>
          <div class="align-self-center">
            <i class="fa fa-bar-chart fa-2x"></i>
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
            <h4 class="mb-0"><?php echo number_format($averageMonthlyRevenue, 2); ?> RWF</h4>
            <p class="mb-0">Average Monthly</p>
          </div>
          <div class="align-self-center">
            <i class="fa fa-line-chart fa-2x"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header">
        <h4 class="mb-0"><i class="fa fa-line-chart"></i> <span id="chartTitle">Weekly Revenue Trend</span></h4>
      </div>
      <div class="card-body">
        <canvas id="revenueTrendChart" height="100"></canvas>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card">
      <div class="card-header">
        <h4 class="mb-0"><i class="fa fa-pie-chart"></i> <span id="pieChartTitle">Weekly Payment Methods</span></h4>
      </div>
      <div class="card-body">
        <canvas id="paymentMethodsChart" height="200"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- Data Tables -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="mb-0"><i class="fa fa-table"></i> <span id="tableTitle">Weekly Revenue Details</span></h4>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-hover" id="revenueTable">
            <thead class="thead-dark">
              <tr>
                <th id="periodHeader">Week</th>
                <th>Period</th>
                <th>Total Revenue</th>
                <th>Transactions</th>
                <th>Avg Transaction</th>
                <th>Cash</th>
                <th>Mobile Money</th>
                <th>Bank Transfer</th>
                <th>Refunds</th>
                <th>Net Revenue</th>
                <th>Growth</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="revenueTableBody">
              <?php foreach ($weeklyRevenue as $revenue) { ?>
              <tr>
                <td>
                  <span class="badge badge-primary"><?php echo $revenue['week_number']; ?></span>
                </td>
                <td>
                  <div>
                    <strong><?php echo date('M d', strtotime($revenue['week_start'])); ?> - <?php echo date('M d, Y', strtotime($revenue['week_end'])); ?></strong>
                  </div>
                </td>
                <td>
                  <strong><?php echo number_format($revenue['total_revenue'], 2); ?> RWF</strong>
                </td>
                <td>
                  <span class="badge badge-info"><?php echo $revenue['total_transactions']; ?></span>
                </td>
                <td>
                  <?php echo number_format($revenue['average_transaction'], 2); ?> RWF
                </td>
                <td>
                  <span class="text-success"><?php echo number_format($revenue['cash_sales'], 2); ?> RWF</span>
                </td>
                <td>
                  <span class="text-info"><?php echo number_format($revenue['mobile_money_sales'], 2); ?> RWF</span>
                </td>
                <td>
                  <span class="text-warning"><?php echo number_format($revenue['bank_transfer_sales'], 2); ?> RWF</span>
                </td>
                <td>
                  <span class="text-danger"><?php echo number_format($revenue['refunds'], 2); ?> RWF</span>
                </td>
                <td>
                  <strong class="text-success"><?php echo number_format($revenue['net_revenue'], 2); ?> RWF</strong>
                </td>
                <td>
                  <span class="badge <?php echo $revenue['growth_percentage'] >= 0 ? 'badge-success' : 'badge-danger'; ?>">
                    <?php echo $revenue['growth_percentage'] >= 0 ? '+' : ''; ?><?php echo number_format($revenue['growth_percentage'], 1); ?>%
                  </span>
                </td>
                <td>
                  <button class="btn btn-sm btn-outline-primary" title="View Details" onclick="viewPeriodDetails('<?php echo $revenue['week_start']; ?>', 'weekly')">
                    <i class="fa fa-eye"></i>
                  </button>
                  <button class="btn btn-sm btn-outline-success" title="Export Period" onclick="exportPeriod('<?php echo $revenue['week_start']; ?>', 'weekly')">
                    <i class="fa fa-download"></i>
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

<!-- Period Details Modal -->
<div class="modal fade" id="periodDetailsModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title"><i class="fa fa-eye"></i> <span id="modalTitle">Period Details</span></h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body" id="periodDetailsContent">
        <!-- Content will be loaded here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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

.modal-header.bg-info {
  background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important;
}

.form-group label {
  font-weight: 600;
  color: #495057;
}

.form-control:focus {
  border-color: #007bff;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
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

.text-info {
  color: #17a2b8 !important;
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

.badge-dark {
  background-color: #343a40;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.card {
  animation: fadeInUp 0.6s ease-out;
}

@media (max-width: 768px) {
  .table-responsive {
    font-size: 0.875rem;
  }
  
  .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
  }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function(){
  let currentView = 'weekly';
  let revenueTrendChart, paymentMethodsChart;
  
  // Weekly data
  const weeklyData = <?php echo json_encode($weeklyRevenue); ?>;
  const monthlyData = <?php echo json_encode($monthlyRevenue); ?>;
  
  // Initialize DataTable
  $(document).ready(function() {
    $('#revenueTable').DataTable({
      pageLength: 10,
      order: [[0, 'desc']],
      responsive: true,
      dom: 'Bfrtip',
      buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
    });
    
    initializeCharts();
  });
  
  // View toggle functionality
  document.getElementById('weeklyView').addEventListener('click', function() {
    switchToWeekly();
  });
  
  document.getElementById('monthlyView').addEventListener('click', function() {
    switchToMonthly();
  });
  
  function switchToWeekly() {
    currentView = 'weekly';
    document.getElementById('weeklyView').classList.add('active');
    document.getElementById('monthlyView').classList.remove('active');
    
    updateCurrentPeriodCards(weeklyData[0], 'week');
    updateCharts(weeklyData, 'weekly');
    updateTable(weeklyData, 'weekly');
  }
  
  function switchToMonthly() {
    currentView = 'monthly';
    document.getElementById('monthlyView').classList.add('active');
    document.getElementById('weeklyView').classList.remove('active');
    
    updateCurrentPeriodCards(monthlyData[0], 'month');
    updateCharts(monthlyData, 'monthly');
    updateTable(monthlyData, 'monthly');
  }
  
  function updateCurrentPeriodCards(data, period) {
    document.getElementById('currentRevenue').textContent = data.net_revenue.toLocaleString() + ' RWF';
    document.getElementById('currentTransactions').textContent = data.total_transactions;
    document.getElementById('currentAverage').textContent = data.average_transaction.toLocaleString() + ' RWF';
    document.getElementById('currentGrowth').textContent = (data.growth_percentage >= 0 ? '+' : '') + data.growth_percentage.toFixed(1) + '%';
    
    if (period === 'week') {
      document.getElementById('currentPeriodLabel').textContent = 'This Week\'s Revenue';
      document.getElementById('currentTransactionsLabel').textContent = 'This Week\'s Transactions';
      document.getElementById('currentGrowthLabel').textContent = 'vs Previous Week';
    } else {
      document.getElementById('currentPeriodLabel').textContent = 'This Month\'s Revenue';
      document.getElementById('currentTransactionsLabel').textContent = 'This Month\'s Transactions';
      document.getElementById('currentGrowthLabel').textContent = 'vs Previous Month';
    }
    
    // Update growth card color
    const growthCard = document.querySelector('#currentPeriodCards .col-md-3:last-child .card');
    growthCard.className = 'card ' + (data.growth_percentage >= 0 ? 'bg-success' : 'bg-danger') + ' text-white';
    
    const growthIcon = growthCard.querySelector('i');
    growthIcon.className = 'fa ' + (data.growth_percentage >= 0 ? 'fa-arrow-up' : 'fa-arrow-down') + ' fa-2x';
  }
  
  function updateCharts(data, type) {
    const labels = data.map(item => {
      if (type === 'weekly') {
        return item.week_number;
      } else {
        return item.month_name.split(' ')[0];
      }
    }).reverse();
    
    const revenueData = data.map(item => item.net_revenue).reverse();
    
    // Update trend chart
    revenueTrendChart.data.labels = labels;
    revenueTrendChart.data.datasets[0].data = revenueData;
    revenueTrendChart.update();
    
    // Update payment methods chart
    const totalCash = data.reduce((sum, item) => sum + item.cash_sales, 0);
    const totalMobile = data.reduce((sum, item) => sum + item.mobile_money_sales, 0);
    const totalBank = data.reduce((sum, item) => sum + item.bank_transfer_sales, 0);
    const totalCard = data.reduce((sum, item) => sum + item.credit_card_sales, 0);
    
    paymentMethodsChart.data.datasets[0].data = [totalCash, totalMobile, totalBank, totalCard];
    paymentMethodsChart.update();
    
    // Update chart titles
    document.getElementById('chartTitle').textContent = type === 'weekly' ? 'Weekly Revenue Trend' : 'Monthly Revenue Trend';
    document.getElementById('pieChartTitle').textContent = type === 'weekly' ? 'Weekly Payment Methods' : 'Monthly Payment Methods';
  }
  
  function updateTable(data, type) {
    const tbody = document.getElementById('revenueTableBody');
    tbody.innerHTML = '';
    
    data.forEach(item => {
      const row = document.createElement('tr');
      const periodLabel = type === 'weekly' ? item.week_number : item.month_name;
      const periodRange = type === 'weekly' ? 
        `${new Date(item.week_start).toLocaleDateString('en-US', {month: 'short', day: 'numeric'})} - ${new Date(item.week_end).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}` :
        item.month_name;
      
      row.innerHTML = `
        <td><span class="badge badge-primary">${periodLabel}</span></td>
        <td><strong>${periodRange}</strong></td>
        <td><strong>${item.total_revenue.toLocaleString()} RWF</strong></td>
        <td><span class="badge badge-info">${item.total_transactions}</span></td>
        <td>${item.average_transaction.toLocaleString()} RWF</td>
        <td><span class="text-success">${item.cash_sales.toLocaleString()} RWF</span></td>
        <td><span class="text-info">${item.mobile_money_sales.toLocaleString()} RWF</span></td>
        <td><span class="text-warning">${item.bank_transfer_sales.toLocaleString()} RWF</span></td>
        <td><span class="text-danger">${item.refunds.toLocaleString()} RWF</span></td>
        <td><strong class="text-success">${item.net_revenue.toLocaleString()} RWF</strong></td>
        <td><span class="badge ${item.growth_percentage >= 0 ? 'badge-success' : 'badge-danger'}">${item.growth_percentage >= 0 ? '+' : ''}${item.growth_percentage.toFixed(1)}%</span></td>
        <td>
          <button class="btn btn-sm btn-outline-primary" title="View Details" onclick="viewPeriodDetails('${type === 'weekly' ? item.week_start : item.month}', '${type}')">
            <i class="fa fa-eye"></i>
          </button>
          <button class="btn btn-sm btn-outline-success" title="Export Period" onclick="exportPeriod('${type === 'weekly' ? item.week_start : item.month}', '${type}')">
            <i class="fa fa-download"></i>
          </button>
        </td>
      `;
      tbody.appendChild(row);
    });
    
    // Update table title and header
    document.getElementById('tableTitle').textContent = type === 'weekly' ? 'Weekly Revenue Details' : 'Monthly Revenue Details';
    document.getElementById('periodHeader').textContent = type === 'weekly' ? 'Week' : 'Month';
  }
  
  function initializeCharts() {
    // Revenue Trend Chart
    const revenueCtx = document.getElementById('revenueTrendChart').getContext('2d');
    revenueTrendChart = new Chart(revenueCtx, {
      type: 'line',
      data: {
        labels: weeklyData.map(item => item.week_number).reverse(),
        datasets: [{
          label: 'Net Revenue (RWF)',
          data: weeklyData.map(item => item.net_revenue).reverse(),
          borderColor: '#007bff',
          backgroundColor: 'rgba(0, 123, 255, 0.1)',
          borderWidth: 3,
          fill: true,
          tension: 0.4
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
        },
        plugins: {
          legend: {
            display: true,
            position: 'top'
          }
        }
      }
    });
    
    // Payment Methods Chart
    const paymentCtx = document.getElementById('paymentMethodsChart').getContext('2d');
    const totalCash = weeklyData.reduce((sum, item) => sum + item.cash_sales, 0);
    const totalMobile = weeklyData.reduce((sum, item) => sum + item.mobile_money_sales, 0);
    const totalBank = weeklyData.reduce((sum, item) => sum + item.bank_transfer_sales, 0);
    const totalCard = weeklyData.reduce((sum, item) => sum + item.credit_card_sales, 0);
    
    paymentMethodsChart = new Chart(paymentCtx, {
      type: 'doughnut',
      data: {
        labels: ['Cash', 'Mobile Money', 'Bank Transfer', 'Credit Card'],
        datasets: [{
          data: [totalCash, totalMobile, totalBank, totalCard],
          backgroundColor: ['#28a745', '#17a2b8', '#ffc107', '#dc3545'],
          borderWidth: 2,
          borderColor: '#fff'
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
  
  // View period details
  window.viewPeriodDetails = function(periodId, type) {
    const data = type === 'weekly' ? weeklyData : monthlyData;
    const period = data.find(item => (type === 'weekly' ? item.week_start : item.month) === periodId);
    
    if (period) {
      const content = `
        <div class="row">
          <div class="col-md-6">
            <h6 class="text-primary">Revenue Summary</h6>
            <table class="table table-borderless">
              <tr><td><strong>Period:</strong></td><td>${type === 'weekly' ? period.week_number : period.month_name}</td></tr>
              <tr><td><strong>Date Range:</strong></td><td>${type === 'weekly' ? `${new Date(period.week_start).toLocaleDateString()} - ${new Date(period.week_end).toLocaleDateString()}` : period.month_name}</td></tr>
              <tr><td><strong>Total Revenue:</strong></td><td><strong>${period.total_revenue.toLocaleString()} RWF</strong></td></tr>
              <tr><td><strong>Net Revenue:</strong></td><td><strong class="text-success">${period.net_revenue.toLocaleString()} RWF</strong></td></tr>
              <tr><td><strong>Refunds:</strong></td><td><span class="text-danger">${period.refunds.toLocaleString()} RWF</span></td></tr>
            </table>
          </div>
          <div class="col-md-6">
            <h6 class="text-primary">Transaction Details</h6>
            <table class="table table-borderless">
              <tr><td><strong>Total Transactions:</strong></td><td>${period.total_transactions}</td></tr>
              <tr><td><strong>Average Transaction:</strong></td><td>${period.average_transaction.toLocaleString()} RWF</td></tr>
              <tr><td><strong>Growth vs Previous:</strong></td><td><span class="badge badge-${period.growth_percentage >= 0 ? 'success' : 'danger'}">${period.growth_percentage >= 0 ? '+' : ''}${period.growth_percentage}%</span></td></tr>
              ${type === 'monthly' ? `<tr><td><strong>Average Daily Revenue:</strong></td><td>${period.average_daily_revenue.toLocaleString()} RWF</td></tr>` : ''}
            </table>
          </div>
        </div>
        <div class="row mt-3">
          <div class="col-md-12">
            <h6 class="text-primary">Payment Methods Breakdown</h6>
            <div class="row">
              <div class="col-md-3">
                <div class="text-center">
                  <h5 class="text-success">${period.cash_sales.toLocaleString()} RWF</h5>
                  <small>Cash Sales</small>
                </div>
              </div>
              <div class="col-md-3">
                <div class="text-center">
                  <h5 class="text-info">${period.mobile_money_sales.toLocaleString()} RWF</h5>
                  <small>Mobile Money</small>
                </div>
              </div>
              <div class="col-md-3">
                <div class="text-center">
                  <h5 class="text-warning">${period.bank_transfer_sales.toLocaleString()} RWF</h5>
                  <small>Bank Transfer</small>
                </div>
              </div>
              <div class="col-md-3">
                <div class="text-center">
                  <h5 class="text-danger">${period.credit_card_sales.toLocaleString()} RWF</h5>
                  <small>Credit Card</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      `;
      
      document.getElementById('modalTitle').textContent = `${type === 'weekly' ? 'Weekly' : 'Monthly'} Revenue Details`;
      document.getElementById('periodDetailsContent').innerHTML = content;
      $('#periodDetailsModal').modal('show');
    }
  };
  
  // Export period
  window.exportPeriod = function(periodId, type) {
    alert(`Exporting ${type} revenue data for ${periodId} functionality would be implemented here.`);
  };
  
  // Add row hover effects
  document.querySelectorAll('tbody tr').forEach(row => {
    row.addEventListener('mouseenter', function() {
      this.style.backgroundColor = '#f8f9fa';
    });
    
    row.addEventListener('mouseleave', function() {
      this.style.backgroundColor = '';
    });
  });
})();
</script>
