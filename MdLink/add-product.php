<?php 
// Start session and check authentication first
include('./constant/check.php');
include('./constant/connect.php');
?>
<?php include('./constant/layout/head.php');?>
<?php include('./constant/layout/header.php');?>
<?php include('./constant/layout/sidebar.php');?>

<style>
.medicine-form-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 20px;
    padding: 35px;
    margin-bottom: 40px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    position: relative;
    overflow: hidden;
}

.medicine-form-header::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(180deg); }
}

.medicine-form-header h3 {
    margin-bottom: 15px;
    font-weight: 700;
    font-size: 2.2rem;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    position: relative;
    z-index: 1;
}

.medicine-form-header p {
    opacity: 0.95;
    margin-bottom: 0;
    font-size: 1.1rem;
    position: relative;
    z-index: 1;
}

.form-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    overflow: hidden;
    border: 1px solid rgba(102, 126, 234, 0.1);
    transition: all 0.3s ease;
    position: relative;
}

.form-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.form-card .card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 2px solid #e9ecef;
    padding: 25px 30px;
    position: relative;
}

.form-card .card-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background: linear-gradient(90deg, #667eea, #764ba2);
}

.form-card .card-body {
    padding: 40px;
    background: linear-gradient(135deg, #fafbfc 0%, #ffffff 100%);
}

.form-group {
    margin-bottom: 25px;
    position: relative;
}

.form-group label {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.95rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.form-group label i {
    color: #667eea;
    width: 18px;
    font-size: 1.1rem;
    transition: all 0.3s ease;
}

.form-group:hover label i {
    color: #764ba2;
    transform: scale(1.1);
}

.form-control {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 15px 20px;
    font-size: 15px;
    transition: all 0.3s ease;
    background: #fafbfc;
    position: relative;
}

.form-control:hover {
    border-color: #667eea;
    background: white;
}

/* Selected state for select dropdowns */
.form-control:not([value=""]):not([value="~~ SELECT CATEGORY ~~"]):not([value="~~ SELECT MEDICINE ~~"]):not([value="~~ SELECT PHARMACY ~~"]) {
    border-color: #28a745;
    background: linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%);
    color: #155724;
    font-weight: 600;
}

.form-control:not([value=""]):not([value="~~ SELECT CATEGORY ~~"]):not([value="~~ SELECT MEDICINE ~~"]):not([value="~~ SELECT PHARMACY ~~"])::after {
    content: '✓';
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #28a745;
    font-weight: bold;
    font-size: 1.2rem;
}

.form-control.is-invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.3rem rgba(220, 53, 69, 0.15);
}

.form-control.is-valid {
    border-color: #28a745;
    box-shadow: 0 0 0 0.3rem rgba(40, 167, 69, 0.15);
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 5px;
    font-size: 12px;
    color: #dc3545;
}

.valid-feedback {
    display: block;
    width: 100%;
    margin-top: 5px;
    font-size: 12px;
    color: #28a745;
}

.form-text {
    font-size: 12px;
    color: #6c757d;
    margin-top: 5px;
}

.btn-submit {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    padding: 18px 40px;
    border-radius: 50px;
    font-weight: 700;
    font-size: 18px;
    transition: all 0.4s ease;
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 0 auto;
    position: relative;
    overflow: hidden;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.btn-submit::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.btn-submit:hover::before {
    left: 100%;
}

.btn-submit:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
    color: white;
}

.btn-submit:active {
    transform: translateY(-1px);
}

.btn-submit:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
}


.required {
    color: #dc3545;
}

.selection-display {
    border: 3px solid #ff8c00 !important;
    background: linear-gradient(135deg, #fff8f0 0%, #ffeaa7 100%) !important;
    color: #d63031 !important;
    font-weight: 700;
    border-radius: 12px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(255, 140, 0, 0.3);
    font-size: 16px;
    padding: 15px 20px;
}

.selection-display::before {
    content: '✓ SELECTED';
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #ff8c00;
    font-weight: bold;
    font-size: 0.9rem;
    background: rgba(255, 140, 0, 0.1);
    padding: 4px 8px;
    border-radius: 15px;
    border: 1px solid #ff8c00;
}

.selection-display.has-value {
    background: linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%) !important;
    border-color: #28a745 !important;
    color: #155724 !important;
}

