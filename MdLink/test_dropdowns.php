<?php
require_once 'constant/connect.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Dropdowns</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Test Dropdowns</h2>
        
        <div class="row">
            <div class="col-md-6">
                <h4>Pharmacy Dropdown</h4>
                <select id="pharmacy_dropdown" class="form-control">
                    <option value="">Loading...</option>
                </select>
            </div>
            
            <div class="col-md-6">
                <h4>Category Dropdown</h4>
                <select id="category_dropdown" class="form-control">
                    <option value="">Loading...</option>
                </select>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <h4>Test Results</h4>
                <div id="results"></div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Test pharmacy dropdown
            $.ajax({
                url: 'actions/fetch_data.php?fetch=pharmacies',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        let options = '<option value="">Select Pharmacy</option>';
                        response.data.forEach(function(pharmacy) {
                            options += `<option value="${pharmacy.pharmacy_id}">${pharmacy.name}</option>`;
                        });
                        $('#pharmacy_dropdown').html(options);
                        $('#results').append('<p class="text-success">✓ Pharmacy dropdown loaded successfully</p>');
                    } else {
                        $('#results').append('<p class="text-danger">✗ Error loading pharmacies: ' + response.message + '</p>');
                    }
                },
                error: function(xhr, status, error) {
                    $('#results').append('<p class="text-danger">✗ AJAX Error loading pharmacies: ' + error + '</p>');
                }
            });
            
            // Test category dropdown
            $.ajax({
                url: 'actions/fetch_data.php?fetch=categories',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        let options = '<option value="">Select Category</option>';
                        response.data.forEach(function(category) {
                            options += `<option value="${category.category_id}">${category.category_name}</option>`;
                        });
                        $('#category_dropdown').html(options);
                        $('#results').append('<p class="text-success">✓ Category dropdown loaded successfully</p>');
                    } else {
                        $('#results').append('<p class="text-danger">✗ Error loading categories: ' + response.message + '</p>');
                    }
                },
                error: function(xhr, status, error) {
                    $('#results').append('<p class="text-danger">✗ AJAX Error loading categories: ' + error + '</p>');
                }
            });
        });
    </script>
</body>
</html>
