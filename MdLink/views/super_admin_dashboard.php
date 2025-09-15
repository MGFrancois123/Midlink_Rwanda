<!-- Super Admin Dashboard -->
<div class="row kpi-row">
    <!-- Total Medicines Card -->
    <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 dashboard">
        <div class="card" style="background: #2BC155">
            <div class="media widget-ten">
                <div class="media-left meida media-middle">
                    <span><i class="ti-package"></i></span>
                </div>
                <div class="media-body media-text-right">
                    <h2 class="color-white kpi-value"><?php echo number_format($medicineCount); ?></h2>
                    <a href="medicines.php"><p class="kpi-label">Total Medicines</p></a>
                    <p class="kpi-sub">Low stock: <?php echo $lowStockCount; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Pharmacies Card -->
    <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 dashboard">
        <div class="card" style="background:#2563eb">
            <div class="media widget-ten">
                <div class="media-left meida media-middle">
                    <span><i class="ti-home"></i></span>
                </div>
                <div class="media-body media-text-right">
                    <h2 class="color-white kpi-value"><?php echo number_format($pharmacyCount); ?></h2>
                    <p class="kpi-label">Pharmacies</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Users Card -->
    <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 dashboard">
        <div class="card" style="background:#A02CFA">
            <div class="media widget-ten">
                <div class="media-left meida media-middle">
                    <span><i class="ti-user"></i></span>
                </div>
                <div class="media-body media-text-right">
                    <h2 class="color-white kpi-value"><?php echo number_format($userCount); ?></h2>
                    <p class="kpi-label">Users</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Appointments Card -->
    <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 dashboard">
        <div class="card" style="background:#F94687">
            <div class="media widget-ten">
                <div class="media-left meida media-middle">
                    <span><i class="ti-calendar"></i></span>
                </div>
                <div class="media-body media-text-right">
                    <h2 class="color-white kpi-value"><?php echo number_format($appointmentCount); ?></h2>
                    <a href="appointments.php"><p class="kpi-label">Appointments</p></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Sales Card -->
    <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 dashboard">
        <div class="card" style="background:#FFBC11">
            <div class="media widget-ten">
                <div class="media-left meida media-middle">
                    <span><i class="ti-money"></i></span>
                </div>
                <div class="media-body media-text-right">
                    <h2 class="color-white kpi-value">$<?php echo number_format($totalSales, 2); ?></h2>
                    <p class="kpi-label">Total Sales</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity Section -->
<div class="row mt-4">
    <!-- Recent Appointments -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <strong class="card-title">Recent Appointments</strong>
            </div>
            <div class="card-body">
                <?php
                $recentAppointments = $connect->query("
                    SELECT a.*, u.full_name 
                    FROM appointments a 
                    JOIN users u ON a.user_id = u.user_id 
                    ORDER BY a.appointment_date DESC 
                    LIMIT 5
                ")->fetch_all(MYSQLI_ASSOC);
                
                if (!empty($recentAppointments)) {
                    echo '<div class="table-responsive">';
                    echo '<table class="table table-striped">';
                    echo '<thead><tr><th>Patient</th><th>Date</th><th>Status</th></tr></thead>';
                    echo '<tbody>';
                    
                    foreach ($recentAppointments as $appt) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($appt['full_name']) . '</td>';
                        echo '<td>' . date('M d, Y', strtotime($appt['appointment_date'])) . '</td>';
                        echo '<td><span class="badge badge-' . 
                             ($appt['status'] == 'confirmed' ? 'success' : 'warning') . '">' . 
                             ucfirst($appt['status']) . '</span></td>';
                        echo '</tr>';
                    }
                    
                    echo '</tbody></table>';
                    echo '</div>';
                } else {
                    echo '<p>No recent appointments found.</p>';
                }
                ?>
            </div>
        </div>
    </div>
    
    <!-- Low Stock Alerts -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <strong class="card-title">Low Stock Alerts</strong>
            </div>
            <div class="card-body">
                <?php
                $lowStockItems = $connect->query("
                    SELECT m.name, m.stock_quantity 
                    FROM medicines m 
                    WHERE m.stock_quantity <= $lowStockThreshold 
                    ORDER BY m.stock_quantity ASC 
                    LIMIT 5
                ")->fetch_all(MYSQLI_ASSOC);
                
                if (!empty($lowStockItems)) {
                    echo '<div class="table-responsive">';
                    echo '<table class="table table-striped">';
                    echo '<thead><tr><th>Medicine</th><th>Stock</th><th>Status</th></tr></thead>';
                    echo '<tbody>';
                    
                    foreach ($lowStockItems as $item) {
                        $statusClass = $item['stock_quantity'] <= 5 ? 'danger' : 'warning';
                        $statusText = $item['stock_quantity'] <= 5 ? 'Critical' : 'Low';
                        
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($item['name']) . '</td>';
                        echo '<td>' . $item['stock_quantity'] . '</td>';
                        echo '<td><span class="badge badge-' . $statusClass . '">' . $statusText . '</span></td>';
                        echo '</tr>';
                    }
                    
                    echo '</tbody></table>';
                    echo '</div>';
                } else {
                    echo '<p>No low stock items found.</p>';
                }
                ?>
            </div>
        </div>
    </div>
</div>
