<?php 
// Start session and check authentication first
include('./constant/check.php');
include('./constant/connect.php');

// Log view activity
require_once 'activity_logger.php';
logView($_SESSION['adminId'], 'order_history', 'Viewed order history');
?>
<?php include('./constant/layout/head.php');?>
<?php include('./constant/layout/header.php');?>
<?php include('./constant/layout/sidebar.php');?>

<?php
$user_id = $_SESSION['adminId'];

// Fetch order history with order items
$orders_sql = "SELECT 
                    o.order_id,
                    o.order_number,
                    o.total_amount,
                    o.payment_method,
                    o.payment_status,
                    o.order_status,
                    o.order_date,
                    COUNT(oi.item_id) as item_count
                FROM order_history o
                LEFT JOIN order_items oi ON o.order_id = oi.order_id
                WHERE o.user_id = ?
                GROUP BY o.order_id
                ORDER BY o.order_date DESC";

$orders_stmt = $connect->prepare($orders_sql);
$orders_stmt->bind_param("i", $user_id);
$orders_stmt->execute();
$orders_result = $orders_stmt->get_result();

$orders = [];
while ($row = $orders_result->fetch_assoc()) {
    $orders[] = $row;
}

// Calculate statistics
$total_orders = count($orders);
$completed_orders = 0;
$pending_orders = 0;
$total_spent = 0;

foreach ($orders as $order) {
    if ($order['payment_status'] === 'completed') {
        $completed_orders++;
    }
    if ($order['order_status'] === 'pending') {
        $pending_orders++;
    }
    $total_spent += $order['total_amount'];
}
?>

<style>
.order-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 30px;
    text-align: center;
}

.order-header h3 {
    margin-bottom: 10px;
    font-weight: bold;
}

.order-header p {
    opacity: 0.9;
    margin-bottom: 0;
}

.order-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    overflow: hidden;
}

.order-item {
    border: 1px solid #eee;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 15px;
    background: white;
    transition: all 0.3s ease;
}

