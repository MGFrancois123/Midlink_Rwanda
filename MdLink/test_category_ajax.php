<!DOCTYPE html>
<html>
<head>
    <title>Category AJAX Test</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select, textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .success { color: green; background: #d4edda; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .error { color: red; background: #f8d7da; padding: 10px; border-radius: 4px; margin: 10px 0; }
        #messages { margin: 10px 0; }
        #debug { background: #f8f9fa; padding: 10px; border-radius: 4px; margin: 10px 0; font-family: monospace; }
    </style>
</head>
<body>
    <h1>Category AJAX Test</h1>
    
    <div id="messages"></div>
    <div id="debug"></div>
    
    <form id="categoryForm">
        <div class="form-group">
            <label>Category Name *</label>
            <input type="text" name="categoriesName" id="category_name" required>
        </div>
        
        <div class="form-group">
            <label>Status *</label>
            <select name="categoriesStatus" id="category_status" required>
                <option value="">Select Status</option>
                <option value="1">Available</option>
                <option value="2">Not Available</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" id="category_description" rows="3"></textarea>
        </div>
        
        <button type="button" id="submitBtn">Add Category via AJAX</button>
    </form>
    
    <h2>Current Categories</h2>
    <div id="categoriesList">Loading...</div>
    
    <script>
        $(document).ready(function() {
            // Load categories on page load
            loadCategories();
            
            // Handle form submission
            $('#submitBtn').on('click', function() {
                submitCategory();
            });
        });
        
        function submitCategory() {
            let formData = new FormData($('#categoryForm')[0]);
            
            // Debug: Log form data
            $('#debug').html('<strong>Form Data:</strong><br>');
            for (let pair of formData.entries()) {
                $('#debug').append(pair[0] + ': ' + pair[1] + '<br>');
            }
            
            $('#submitBtn').prop('disabled', true).text('Adding...');
            
            $.ajax({
                url: 'php_action/createCategories_test.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    $('#debug').append('<strong>Response:</strong> ' + JSON.stringify(response) + '<br>');
                    
                    if (response.success) {
                        showMessage('✅ ' + response.messages, 'success');
                        $('#categoryForm')[0].reset();
                        loadCategories();
                    } else {
                        showMessage('❌ ' + response.messages, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    $('#debug').append('<strong>Error:</strong> ' + error + '<br>');
                    $('#debug').append('<strong>Response Text:</strong> ' + xhr.responseText + '<br>');
                    showMessage('❌ AJAX Error: ' + error, 'error');
                },
                complete: function() {
                    $('#submitBtn').prop('disabled', false).text('Add Category via AJAX');
                }
            });
        }
        
        function loadCategories() {
            $.ajax({
                url: 'php_action/fetchCategories.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data.length > 0) {
                        let html = '<table border="1" style="width:100%; border-collapse: collapse;">';
                        html += '<tr><th>ID</th><th>Name</th><th>Description</th><th>Status</th><th>Created</th></tr>';
                        
                        response.data.forEach(function(category) {
                            html += '<tr>';
                            html += '<td>' + category.category_id + '</td>';
                            html += '<td>' + category.category_name + '</td>';
                            html += '<td>' + (category.description || 'N/A') + '</td>';
                            html += '<td>' + category.status + '</td>';
                            html += '<td>' + category.created_at + '</td>';
                            html += '</tr>';
                        });
                        
                        html += '</table>';
                        $('#categoriesList').html(html);
                    } else {
                        $('#categoriesList').html('<p>No categories found.</p>');
                    }
                },
                error: function(xhr, status, error) {
                    $('#categoriesList').html('<p>Error loading categories: ' + error + '</p>');
                }
            });
        }
        
        function showMessage(message, type) {
            $('#messages').html('<div class="' + type + '">' + message + '</div>');
        }
    </script>
    
    <p><a href="placeholder.php?title=Categories">← Back to Categories Page</a></p>
</body>
</html>
