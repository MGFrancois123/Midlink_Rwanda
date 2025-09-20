<?php 
// Start session and check authentication
include('./constant/check.php');
include('./constant/connect.php');

// Debug: Check database connection
if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}

// Log view activity
require_once 'activity_logger.php';
logView($_SESSION['adminId'], 'store', 'Viewed medicine store');
?>

<?php include('./constant/layout/head.php');?>
<!-- Add Flutterwave script -->
<script src="https://checkout.flutterwave.com/v3.js"></script>
<?php include('./constant/layout/header.php');?>
<?php include('./constant/layout/sidebar.php');?>

<?php
// Get search parameters
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$pharmacy_filter = isset($_GET['pharmacy']) ? (int)$_GET['pharmacy'] : 0;
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'name_asc';

// Build the WHERE clause
$where_conditions = [];
$params = [];
$param_types = "";

if (!empty($search_query)) {
    $where_conditions[] = "(m.name LIKE ? OR m.description LIKE ?)";
    $search_param = "%{$search_query}%";
    $params[] = $search_param;
    $params[] = $search_param;
    $param_types .= "ss";
}

if ($category_filter > 0) {
    $where_conditions[] = "m.category_id = ?";
    $params[] = $category_filter;
    $param_types .= "i";
}

if ($pharmacy_filter > 0) {
    $where_conditions[] = "m.pharmacy_id = ?";
    $params[] = $pharmacy_filter;
    $param_types .= "i";
}

// Build WHERE clause or default to 1=1
$where_clause = !empty($where_conditions) ? implode(" AND ", $where_conditions) : "1=1";

// Build ORDER BY clause
$order_clause = "ORDER BY ";
switch ($sort_by) {
    case 'name_asc':
        $order_clause .= "m.name ASC";
        break;
    case 'name_desc':
        $order_clause .= "m.name DESC";
        break;
    case 'price_asc':
        $order_clause .= "m.price ASC";
        break;
    case 'price_desc':
        $order_clause .= "m.price DESC";
        break;
    case 'stock_desc':
        $order_clause .= "m.stock_quantity DESC";
        break;
    default:
        $order_clause .= "m.name ASC";
}

// Fixed medicines SQL query - properly handle the column name with space
$medicines_sql = "SELECT 
                    m.medicine_id,
                    m.pharmacy_id,
                    m.name,
                    m.description,
                    m.price,
                    m.stock_quantity,
                    m.expiry_date,
                    m.`Restricted Medicine` as restricted_medicine,
                    m.category_id,
                    COALESCE(p.name, 'No Pharmacy') as pharmacy_name,
                    COALESCE(c.category_name, 'Uncategorized') as category_name
                FROM medicines m
                LEFT JOIN pharmacies p ON m.pharmacy_id = p.pharmacy_id
                LEFT JOIN category c ON m.category_id = c.category_id
                WHERE {$where_clause} 
                {$order_clause}";

echo "<!-- Debug: SQL Query - $medicines_sql -->";
echo "<!-- Debug: Parameters - " . print_r($params, true) . " -->";
echo "<!-- Debug: Param Types - $param_types -->";

$medicines_stmt = $connect->prepare($medicines_sql);
if ($medicines_stmt === false) {
    echo "<!-- Debug: SQL Error - " . $connect->error . " -->";
    die("SQL Error: " . $connect->error);
}

if (!empty($params)) {
    $medicines_stmt->bind_param($param_types, ...$params);
}

if (!$medicines_stmt->execute()) {
    echo "<!-- Debug: Execute Error - " . $medicines_stmt->error . " -->";
    die("Execute Error: " . $medicines_stmt->error);
}

$medicines_result = $medicines_stmt->get_result();
if ($medicines_result === false) {
    echo "<!-- Debug: Query Execution Error - " . $medicines_stmt->error . " -->";
    die("Query Execution Error: " . $medicines_stmt->error);
}