.order-item:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.order-number {
    font-weight: bold;
    color: #007bff;
    font-size: 1.1rem;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.status-completed { background-color: #d4edda; color: #155724; }
.status-pending { background-color: #fff3cd; color: #856404; }
.status-processing { background-color: #d1ecf1; color: #0c5460; }
.status-shipped { background-color: #e2e3e5; color: #383d41; }
.status-delivered { background-color: #d4edda; color: #155724; }
.status-cancelled { background-color: #f8d7da; color: #721c24; }
.status-failed { background-color: #f8d7da; color: #721c24; }

.stat-item {
    padding: 15px;
    border-radius: 10px;
    background: #f8f9fa;
    min-width: 120px;
    text-align: center;
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

.view-details-btn {
    background: linear-gradient(135deg, #007bff, #0056b3);
    border: none;
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.view-details-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
    color: white;
}

.payment-method {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 8px;
    background: #f8f9fa;
    border-radius: 15px;
    font-size: 0.8rem;
}

.empty-orders {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
}

.empty-orders i {
    font-size: 4rem;
    margin-bottom: 20px;
}

.continue-shopping {
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

.continue-shopping:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
    color: white;
    text-decoration: none;
}

.order-details-modal .modal-content {
    border-radius: 15px;
}

.order-details-modal .modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-bottom: none;
    border-radius: 15px 15px 0 0;
}

.order-item-row {
    padding: 15px;
    border-bottom: 1px solid #eee;
}

.order-item-row:last-child {
    border-bottom: none;
}
</style>

<div class="page-wrapper">
    <div class="container-fluid">
        <!-- Order Header -->
        <div class="order-header">
            <h3><i class="fa fa-history"></i> Order History</h3>
            <p>Track all your medicine orders and purchases</p>
        </div>

        <!-- Statistics Row -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-item btn-info">
                    <span class="stat-number"><?php echo $total_orders; ?></span>
                    <span class="stat-label">Total Orders</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-item btn-success">
                    <span class="stat-number"><?php echo $completed_orders; ?></span>
                    <span class="stat-label">Completed</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-item btn-warning">
                    <span class="stat-number"><?php echo $pending_orders; ?></span>
                    <span class="stat-label">Pending</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-item btn-primary">
                    <span class="stat-number">RWF <?php echo number_format($total_spent, 0); ?></span>
                    <span class="stat-label">Total Spent</span>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="order-card">
            <div class="card-body">
                <?php if (empty($orders)): ?>
                    <div class="empty-orders">
                        <i class="fa fa-clipboard-list"></i>
                        <h4>No orders found</h4>
                        <p>You haven't placed any orders yet</p>
                        <a href="product.php" class="continue-shopping mt-3">
                            <i class="fa fa-shopping-cart"></i> Start Shopping
                        </a>
                    </div>
                <?php else: ?>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5><i class="fa fa-list"></i> Your Orders (<?php echo $total_orders; ?>)</h5>
                        <a href="product.php" class="continue-shopping">
                            <i class="fa fa-plus"></i> Order More
                        </a>
                    </div>

                    <?php foreach ($orders as $order): ?>
                        <div class="order-item">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <div class="order-number"><?php echo htmlspecialchars($order['order_number']); ?></div>
                                    <small class="text-muted">
                                        <?php echo date('M j, Y g:i A', strtotime($order['order_date'])); ?>
                                    </small>
                                </div>
                                
                                <div class="col-md-2">
                                    <strong>RWF <?php echo number_format($order['total_amount'], 0); ?></strong>
                                    <br><small class="text-muted"><?php echo $order['item_count']; ?> item(s)</small>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="payment-method">
                                        <?php if ($order['payment_method'] === 'card'): ?>
                                            <i class="fa fa-credit-card"></i> Card
                                        <?php else: ?>
                                            <i class="fa fa-mobile"></i> Mobile Money
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <span class="status-badge status-<?php echo $order['payment_status']; ?>">
                                        <?php echo ucfirst($order['payment_status']); ?>
                                    </span>
                                </div>
                                
                                <div class="col-md-2">
                                    <span class="status-badge status-<?php echo $order['order_status']; ?>">
                                        <?php echo ucfirst($order['order_status']); ?>
                                    </span>
                                </div>
                                
                                <div class="col-md-1">
                                    <button type="button" class="view-details-btn" onclick="viewOrderDetails(<?php echo $order['order_id']; ?>)">
                                        <i class="fa fa-eye"></i> View
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Order Details Modal -->
<div class="modal fade order-details-modal" id="orderDetailsModal" tabindex="-1" role="dialog" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderDetailsModalLabel">
                    <i class="fa fa-clipboard-list"></i> Order Details
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="orderDetailsContent">
                    <!-- Order details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php include('./constant/layout/footer.php');?>

<script>
function viewOrderDetails(orderId) {
    // Show loading
    document.getElementById('orderDetailsContent').innerHTML = '<div class="text-center p-4"><i class="fa fa-spinner fa-spin"></i> Loading...</div>';
    
    // Load order details via AJAX
    $.ajax({
        url: 'php_action/get_order_details.php',
        method: 'POST',
        data: { order_id: orderId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayOrderDetails(response.data);
            } else {
                document.getElementById('orderDetailsContent').innerHTML = '<div class="alert alert-danger">Failed to load order details</div>';
            }
        },
        error: function() {
            document.getElementById('orderDetailsContent').innerHTML = '<div class="alert alert-danger">An error occurred while loading order details</div>';
        }
    });
    
    $('#orderDetailsModal').modal('show');
}

function displayOrderDetails(orderData) {
    const order = orderData.order;
    const items = orderData.items;
    
    let html = `
        <div class="row mb-4">
            <div class="col-md-6">
                <h6>Order Information</h6>
                <p><strong>Order Number:</strong> ${order.order_number}</p>
                <p><strong>Order Date:</strong> ${new Date(order.order_date).toLocaleDateString()}</p>
                <p><strong>Payment Method:</strong> ${order.payment_method === 'card' ? 'Credit Card' : 'Mobile Money'}</p>
            </div>
            <div class="col-md-6">
                <h6>Status</h6>
                <p><strong>Payment Status:</strong> <span class="status-badge status-${order.payment_status}">${order.payment_status}</span></p>
                <p><strong>Order Status:</strong> <span class="status-badge status-${order.order_status}">${order.order_status}</span></p>
                <p><strong>Total Amount:</strong> <strong>RWF ${parseFloat(order.total_amount).toLocaleString()}</strong></p>
            </div>
        </div>
        
        <h6>Order Items</h6>
        <div class="border rounded">
    `;
    
    items.forEach(item => {
        html += `
            <div class="order-item-row">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <strong>${item.medicine_name}</strong>
                        <br><small class="text-muted">${item.pharmacy_name || 'No pharmacy'}</small>
                    </div>
                    <div class="col-md-2">
                        <span>Qty: ${item.quantity}</span>
                    </div>
                    <div class="col-md-2">
                        <span>RWF ${parseFloat(item.unit_price).toLocaleString()}</span>
                    </div>
                    <div class="col-md-2">
                        <strong>RWF ${parseFloat(item.total_price).toLocaleString()}</strong>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    
    document.getElementById('orderDetailsContent').innerHTML = html;
}

// Add some additional functionality for order management
function reorderItems(orderId) {
    if (confirm('Add all items from this order to your cart?')) {
        $.ajax({
            url: 'php_action/reorder_items.php',
            method: 'POST',
            data: { order_id: orderId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showNotification('Items added to cart successfully!', 'success');
                } else {
                    showNotification(response.message || 'Failed to add items to cart', 'error');
                }
            },
            error: function() {
                showNotification('An error occurred while adding items to cart', 'error');
            }
        });
    }
}

function showNotification(message, type) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    
    const notification = $(`
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
            <i class="fa ${icon}"></i> ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `);
    
    $('body').append(notification);
    
    // Auto-remove notification after 5 seconds
    setTimeout(() => {
        notification.fadeOut();
    }, 5000);
}
</script>