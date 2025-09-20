<?php 
// Start session and check authentication first
include('./constant/check.php');
include('./constant/connect.php');

// Log view activity
require_once 'activity_logger.php';
logView($_SESSION['adminId'], 'cart', 'Viewed shopping cart');
?>
<?php include('./constant/layout/head.php');?>
<?php include('./constant/layout/header.php');?>
<?php include('./constant/layout/sidebar.php');?>

<?php
$user_id = $_SESSION['adminId'];

// Fetch cart items with medicine details
$cart_sql = "SELECT 
                c.cart_id,
                c.quantity,
                c.date_added,
                m.medicine_id,
                m.name,
                m.description,
                m.price,
                m.stock_quantity,
                m.expiry_date,
                p.name as pharmacy_name,
                (c.quantity * m.price) as item_total
            FROM cart c
            JOIN medicines m ON c.medicine_id = m.medicine_id
            LEFT JOIN pharmacies p ON m.pharmacy_id = p.pharmacy_id
            WHERE c.user_id = ?
            ORDER BY c.date_added DESC";

$cart_stmt = $connect->prepare($cart_sql);
$cart_stmt->bind_param("i", $user_id);
$cart_stmt->execute();
$cart_result = $cart_stmt->get_result();

$cart_items = [];
$cart_total = 0;

while ($row = $cart_result->fetch_assoc()) {
    $cart_items[] = $row;
    $cart_total += $row['item_total'];
}
?>

<style>
.cart-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 30px;
    text-align: center;
}

.cart-header h3 {
    margin-bottom: 10px;
    font-weight: bold;
}

.cart-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    overflow: hidden;
}

.cart-item {
    border: 1px solid #eee;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 15px;
    background: white;
    transition: all 0.3s ease;
}

.cart-item:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.item-image {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
}

.quantity-controls {
    display: flex;
    align-items: center;
    gap: 10px;
}