$medicines_data = [];
while ($row = $medicines_result->fetch_assoc()) {
    $medicines_data[] = [
        'medicine_id' => (int)$row['medicine_id'],
        'name' => $row['name'],
        'description' => $row['description'],
        'price' => (float)$row['price'],
        'stock_quantity' => (int)$row['stock_quantity'],
        'expiry_date' => $row['expiry_date'],
        'Restricted_Medicine' => (int)$row['restricted_medicine'], // Fixed key name
        'category_id' => (int)$row['category_id'],
        'pharmacy_id' => (int)$row['pharmacy_id'],
        'pharmacy_name' => $row['pharmacy_name'],
        'category_name' => $row['category_name']
    ];
}
echo "<!-- Debug: Fetched " . count($medicines_data) . " medicines -->";

// Fetch categories for filter dropdown
$categories_sql = "SELECT category_id, category_name FROM category ORDER BY category_name";
$categories_result = $connect->query($categories_sql);
$categories = [];
if ($categories_result) {
    while ($row = $categories_result->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Fetch pharmacies for filter dropdown
$pharmacies_sql = "SELECT pharmacy_id, name FROM pharmacies ORDER BY name";
$pharmacies_result = $connect->query($pharmacies_sql);
$pharmacies = [];
if ($pharmacies_result) {
    while ($row = $pharmacies_result->fetch_assoc()) {
        $pharmacies[] = $row;
    }
}

$total_products = count($medicines_data);

// Assuming user email is stored in session; adjust as needed based on your auth system
$user_email = isset($_SESSION['email']) ? $_SESSION['email'] : 'customer@passtrack.com'; // Fallback to reference email
?>

<style>
.store-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 25px;
    border-radius: 10px;
    margin-bottom: 25px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.store-header h3 {
    font-weight: 600;
    margin-bottom: 5px;
}

.search-filters-card {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    margin-bottom: 25px;
}

.search-bar {
    position: relative;
}

.search-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
}

.search-input {
    padding-left: 40px;
    border-radius: 25px;
    height: 45px;
    border: 1px solid #e1e5eb;
    transition: all 0.3s;
}

.search-input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.filter-select {
    height: 45px;
    border-radius: 8px;
    border: 1px solid #e1e5eb;
}

.clear-filters-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 10px 15px;
    background: #6c757d;
    color: white;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.3s;
}

.clear-filters-btn:hover {
    background: #5a6268;
    color: white;
    text-decoration: none;
}

.results-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding: 15px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.results-count {
    font-weight: 500;
    color: #495057;
}

.sort-dropdown {
    padding: 8px 12px;
    border-radius: 6px;
    border: 1px solid #e1e5eb;
    background: white;
}

.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.product-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 3px 15px rgba(0,0,0,0.08);
    transition: transform 0.3s, box-shadow 0.3s;
    border: 1px solid #f1f3f4;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.product-image {
    height: 160px;
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 64px;
}

.product-body {
    padding: 20px;
}

.product-title {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 10px;
    font-size: 18px;
}

.product-description {
    color: #718096;
    font-size: 14px;
    margin-bottom: 15px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.product-price {
    font-size: 20px;
    font-weight: 700;
    color: #2d3748;
}

.stock-badge {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.stock-high {
    background: #c6f6d5;
    color: #22543d;
}

.stock-medium {
    background: #fed7d7;
    color: #742a2a;
}

.stock-low {
    background: #feebcb;
    color: #744210;
}

.product-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    font-size: 13px;
    color: #4a5568;
}

.product-info i {
    margin-right: 5px;
    color: #667eea;
}

.product-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.action-btn {
    flex: 1;
    padding: 10px;
    border: none;
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.3s;
    cursor: pointer;
}

.buy-btn {
    background: #667eea;
    color: white;
}

.buy-btn:hover {
    background: #5a67d8;
}

.cart-btn {
    background: #e9ecef;
    color: #495057;
}

.cart-btn:hover {
    background: #dee2e6;
}

.no-results {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
}

.no-results i {
    font-size: 64px;
    color: #cbd5e0;
    margin-bottom: 20px;
}

.no-results h4 {
    color: #2d3748;
    margin-bottom: 10px;
}

.no-results p {
    color: #718096;
    margin-bottom: 20px;
}

.product-detail-card {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
}

.quantity-selector {
    display: flex;
    align-items: center;
    gap: 15px;
    margin: 20px 0;
}

.quantity-btn {
    width: 40px;
    height: 40px;
    border: 1px solid #e1e5eb;
    background: white;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
}

.quantity-btn:hover {
    background: #667eea;
    color: white;
    border-color: #667eea;
}

.quantity-input {
    width: 70px;
    height: 40px;
    text-align: center;
    border: 1px solid #e1e5eb;
    border-radius: 8px;
    font-weight: 500;
}

.total-price {
    text-align: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 10px;
    margin: 20px 0;
}

.payment-options {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}

.payment-btn {
    padding: 12px;
    font-weight: 500;
}

.cart-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1050;
    min-width: 300px;
}

