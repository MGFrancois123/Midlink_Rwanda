<!-- Pharmacy Admin Dashboard -->
<div class="row kpi-row">
    <!-- Pharmacy Summary Card -->
    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 dashboard">
        <div class="card" style="background: #2BC155">
            <div class="media widget-ten">
                <div class="media-left meida media-middle">
                    <span><i class="ti-home"></i></span>
                </div>
                <div class="media-body media-text-right">
                    <h2 class="color-white kpi-value"><?php echo htmlspecialchars($pharmacyName); ?></h2>
                    <p class="kpi-label">Your Pharmacy</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Medicines Card -->
    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 dashboard">
        <div class="card" style="background:#2563eb">
            <div class="media widget-ten">
                <div class="media-left meida media-middle">
                    <span><i class="ti-package"></i></span>
                </div>
                <div class="media-body media-text-right">
                    <h2 class="color-white kpi-value"><?php echo number_format($medicineCount); ?></h2>
                    <a href="medicines.php"><p class="kpi-label">Medicines</p></a>
                    <p class="kpi-sub">Low stock: <?php echo $lowStockCount; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Appointments Card -->
    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 dashboard">
        <div class="card" style="background:#A02CFA">
            <div class="media widget-ten">
                <div class="media-left meida media-middle">
                    <span><i class="ti-calendar"></i></span>
                </div>
                <div class="media-body media-text-right">
                    <h2 class="color-white kpi-value"><?php echo number_format($appointmentCount); ?></h2>
                    <a href="appointments.php"><p class="kpi-label">Appointments</p></a>
                    <?php if(isset($todayAppointments)): ?>
                        <p class="kpi-sub">Today: <?php echo $todayAppointments; ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Sales Card -->
    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 dashboard">
        <div class="card" style="background:#F94687">
            <div class="media widget-ten">
                <div class="media-left meida media-middle">
                    <span><i class="ti-money"></i></span>
                </div>
                <div class="media-body media-text-right">
                    <h2 class="color-white kpi-value">$<?php echo number_format($totalSales, 2); ?></h2>
                    <p class="kpi-label">Total Sales</p>
                    <?php if(isset($monthlySales)): ?>
                        <p class="kpi-sub">This Month: $<?php echo number_format($monthlySales, 2); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pharmacy Dashboard Content -->
<div class="row mt-4">
    <!-- Today's Appointments -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <strong class="card-title">Today's Appointments</strong>
            </div>
            <div class="card-body">
                <?php
                $today = date('Y-m-d');
                $todayAppts = $connect->query("
                    SELECT a.*, u.full_name, u.phone 
                    FROM appointments a 
                    JOIN users u ON a.user_id = u.user_id 
                    WHERE DATE(a.appointment_date) = '$today' 
                    AND a.pharmacy_id = $pharmacyId 
                    ORDER BY a.appointment_date ASC
                ")->fetch_all(MYSQLI_ASSOC);
                
                if (!empty($todayAppts)) {
                    echo '<div class="table-responsive">';
                    echo '<table class="table table-striped">';
                    echo '<thead><tr><th>Time</th><th>Patient</th><th>Contact</th><th>Status</th></tr></thead>';
                    echo '<tbody>';
                    
                    foreach ($todayAppts as $appt) {
                        echo '<tr>';
                        echo '<td>' . date('h:i A', strtotime($appt['appointment_date'])) . '</td>';
                        echo '<td>' . htmlspecialchars($appt['full_name']) . '</td>';
                        echo '<td>' . htmlspecialchars($appt['phone']) . '</td>';
                        echo '<td><span class="badge badge-' . 
                             ($appt['status'] == 'confirmed' ? 'success' : 'warning') . '">' . 
                             ucfirst($appt['status']) . '</span></td>';
                        echo '</tr>';
                    }
                    
                    echo '</tbody></table>';
                    echo '</div>';
                } else {
                    echo '<p>No appointments scheduled for today.</p>';
                }
                ?>
            </div>
        </div>
    </div>
    
    <!-- Low Stock in Your Pharmacy -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <strong class="card-title">Low Stock Alert</strong>
            </div>
            <div class="card-body">
                <?php
                $lowStockItems = $connect->query("
                    SELECT name, stock_quantity, price 
                    FROM medicines 
                    WHERE pharmacy_id = $pharmacyId 
                    AND stock_quantity <= $lowStockThreshold 
                    ORDER BY stock_quantity ASC 
                    LIMIT 5
                ")->fetch_all(MYSQLI_ASSOC);
                
                if (!empty($lowStockItems)) {
                    echo '<div class="table-responsive">';
                    echo '<table class="table table-striped">';
                    echo '<thead><tr><th>Medicine</th><th>In Stock</th><th>Price</th><th>Status</th></tr></thead>';
                    echo '<tbody>';
                    
                    foreach ($lowStockItems as $item) {
                        $statusClass = $item['stock_quantity'] <= 2 ? 'danger' : 'warning';
                        $statusText = $item['stock_quantity'] <= 2 ? 'Critical' : 'Low';
                        
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($item['name']) . '</td>';
                        echo '<td>' . $item['stock_quantity'] . '</td>';
                        echo '<td>$' . number_format($item['price'], 2) . '</td>';
                        echo '<td><span class="badge badge-' . $statusClass . '">' . $statusText . '</span></td>';
                        echo '</tr>';
                    }
                    
                    echo '</tbody></table>';
                    echo '<div class="text-right mt-2">';
                    echo '<a href="medicines.php?filter=low_stock" class="btn btn-sm btn-outline-primary">View All</a>';
                    echo '</div>';
                    echo '</div>';
                } else {
                    echo '<p>No low stock items in your pharmacy.</p>';
                }
                ?>
            </div>
        </div>
    </div>
</div>
