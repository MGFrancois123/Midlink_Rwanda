<?php
require_once './constant/connect.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Dropdown Functionality</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 20px; border: 1px solid #ccc; border-radius: 5px; }
        .form-group { margin: 10px 0; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        select, input { padding: 8px; margin: 5px 0; width: 300px; }
        .display-box { 
            background: #fff8f0; 
            border: 2px solid #ff8c00; 
            padding: 10px; 
            margin-top: 5px;
            border-radius: 5px;
            font-weight: bold;
        }
        .display-box.has-value {
            background: #e8f5e8;
            border-color: #28a745;
            color: #155724;
        }
        .test-button { 
            background: #007bff; 
            color: white; 
            padding: 10px 20px; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            margin: 10px 5px;
        }
        .test-button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h1>Dropdown Functionality Test</h1>
    
    <div class="test-section">
        <h2>Test Category Dropdown</h2>
        <div class="form-group">
            <label for="test_category">Category:</label>
            <select id="test_category" name="test_category">
                <option value="">~~ SELECT CATEGORY ~~</option>
                <?php 
                $sql = "SELECT category_id, category_name FROM category WHERE status = '1' ORDER BY category_name";
                $result = $connect->query($sql);
                while($row = $result->fetch_array()) {
                    echo "<option value='".$row[0]."' data-name='".htmlspecialchars($row[1])."'>".$row[1]."</option>";
                }
                ?>
            </select>
            <input type="text" class="display-box" id="test_category_display" 
                   placeholder="Selected category will appear here" readonly/>
        </div>
    </div>
    
    <div class="test-section">
        <h2>Test Medicine Dropdown</h2>
        <div class="form-group">
            <label for="test_medicine">Medicine:</label>
            <select id="test_medicine" name="test_medicine">
                <option value="">~~ SELECT MEDICINE ~~</option>
                <?php 
                $medicine_sql = "SELECT DISTINCT name, description FROM medicines ORDER BY name";
                $medicine_result = $connect->query($medicine_sql);
                if ($medicine_result && $medicine_result->num_rows > 0) {
                    while($row = $medicine_result->fetch_array()) {
                        echo "<option value='".htmlspecialchars($row[0])."' data-description='".htmlspecialchars($row[1] ?: '')."'>".htmlspecialchars($row[0])."</option>";
                    }
                }
                ?>
            </select>
            <input type="text" class="display-box" id="test_medicine_display" 
                   placeholder="Selected medicine will appear here" readonly/>
        </div>
    </div>
    
    <div class="test-section">
        <h2>Test Pharmacy Dropdown</h2>
        <div class="form-group">
            <label for="test_pharmacy">Pharmacy:</label>
            <select id="test_pharmacy" name="test_pharmacy">
                <option value="">~~ SELECT PHARMACY ~~</option>
                <?php 
                $sql = "SELECT pharmacy_id, name FROM pharmacies ORDER BY name";
                $result = $connect->query($sql);
                while($row = $result->fetch_array()) {
                    echo "<option value='".$row[0]."' data-name='".$row[1]."'>".$row[1]."</option>";
                }
                ?>
            </select>
            <input type="text" class="display-box" id="test_pharmacy_display" 
                   placeholder="Selected pharmacy will appear here" readonly/>
        </div>
    </div>
    
    <div class="test-section">
        <h2>Test Input Fields</h2>
        <div class="form-group">
            <label for="test_price">Price (RWF):</label>
            <input type="number" id="test_price" placeholder="Enter price" />
            <input type="text" class="display-box" id="test_price_display" 
                   placeholder="Entered price will appear here" readonly/>
        </div>
        
        <div class="form-group">
            <label for="test_stock">Stock Quantity:</label>
            <input type="number" id="test_stock" placeholder="Enter stock quantity" />
            <input type="text" class="display-box" id="test_stock_display" 
                   placeholder="Entered stock quantity will appear here" readonly/>
        </div>
    </div>
    
    <div class="test-section">
        <h2>Test Controls</h2>
        <button class="test-button" onclick="testAllDropdowns()">Test All Dropdowns</button>
        <button class="test-button" onclick="clearAllFields()">Clear All Fields</button>
        <button class="test-button" onclick="window.location.href='add-product.php'">Go to Add Product Form</button>
    </div>
    
    <div class="test-section">
        <h2>Debug Information</h2>
        <div id="debug-info" style="background: #f8f9fa; padding: 10px; border-radius: 5px; font-family: monospace; white-space: pre-wrap;"></div>
    </div>

    <script>
        // Test dropdown functionality
        function setupDropdownTest(selectId, displayId, getValueFunction) {
            const select = document.getElementById(selectId);
            const display = document.getElementById(displayId);
            
            if (select && display) {
                select.addEventListener('change', function() {
                    const value = getValueFunction(this);
                    display.value = value || '';
                    
                    if (value) {
                        display.classList.add('has-value');
                    } else {
                        display.classList.remove('has-value');
                    }
                    
                    logDebug(`${selectId} changed: "${value}"`);
                });
            }
        }
        
        // Test input functionality
        function setupInputTest(inputId, displayId, formatFunction) {
            const input = document.getElementById(inputId);
            const display = document.getElementById(displayId);
            
            if (input && display) {
                input.addEventListener('input', function() {
                    const value = this.value;
                    const formattedValue = formatFunction(value);
                    display.value = formattedValue || '';
                    
                    if (formattedValue) {
                        display.classList.add('has-value');
                    } else {
                        display.classList.remove('has-value');
                    }
                    
                    logDebug(`${inputId} changed: "${value}" -> "${formattedValue}"`);
                });
            }
        }
        
        function logDebug(message) {
            const debugDiv = document.getElementById('debug-info');
            const timestamp = new Date().toLocaleTimeString();
            debugDiv.textContent += `[${timestamp}] ${message}\n`;
            debugDiv.scrollTop = debugDiv.scrollHeight;
        }
        
        function testAllDropdowns() {
            logDebug('Testing all dropdowns...');
            
            // Test category dropdown
            const categorySelect = document.getElementById('test_category');
            if (categorySelect && categorySelect.options.length > 1) {
                categorySelect.selectedIndex = 1;
                categorySelect.dispatchEvent(new Event('change'));
            }
            
            // Test medicine dropdown
            const medicineSelect = document.getElementById('test_medicine');
            if (medicineSelect && medicineSelect.options.length > 1) {
                medicineSelect.selectedIndex = 1;
                medicineSelect.dispatchEvent(new Event('change'));
            }
            
            // Test pharmacy dropdown
            const pharmacySelect = document.getElementById('test_pharmacy');
            if (pharmacySelect && pharmacySelect.options.length > 1) {
                pharmacySelect.selectedIndex = 1;
                pharmacySelect.dispatchEvent(new Event('change'));
            }
            
            // Test input fields
            const priceInput = document.getElementById('test_price');
            if (priceInput) {
                priceInput.value = '1500';
                priceInput.dispatchEvent(new Event('input'));
            }
            
            const stockInput = document.getElementById('test_stock');
            if (stockInput) {
                stockInput.value = '50';
                stockInput.dispatchEvent(new Event('input'));
            }
            
            logDebug('Test completed!');
        }
        
        function clearAllFields() {
            logDebug('Clearing all fields...');
            
            // Clear dropdowns
            document.getElementById('test_category').selectedIndex = 0;
            document.getElementById('test_medicine').selectedIndex = 0;
            document.getElementById('test_pharmacy').selectedIndex = 0;
            
            // Clear inputs
            document.getElementById('test_price').value = '';
            document.getElementById('test_stock').value = '';
            
            // Clear displays
            document.querySelectorAll('.display-box').forEach(box => {
                box.value = '';
                box.classList.remove('has-value');
            });
            
            logDebug('All fields cleared!');
        }
        
        // Initialize all tests
        document.addEventListener('DOMContentLoaded', function() {
            logDebug('Initializing dropdown tests...');
            
            // Setup dropdown tests
            setupDropdownTest('test_category', 'test_category_display', function(select) {
                const sel = select.options[select.selectedIndex];
                return sel ? sel.getAttribute('data-name') : '';
            });
            
            setupDropdownTest('test_medicine', 'test_medicine_display', function(select) {
                const sel = select.options[select.selectedIndex];
                return sel ? sel.textContent : '';
            });
            
            setupDropdownTest('test_pharmacy', 'test_pharmacy_display', function(select) {
                const sel = select.options[select.selectedIndex];
                return sel ? sel.getAttribute('data-name') : '';
            });
            
            // Setup input tests
            setupInputTest('test_price', 'test_price_display', function(value) {
                return value ? 'RWF ' + parseFloat(value).toLocaleString() : '';
            });
            
            setupInputTest('test_stock', 'test_stock_display', function(value) {
                return value ? value + ' units' : '';
            });
            
            logDebug('All tests initialized successfully!');
        });
    </script>
</body>
</html>