@media (max-width: 768px) {
    .product-grid {
        grid-template-columns: 1fr;
    }
    
    .payment-options {
        grid-template-columns: 1fr;
    }
    
    .results-info {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }
}
</style>

<div class="page-wrapper">
    <div class="container-fluid">
        <!-- Store Header -->
        <div class="store-header">
            <h3><i class="fa fa-store"></i> Medicine Store</h3>
            <p>Browse and purchase medicines from our extensive catalog</p>
        </div>

        <!-- Search and Filters -->
        <div class="search-filters-card">
            <form method="GET" action="store.php" id="storeFilters">
                <div class="row align-items-end">
                    <div class="col-md-6 mb-3">
                        <label for="search" class="form-label">Search Medicines</label>
                        <div class="search-bar">
                            <i class="fa fa-search search-icon"></i>
                            <input type="text" 
                                   id="search" 
                                   name="search" 
                                   class="search-input" 
                                   placeholder="Search by name or description..." 
                                   value="<?php echo htmlspecialchars($search_query); ?>">
                        </div>
                    </div>
                    
                    <div class="col-md-2 mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select name="category" id="category" class="filter-select w-100">
                            <option value="0">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['category_id']; ?>" 
                                        <?php echo $category_filter == $category['category_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['category_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2 mb-3">
                        <label for="pharmacy" class="form-label">Pharmacy</label>
                        <select name="pharmacy" id="pharmacy" class="filter-select w-100">
                            <option value="0">All Pharmacies</option>
                            <?php foreach ($pharmacies as $pharmacy): ?>
                                <option value="<?php echo $pharmacy['pharmacy_id']; ?>"
                                        <?php echo $pharmacy_filter == $pharmacy['pharmacy_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($pharmacy['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2 mb-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fa fa-search"></i> Search
                            </button>
                            <?php if (!empty($search_query) || $category_filter > 0 || $pharmacy_filter > 0): ?>
                                <a href="store.php" class="clear-filters-btn">
                                    <i class="fa fa-times"></i> Clear
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Results Info and Sort -->
        <div class="results-info">
            <div class="results-count">
                <i class="fa fa-pills"></i> 
                Showing <?php echo $total_products; ?> product(s)
                <?php if (!empty($search_query)): ?>
                    for "<?php echo htmlspecialchars($search_query); ?>"
                <?php endif; ?>
            </div>
            <div class="sort-controls">
                <select name="sort" class="sort-dropdown" onchange="applySorting(this.value)">
                    <option value="name_asc" <?php echo $sort_by === 'name_asc' ? 'selected' : ''; ?>>Name (A-Z)</option>
                    <option value="name_desc" <?php echo $sort_by === 'name_desc' ? 'selected' : ''; ?>>Name (Z-A)</option>
                    <option value="price_asc" <?php echo $sort_by === 'price_asc' ? 'selected' : ''; ?>>Price (Low-High)</option>
                    <option value="price_desc" <?php echo $sort_by === 'price_desc' ? 'selected' : ''; ?>>Price (High-Low)</option>
                    <option value="stock_desc" <?php echo $sort_by === 'stock_desc' ? 'selected' : ''; ?>>Stock (High-Low)</option>
                </select>
            </div>
        </div>

        <!-- Product Grid -->
        <?php if (empty($medicines_data)): ?>
            <div class="no-results">
                <i class="fa fa-search"></i>
                <h4>No products found</h4>
                <p>Try adjusting your search criteria or browse all products</p>
                <a href="store.php" class="btn btn-primary mt-3">
                    <i class="fa fa-store"></i> View All Products
                </a>
            </div>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($medicines_data as $medicine): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <i class="fa fa-pills"></i>
                        </div>
                        <div class="product-body">
                            <h5 class="product-title"><?php echo htmlspecialchars($medicine['name']); ?></h5>
                            <p class="product-description">
                                <?php echo htmlspecialchars($medicine['description'] ?: 'No description available'); ?>
                            </p>
                            
                            <div class="product-meta">
                                <div class="product-price">
                                    RWF <?php echo number_format($medicine['price'], 0); ?>
                                </div>
                                <div>
                                    <?php 
                                    $stock = $medicine['stock_quantity'];
                                    if ($stock > 20) {
                                        echo '<span class="stock-badge stock-high">In Stock</span>';
                                    } elseif ($stock > 5) {
                                        echo '<span class="stock-badge stock-medium">Limited</span>';
                                    } else {
                                        echo '<span class="stock-badge stock-low">Low Stock</span>';
                                    }
                                    ?>
                                </div>
                            </div>
                            
                            <div class="product-info">
                                <span><i class="fa fa-building"></i> <?php echo htmlspecialchars($medicine['pharmacy_name']); ?></span>
                                <span><i class="fa fa-cubes"></i> <?php echo $medicine['stock_quantity']; ?> units</span>
                            </div>
                            
                            <div class="product-info">
                                <span><i class="fa fa-tag"></i> <?php echo htmlspecialchars($medicine['category_name']); ?></span>
                                <?php if ($medicine['Restricted_Medicine']): ?>
                                    <span class="text-danger"><i class="fa fa-exclamation-triangle"></i> Restricted</span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="product-actions">
                                <button type="button" class="action-btn buy-btn" onclick="buyMedicine(<?php echo $medicine['medicine_id']; ?>)">
                                    <i class="fa fa-shopping-bag"></i> Buy Now
                                </button>
                                <button type="button" class="action-btn cart-btn" onclick="addToCart(<?php echo $medicine['medicine_id']; ?>, this)">
                                    <i class="fa fa-cart-plus"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
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
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
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
// Medicine data for JavaScript
let medicineData = <?php echo json_encode($medicines_data); ?>;
let currentMedicine = null;

// Check if user is authorized for restricted medicines (mock function, replace with actual logic)
function isUserAuthorizedForRestricted() {
    // Replace with actual logic to check user role or permissions
    // For example, check if $_SESSION['user_role'] == 'doctor' or similar
    return false; // Default to false for demonstration
}

// Apply sorting
function applySorting(sortValue) {
    const url = new URL(window.location.href);
    url.searchParams.set('sort', sortValue);
    window.location.href = url.toString();
}

// Auto-submit form when filters change
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category');
    const pharmacySelect = document.getElementById('pharmacy');
    
    categorySelect.addEventListener('change', function() {
        document.getElementById('storeFilters').submit();
    });
    
    pharmacySelect.addEventListener('change', function() {
        document.getElementById('storeFilters').submit();
    });
    
    // Real-time search with debounce
    let searchTimeout;
    const searchInput = document.getElementById('search');
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            document.getElementById('storeFilters').submit();
        }, 500); // Wait 500ms after user stops typing
    });
    
    // Update total price when quantity changes
    const quantityInput = document.getElementById('quantity');
    if (quantityInput) {
        quantityInput.addEventListener('input', updateTotalPrice);
    }
});

