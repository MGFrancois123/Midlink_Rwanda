<!-- Finance Admin Dashboard -->
<div class="row kpi-row">
    <!-- Total Revenue Card -->
    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 dashboard">
        <div class="card" style="background: #2BC155">
            <div class="media widget-ten">
                <div class="media-left meida media-middle">
                    <span><i class="ti-money"></i></span>
                </div>
                <div class="media-body media-text-right">
                    <h2 class="color-white kpi-value">$<?php echo number_format($totalSales, 2); ?></h2>
                    <p class="kpi-label">Total Revenue</p>
                </div>
            </div>
        </div>
    </div>

    <!-- This Month's Revenue -->
    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 dashboard">
        <div class="card" style="background:#2563eb">
            <div class="media widget-ten">
                <div class="media-left meida media-middle">
                    <span><i class="ti-calendar"></i></span>
                </div>
                <div class="media-body media-text-right">
                    <h2 class="color-white kpi-value">$<?php echo number_format($monthlySales, 2); ?></h2>
                    <p class="kpi-label">This Month</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Status -->
    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 dashboard">
        <div class="card" style="background:#A02CFA">
            <div class="media widget-ten">
                <div class="media-left meida media-middle">
                    <span><i class="ti-check-box"></i></span>
                </div>
                <div class="media-body media-text-right">
                    <h2 class="color-white kpi-value"><?php echo number_format($completedPayments); ?></h2>
                    <p class="kpi-label">Completed Payments</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <strong class="card-title">Recent Transactions</strong>
            </div>
            <div class="card-body">
                <?php
                $recentPayments = $connect->query("
                    SELECT p.*, u.full_name, ph.name as pharmacy_name
                    FROM payments p
                    LEFT JOIN appointments a ON p.appointment_id = a.appointment_id
                    LEFT JOIN users u ON a.user_id = u.user_id
                    LEFT JOIN pharmacies ph ON a.pharmacy_id = ph.pharmacy_id
                    ORDER BY p.paid_at DESC 
                    LIMIT 10
                ")->fetch_all(MYSQLI_ASSOC);
                
                if (!empty($recentPayments)) {
                    echo '<div class="table-responsive">';
                    echo '<table class="table table-striped">';
                    echo '<thead><tr><th>Date</th><th>Patient</th><th>Pharmacy</th><th>Amount</th><th>Status</th></tr></thead>';
                    echo '<tbody>';
                    
                    foreach ($recentPayments as $payment) {
                        $statusClass = $payment['status'] == 'completed' ? 'success' : 
                                     ($payment['status'] == 'pending' ? 'warning' : 'danger');
                        
                        echo '<tr>';
                        echo '<td>' . date('M d, Y', strtotime($payment['paid_at'])) . '</td>';
                        echo '<td>' . htmlspecialchars($payment['full_name'] ?? 'N/A') . '</td>';
                        echo '<td>' . htmlspecialchars($payment['pharmacy_name'] ?? 'N/A') . '</td>';
                        echo '<td>$' . number_format($payment['amount'], 2) . '</td>';
                        echo '<td><span class="badge badge-' . $statusClass . '">' . 
                             ucfirst($payment['status']) . '</span></td>';
                        echo '</tr>';
                    }
                    
                    echo '</tbody></table>';
                    echo '<div class="text-right mt-2">';
                    echo '<a href="payments.php" class="btn btn-sm btn-outline-primary">View All</a>';
                    echo '</div>';
                    echo '</div>';
                } else {
                    echo '<p>No recent transactions found.</p>';
                }
                ?>
            </div>
        </div>
    </div>
</div>