.selection-display.has-value::before {
    content: '✓ CONFIRMED';
    color: #28a745;
    background: rgba(40, 167, 69, 0.1);
    border-color: #28a745;
}

.selection-display:focus {
    border-color: #ff8c00 !important;
    box-shadow: 0 0 0 0.3rem rgba(255, 140, 0, 0.3) !important;
    background: linear-gradient(135deg, #fff8f0 0%, #ffeaa7 100%) !important;
    transform: translateY(-2px);
}

.selection-display:hover {
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(255, 140, 0, 0.2);
}

.selection-display::placeholder {
    color: #ff8c00;
    opacity: 0.8;
    font-style: italic;
}

.quick-info {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 30px;
    margin-bottom: 20px;
    color: white;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    position: relative;
    overflow: hidden;
}

.quick-info::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: float 8s ease-in-out infinite reverse;
}

.quick-info h6 {
    color: white;
    font-weight: 700;
    margin-bottom: 20px;
    font-size: 1.3rem;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    position: relative;
    z-index: 1;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 15px;
    font-size: 15px;
    color: rgba(255,255,255,0.9);
    padding: 8px 0;
    transition: all 0.3s ease;
    position: relative;
    z-index: 1;
}

.info-item:hover {
    color: white;
    transform: translateX(5px);
}

.info-item i {
    color: #ffeaa7;
    width: 18px;
    font-size: 1.1rem;
    transition: all 0.3s ease;
}

.info-item:hover i {
    color: white;
    transform: scale(1.2);
}

/* Additional animations and effects */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.form-card {
    animation: slideInUp 0.6s ease-out;
}

.form-group {
    animation: slideInUp 0.8s ease-out;
}

.form-group:nth-child(1) { animation-delay: 0.1s; }
.form-group:nth-child(2) { animation-delay: 0.2s; }
.form-group:nth-child(3) { animation-delay: 0.3s; }
.form-group:nth-child(4) { animation-delay: 0.4s; }
.form-group:nth-child(5) { animation-delay: 0.5s; }
.form-group:nth-child(6) { animation-delay: 0.6s; }

/* Responsive design enhancements */
@media (max-width: 768px) {
    .medicine-form-header {
        padding: 25px 20px;
        margin-bottom: 30px;
    }
    
    .medicine-form-header h3 {
        font-size: 1.8rem;
    }
    
    .form-card .card-body {
        padding: 25px;
    }
    
    .btn-submit {
        padding: 15px 30px;
        font-size: 16px;
    }
    
    .quick-info {
        padding: 20px;
    }
}

@media (max-width: 576px) {
    .medicine-form-header h3 {
        font-size: 1.5rem;
    }
    
    .form-card .card-body {
        padding: 20px;
    }
    
    .form-control {
        padding: 12px 15px;
        font-size: 14px;
    }
    
    .btn-submit {
        padding: 12px 25px;
        font-size: 14px;
    }
}
</style>

