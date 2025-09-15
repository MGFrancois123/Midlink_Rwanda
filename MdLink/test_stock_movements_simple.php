<?php 
// Simple test version without authentication
require_once './constant/connect.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Stock Movements Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid mt-3">
        <h2>Stock Movements Test Page</h2>
        
        <?php
        if ($connect) {
            echo "<div class='alert alert-success'>✅ Database connected successfully</div>";
            
            // Test basic queries
            $userCount = 0;
            $medCount = 0;
            $pharmCount = 0;
            $auditCount = 0;
            $stockCount = 0;
            
            // Count admin users
            $userQ = $connect->query("SELECT COUNT(*) as count FROM admin_users");
            if($userQ) { $userCount = $userQ->fetch_assoc()['count']; }
            
            // Count medicines
            $medQ = $connect->query("SELECT COUNT(*) as count FROM medicines");
            if($medQ) { $medCount = $medQ->fetch_assoc()['count']; }
            
            // Count pharmacies
            $pharmQ = $connect->query("SELECT COUNT(*) as count FROM pharmacies");
            if($pharmQ) { $pharmCount = $pharmQ->fetch_assoc()['count']; }
            
            // Count audit logs
            $auditQ = $connect->query("SELECT COUNT(*) as count FROM audit_logs");
            if($auditQ) { $auditCount = $auditQ->fetch_assoc()['count']; }
            
            // Count stock movements
            $stockQ = $connect->query("SELECT COUNT(*) as count FROM stock_movements");
            if($stockQ) { $stockCount = $stockQ->fetch_assoc()['count']; }
            
            echo "<div class='row mb-3'>";
            echo "<div class='col-md-2'><div class='card bg-primary text-white'><div class='card-body text-center'><h4>$userCount</h4><p>Admin Users</p></div></div></div>";
            echo "<div class='col-md-2'><div class='card bg-success text-white'><div class='card-body text-center'><h4>$medCount</h4><p>Medicines</p></div></div></div>";
            echo "<div class='col-md-2'><div class='card bg-info text-white'><div class='card-body text-center'><h4>$pharmCount</h4><p>Pharmacies</p></div></div></div>";
            echo "<div class='col-md-2'><div class='card bg-warning text-white'><div class='card-body text-center'><h4>$auditCount</h4><p>Audit Logs</p></div></div></div>";
            echo "<div class='col-md-2'><div class='card bg-danger text-white'><div class='card-body text-center'><h4>$stockCount</h4><p>Stock Movements</p></div></div></div>";
            echo "</div>";
            
            // Test the main query
            echo "<h3>Recent Transactions</h3>";
            $sql = "(SELECT 
                      'audit' as source,
                      al.log_id as id,
                      al.action as transaction_type,
                      al.entity_type,
                      al.entity_id,
                      al.created_at as transaction_date,
                      au.username as user_name
                    FROM audit_logs al
                    LEFT JOIN admin_users au ON al.user_id = au.admin_id)
                    UNION ALL
                    (SELECT 
                      'stock' as source,
                      sm.movement_id as id,
                      sm.movement_type as transaction_type,
                      'medicine' as entity_type,
                      sm.medicine_id as entity_id,
                      sm.movement_date as transaction_date,
                      au.username as user_name
                    FROM stock_movements sm
                    LEFT JOIN admin_users au ON sm.user_id = au.admin_id)
                    ORDER BY transaction_date DESC, id DESC
                    LIMIT 10";
                    
            $q = $connect->query($sql);
            if ($q && $q->num_rows > 0) {
                echo "<table class='table table-striped'>";
                echo "<thead><tr><th>ID</th><th>Type</th><th>Entity</th><th>Date</th><th>User</th></tr></thead>";
                echo "<tbody>";
                while($r = $q->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>".$r['id']."</td>";
                    echo "<td><span class='badge bg-primary'>".$r['transaction_type']."</span></td>";
                    echo "<td>".$r['entity_type']." (".$r['entity_id'].")</td>";
                    echo "<td>".$r['transaction_date']."</td>";
                    echo "<td>".($r['user_name'] ?? 'N/A')."</td>";
                    echo "</tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<div class='alert alert-warning'>No transactions found or query error: " . $connect->error . "</div>";
            }
            
        } else {
            echo "<div class='alert alert-danger'>❌ Database connection failed</div>";
        }
        ?>
        
        <div class="mt-3">
            <a href="stock_movements.php" class="btn btn-primary">Go to Full Stock Movements Page</a>
            <a href="login.php" class="btn btn-secondary">Go to Login</a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
