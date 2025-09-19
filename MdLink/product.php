<?php 
// Start session and check authentication first
include('./constant/check.php');
include('./constant/connect.php');

// Log view activity
require_once 'activity_logger.php';
logView($_SESSION['adminId'], 'medicines', 'Viewed medicine catalog');
?>
<?php include('./constant/layout/head.php');?>
<?php include('./constant/layout/header.php');?>
<?php include('./constant/layout/sidebar.php');?>

<?php
// Fetch medicines data with pharmacy names
$medicines_sql = "SELECT 
                    m.medicine_id,
                    m.pharmacy_id,
                    m.name,
                    m.description,
                    m.price,
                    m.stock_quantity,
                    m.expiry_date,
                    m.`Restricted Medicine`,
                    m.category_id,
                    COALESCE(p.name, 'No Pharmacy') as pharmacy_name
                FROM medicines m
                LEFT JOIN pharmacies p ON m.pharmacy_id = p.pharmacy_id
                ORDER BY m.medicine_id DESC";

$medicines_result = $connect->query($medicines_sql);
$medicines_data = [];

// Debug: Check for SQL errors
if (!$medicines_result) {
    echo "SQL Error: " . $connect->error;
}

if ($medicines_result && $medicines_result->num_rows > 0) {
    while ($row = $medicines_result->fetch_assoc()) {
        $medicines_data[] = [
            'medicine_id' => (int)$row['medicine_id'],
            'name' => $row['name'],
            'description' => $row['description'],
            'price' => (float)$row['price'],
            'stock_quantity' => (int)$row['stock_quantity'],
            'expiry_date' => $row['expiry_date'],
            'Restricted_Medicine' => (int)$row['Restricted Medicine'],
            'category_id' => (int)$row['category_id'],
            'pharmacy_id' => (int)$row['pharmacy_id'],
            'pharmacy_name' => $row['pharmacy_name']
        ];
    }
} else {
    // Debug: Show if no data found
    echo "<!-- Debug: No medicines found or query failed -->";
}

// Debug: Show count of fetched medicines
echo "<!-- Debug: Fetched " . count($medicines_data) . " medicines -->";

// Calculate statistics
$total_medicines = count($medicines_data);
$active_medicines = 0;
$low_stock_medicines = 0;
$expiring_medicines = 0;

foreach ($medicines_data as $medicine) {
    if ($medicine['stock_quantity'] > 0) {
        $active_medicines++;
    }
    if ($medicine['stock_quantity'] <= 10) {
        $low_stock_medicines++;
    }
    if ($medicine['expiry_date'] && $medicine['expiry_date'] !== 'N/A') {
        $expiry_date = new DateTime($medicine['expiry_date']);
        $today = new DateTime();
        $diff = $today->diff($expiry_date);
        if ($diff->days <= 30 && $expiry_date > $today) {
            $expiring_medicines++;
        }
    }
}
?>

<style>
.medicine-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 30px;
    text-align: center;
}

.medicine-header h3 {
    margin-bottom: 10px;
    font-weight: bold;
}

.medicine-header p {
    opacity: 0.9;
    margin-bottom: 0;
}

.medicine-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    overflow: hidden;
}

.medicine-card .card-body {
    padding: 25px;
}

.medicine-stats {
    display: flex;
    justify-content: space-around;
    margin-bottom: 25px;
    text-align: center;
}

.stat-item {
    padding: 15px;
    border-radius: 10px;
    background: #f8f9fa;
    min-width: 120px;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: bold;
    color: #007bff;
    display: block;
}

