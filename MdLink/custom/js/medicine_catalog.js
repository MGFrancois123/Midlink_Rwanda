$(document).ready(function() {
    console.log('Medicine catalog page loaded');
    
    // Load medicines on page load
    loadMedicines();
    
    // Form submission
    $('#medicineForm').on('submit', function(e) {
        e.preventDefault();
        submitMedicineForm();
    });
    
    // Reset button
    $('#resetBtn').on('click', function() {
        resetForm();
    });
});

// Load medicines table (for refresh after add/update/delete)
function loadMedicines() {
    console.log('Loading medicines...');
    
    $.ajax({
        url: 'php_action/manageMedicine.php?action=list',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Medicine list response:', response);
            
            if (response.success) {
                displayMedicines(response.data);
            } else {
                showSimpleMessage(response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Medicine list failed:', xhr, status, error);
            showSimpleMessage('Error loading medicines: ' + error, 'error');
        }
    });
}

// Display medicines in table
function displayMedicines(medicines) {
    let tbody = $('#medicinesTableBody');
    tbody.empty();
    
    if (medicines.length === 0) {
        tbody.html('<tr><td colspan="9" class="text-center">No medicines found</td></tr>');
        return;
    }
    
    medicines.forEach(function(medicine) {
        // Handle restricted medicine display
        let isRestricted = (medicine.restricted_medicine === 'Yes');
        let restrictedDisplay = medicine.restricted_medicine; // Already 'Yes' or 'No' from PHP
        let badgeClass = isRestricted ? 'badge-danger' : 'badge-success';
        
        let row = `
            <tr>
                <td>${medicine.medicine_id}</td>
                <td>${medicine.name}</td>
                <td>${medicine.pharmacy_name || 'N/A'}</td>
                <td>${medicine.category_name || 'N/A'}</td>
                <td>RWF ${medicine.price}</td>
                <td>${medicine.stock_quantity}</td>
                <td>${formatDate(medicine.expiry_date)}</td>
                <td><span class="badge ${badgeClass}">${restrictedDisplay}</span></td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="editMedicine(${medicine.medicine_id})">
                        <i class="fa fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteMedicine(${medicine.medicine_id})">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
}

// Submit medicine form
function submitMedicineForm(event) {
    if (event) {
        event.preventDefault();
    }
    console.log('Submitting medicine form...');
    
    const form = $('#medicineForm')[0];
    const formData = new FormData(form);
    
    // Show loading state
    const submitBtn = $('#submitBtn');
    const originalBtnText = submitBtn.html();
    submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
    
    $.ajax({
        url: 'php_action/manageMedicine.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Determine if this was an add or update operation
                const action = $('#medicineForm input[name="action"]').val();
                const successMessage = action === 'update' ? 'Medicine updated successfully!' : 'Medicine added successfully!';
                
                // Show success message (no modal)
                showSimpleMessage(successMessage, 'success');
                
                // Reset form and reload medicines list
                resetForm();
                loadMedicines();
            } else {
                const action = $('#medicineForm input[name="action"]').val();
                const errorMessage = action === 'update' ? 'Error updating medicine' : 'Error adding medicine';
                showSimpleMessage(response.message || errorMessage, 'error');
            }
        },
        error: function(xhr, status, error) {
            const action = $('#medicineForm input[name="action"]').val();
            const operation = action === 'update' ? 'updating' : 'adding';
            console.error(`Medicine ${operation} failed:`, error);
            showSimpleMessage(`Error ${operation} medicine: ` + error, 'error');
        },
        complete: function() {
            // Re-enable submit button
            submitBtn.prop('disabled', false).html(originalBtnText);
        }
    });
    
    return false; // Prevent default form submission
}

// Edit medicine
function editMedicine(medicineId) {
    console.log('Editing medicine:', medicineId);
    
    $.ajax({
        url: `php_action/manageMedicine.php?action=get&medicine_id=${medicineId}`,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                populateForm(response.data);
                $('#medicineForm input[name="action"]').val('update');
                $('#submitBtn').html('<i class="fa fa-save"></i> Update Medicine');
                $('#resetBtn').show();
            } else {
                showSimpleMessage(response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Medicine edit failed:', xhr, status, error);
            showSimpleMessage('Error loading medicine data: ' + error, 'error');
        }
    });
}

// Populate form with medicine data
function populateForm(medicine) {
    $('#medicine_id').val(medicine.medicine_id);
    $('#medicine_name').val(medicine.name);
    $('#medicine_pharmacy').val(medicine.pharmacy_id);
    $('#medicine_category').val(medicine.category_id);
    $('#medicine_description').val(medicine.description);
    $('#medicine_price').val(medicine.price);
    $('#medicine_stock').val(medicine.stock_quantity);
    $('#medicine_expiry').val(medicine.expiry_date);
    
    // Handle restricted medicine field
    let restrictedValue = 0;
    if (medicine.restricted_medicine !== undefined) {
        restrictedValue = medicine.restricted_medicine;
    } else if (medicine['Restricted Medicine'] !== undefined) {
        restrictedValue = medicine['Restricted Medicine'];
    } else if (medicine.Restricted_Medicine !== undefined) {
        restrictedValue = medicine.Restricted_Medicine;
    }
    // Convert to string and ensure it's '0' or '1'
    const restrictedString = String(Number(restrictedValue));
    console.log('Setting restricted value:', { restrictedValue, restrictedString, medicine });
    $('#medicine_restricted').val(restrictedString);
    
    // Update form title
    $('#medicineModalLabel').text('Edit Medicine');
    $('#medicineModal').modal('show');
}

// Delete medicine
function deleteMedicine(medicineId) {
    if (confirm('Are you sure you want to delete this medicine?')) {
        console.log('Deleting medicine:', medicineId);
        
        $.ajax({
            url: 'php_action/manageMedicine.php',
            type: 'POST',
            data: {
                action: 'delete',
                medicine_id: medicineId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Show success message and reload medicines list
                    showSimpleMessage('Medicine deleted successfully!', 'success');
                    loadMedicines();
                } else {
                    showSimpleMessage(response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Medicine delete failed:', xhr, status, error);
                showSimpleMessage('Error deleting medicine: ' + error, 'error');
            }
        });
    }
}

// Reset form
function resetForm() {
    $('#medicineForm')[0].reset();
    $('#medicineForm input[name="action"]').val('add');
    $('#medicine_id').val('');
    $('#submitBtn').html('<i class="fa fa-save"></i> Add Medicine');
    $('#resetBtn').hide();
}

// Reset medicine form (alias for compatibility)
function resetMedicineForm() {
    resetForm();
}

// Show popup message
function showPopupMessage(message, type) {
    let icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    let alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    
    let popupHtml = `
        <div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header ${alertClass}">
                        <h5 class="modal-title" id="messageModalLabel">
                            <i class="fa ${icon}"></i> ${type === 'success' ? 'Success' : 'Error'}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        ${message}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    $('#messageModal').remove();
    
    // Add new modal to body
    $('body').append(popupHtml);
    
    // Initialize modal with Bootstrap 4 settings
    var $modal = $('#messageModal');
    $modal.modal({
        backdrop: true,
        keyboard: true,
        focus: true,
        show: true
    });
    
    // ROBUST CLOSE BUTTON HANDLING FOR BOOTSTRAP 4
    
    // Method 1: Direct jQuery event binding (most reliable)
    $modal.find('.close').off('click.closeModal').on('click.closeModal', function(e) {
        console.log('Close button (×) clicked - Method 1');
        e.preventDefault();
        e.stopPropagation();
        $modal.modal('hide');
        return false;
    });
    
    // Method 2: Event delegation from document
    $(document).off('click.messageModalClose').on('click.messageModalClose', '#messageModal .close', function(e) {
        console.log('Close button (×) clicked - Method 2');
        e.preventDefault();
        e.stopPropagation();
        $('#messageModal').modal('hide');
        return false;
    });
    
    // Method 3: Target the span inside close button specifically
    $modal.find('.close span').off('click.closeSpan').on('click.closeSpan', function(e) {
        console.log('Close span (×) clicked - Method 3');
        e.preventDefault();
        e.stopPropagation();
        $modal.modal('hide');
        return false;
    });
    
    // Method 4: Handle data-dismiss attribute manually
    $modal.find('[data-dismiss="modal"]').off('click.dataDismiss').on('click.dataDismiss', function(e) {
        console.log('Data-dismiss clicked - Method 4');
        e.preventDefault();
        e.stopPropagation();
        $modal.modal('hide');
        return false;
    });
    
    // Method 5: Raw JavaScript event listener as fallback
    setTimeout(function() {
        var closeBtn = document.querySelector('#messageModal .close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function(e) {
                console.log('Close button clicked - Method 5 (Raw JS)');
                e.preventDefault();
                e.stopPropagation();
                $('#messageModal').modal('hide');
                return false;
            });
        }
        
        var closeSpan = document.querySelector('#messageModal .close span');
        if (closeSpan) {
            closeSpan.addEventListener('click', function(e) {
                console.log('Close span clicked - Method 5 (Raw JS)');
                e.preventDefault();
                e.stopPropagation();
                $('#messageModal').modal('hide');
                return false;
            });
        }
    }, 50);
    
    // Handle backdrop click to close modal
    $modal.on('click', function(e) {
        if (e.target === this) {
            console.log('Backdrop clicked');
            $modal.modal('hide');
        }
    });
    
    // Remove modal from DOM after hidden to avoid duplicates
    $modal.on('hidden.bs.modal', function () { 
        console.log('Modal hidden, removing from DOM');
        $(this).remove(); 
    });
    
    // Fallback cleanup in case hidden.bs.modal doesn't fire
    setTimeout(function(){ 
        if ($modal.length && !$modal.hasClass('show')) { 
            console.log('Fallback cleanup triggered');
            $modal.remove(); 
        } 
    }, 350);
    
    // Auto hide after 1 second for success messages
    if (type === 'success') {
        setTimeout(function() {
            console.log('Auto-hiding success modal');
            $modal.modal('hide');
        }, 1000);
    }
}

// Show simple message without modal
function showSimpleMessage(message, type) {
    // Remove any existing messages
    $('.alert-message').remove();
    
    // Create alert HTML
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show alert-message" role="alert" style="margin: 10px 0;">
            <i class="fa ${icon}"></i> ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
    
    // Find the best place to show the message
    let $container = $('.card-body').first();
    if ($container.length === 0) {
        $container = $('.container-fluid').first();
    }
    
    // Add the alert at the top
    $container.prepend(alertHtml);
    
    // Auto-dismiss success messages after 3 seconds
    if (type === 'success') {
        setTimeout(function() {
            $('.alert-message.alert-success').fadeOut(500, function() {
                $(this).remove();
            });
        }, 3000);
    }
}

// Show message (legacy function for compatibility)
function showMessage(message, type) {
    showSimpleMessage(message, type);
}

// Format date
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString();
}

// Global function for external calls - this is already defined above