// Buy medicine function
function buyMedicine(medicineId) {
    currentMedicine = medicineData.find(med => med.medicine_id == medicineId);
    if (!currentMedicine) {
        alert('Medicine not found!');
        return;
    }
    
    // Check if medicine is restricted
    if (currentMedicine.Restricted_Medicine && !isUserAuthorizedForRestricted()) {
        showNotification('This is a restricted medicine. Please contact a healthcare professional.', 'error');
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
                <p><strong>Category:</strong> ${currentMedicine.category_name}</p>
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
function addToCart(medicineId, buttonElement) {
    const medicine = medicineData.find(med => med.medicine_id == medicineId);
    if (!medicine) {
        showNotification('Medicine not found!', 'error');
        return;
    }
    
    // Check if medicine is restricted
    if (medicine.Restricted_Medicine && !isUserAuthorizedForRestricted()) {
        showNotification('This is a restricted medicine. Please contact a healthcare professional.', 'error');
        return;
    }
    
    setButtonLoading(buttonElement, true);
    
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
            setButtonLoading(buttonElement, false);
            if (response.success) {
                showNotification(`${medicine.name} added to cart successfully!`, 'success');
                buttonElement.classList.add('btn-success');
                buttonElement.innerHTML = '<i class="fa fa-check"></i> Added';
                setTimeout(() => {
                    buttonElement.classList.remove('btn-success');
                    buttonElement.innerHTML = '<i class="fa fa-cart-plus"></i> Add to Cart';
                }, 2000);
            } else {
                showNotification(response.message || 'Failed to add medicine to cart', 'error');
            }
        },
        error: function() {
            setButtonLoading(buttonElement, false);
            showNotification('An error occurred while adding to cart', 'error');
        }
    });
}

