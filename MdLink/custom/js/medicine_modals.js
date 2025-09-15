// Initialize all modals on page load
$(document).ready(function() {
    // Initialize Bootstrap modals
    var modals = document.querySelectorAll('.modal');
    
    // Handle all close buttons and dismiss elements
    document.addEventListener('click', function(e) {
        // Support clicks on inner elements (e.g., <span> inside the button)
        var dismissEl = e.target.closest('[data-dismiss="modal"], .close, [data-bs-dismiss="modal"]');
        if (dismissEl) {
            e.preventDefault();
            var modal = dismissEl.closest('.modal');
            if (modal) {
                // Prefer Bootstrap 5 API if available, otherwise fallback to Bootstrap 4 jQuery API
                if (window.bootstrap && bootstrap.Modal) {
                    var bsModal = bootstrap.Modal.getInstance(modal);
                    if (!bsModal) {
                        bsModal = new bootstrap.Modal(modal);
                    }
                    bsModal.hide();
                } else if (window.jQuery) {
                    $(modal).modal('hide');
                } else {
                    // Last resort: remove modal from DOM
                    modal.style.display = 'none';
                    modal.classList.remove('show');
                }
            }
        }
    });

    modals.forEach(function(modal) {
        // Initialize each modal (Bootstrap 5 if available, else Bootstrap 4 jQuery init)
        if (window.bootstrap && bootstrap.Modal) {
            var bsModal = new bootstrap.Modal(modal, {
                backdrop: true,
                keyboard: true,
                focus: true
            });
        } else if (window.jQuery) {
            $(modal).modal({
                backdrop: true,
                keyboard: true,
                show: false
            });
        }
        
        // Reset form when modal is hidden
        modal.addEventListener('hidden.bs.modal', function() {
            var form = modal.querySelector('form');
            if (form) {
                form.reset();
                // Clear any validation errors
                $(form).find('.is-invalid').removeClass('is-invalid');
                $(form).find('.invalid-feedback').remove();
            }
        });
        
        // Handle form submission
        var form = modal.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (typeof validateForm === 'function') {
                    if (validateForm(form)) {
                        // If validation passes, submit the form via AJAX
                        submitForm(form, bsModal);
                    }
                } else {
                    // If no validation function exists, submit the form
                    submitForm(form, bsModal);
                }
            });
        }
    });
    
    // Function to submit form via AJAX
    function submitForm(form, modal) {
        var formData = new FormData(form);
        var submitBtn = form.querySelector('button[type="submit"]');
        var originalBtnText = submitBtn ? submitBtn.innerHTML : '';
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
        
        // Get form action and method
        var action = form.getAttribute('action') || window.location.href;
        var method = form.getAttribute('method') || 'POST';
        
        // Send AJAX request
        fetch(action, {
            method: method,
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showAlert(data.message || 'Operation completed successfully', 'success');
                
                // Close the modal if it exists
                if (modal) {
                    modal.hide();
                } else {
                    // Fallback: try to find and close the modal
                    var modalElement = form.closest('.modal');
                    if (modalElement) {
                        var bsModal = bootstrap.Modal.getInstance(modalElement);
                        if (bsModal) {
                            bsModal.hide();
                        } else {
                            $(modalElement).modal('hide');
                        }
                    }
                }
                
                // Refresh the page or update the table
                if (typeof loadRestrictedMedicines === 'function') {
                    loadRestrictedMedicines();
                } else if (typeof window.location.reload === 'function') {
                    setTimeout(() => window.location.reload(), 1000);
                }
            } else {
                // Show error message
                showAlert(data.message || 'An error occurred', 'danger');
                
                // Handle form validation errors
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        var input = form.querySelector(`[name="${field}"]`);
                        if (input) {
                            var errorDiv = document.createElement('div');
                            errorDiv.className = 'invalid-feedback';
                            errorDiv.textContent = data.errors[field];
                            
                            input.classList.add('is-invalid');
                            input.parentNode.appendChild(errorDiv);
                        }
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('An error occurred while processing your request', 'danger');
        })
        .finally(() => {
            // Reset button state if submit button exists
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        });
    }
});

// Global function to show alerts
function showAlert(message, type = 'success') {
    
    // Remove any existing alerts
    $('.alert-dismissible').alert('close');
    
    // Create alert HTML
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
    
    // Find the alert container or use the first card body
    let $alertContainer = $('#alertContainer');
    if ($alertContainer.length === 0) {
        $alertContainer = $('.card-body').first();
    }
    
    // Add the alert
    $alertContainer.prepend(alertHtml);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        $('.alert').alert('close');
    }, 5000);
}

// Function to validate form
function validateForm(form) {
    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');
    
    // Clear previous validation
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
    
    // Check required fields
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.textContent = 'This field is required';
            field.parentNode.appendChild(errorDiv);
            isValid = false;
        }
    });
    
    return isValid;
}
