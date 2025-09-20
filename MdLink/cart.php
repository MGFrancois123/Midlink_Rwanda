<?php 
// Start session and check authentication first
include('./constant/check.php');
include('./constant/connect.php');

// Log view activity
require_once 'activity_logger.php';
logView($_SESSION['adminId'], 'cart', 'Viewed shopping cart');
?>
<?php include('./constant/layout/head.php');?>
<!-- Add Flutterwave script -->
<script src="https://checkout.flutterwave.com/v3.js"></script>
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

// Assuming user email is stored in session; adjust as needed based on your auth system
$user_email = isset($_SESSION['email']) ? $_SESSION['email'] : 'customer@passtrack.com'; // Fallback to reference email
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

/* Payment Modal Styles */
.payment-modal .modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.payment-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin: 20px 0;
}

.payment-btn {
    padding: 15px 20px;
    border: 2px solid #e1e5eb;
    border-radius: 10px;
    background: white;
    color: #495057;
    font-weight: 500;
    transition: all 0.3s ease;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.payment-btn:hover {
    border-color: #667eea;
    background: #f8f9ff;
    color: #667eea;
    transform: translateY(-2px);
}

.payment-btn.card-payment {
    border-color: #007bff;
    color: #007bff;
}

.payment-btn.mobile-payment {
    border-color: #28a745;
    color: #28a745;
}

.payment-btn:hover.card-payment {
    background: #007bff;
    color: white;
}

.payment-btn:hover.mobile-payment {
    background: #28a745;
    color: white;
}

.cart-summary-modal {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    margin: 15px 0;
}

.cart-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1050;
    min-width: 300px;
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

<!-- Payment Modal -->
<div class="modal fade payment-modal" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">
                    <i class="fa fa-credit-card"></i> Choose Payment Method
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="cart-summary-modal">
                    <h6 class="mb-3"><i class="fa fa-shopping-cart"></i> Order Summary</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span id="modal-cart-subtotal">RWF <?php echo number_format($cart_total, 0); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Delivery Fee:</span>
                        <span>RWF 0</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Total Amount:</strong>
                        <strong id="modal-cart-total" class="text-success">RWF <?php echo number_format($cart_total, 0); ?></strong>
                    </div>
                </div>
                
                <h6 class="mb-3">Select Payment Method:</h6>
                <div class="payment-options">
                    <button type="button" class="payment-btn card-payment" onclick="payWithCard()">
                        <i class="fa fa-credit-card"></i>
                        <div>
                            <div>Pay with Card</div>
                            <small>Visa, Mastercard, etc.</small>
                        </div>
                    </button>
                    <button type="button" class="payment-btn mobile-payment" onclick="payWithMobile()">
                        <i class="fa fa-mobile"></i>
                        <div>
                            <div>Mobile Money</div>
                            <small>MTN, Airtel Money</small>
                        </div>
                    </button>
                </div>
                
                <div class="text-center mt-3">
                    <small class="text-muted">
                        <i class="fa fa-lock"></i> Your payment information is secure and encrypted
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Cart Notification -->
<div id="cartNotification" class="cart-notification"></div>

<!-- Hidden user email for JS -->
<input type="hidden" id="userEmail" value="<?php echo htmlspecialchars($user_email); ?>">

<?php include('./constant/layout/footer.php');?>

<script>
// Cart data for JavaScript
let cartData = <?php echo json_encode($cart_items); ?>;
let cartTotal = <?php echo $cart_total; ?>;

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
                document.getElementById('modal-cart-subtotal').textContent = 'RWF ' + response.cart_total.toLocaleString();
                document.getElementById('modal-cart-total').textContent = 'RWF ' + response.cart_total.toLocaleString();
                
                // Update global cart total
                cartTotal = response.cart_total;
                
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
                        document.getElementById('modal-cart-subtotal').textContent = 'RWF ' + response.cart_total.toLocaleString();
                        document.getElementById('modal-cart-total').textContent = 'RWF ' + response.cart_total.toLocaleString();
                        cartTotal = response.cart_total;
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
    if (cartTotal <= 0) {
        showNotification('Your cart is empty!', 'error');
        return;
    }
    $('#paymentModal').modal('show');
}

// Payment functions
function payWithCard() {
    if (cartTotal <= 0) {
        showNotification('Your cart is empty!', 'error');
        return;
    }

    const email = document.getElementById('userEmail').value;
    const tx_ref = Date.now().toString() + Math.floor(Math.random() * 1000); // Simple unique ref

    FlutterwaveCheckout({
        public_key: "FLWPUBK_TEST-ab0db75066081fdc2501e5eb2cf42da1-X",
        tx_ref: tx_ref,
        amount: cartTotal,
        currency: "RWF",
        payment_options: "card",
        redirect_url: "https://your-website.com/redirect", // Replace with your actual redirect URL if needed
        customer: {
            email: email,
        },
        customizations: {
            title: "Purchase Medicines",
            description: "Payment for cart items",
        },
        callback: function (data) {
            if (data.status === "successful") {
                verifyCartPayment(data.transaction_id, 'card');
            } else {
                showNotification('Payment failed: ' + data.status, 'error');
            }
        },
        onclose: function() {
            // Optional: Handle modal close if needed
        },
    });
}

function payWithMobile() {
    if (cartTotal <= 0) {
        showNotification('Your cart is empty!', 'error');
        return;
    }

    const email = document.getElementById('userEmail').value;
    const tx_ref = Date.now().toString() + Math.floor(Math.random() * 1000); // Simple unique ref

    FlutterwaveCheckout({
        public_key: "FLWPUBK_TEST-ab0db75066081fdc2501e5eb2cf42da1-X",
        tx_ref: tx_ref,
        amount: cartTotal,
        currency: "RWF",
        payment_options: "mobilemoneyrwanda",
        redirect_url: "https://your-website.com/redirect", // Replace with your actual redirect URL if needed
        customer: {
            email: email,
        },
        customizations: {
            title: "Purchase Medicines",
            description: "Payment for cart items",
        },
        callback: function (data) {
            if (data.status === "successful") {
                verifyCartPayment(data.transaction_id, 'mobile_money');
            } else {
                showNotification('Payment failed: ' + data.status, 'error');
            }
        },
        onclose: function() {
            // Optional: Handle modal close if needed
        },
    });
}

// Verify cart payment
function verifyCartPayment(transaction_id, payment_method) {
    $.ajax({
        url: 'php_action/process_cart_payment.php',
        method: 'POST',
        data: {
            payment_method: payment_method,
            total_amount: cartTotal,
            transaction_id: transaction_id
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#paymentModal').modal('hide');
                showNotification('Payment successful! Your order has been placed.', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                showNotification(response.message || 'Payment failed. Please try again.', 'error');
            }
        },
        error: function() {
            showNotification('An error occurred while processing payment', 'error');
        }
    });
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