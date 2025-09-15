$(document).ready(function() {
    // Initialize the page
    loadCategories();
    
    // Initialize real-time validation
    addRealTimeValidation();
    
    // Reset button
    $('#categoryResetBtn').on('click', function() {
        resetForm();
    });
    
    // Handle form submission via button click
    $('#categorySubmitBtn').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        submitCategoryForm();
        return false;
    });
});

// Expose wrapper to match inline form onsubmit handler
window.submitCategory = function(e){
    if (e && e.preventDefault) e.preventDefault();
    submitCategoryForm();
    return false;
};

// Load categories table
function loadCategories() {
    $.ajax({
        url: 'php_action/fetchCategories.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayCategories(response.data);
            } else {
                showErrorMessage('Error loading categories');
            }
        },
        error: function(xhr, status, error) {
            showErrorMessage('Error loading categories: ' + error);
        }
    });
}

// Display categories in table
function displayCategories(categories) {
    let tbody = $('#categoriesTableBody');
    tbody.empty();
    
    if (categories.length === 0) {
        tbody.html('<tr><td colspan="6" class="text-center">No categories found</td></tr>');
        return;
    }
    
    categories.forEach(function(category) {
        let row = `
            <tr>
                <td>${category.category_id}</td>
                <td>${category.category_name}</td>
                <td>${category.description || 'N/A'}</td>
                <td><span class="badge badge-success">${category.status}</span></td>
                <td>${formatDate(category.created_at)}</td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="editCategory(${category.category_id})">
                        <i class="fa fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteCategory(${category.category_id})">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
}

// Submit category form
function submitCategoryForm() {
    // Clear previous validation errors
    clearValidationErrors();
    
    // Validate form
    let isValid = true;
    let categoryName = $('#category_name').val().trim();
    let categoryStatus = $('#category_status').val();
    
    // Validate Category Name
    if (!categoryName) {
        showFieldError('category_name', 'Category name is required');
        isValid = false;
    } else if (categoryName.length < 2) {
        showFieldError('category_name', 'Category name must be at least 2 characters');
        isValid = false;
    }
    
    // Validate Status
    if (!categoryStatus) {
        showFieldError('category_status', 'Please select a status');
        isValid = false;
    }
    
    // If validation fails, show error message and return
    if (!isValid) {
        showErrorMessage('Please correct the errors below');
        return;
    }
    
    // Build payload for manageCategory.php API (handles both add and update)
    var isUpdate = ($('#categoryForm input[name="action"]').val() || '').toLowerCase() === 'update' || !!$('#category_id').val();
    var payload = {
        action: isUpdate ? 'update' : 'add',
        category_name: $('#category_name').val().trim(),
        description: $('#category_description').val() || '',
        is_active: $('#category_status').val() === '1' ? 1 : 0
    };
    if (isUpdate) { payload.category_id = $('#category_id').val(); }

    // Show loading state
    var originalBtnHtml = $('#categorySubmitBtn').html();
    $('#categorySubmitBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> ' + (isUpdate ? 'Updating...' : 'Adding...'));

    $.ajax({
        url: 'php_action/manageCategory.php',
        type: 'POST',
        data: payload,
        dataType: 'json',
        success: function(response) {
            // Reset button state
            $('#categorySubmitBtn').prop('disabled', false).html(originalBtnHtml);
            
            if (response && response.success) {
                showSuccessMessage(isUpdate ? 'Category updated successfully' : 'Category added successfully');
                resetForm();
                loadCategories();
            } else {
                showErrorMessage((response && (response.message || response.messages)) || 'Error saving category');
            }
        },
        error: function(xhr, status, error) {
            $('#categorySubmitBtn').prop('disabled', false).html(originalBtnHtml);
            showErrorMessage('Error submitting form: ' + (xhr.responseText || error));
        }
    });
}

// Show field-specific validation error
function showFieldError(fieldId, message) {
    let field = $('#' + fieldId);
    let errorHtml = '<div class="text-danger validation-error" style="font-size: 12px; margin-top: 5px;"><i class="fa fa-exclamation-circle"></i> ' + message + '</div>';
    
    field.addClass('is-invalid');
    field.after(errorHtml);
}

// Clear all validation errors
function clearValidationErrors() {
    $('.validation-error').remove();
    $('.is-invalid').removeClass('is-invalid');
    $('.is-valid').removeClass('is-valid');
}

// Add real-time validation
function addRealTimeValidation() {
    $('#category_name').on('input', function() {
        let value = $(this).val().trim();
        let field = $(this);
        
        // Remove existing error
        field.next('.validation-error').remove();
        field.removeClass('is-invalid is-valid');
        
        if (value.length === 0) {
            showFieldError('category_name', 'Category name is required');
        } else if (value.length < 2) {
            showFieldError('category_name', 'Category name must be at least 2 characters');
        } else {
            field.addClass('is-valid');
        }
    });
    
    $('#category_status').on('change', function() {
        let value = $(this).val();
        let field = $(this);
        
        // Remove existing error
        field.next('.validation-error').remove();
        field.removeClass('is-invalid is-valid');
        
        if (!value) {
            showFieldError('category_status', 'Please select a status');
        } else {
            field.addClass('is-valid');
        }
    });
}

// Edit category
function editCategory(categoryArg) {
    // Support being called with either an id or a full category object
    var categoryId = categoryArg && categoryArg.category_id ? categoryArg.category_id : categoryArg;
    $.ajax({
        url: `php_action/manageCategory.php?action=get&category_id=${categoryId}`,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                populateForm(response.data);
                $('#categoryForm input[name="action"]').val('update');
                $('#categorySubmitBtn').html('<i class="fa fa-save"></i> Update Category');
                $('#categoryResetBtn').show();
            } else {
                showPopupMessage(response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            showPopupMessage('Error loading category data: ' + error, 'error');
        }
    });
}

// Populate form with category data
function populateForm(category) {
    $('#category_id').val(category.category_id);
    $('#category_name').val(category.category_name);
    $('#category_description').val(category.description);
    // Set status based on the category status field
    if (category.status === 'Available' || category.status === '1') {
        $('#category_status').val(1);
    } else {
        $('#category_status').val(0);
    }
}

// Delete category
function deleteCategory(categoryId) {
    if (confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
        $.ajax({
            url: 'php_action/manageCategory.php',
            type: 'POST',
            data: {
                action: 'delete',
                category_id: categoryId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showPopupMessage(response.message, 'success');
                    loadCategories();
                } else {
                    showPopupMessage(response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                showPopupMessage('Error deleting category: ' + error, 'error');
            }
        });
    }
}

// Reset form
function resetForm() {
    $('#categoryForm')[0].reset();
    $('#categoryForm input[name="action"]').val('add');
    $('#category_id').val('');
    $('#categorySubmitBtn').html('<i class="fa fa-save"></i> Add Category');
    $('#categoryResetBtn').hide();
    
    // Clear validation errors
    clearValidationErrors();
}

// Show success message at top of form
function showSuccessMessage(message) {
    let successHtml = `
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="background-color: #d4edda; border-color: #c3e6cb; color: #155724; font-size: 16px; padding: 15px;">
            <i class="fa fa-check-circle" style="font-size: 18px;"></i> <strong>Success!</strong> ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
    
    // Remove any existing messages
    $('#category-messages').empty();
    
    // Add success message
    $('#category-messages').html(successHtml);
    
    // Scroll to top to show the message immediately
    $('html, body').animate({
        scrollTop: 0
    }, 300);
    
    // Auto hide after 5 seconds
    setTimeout(function() {
        $('#category-messages .alert').fadeOut();
    }, 5000);
}

// Show error message at top of form
function showErrorMessage(message) {
    let errorHtml = `
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-circle"></i> <strong>Error!</strong> ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
    
    // Remove any existing messages
    $('#category-messages').empty();
    
    // Add error message
    $('#category-messages').html(errorHtml);
}

// Show popup message (legacy function for compatibility)
function showPopupMessage(message, type) {
    if (type === 'success') {
        showSuccessMessage(message);
    } else {
        showErrorMessage(message);
    }
}

// Show message (legacy function for compatibility)
function showMessage(message, type) {
    showPopupMessage(message, type);
}

// Format date
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString();
}