.quantity-btn {
    width: 35px;
    height: 35px;
    border: 1px solid #ddd;
    background: white;
    border-radius: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.quantity-btn:hover {
    background: #f8f9fa;
    border-color: #007bff;
}

.quantity-input {
    width: 60px;
    text-align: center;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 8px;
}

.cart-summary {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 25px;
    position: sticky;
    top: 20px;
}

.checkout-btn {
    background: linear-gradient(135deg, #28a745, #20c997);
    border: none;
    color: white;
    padding: 15px 30px;
    border-radius: 25px;
    font-weight: 500;
    width: 100%;
    font-size: 1.1rem;
    transition: all 0.3s ease;
}

.checkout-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
    color: white;
}

.empty-cart {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
}

.empty-cart i {
    font-size: 4rem;
    margin-bottom: 20px;
}

.remove-btn {
    color: #dc3545;
    cursor: pointer;
    font-size: 1.2rem;
    transition: all 0.3s ease;
}

.remove-btn:hover {
    color: #c82333;
    transform: scale(1.1);
}

.continue-shopping {
    background: linear-gradient(135deg, #007bff, #0056b3);
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
    box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
    color: white;
    text-decoration: none;
}
</style>

<div class="page-wrapper">
    <div class="container-fluid">
        <!-- Cart Header -->
        <div class="cart-header">
            <h3><i class="fa fa-shopping-cart"></i> Shopping Cart</h3>
            <p>Review your selected medicines before checkout</p>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="cart-card">
                    <div class="card-body">
                        <?php if (empty($cart_items)): ?>
                            <div class="empty-cart">
                                <i class="fa fa-shopping-cart"></i>
                                <h4>Your cart is empty</h4>
                                <p>Start shopping to add medicines to your cart</p>
                                <a href="product.php" class="continue-shopping mt-3">
                                    <i class="fa fa-arrow-left"></i> Continue Shopping
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5><i class="fa fa-list"></i> Cart Items (<?php echo count($cart_items); ?>)</h5>
                                <a href="product.php" class="continue-shopping">
                                    <i class="fa fa-arrow-left"></i> Continue Shopping
                                </a>
                            </div>

                            <?php foreach ($cart_items as $item): ?>
                                <div class="cart-item" data-cart-id="<?php echo $item['cart_id']; ?>">
                                    <div class="row align-items-center">
                                        <div class="col-md-1">
                                            <div class="item-image">
                                                <i class="fa fa-medkit"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                            <small class="text-muted"><?php echo htmlspecialchars($item['pharmacy_name'] ?: 'No pharmacy'); ?></small>
                                            <br><small class="text-muted">Stock: <?php echo $item['stock_quantity']; ?> units</small>
                                        </div>
                                        <div class="col-md-2">
                                            <strong>RWF <?php echo number_format($item['price'], 0); ?></strong>
                                            <br><small class="text-muted">per unit</small>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="quantity-controls">
                                                <button type="button" class="quantity-btn" onclick="updateQuantity(<?php echo $item['cart_id']; ?>, -1)">-</button>
                                                <input type="number" class="quantity-input" value="<?php echo $item['quantity']; ?>" 
                                                       min="1" max="<?php echo $item['stock_quantity']; ?>" 
                                                       onchange="updateQuantity(<?php echo $item['cart_id']; ?>, 0, this.value)">
                                                <button type="button" class="quantity-btn" onclick="updateQuantity(<?php echo $item['cart_id']; ?>, 1)">+</button>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <strong class="item-total">RWF <?php echo number_format($item['item_total'], 0); ?></strong>
                                        </div>
                                        <div class="col-md-1">
                                            <i class="fa fa-trash remove-btn" onclick="removeFromCart(<?php echo $item['cart_id']; ?>)" title="Remove item"></i>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if (!empty($cart_items)): ?>
                <div class="col-lg-4">
                    <div class="cart-summary">
                        <h5 class="mb-4"><i class="fa fa-calculator"></i> Order Summary</h5>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="cart-subtotal">RWF <?php echo number_format($cart_total, 0); ?></span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Delivery Fee:</span>
                            <span>RWF 0</span>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-4">
                            <strong>Total:</strong>
                            <strong id="cart-total">RWF <?php echo number_format($cart_total, 0); ?></strong>
                        </div>
                        
                        <button type="button" class="checkout-btn" onclick="proceedToCheckout()">
                            <i class="fa fa-credit-card"></i> Proceed to Checkout
                        </button>
                        
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fa fa-shield"></i> Secure checkout with SSL encryption
                            </small>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include('./constant/layout/footer.php');?>

<script>
function updateQuantity(cartId, change, newValue = null) {
    let quantity;
    
    if (newValue !== null) {
        quantity = parseInt(newValue);
    } else {
        const cartItem = document.querySelector(`[data-cart-id="${cartId}"]`);
        const quantityInput = cartItem.querySelector('.quantity-input');
        const currentQuantity = parseInt(quantityInput.value);
        const maxQuantity = parseInt(quantityInput.max);
        
        quantity = Math.max(1, Math.min(maxQuantity, currentQuantity + change));
    }
    
    // Send AJAX request to update quantity
    $.ajax({
        url: 'php_action/update_cart_quantity.php',
        method: 'POST',
        data: {
            cart_id: cartId,
            quantity: quantity
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Update the UI
                const cartItem = document.querySelector(`[data-cart-id="${cartId}"]`);
                const quantityInput = cartItem.querySelector('.quantity-input');
                const itemTotal = cartItem.querySelector('.item-total');
                
                quantityInput.value = quantity;
                itemTotal.textContent = 'RWF ' + response.item_total.toLocaleString();
                
                // Update cart totals
                document.getElementById('cart-subtotal').textContent = 'RWF ' + response.cart_total.toLocaleString();
                document.getElementById('cart-total').textContent = 'RWF ' + response.cart_total.toLocaleString();
                
                showNotification('Cart updated successfully!', 'success');
            } else {
                showNotification(response.message || 'Failed to update cart', 'error');
            }
        },
        error: function() {
            showNotification('An error occurred while updating cart', 'error');
        }
    });
}

function removeFromCart(cartId) {
    if (confirm('Are you sure you want to remove this item from your cart?')) {
        $.ajax({
            url: 'php_action/remove_from_cart.php',
            method: 'POST',
            data: {
                cart_id: cartId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Remove the item from UI
                    const cartItem = document.querySelector(`[data-cart-id="${cartId}"]`);
                    cartItem.remove();
                    
                    // Update cart totals or show empty cart if no items left
                    if (response.cart_total > 0) {
                        document.getElementById('cart-subtotal').textContent = 'RWF ' + response.cart_total.toLocaleString();
                        document.getElementById('cart-total').textContent = 'RWF ' + response.cart_total.toLocaleString();
                    } else {
                        location.reload(); // Reload to show empty cart
                    }
                    
                    showNotification('Item removed from cart successfully!', 'success');
                } else {
                    showNotification(response.message || 'Failed to remove item from cart', 'error');
                }
            },
            error: function() {
                showNotification('An error occurred while removing item from cart', 'error');
            }
        });
    }
}

function proceedToCheckout() {
    // Here you can redirect to a checkout page or show a checkout modal
    window.location.href = 'checkout.php';
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