// Add to cart from modal
function addToCartFromModal() {
    if (!currentMedicine) {
        showNotification('No medicine selected!', 'error');
        return;
    }
    
    // Check if medicine is restricted
    if (currentMedicine.Restricted_Medicine && !isUserAuthorizedForRestricted()) {
        showNotification('This is a restricted medicine. Please contact a healthcare professional.', 'error');
        return;
    }
    
    const quantity = parseInt(document.getElementById('quantity').value);
    if (quantity < 1 || quantity > currentMedicine.stock_quantity) {
        showNotification('Invalid quantity selected!', 'error');
        return;
    }
    
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

// Payment functions
function payWithCard() {
    if (!currentMedicine) {
        showNotification('No medicine selected!', 'error');
        return;
    }
    
    // Check if medicine is restricted
    if (currentMedicine.Restricted_Medicine && !isUserAuthorizedForRestricted()) {
        showNotification('This is a restricted medicine. Please contact a healthcare professional.', 'error');
        return;
    }

    const quantity = parseInt(document.getElementById('quantity').value);
    if (quantity < 1 || quantity > currentMedicine.stock_quantity) {
        showNotification('Invalid quantity selected!', 'error');
        return;
    }

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
    if (!currentMedicine) {
        showNotification('No medicine selected!', 'error');
        return;
    }
    
    // Check if medicine is restricted
    if (currentMedicine.Restricted_Medicine && !isUserAuthorizedForRestricted()) {
        showNotification('This is a restricted medicine. Please contact a healthcare professional.', 'error');
        return;
    }

    const quantity = parseInt(document.getElementById('quantity').value);
    if (quantity < 1 || quantity > currentMedicine.stock_quantity) {
        showNotification('Invalid quantity selected!', 'error');
        return;
    }

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

// Verify payment
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

// Set button loading state
function setButtonLoading(button, loading = true) {
    if (loading) {
        button.disabled = true;
        const originalContent = button.innerHTML;
        button.setAttribute('data-original-content', originalContent);
        button.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Loading...';
    } else {
        button.disabled = false;
        const originalContent = button.getAttribute('data-original-content');
        if (originalContent) {
            button.innerHTML = originalContent;
            button.removeAttribute('data-original-content');
        }
    }
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        document.getElementById('search').focus();
    }
    
    if (e.key === 'Escape' && document.activeElement === document.getElementById('search')) {
        document.getElementById('search').value = '';
        document.getElementById('storeFilters').submit();
    }
});

// Smooth scroll to top
function smoothScrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Lazy loading for images (if used in the future)
function initializeLazyLoading() {
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });

        const lazyImages = document.querySelectorAll('.lazy');
        lazyImages.forEach(img => imageObserver.observe(img));
    }
}

// Initialize animations for product cards
document.addEventListener('DOMContentLoaded', function() {
    initializeLazyLoading();
    
    const observeCards = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    });

    document.querySelectorAll('.product-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observeCards.observe(card);
    });
});



</script>
