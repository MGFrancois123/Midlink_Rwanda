<?php 
// Start session and check authentication first
include('./constant/check.php');
include('./constant/connect.php');

// Log view activity
require_once 'activity_logger.php';
logView($_SESSION['adminId'], 'medicines', 'Viewed medicine catalog');
?>
<?php include('./constant/layout/head.php');?>
<!-- Add Flutterwave script -->
<script src="https://checkout.flutterwave.com/v3.js"></script>
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

// Assuming user email is stored in session; adjust as needed based on your auth system
$user_email = isset($_SESSION['email']) ? $_SESSION['email'] : 'customer@passtrack.com'; // Fallback to reference email
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
    margin-bottom: 5px;
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

.btn-success.action-btn {
    background: linear-gradient(135deg, #28a745, #20c997);
    border: none;
    transition: all 0.3s ease;
}

.btn-success.action-btn:hover {
    background: linear-gradient(135deg, #20c997, #17a2b8);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
}

.btn-warning.action-btn {
    background: linear-gradient(135deg, #ffc107, #e0a800);
    border: none;
    color: #000;
    transition: all 0.3s ease;
}

.btn-warning.action-btn:hover {
    background: linear-gradient(135deg, #e0a800, #d39e00);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
}

/* Modal Styles */
.modal-content {
    border-radius: 15px;
    border: none;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
}

.modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-bottom: none;
    border-radius: 15px 15px 0 0;
}

.modal-footer {
    border-top: 1px solid #eee;
    padding: 20px;
}

.product-detail-card {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    margin: 15px 0;
}

.quantity-selector {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 15px 0;
}

.quantity-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 1px solid #ddd;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.quantity-input {
    width: 80px;
    text-align: center;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 8px;
}

.payment-options {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.payment-btn {
    flex: 1;
    min-width: 150px;
    padding: 12px 20px;
    border-radius: 25px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.cart-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    min-width: 300px;
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
                        case 'added_to_cart':
                            $message = 'Medicine added to cart successfully!';
                            break;
                        case 'order_placed':
                            $message = 'Order placed successfully!';
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
                        case 'insufficient_stock':
                            $message = 'Insufficient stock for the requested quantity.';
                            break;
                        case 'cart_add_failed':
                            $message = 'Failed to add item to cart. Please try again.';
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
                                                <a href="javascript:void(0)" onclick="buyMedicine(<?php echo $medicine['medicine_id']; ?>)" class="btn btn-xs btn-success action-btn" title="Buy">
                                                    <i class="fa fa-shopping-bag"></i>
                                                </a>
                                                <a href="javascript:void(0)" onclick="addToCart(<?php echo $medicine['medicine_id']; ?>)" class="btn btn-xs btn-warning action-btn" title="Add to Cart">
                                                    <i class="fa fa-cart-plus"></i>
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

<!-- Buy Medicine Modal -->
<div class="modal fade" id="buyMedicineModal" tabindex="-1" role="dialog" aria-labelledby="buyMedicineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="buyMedicineModalLabel">
                    <i class="fa fa-shopping-bag"></i> Purchase Medicine
                </h5>
                <!-- <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button> -->
            </div>
            <div class="modal-body">
                <div id="medicineDetails" class="product-detail-card">
                    <!-- Medicine details will be loaded here -->
                </div>
                
                <div class="quantity-selector">
                    <label for="quantity" class="font-weight-bold">Quantity:</label>
                    <button type="button" class="quantity-btn" onclick="decreaseQuantity()">-</button>
                    <input type="number" id="quantity" class="quantity-input" value="1" min="1" max="100">
                    <button type="button" class="quantity-btn" onclick="increaseQuantity()">+</button>
                </div>
                
                <div class="total-price">
                    <h5>Total: <span id="totalPrice" class="text-success">RWF 0</span></h5>
                </div>
            </div>
            <div class="modal-footer">
                <div class="payment-options w-100">
                    <button type="button" class="btn btn-primary payment-btn" onclick="payWithCard()">
                        <i class="fa fa-credit-card"></i> Pay with Card
                    </button>
                    <button type="button" class="btn btn-info payment-btn" onclick="payWithMobile()">
                        <i class="fa fa-mobile"></i> Pay with Mobile Money
                    </button>
                    <button type="button" class="btn btn-warning payment-btn" onclick="addToCartFromModal()">
                        <i class="fa fa-cart-plus"></i> Add to Cart
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="window.location.reload()">Cancel</button>
                </div>
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
});

// Global variables for medicine data
let currentMedicine = null;
let medicineData = <?php echo json_encode($medicines_data); ?>;

// Delete medicine function
function deleteMedicine(id) {
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
}

// Buy medicine function
function buyMedicine(medicineId) {
    currentMedicine = medicineData.find(med => med.medicine_id == medicineId);
    if (!currentMedicine) {
        alert('Medicine not found!');
        return;
    }
    
    // Populate modal with medicine details
    const detailsHtml = `
        <div class="row">
            <div class="col-md-6">
                <h5 class="text-primary">${currentMedicine.name}</h5>
                <p><strong>Description:</strong> ${currentMedicine.description || 'No description'}</p>
                <p><strong>Pharmacy:</strong> ${currentMedicine.pharmacy_name}</p>
                <p><strong>Stock Available:</strong> ${currentMedicine.stock_quantity} units</p>
            </div>
            <div class="col-md-6">
                <p><strong>Unit Price:</strong> RWF ${currentMedicine.price.toLocaleString()}</p>
                <p><strong>Expiry Date:</strong> ${currentMedicine.expiry_date || 'N/A'}</p>
                <p><strong>Restricted:</strong> ${currentMedicine.Restricted_Medicine ? 'Yes' : 'No'}</p>
            </div>
        </div>
    `;
    
    document.getElementById('medicineDetails').innerHTML = detailsHtml;
    document.getElementById('quantity').max = currentMedicine.stock_quantity;
    updateTotalPrice();
    
    $('#buyMedicineModal').modal('show');
}

// Add to cart function
function addToCart(medicineId) {
    const medicine = medicineData.find(med => med.medicine_id == medicineId);
    if (!medicine) {
        alert('Medicine not found!');
        return;
    }
    
    // Send AJAX request to add to cart
    $.ajax({
        url: 'php_action/add_to_cart.php',
        method: 'POST',
        data: {
            medicine_id: medicineId,
            quantity: 1
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showNotification('Medicine added to cart successfully!', 'success');
            } else {
                showNotification(response.message || 'Failed to add medicine to cart', 'error');
            }
        },
        error: function() {
            showNotification('An error occurred while adding to cart', 'error');
        }
    });
}

// Quantity control functions
function increaseQuantity() {
    const quantityInput = document.getElementById('quantity');
    const currentValue = parseInt(quantityInput.value);
    const maxValue = parseInt(quantityInput.max);
    
    if (currentValue < maxValue) {
        quantityInput.value = currentValue + 1;
        updateTotalPrice();
    }
}

function decreaseQuantity() {
    const quantityInput = document.getElementById('quantity');
    const currentValue = parseInt(quantityInput.value);
    
    if (currentValue > 1) {
        quantityInput.value = currentValue - 1;
        updateTotalPrice();
    }
}

// Update total price
function updateTotalPrice() {
    if (currentMedicine) {
        const quantity = parseInt(document.getElementById('quantity').value);
        const total = currentMedicine.price * quantity;
        document.getElementById('totalPrice').textContent = `RWF ${total.toLocaleString()}`;
    }
}

// Payment functions (integrated with Flutterwave for both card and mobile money)
function payWithCard() {
    const quantity = parseInt(document.getElementById('quantity').value);
    const total = currentMedicine.price * quantity;
    const email = document.getElementById('userEmail').value;
    const tx_ref = Date.now().toString() + Math.floor(Math.random() * 1000); // Simple unique ref

    FlutterwaveCheckout({
        public_key: "FLWPUBK_TEST-ab0db75066081fdc2501e5eb2cf42da1-X",
        tx_ref: tx_ref,
        amount: total,
        currency: "RWF",
        payment_options: "card",
        redirect_url: "https://your-website.com/redirect", // Replace with your actual redirect URL if needed
        customer: {
            email: email,
        },
        customizations: {
            title: "Purchase Medicine",
            description: `Payment for ${currentMedicine.name}`,
        },
        callback: function (data) {
            if (data.status === "successful") {
                verifyPayment(data.transaction_id, quantity, total, 'card');
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
    const quantity = parseInt(document.getElementById('quantity').value);
    const total = currentMedicine.price * quantity;
    const email = document.getElementById('userEmail').value;
    const tx_ref = Date.now().toString() + Math.floor(Math.random() * 1000); // Simple unique ref

    FlutterwaveCheckout({
        public_key: "FLWPUBK_TEST-ab0db75066081fdc2501e5eb2cf42da1-X",
        tx_ref: tx_ref,
        amount: total,
        currency: "RWF",
        payment_options: "mobilemoneyrwanda",
        redirect_url: "https://your-website.com/redirect", // Replace with your actual redirect URL if needed
        customer: {
            email: email,
        },
        customizations: {
            title: "Purchase Medicine",
            description: `Payment for ${currentMedicine.name}`,
        },
        callback: function (data) {
            if (data.status === "successful") {
                verifyPayment(data.transaction_id, quantity, total, 'mobile_money');
            } else {
                showNotification('Payment failed: ' + data.status, 'error');
            }
        },
        onclose: function() {
            // Optional: Handle modal close if needed
        },
    });
}

function addToCartFromModal() {
    const quantity = parseInt(document.getElementById('quantity').value);
    
    $.ajax({
        url: 'php_action/add_to_cart.php',
        method: 'POST',
        data: {
            medicine_id: currentMedicine.medicine_id,
            quantity: quantity
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#buyMedicineModal').modal('hide');
                showNotification(`${quantity} units of ${currentMedicine.name} added to cart!`, 'success');
            } else {
                showNotification(response.message || 'Failed to add medicine to cart', 'error');
            }
        },
        error: function() {
            showNotification('An error occurred while adding to cart', 'error');
        }
    });
}

// Verify payment on server
function verifyPayment(transaction_id, quantity, total, payment_method) {
    $.ajax({
        url: 'php_action/process_payment.php',
        method: 'POST',
        data: {
            medicine_id: currentMedicine.medicine_id,
            quantity: quantity,
            payment_method: payment_method,
            total_amount: total,
            transaction_id: transaction_id
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#buyMedicineModal').modal('hide');
                showNotification(`Payment successful! Order placed for ${quantity} units of ${currentMedicine.name}`, 'success');
                // Optionally reload the page to update stock quantities
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                showNotification(response.message || 'Payment verification failed. Please contact support.', 'error');
            }
        },
        error: function() {
            showNotification('An error occurred while verifying payment', 'error');
        }
    });
}

// Show notification
function showNotification(message, type) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    
    const notification = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="fa ${icon}"></i> ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
    
    document.getElementById('cartNotification').innerHTML = notification;
    
    // Auto-remove notification after 5 seconds
    setTimeout(() => {
        $('#cartNotification .alert').fadeOut();
    }, 5000);
}

// Update quantity when input changes
document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('quantity');
    if (quantityInput) {
        quantityInput.addEventListener('input', updateTotalPrice);
    }
});




</script>