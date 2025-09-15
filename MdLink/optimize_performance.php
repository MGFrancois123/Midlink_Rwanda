<?php
// Performance optimization script for MdLink Rwanda
// This script removes loading delays and optimizes the system

echo "<h2>MdLink Rwanda Performance Optimization</h2>";

// 1. Remove DataTables loading delays
$files_to_optimize = [
    'includes/transactions_content.php',
    'includes/refund_requests_content.php',
    'includes/failed_payments_content.php',
    'includes/daily_revenue_content.php',
    'includes/weekly_monthly_revenue_content.php',
    'includes/outstanding_balances_content.php',
    'includes/branch_reconciliation_content.php',
    'includes/revenue_reports_content.php',
    'includes/transaction_exceptions_content.php',
    'includes/suspicious_transactions_content.php',
    'includes/audit_logs_content.php',
    'includes/security_logs_content.php'
];

$optimizations = 0;

foreach ($files_to_optimize as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Remove loading spinners and delays
        $content = preg_replace('/setTimeout\([^)]+\),?\s*\d+\);/', '', $content);
        $content = preg_replace('/\.fadeIn\(\d+\)/', '.fadeIn()', $content);
        $content = preg_replace('/\.fadeOut\(\d+\)/', '.fadeOut()', $content);
        $content = preg_replace('/\.delay\(\d+\)/', '', $content);
        
        // Optimize DataTables initialization
        $content = preg_replace('/pageLength:\s*\d+/', 'pageLength: 10', $content);
        $content = preg_replace('/responsive:\s*true/', 'responsive: true, processing: false', $content);
        
        // Remove unnecessary animations
        $content = preg_replace('/animation:\s*[^;]+;/', '', $content);
        $content = preg_replace('/transition:\s*[^;]+;/', '', $content);
        
        file_put_contents($file, $content);
        $optimizations++;
        echo "<p style='color: green;'>✓ Optimized: $file</p>";
    }
}

// 2. Create a simple CSS file for performance
$performance_css = "
/* Performance optimizations */
.card { transition: none !important; }
.table-hover tbody tr:hover { background-color: rgba(0, 123, 255, 0.05) !important; }
.btn { transition: none !important; }
.modal { transition: none !important; }

/* Remove animations */
@keyframes fadeInUp { from { opacity: 1; transform: none; } to { opacity: 1; transform: none; } }
@keyframes fadeIn { from { opacity: 1; } to { opacity: 1; } }

/* Optimize DataTables */
.dataTables_wrapper { font-size: 14px; }
.dataTables_filter input { width: 200px; }
.dataTables_length select { width: 60px; }

/* Remove loading states */
.loading { display: none !important; }
.spinner { display: none !important; }
";

file_put_contents('css/performance.css', $performance_css);
echo "<p style='color: green;'>✓ Created performance.css</p>";

// 3. Create a simple JavaScript file for performance
$performance_js = "
// Performance optimizations
$(document).ready(function() {
    // Disable animations
    $.fx.off = true;
    
    // Optimize DataTables
    $.extend($.fn.dataTable.defaults, {
        processing: false,
        serverSide: false,
        responsive: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        language: {
            processing: 'Loading...',
            emptyTable: 'No data available',
            zeroRecords: 'No matching records found'
        }
    });
    
    // Remove loading delays
    $('.loading').hide();
    $('.spinner').hide();
    
    // Optimize form submissions
    $('form').on('submit', function() {
        $(this).find('button[type=submit]').prop('disabled', true);
    });
});

// Fast AJAX calls
function fastAjax(url, data, callback) {
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        dataType: 'json',
        timeout: 5000,
        success: callback,
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            if (callback) callback({success: false, message: error});
        }
    });
}
";

file_put_contents('js/performance.js', $performance_js);
echo "<p style='color: green;'>✓ Created performance.js</p>";

// 4. Create database indexes for performance
$indexes = [
    "CREATE INDEX IF NOT EXISTS idx_stock_movements_pharmacy ON stock_movements(medicine_id)",
    "CREATE INDEX IF NOT EXISTS idx_refund_requests_pharmacy ON refund_requests(pharmacy_id)",
    "CREATE INDEX IF NOT EXISTS idx_failed_payments_pharmacy ON failed_payments(pharmacy_id)",
    "CREATE INDEX IF NOT EXISTS idx_daily_revenue_pharmacy ON daily_revenue(pharmacy_id, revenue_date)",
    "CREATE INDEX IF NOT EXISTS idx_outstanding_balances_pharmacy ON outstanding_balances(pharmacy_id)",
    "CREATE INDEX IF NOT EXISTS idx_audit_logs_pharmacy ON audit_logs(pharmacy_id, created_at)",
    "CREATE INDEX IF NOT EXISTS idx_security_logs_pharmacy ON security_logs(pharmacy_id, created_at)"
];

try {
    require_once 'constant/connect.php';
    foreach ($indexes as $index) {
        if ($connect->query($index)) {
            echo "<p style='color: green;'>✓ Created database index</p>";
        }
    }
} catch (Exception $e) {
    echo "<p style='color: orange;'>⚠ Database optimization skipped: " . $e->getMessage() . "</p>";
}

echo "<h3>Optimization Complete</h3>";
echo "<p>Files optimized: $optimizations</p>";
echo "<p style='color: green; font-weight: bold;'>Performance optimizations applied successfully!</p>";
echo "<p><strong>Next steps:</strong></p>";
echo "<ul>";
echo "<li>Include 'css/performance.css' in your head section</li>";
echo "<li>Include 'js/performance.js' before closing body tag</li>";
echo "<li>Run the database setup script to create tables and indexes</li>";
echo "</ul>";
?>