.stat-label {
    font-size: 0.8rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.add-medicine-btn {
    background: linear-gradient(135deg, #28a745, #20c997);
    border: none;
    color: white;
    padding: 12px 25px;
    border-radius: 25px;
    font-weight: 500;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.add-medicine-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
    color: white;
    text-decoration: none;
}

.table-container {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}

.table-container .table {
    margin-bottom: 0;
}

.table-container .table th {
    background: #f8f9fa;
    border: none;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 0.5px;
}

.table-container .table td {
    border: none;
    vertical-align: middle;
}

.badge-custom {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.action-btn {
    border-radius: 20px;
    padding: 6px 12px;
    font-size: 0.8rem;
    margin-right: 5px;
}

.btn-danger.action-btn {
    background: linear-gradient(135deg, #dc3545, #c82333);
    border: none;
    transition: all 0.3s ease;
}

.btn-danger.action-btn:hover {
    background: linear-gradient(135deg, #c82333, #bd2130);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
}
</style>

<div class="page-wrapper">
    <div class="container-fluid">
        <!-- Enhanced Header -->
        <div class="medicine-header">
            <h3><i class="fa fa-medkit"></i> Medicine Management</h3>
            <p>View, manage, and track all medicines across the pharmacy network</p>
        </div>

        <!-- Statistics Row -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-item  btn-info">
                    <span class="stat-number"><?php echo $total_medicines; ?></span>
                    <span class="stat-label">Total Medicines</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-item btn-warning">
                    <span class="stat-number"><?php echo $active_medicines; ?></span>
                    <span class="stat-label">Active Stock</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-item btn-danger">
                    <span class="stat-number"><?php echo $low_stock_medicines; ?></span>
                    <span class="stat-label">Low Stock</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-item btn-success">
                    <span class="stat-number"><?php echo $expiring_medicines; ?></span>
                    <span class="stat-label">Expiring Soon</span>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="medicine-card">
            <div class="card-body">
                <?php
                // Display success messages
                if (isset($_GET['success'])) {
                    $message = '';
                    $medicine_name = isset($_GET['medicine']) ? htmlspecialchars($_GET['medicine']) : '';
                    
                    switch ($_GET['success']) {
                        case 'updated':
                            $message = 'Medicine "' . $medicine_name . '" updated successfully!';
                            break;
                        case 'created':
                            $message = 'Medicine "' . $medicine_name . '" added successfully!';
                            break;
                        case 'deleted':
                            $message = 'Medicine deleted successfully!';
                            break;
                        default:
                            $message = htmlspecialchars($_GET['success']);
                    }
                    
                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
                    echo '<i class="fa fa-check-circle"></i> ' . $message;
                    echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
                    echo '<span aria-hidden="true">&times;</span>';
                    echo '</button>';
                    echo '</div>';
                }
                
                // Display error messages
                if (isset($_GET['error'])) {
                    $message = '';
                    
                    switch ($_GET['error']) {
                        case 'update_failed':
                            $message = 'Failed to update medicine. Please try again.';
                            break;
                        case 'validation_failed':
                            $message = 'Please fill in all required fields with valid values.';
                            break;
                        case 'invalid_request':
                            $message = 'Invalid request. Please try again.';
                            break;
                        case 'missing_medicine_id':
                            $message = 'Medicine ID is missing. Please try again.';
                            break;
                        default:
                            $message = htmlspecialchars($_GET['error']);
                    }
                    
                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
                    echo '<i class="fa fa-exclamation-circle"></i> ' . $message;
                    echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
                    echo '<span aria-hidden="true">&times;</span>';
                    echo '</button>';
                    echo '</div>';
                }
                ?>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0"><i class="fa fa-list"></i> Medicine Inventory</h4>
                    <a href="add-product.php" class="add-medicine-btn">
                        <i class="fa fa-plus"></i> Add Medicine
                    </a>
                </div>

                <div class="table-container">
                    <div class="table-responsive">
                        <table id="medicineTable" class="table table-bordered table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th>Medicine Name</th>
                                    <th>Description</th>
                                    <th>Pharmacy</th>
                                    <th>Price (RWF)</th>
                                    <th>Stock</th>
                                    <th>Expiry</th>
                                    <th>Restricted</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($medicines_data)): ?>
                                    <?php foreach ($medicines_data as $medicine): ?>
                                        <tr>
                                            <td class="text-center"><?php echo $medicine['medicine_id']; ?></td>
                                            <td><?php echo htmlspecialchars($medicine['name']); ?></td>
                                            <td><?php echo htmlspecialchars($medicine['description'] ?: 'No description'); ?></td>
                                            <td><?php echo $medicine['pharmacy_name'] ?: 'No pharmacy'; ?></td>
                                            <td>RWF <?php echo number_format($medicine['price'], 0); ?></td>
                                            <td>
                                                <?php 
                                                $stock = $medicine['stock_quantity'];
                                                if ($stock <= 5) {
                                                    echo '<span class="badge badge-danger badge-custom">' . $stock . '</span>';
                                                } elseif ($stock <= 10) {
                                                    echo '<span class="badge badge-warning badge-custom">' . $stock . '</span>';
                                                } else {
                                                    echo '<span class="badge badge-success badge-custom">' . $stock . '</span>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php 
                                                $expiry = $medicine['expiry_date'];
                                                if (!$expiry || $expiry === 'N/A') {
                                                    echo '<span class="badge badge-secondary badge-custom">N/A</span>';
                                                } else {
                                                    $expiryDate = new DateTime($expiry);
                                                    $today = new DateTime();
                                                    $diffTime = $expiryDate->getTimestamp() - $today->getTimestamp();
                                                    $diffDays = ceil($diffTime / (60 * 60 * 24));
                                                    
                                                    if ($diffDays < 0) {
                                                        echo '<span class="badge badge-danger badge-custom">Expired</span>';
                                                    } elseif ($diffDays <= 30) {
                                                        echo '<span class="badge badge-warning badge-custom">' . $diffDays . ' days</span>';
                                                    } else {
                                                        echo '<span class="badge badge-success badge-custom">' . $diffDays . ' days</span>';
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php 
                                                if ($medicine['Restricted_Medicine'] == 1) {
                                                    echo '<span class="badge badge-danger badge-custom">Yes</span>';
                                                } else {
                                                    echo '<span class="badge badge-secondary badge-custom">No</span>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <a href="add-product.php?edit=<?php echo $medicine['medicine_id']; ?>" class="btn btn-xs btn-primary action-btn" title="Edit">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                <a href="javascript:void(0)" onclick="deleteMedicine(<?php echo $medicine['medicine_id']; ?>)" class="btn btn-xs btn-danger action-btn" title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">No medicines found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('./constant/layout/footer.php');?>

<script>
$(document).ready(function() {
    // Initialize DataTable with existing data
    if (typeof $.fn.DataTable !== 'undefined') {
        var table = $('#medicineTable').DataTable({
            order: [[0,'desc']],
            lengthMenu: [[10,25,50,100],[10,25,50,100]],
            dom: 'Bfrtip',
            buttons: [
                { extend:'copy', className:'btn btn-sm btn-secondary' },
                { extend:'csv', className:'btn btn-sm btn-secondary' },
                { extend:'excel', className:'btn btn-sm btn-secondary' },
                { extend:'print', className:'btn btn-sm btn-secondary' }
            ],
            pageLength: 10,
            responsive: true
        });

        // Move buttons to the header tools container
        table.on('init', function(){
            var $btns = $('.dt-buttons').addClass('btn-group btn-group-sm');
            $('.card-body').prepend('<div id="medicineTableTools" class="mb-3"></div>');
            $('#medicineTableTools').append($btns);
        });
    }

    // Add delete medicine function
    window.deleteMedicine = function(id) {
        if (confirm('Are you sure you want to delete this medicine? This action cannot be undone.')) {
            // Create a form to submit the delete request
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = 'php_action/delete_medicine.php';
            
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'medicine_id';
            input.value = id;
            
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    };
});
</script>