<div class="page-wrapper">
    <div class="container-fluid">
        <!-- Enhanced Header -->
        <div class="medicine-form-header">
            <h3><i class="fa fa-pills"></i> Add New Medicine</h3>
            <p>Complete the form below to add a new medicine to the pharmacy inventory system</p>
        </div>
            
        <!-- Main Form -->
        <div class="row">
            <div class="col-lg-8">
                <div class="form-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-pills"></i> Medicine Information</h5>
                        <small class="text-muted">Fill in all required fields to add the medicine to the system</small>
                    </div>
                    <div class="card-body">
                        <?php
                        // Display error messages
                        if (isset($_GET['error'])) {
                            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
                            echo '<i class="fa fa-exclamation-triangle"></i> ' . htmlspecialchars($_GET['error']);
                            echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
                            echo '<span aria-hidden="true">&times;</span>';
                            echo '</button>';
                            echo '</div>';
                        }
                        
                        // Display success messages
                        if (isset($_GET['success'])) {
                            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
                            echo '<i class="fa fa-check-circle"></i> ' . htmlspecialchars($_GET['success']);
                            echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
                            echo '<span aria-hidden="true">&times;</span>';
                            echo '</button>';
                            echo '</div>';
                        }
                        ?>
                        <div id="medicineMessage" class="alert" style="display: none;"></div>
                        
                        <form method="POST" id="submitProductForm" action="php_action/create_medicine.php">
                            <input type="hidden" name="currnt_date" class="form-control">
                            
                            <div class="row">
                                <!-- Category Selection -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="category_id">
                                            <i class="fa fa-tags"></i> Category <span class="required">*</span>
                                        </label>
                                        <select class="form-control" id="category_id" name="category_id" required>
                                            <option value="">~~ SELECT CATEGORY ~~</option>
                                            <?php 
                                            $sql = "SELECT category_id, category_name FROM category WHERE status = '1' ORDER BY category_name";
                                            $result = $connect->query($sql);
                                            while($row = $result->fetch_array()) {
                                                echo "<option value='".$row[0]."' data-name='".htmlspecialchars($row[1])."'>".$row[1]."</option>";
                                            }
                                            ?>
                                        </select>
                                        <input type="text" class="form-control mt-2 selection-display" id="category_display" 
                                               placeholder="Selected category will appear here" readonly/>
                                        <div class="form-text">Select the medicine category first</div>
                                    </div>
                                </div>
                                
                                <!-- Medicine Name -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">
                                            <i class="fa fa-capsules"></i> Medicine Name <span class="required">*</span>
                                        </label>
                                        <select class="form-control" id="name" name="name" required>
                                            <option value="">~~ SELECT MEDICINE ~~</option>
                                            <?php 
                                            // Fetch all unique medicine names from the database
                                            $medicine_sql = "SELECT DISTINCT name, description FROM medicines ORDER BY name";
                                            $medicine_result = $connect->query($medicine_sql);
                                            if ($medicine_result && $medicine_result->num_rows > 0) {
                                                while($row = $medicine_result->fetch_array()) {
                                                    echo "<option value='".htmlspecialchars($row[0])."' data-description='".htmlspecialchars($row[1] ?: '')."'>".htmlspecialchars($row[0])."</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                        <input type="text" class="form-control mt-2 selection-display" id="medicine_display" 
                                               placeholder="Selected medicine will appear here" readonly/>
                                        <div class="form-text">Select from existing medicines in the database</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <!-- Description -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="description">
                                            <i class="fa fa-align-left"></i> Description
                                        </label>
                                        <input type="text" class="form-control" id="description" name="description" 
                                               placeholder="Medicine description"/>
                                        <input type="text" class="form-control mt-2 selection-display" id="description_display" 
                                               placeholder="Selected description will appear here" readonly/>
                                        <div class="form-text">Auto-populated when selecting medicine - you can edit this field</div>
                                    </div>
                                </div>
                                
                                <!-- Pharmacy -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pharmacy_id">
                                            <i class="fa fa-hospital"></i> Pharmacy
                                        </label>
                                        <select class="form-control" id="pharmacy_id" name="pharmacy_id">
                                            <option value="">~~ SELECT PHARMACY ~~</option>
                                            <?php 
                                            $sql = "SELECT pharmacy_id, name FROM pharmacies ORDER BY name";
                                            $result = $connect->query($sql);
                                            while($row = $result->fetch_array()) {
                                                echo "<option value='".$row[0]."' data-name='".$row[1]."'>".$row[1]."</option>";
                                            }
                                            ?>
                                        </select>
                                        <input type="text" class="form-control mt-2 selection-display" id="pharmacy_name_display" 
                                               placeholder="Selected pharmacy will appear here" readonly/>
                                        <div class="form-text">Select the pharmacy location</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <!-- Price -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="price">
                                            <i class="fa fa-money-bill"></i> Price (RWF) <span class="required">*</span>
                                        </label>
                                        <input type="number" step="0.01" class="form-control" id="price" 
                                               placeholder="Enter price in RWF" name="price" autocomplete="off" required min="0"/>
                                        <input type="text" class="form-control mt-2 selection-display" id="price_display" 
                                               placeholder="Entered price will appear here" readonly/>
                                        <div class="form-text">Enter the selling price in Rwandan Francs</div>
                                    </div>
                                </div>
                                
                                <!-- Stock Quantity -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="stock_quantity">
                                            <i class="fa fa-boxes"></i> Stock Quantity <span class="required">*</span>
                                        </label>
                                        <input type="number" class="form-control" id="stock_quantity" 
                                               placeholder="Enter stock quantity" name="stock_quantity" autocomplete="off" required min="0"/>
                                        <input type="text" class="form-control mt-2 selection-display" id="stock_display" 
                                               placeholder="Entered stock quantity will appear here" readonly/>
                                        <div class="form-text">Enter the initial stock quantity</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <!-- Expiry Date -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="expiry_date">
                                            <i class="fa fa-calendar"></i> Expiry Date
                                        </label>
                                        <input type="date" class="form-control" id="expiry_date" 
                                               placeholder="Select expiry date" name="expiry_date" autocomplete="off"/>
                                        <input type="text" class="form-control mt-2 selection-display" id="expiry_display" 
                                               placeholder="Selected expiry date will appear here" readonly/>
                                        <div class="form-text">Select the medicine expiry date (optional)</div>
                                    </div>
                                </div>
                                
                                <!-- Restricted Medicine -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="restricted_medicine">
                                            <i class="fa fa-ban"></i> Restricted Medicine
                                        </label>
                                        <select class="form-control" id="restricted_medicine" name="restricted_medicine">
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        </select>
                                        <input type="text" class="form-control mt-2 selection-display" id="restricted_display" 
                                               placeholder="Selected restriction status will appear here" readonly/>
                                        <div class="form-text">Is this a restricted/prescription medicine?</div>
                                    </div>
                                </div>
                            </div>
                            
                            
                            <!-- Submit Button -->
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <button type="submit" name="create" id="createProductBtn" class="btn-submit">
                                        <i class="fa fa-plus"></i> Add Medicine
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Quick Info Sidebar -->
            <div class="col-lg-4">
                <div class="quick-info">
                    <h6><i class="fa fa-info-circle"></i> Quick Information</h6>
                    
                    <div class="info-item">
                        <i class="fa fa-check-circle"></i>
                        <span>All required fields must be filled</span>
                    </div>
                    
                    <div class="info-item">
                        <i class="fa fa-tags"></i>
                        <span>Medicines are loaded from existing database records</span>
                    </div>
                    
                    <div class="info-item">
                        <i class="fa fa-money-bill"></i>
                        <span>Price should be in Rwandan Francs (RWF)</span>
                    </div>
                    
                    <div class="info-item">
                        <i class="fa fa-boxes"></i>
                        <span>Stock quantity must be a positive number</span>
                    </div>
                    
                    <div class="info-item">
                        <i class="fa fa-calendar"></i>
                        <span>Expiry date is optional but recommended</span>
                    </div>
                    
                    <div class="info-item">
                        <i class="fa fa-ban"></i>
                        <span>Mark restricted medicines appropriately</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Medicine form functionality
    var cat = document.getElementById('category_id');
    var med = document.getElementById('name');
    var desc = document.getElementById('description');
    
    // Initialize form elements
    
    // Medicines are now loaded directly from database in PHP
    
    function clearSelect(sel, placeholder) {
        while (sel.firstChild) sel.removeChild(sel.firstChild);
        var opt = document.createElement('option');
        opt.value = '';
        opt.textContent = placeholder;
        sel.appendChild(opt);
    }
    
    // Medicine loading functions removed - medicines are now loaded directly from database
    
    // Event listeners
    // Category selection handler
    if (cat) {
        cat.addEventListener('change', function() {
            var sel = this.options[this.selectedIndex];
            var categoryName = sel ? sel.getAttribute('data-name') : '';
            var categoryDisplay = document.getElementById('category_display');
            if (categoryDisplay) {
                categoryDisplay.value = categoryName || '';
                // Update display field styling
                if (categoryName) {
                    categoryDisplay.classList.add('has-value');
                    categoryDisplay.style.background = 'linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%)';
                    categoryDisplay.style.borderColor = '#28a745';
                    categoryDisplay.style.color = '#155724';
                } else {
                    categoryDisplay.classList.remove('has-value');
                    categoryDisplay.style.background = 'linear-gradient(135deg, #fff8f0 0%, #ffeaa7 100%)';
                    categoryDisplay.style.borderColor = '#ff8c00';
                    categoryDisplay.style.color = '#d63031';
                }
            }
            
            // Add visual feedback for selection
            if (this.value && this.value !== '') {
                this.classList.add('selected');
                this.style.borderColor = '#28a745';
                this.style.background = 'linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%)';
                this.style.color = '#155724';
                this.style.fontWeight = '600';
            } else {
                this.classList.remove('selected');
                this.style.borderColor = '#e9ecef';
                this.style.background = '#fafbfc';
                this.style.color = '#495057';
                this.style.fontWeight = 'normal';
            }
        });
    }
    
    if (med) {
        med.addEventListener('change', function() {
            var sel = this.options[this.selectedIndex];
            var medicineName = sel ? sel.textContent : '';
            var d = sel ? sel.getAttribute('data-description') : '';
            
            // Populate description input field (editable)
            var descField = document.getElementById('description');
            if (descField) {
                descField.value = d || '';
            }
            
            // Populate medicine display field (readonly, shows selection)
            var medicineDisplay = document.getElementById('medicine_display');
            if (medicineDisplay) {
                medicineDisplay.value = medicineName || '';
                // Update display field styling
                if (medicineName) {
                    medicineDisplay.classList.add('has-value');
                    medicineDisplay.style.background = 'linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%)';
                    medicineDisplay.style.borderColor = '#28a745';
                    medicineDisplay.style.color = '#155724';
                } else {
                    medicineDisplay.classList.remove('has-value');
                    medicineDisplay.style.background = 'linear-gradient(135deg, #fff8f0 0%, #ffeaa7 100%)';
                    medicineDisplay.style.borderColor = '#ff8c00';
                    medicineDisplay.style.color = '#d63031';
                }
            }
            
            // Populate description display field (readonly, shows selection)
            var descDisplay = document.getElementById('description_display');
            if (descDisplay) {
                descDisplay.value = d || '';
                // Update display field styling
                if (d) {
                    descDisplay.classList.add('has-value');
                    descDisplay.style.background = 'linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%)';
                    descDisplay.style.borderColor = '#28a745';
                    descDisplay.style.color = '#155724';
                } else {
                    descDisplay.classList.remove('has-value');
                    descDisplay.style.background = 'linear-gradient(135deg, #fff8f0 0%, #ffeaa7 100%)';
                    descDisplay.style.borderColor = '#ff8c00';
                    descDisplay.style.color = '#d63031';
                }
            }
            
            // Add visual feedback for selection
            if (this.value && this.value !== '') {
                this.classList.add('selected');
                this.style.borderColor = '#28a745';
                this.style.background = 'linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%)';
                this.style.color = '#155724';
                this.style.fontWeight = '600';
            } else {
                this.classList.remove('selected');
                this.style.borderColor = '#e9ecef';
                this.style.background = '#fafbfc';
                this.style.color = '#495057';
                this.style.fontWeight = 'normal';
            }
        });
        
        // Also update display fields when description is manually edited
        var descField = document.getElementById('description');
        var descDisplay = document.getElementById('description_display');
        if (descField && descDisplay) {
            descField.addEventListener('input', function() {
                descDisplay.value = this.value || '';
                // Update display field styling
                if (this.value) {
                    descDisplay.classList.add('has-value');
                    descDisplay.style.background = 'linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%)';
                    descDisplay.style.borderColor = '#28a745';
                    descDisplay.style.color = '#155724';
                } else {
                    descDisplay.classList.remove('has-value');
                    descDisplay.style.background = 'linear-gradient(135deg, #fff8f0 0%, #ffeaa7 100%)';
                    descDisplay.style.borderColor = '#ff8c00';
                    descDisplay.style.color = '#d63031';
                }
            });
        }
        
        // Also update medicine display when medicine name is manually edited
        var medicineDisplay = document.getElementById('medicine_display');
        if (medicineDisplay) {
            med.addEventListener('input', function() {
                if (this.tagName === 'INPUT') {
                    medicineDisplay.value = this.value || '';
                }
            });
        }
    }
    
    // Add pharmacy selection handler
    var pharmacySelect = document.getElementById('pharmacy_id');
    var pharmacyDisplay = document.getElementById('pharmacy_name_display');
    
    if (pharmacySelect && pharmacyDisplay) {
        pharmacySelect.addEventListener('change', function() {
            var sel = this.options[this.selectedIndex];
            var pharmacyName = sel ? sel.getAttribute('data-name') : '';
            pharmacyDisplay.value = pharmacyName || '';
            
            // Update display field styling
            if (pharmacyName) {
                pharmacyDisplay.classList.add('has-value');
                pharmacyDisplay.style.background = 'linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%)';
                pharmacyDisplay.style.borderColor = '#28a745';
                pharmacyDisplay.style.color = '#155724';
            } else {
                pharmacyDisplay.classList.remove('has-value');
                pharmacyDisplay.style.background = 'linear-gradient(135deg, #fff8f0 0%, #ffeaa7 100%)';
                pharmacyDisplay.style.borderColor = '#ff8c00';
                pharmacyDisplay.style.color = '#d63031';
            }
            
            // Add visual feedback for selection
            if (this.value && this.value !== '') {
                this.classList.add('selected');
                this.style.borderColor = '#28a745';
                this.style.background = 'linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%)';
                this.style.color = '#155724';
                this.style.fontWeight = '600';
            } else {
                this.classList.remove('selected');
                this.style.borderColor = '#e9ecef';
                this.style.background = '#fafbfc';
                this.style.color = '#495057';
                this.style.fontWeight = 'normal';
            }
        });
    }
    
    // Add restricted medicine selection handler
    var restrictedSelect = document.getElementById('restricted_medicine');
    var restrictedDisplay = document.getElementById('restricted_display');
    
    if (restrictedSelect && restrictedDisplay) {
        restrictedSelect.addEventListener('change', function() {
            var sel = this.options[this.selectedIndex];
            var restrictedText = sel ? sel.textContent : '';
            restrictedDisplay.value = restrictedText || '';
            
            // Update display field styling
            if (restrictedText) {
                restrictedDisplay.classList.add('has-value');
                restrictedDisplay.style.background = 'linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%)';
                restrictedDisplay.style.borderColor = '#28a745';
                restrictedDisplay.style.color = '#155724';
            } else {
                restrictedDisplay.classList.remove('has-value');
                restrictedDisplay.style.background = 'linear-gradient(135deg, #fff8f0 0%, #ffeaa7 100%)';
                restrictedDisplay.style.borderColor = '#ff8c00';
                restrictedDisplay.style.color = '#d63031';
            }
            
            // Add visual feedback for selection
            if (this.value && this.value !== '') {
                this.classList.add('selected');
                this.style.borderColor = '#28a745';
                this.style.background = 'linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%)';
                this.style.color = '#155724';
                this.style.fontWeight = '600';
            } else {
                this.classList.remove('selected');
                this.style.borderColor = '#e9ecef';
                this.style.background = '#fafbfc';
                this.style.color = '#495057';
                this.style.fontWeight = 'normal';
            }
        });
    }
    
    // Add price input handler
    var priceInput = document.getElementById('price');
    var priceDisplay = document.getElementById('price_display');
    
    if (priceInput && priceDisplay) {
        priceInput.addEventListener('input', function() {
            var priceValue = this.value;
            if (priceValue) {
                priceDisplay.value = 'RWF ' + parseFloat(priceValue).toLocaleString();
                // Update display field styling
                priceDisplay.classList.add('has-value');
                priceDisplay.style.background = 'linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%)';
                priceDisplay.style.borderColor = '#28a745';
                priceDisplay.style.color = '#155724';
            } else {
                priceDisplay.value = '';
                priceDisplay.classList.remove('has-value');
                priceDisplay.style.background = 'linear-gradient(135deg, #fff8f0 0%, #ffeaa7 100%)';
                priceDisplay.style.borderColor = '#ff8c00';
                priceDisplay.style.color = '#d63031';
            }
        });
    }
    
    // Add stock quantity input handler
    var stockInput = document.getElementById('stock_quantity');
    var stockDisplay = document.getElementById('stock_display');
    
    if (stockInput && stockDisplay) {
        stockInput.addEventListener('input', function() {
            var stockValue = this.value;
            if (stockValue) {
                stockDisplay.value = stockValue + ' units';
                // Update display field styling
                stockDisplay.classList.add('has-value');
                stockDisplay.style.background = 'linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%)';
                stockDisplay.style.borderColor = '#28a745';
                stockDisplay.style.color = '#155724';
            } else {
                stockDisplay.value = '';
                stockDisplay.classList.remove('has-value');
                stockDisplay.style.background = 'linear-gradient(135deg, #fff8f0 0%, #ffeaa7 100%)';
                stockDisplay.style.borderColor = '#ff8c00';
                stockDisplay.style.color = '#d63031';
            }
        });
    }
    
    // Add expiry date input handler
    var expiryInput = document.getElementById('expiry_date');
    var expiryDisplay = document.getElementById('expiry_display');
    
    if (expiryInput && expiryDisplay) {
        expiryInput.addEventListener('change', function() {
            var expiryValue = this.value;
            if (expiryValue) {
                var date = new Date(expiryValue);
                var formattedDate = date.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                expiryDisplay.value = formattedDate;
                // Update display field styling
                expiryDisplay.classList.add('has-value');
                expiryDisplay.style.background = 'linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%)';
                expiryDisplay.style.borderColor = '#28a745';
                expiryDisplay.style.color = '#155724';
            } else {
                expiryDisplay.value = '';
                expiryDisplay.classList.remove('has-value');
                expiryDisplay.style.background = 'linear-gradient(135deg, #fff8f0 0%, #ffeaa7 100%)';
                expiryDisplay.style.borderColor = '#ff8c00';
                expiryDisplay.style.color = '#d63031';
            }
        });
    }
    
    // Medicines are already loaded from database in PHP
    
    // Function to apply visual feedback to form controls
    function applyVisualFeedback() {
        var formControls = document.querySelectorAll('.form-control');
        formControls.forEach(function(control) {
            if (control.value && control.value !== '' && 
                control.value !== '~~ SELECT CATEGORY ~~' && 
                control.value !== '~~ SELECT MEDICINE ~~' && 
                control.value !== '~~ SELECT PHARMACY ~~') {
                control.classList.add('selected');
                control.style.borderColor = '#28a745';
                control.style.background = 'linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%)';
                control.style.color = '#155724';
                control.style.fontWeight = '600';
            }
        });
    }
    
    // Apply visual feedback on page load
    applyVisualFeedback();
    
    // Form validation
    $('#submitProductForm').on('submit', function(e) {
        var isValid = true;
        var errors = [];
        
        // Clear previous validation states
        $('.form-control').removeClass('is-invalid is-valid');
        $('.invalid-feedback').remove();
        
        // Validate required fields
        if (!$('#category_id').val()) {
            $('#category_id').addClass('is-invalid');
            errors.push('Please select a category');
            isValid = false;
        } else {
            $('#category_id').addClass('is-valid');
        }
        
        if (!$('#name').val()) {
            $('#name').addClass('is-invalid');
            errors.push('Please select a medicine');
            isValid = false;
        } else {
            $('#name').addClass('is-valid');
        }
        
        if (!$('#price').val() || parseFloat($('#price').val()) <= 0) {
            $('#price').addClass('is-invalid');
            errors.push('Please enter a valid price');
            isValid = false;
        } else {
            $('#price').addClass('is-valid');
        }
        
        if (!$('#stock_quantity').val() || parseInt($('#stock_quantity').val()) < 0) {
            $('#stock_quantity').addClass('is-invalid');
            errors.push('Please enter a valid stock quantity');
            isValid = false;
        } else {
            $('#stock_quantity').addClass('is-valid');
        }
        
        if (!isValid) {
            e.preventDefault();
            showMessage('Please fix the errors above before submitting.', 'danger');
            return false;
        }
        
        // Show loading state
        $('#createProductBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Adding Medicine...');
    });
    
    
    // Message display function
    function showMessage(message, type) {
        var alertClass = 'alert-' + type;
        $('#medicineMessage').removeClass('alert-success alert-danger alert-warning alert-info')
                           .addClass('alert ' + alertClass)
                           .html('<i class="fa fa-info-circle"></i> ' + message)
                           .show();
        
        setTimeout(function() {
            $('#medicineMessage').fadeOut();
        }, 5000);
    }
    
    // Make showMessage globally available
    window.showMessage = showMessage;
    
});

</script>

<?php include('./constant/layout/footer.php');?>