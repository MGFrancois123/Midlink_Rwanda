<?php
require_once '../includes/SystemLogger.php';
require_once '../constant/connect.php';
// Prefer PDO for SystemLogger; fallback to basic queries if not available
$pdo = null; $pdoOk = false;
if (class_exists('PDO')) {
  try {
    $pdo = new PDO("mysql:host={$localhost};dbname={$dbname};charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    $pdoOk = true;
  } catch (Exception $e) { $pdoOk = false; }
}
$systemLogger = $pdoOk ? new SystemLogger($pdo) : null;

// Get statistics data
if ($systemLogger) {
  $usageStats = $systemLogger->getUsageStatistics('day', 30);
  $activeUsers = $systemLogger->getSystemMetrics('active_users', 'day', 30);
  $memoryUsage = $systemLogger->getSystemMetrics('memory_usage', 'day', 30);
  $responseTime = $systemLogger->getSystemMetrics('response_time', 'day', 30);
} else {
  // Fallback using mysqli queries
  $usageStats = [];
  if ($res = $connect->query("SELECT DATE_FORMAT(created_at, '%Y-%m-%d') as period, COUNT(*) as total_visits, COUNT(DISTINCT ip_address) as unique_visitors FROM usage_analytics GROUP BY period ORDER BY period DESC LIMIT 30")) {
    while ($row = $res->fetch_assoc()) { $usageStats[] = $row; }
    $usageStats = array_reverse($usageStats);
  }
  $activeUsers = $memoryUsage = $responseTime = [];
}
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">System Statistics</h1>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-secondary" id="refreshStats">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
            <div class="btn-group">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-download"></i> Export
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#" data-export="csv">CSV</a></li>
                    <li><a class="dropdown-item" href="#" data-export="pdf">PDF</a></li>
                    <li><a class="dropdown-item" href="#" data-export="excel">Excel</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Visits (30d)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo number_format(array_sum(array_column($usageStats, 'total_visits'))); ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Unique Visitors (30d)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo number_format(max(array_column($usageStats, 'unique_visitors'))); ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Avg. Response Time</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php 
                                $avgResponse = array_sum(array_column($responseTime, 'avg_value')) / count($responseTime);
                                echo number_format($avgResponse, 2) . ' ms'; 
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tachometer-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                System Status</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <span class="badge bg-success">Operational</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-server fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Visits Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Visits Overview</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="visitsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="visitsDropdown">
                            <li><a class="dropdown-item" href="#" data-period="day">Last 30 Days</a></li>
                            <li><a class="dropdown-item" href="#" data-period="week">Last 12 Weeks</a></li>
                            <li><a class="dropdown-item" href="#" data-period="month">Last 12 Months</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="visitsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Metrics -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">System Performance</h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="small font-weight-bold">Memory Usage <span class="float-end">
                            <?php 
                            $avgMemory = array_sum(array_column($memoryUsage, 'avg_value')) / max(count($memoryUsage), 1);
                            echo number_format($avgMemory, 1) . '%'; 
                            ?>
                        </span></h6>
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: <?php echo $avgMemory; ?>%" 
                                 aria-valuenow="<?php echo $avgMemory; ?>" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <h6 class="small font-weight-bold">CPU Usage <span class="float-end">
                            <?php 
                            $cpuMetric = $systemLogger->getSystemMetrics('cpu_usage', 'hour', 1);
                            $cpuUsage = !empty($cpuMetric) ? $cpuMetric[0]['avg_value'] : 0;
                            echo number_format($cpuUsage, 1) . '%'; 
                            ?>
                        </span></h6>
                        <div class="progress">
                            <div class="progress-bar bg-info" role="progressbar" 
                                 style="width: <?php echo $cpuUsage; ?>%" 
                                 aria-valuenow="<?php echo $cpuUsage; ?>" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <h6 class="small font-weight-bold">Database Queries <span class="float-end">
                            <?php 
                            $dbMetric = $systemLogger->getSystemMetrics('db_queries', 'hour', 1);
                            echo !empty($dbMetric) ? number_format($dbMetric[0]['avg_value']) : '0'; 
                            ?>/min
                        </span></h6>
                        <div class="progress">
                            <div class="progress-bar bg-warning" role="progressbar" 
                                 style="width: 65%" 
                                 aria-valuenow="65" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables -->
    <div class="row">
        <!-- Top Pages -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Most Visited Pages</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Page</th>
                                    <th>Visits</th>
                                    <th>% of Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $topPages = $systemLogger->getTopPages(5);
                                $totalVisits = array_sum(array_column($topPages, 'visits'));
                                
                                foreach ($topPages as $page):
                                    $percentage = ($page['visits'] / max($totalVisits, 1)) * 100;
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($page['page_url']); ?></td>
                                    <td><?php echo number_format($page['visits']); ?></td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar bg-primary" role="progressbar" 
                                                 style="width: <?php echo $percentage; ?>%" 
                                                 aria-valuenow="<?php echo $percentage; ?>" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                <?php echo number_format($percentage, 1); ?>%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
                    <a href="?title=Audit%20Logs" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="activity-feed">
                        <?php
                        $recentActivities = $systemLogger->getAuditLogs([], 5);
                        
                        if (empty($recentActivities)) {
                            echo '<div class="text-center text-muted py-3">No recent activities found</div>';
                        } else {
                            foreach ($recentActivities as $activity):
                                $icon = 'fa-info-circle';
                                $color = 'text-primary';
                                
                                if (strpos($activity['action'], 'delete') !== false) {
                                    $icon = 'fa-trash-alt';
                                    $color = 'text-danger';
                                } elseif (strpos($activity['action'], 'create') !== false) {
                                    $icon = 'fa-plus-circle';
                                    $color = 'text-success';
                                } elseif (strpos($activity['action'], 'update') !== false) {
                                    $icon = 'fa-edit';
                                    $color = 'text-warning';
                                } elseif (strpos($activity['action'], 'login') !== false) {
                                    $icon = 'fa-sign-in-alt';
                                    $color = 'text-info';
                                }
                                
                                $timeAgo = timeAgo($activity['created_at']);
                        ?>
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <i class="fas <?php echo $icon; ?> fa-2x <?php echo $color; ?>"></i>
                            </div>
                            <div class="ms-3">
                                <div class="small">
                                    <strong><?php echo htmlspecialchars($activity['username'] ?? 'System'); ?></strong> 
                                    <?php echo ucfirst(str_replace('_', ' ', $activity['action'])); ?>
                                    <?php if ($activity['entity_type']): ?>
                                        <strong><?php echo $activity['entity_type']; ?></strong>
                                        <?php if ($activity['entity_id']): ?>
                                            #<?php echo $activity['entity_id']; ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <div class="text-muted small">
                                    <?php echo $timeAgo; ?> • 
                                    <?php echo date('M j, Y g:i A', strtotime($activity['created_at'])); ?>
                                </div>
                            </div>
                        </div>
                        <?php 
                            endforeach;
                        } 
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="assets/js/lib/chart-js/Chart.bundle.js"></script>

<script>
// Time ago function
function timeAgo(date) {
    const seconds = Math.floor((new Date() - new Date(date)) / 1000);
    
    let interval = Math.floor(seconds / 31536000);
    if (interval >= 1) return interval + ' year' + (interval === 1 ? '' : 's') + ' ago';
    
    interval = Math.floor(seconds / 2592000);
    if (interval >= 1) return interval + ' month' + (interval === 1 ? '' : 's') + ' ago';
    
    interval = Math.floor(seconds / 86400);
    if (interval >= 1) return interval + ' day' + (interval === 1 ? '' : 's') + ' ago';
    
    interval = Math.floor(seconds / 3600);
    if (interval >= 1) return interval + ' hour' + (interval === 1 ? '' : 's') + ' ago';
    
    interval = Math.floor(seconds / 60);
    if (interval >= 1) return interval + ' minute' + (interval === 1 ? '' : 's') + ' ago';
    
    return 'just now';
}

// Visits Chart
var visitsCanvas = document.getElementById('visitsChart');
if (visitsCanvas && window.Chart) {
const visitsCtx = visitsCanvas.getContext('2d');
const visitsChart = new Chart(visitsCtx, {
    type: 'line',
    data: {
        labels: [
            <?php 
            $labels = [];
            foreach (array_reverse($usageStats) as $stat) {
                $labels[] = "'" . date('M j', strtotime($stat['period'])) . "'";
            }
            echo implode(', ', $labels);
            ?>
        ],
        datasets: [
            {
                label: 'Total Visits',
                data: [
                    <?php 
                    $data = [];
                    foreach (array_reverse($usageStats) as $stat) {
                        $data[] = $stat['total_visits'];
                    }
                    echo implode(', ', $data);
                    ?>
                ],
                borderColor: 'rgba(78, 115, 223, 1)',
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                tension: 0.3,
                fill: true
            },
            {
                label: 'Unique Visitors',
                data: [
                    <?php 
                    $data = [];
                    foreach (array_reverse($usageStats) as $stat) {
                        $data[] = $stat['unique_visitors'];
                    }
                    echo implode(', ', $data);
                    ?>
                ],
                borderColor: 'rgba(28, 200, 138, 1)',
                backgroundColor: 'rgba(28, 200, 138, 0.05)',
                tension: 0.3,
                borderDash: [5, 5],
                fill: true
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            },
            tooltip: {
                mode: 'index',
                intersect: false,
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                }
            }
        },
        interaction: {
            mode: 'nearest',
            axis: 'x',
            intersect: false
        }
    }
});
}

// Period filter for visits chart
document.querySelectorAll('[data-period]').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const period = this.getAttribute('data-period');
        
        // Update active state
        document.querySelectorAll('[data-period]').forEach(el => {
            el.classList.remove('active');
        });
        this.classList.add('active');
        
        // Fetch new data
        fetch(`api/get_usage_stats.php?period=${period}`)
            .then(response => response.json())
            .then(data => {
                // Update chart data
                visitsChart.data.labels = data.labels;
                visitsChart.data.datasets[0].data = data.total_visits;
                visitsChart.data.datasets[1].data = data.unique_visitors;
                visitsChart.update();
            });
    });
});

// Refresh button
document.getElementById('refreshStats').addEventListener('click', function() {
    window.location.href = window.location.href;
});

// Export functionality
document.querySelectorAll('[data-export]').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const format = this.getAttribute('data-export');
        alert(`Exporting data as ${format.toUpperCase()}...`);
        // In a real app, this would trigger a download
    });
});
</script>

<!-- Refresh Toast -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="refreshToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
        <div class="toast-header">
            <strong class="me-auto">Statistics Updated</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            The statistics have been successfully updated.
        </div>
    </div>
</div>
