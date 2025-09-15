// Initialize when document is ready
$(document).ready(function() {
    console.log('Initializing restricted medicines page...');
    
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Initialize popovers
    $('[data-bs-toggle="popover"]').popover({
        trigger: 'hover',
        html: true
    });
    
    // Initialize restricted medicines functionality
    if (typeof initRestrictedMedicines === 'function') {
        initRestrictedMedicines();
    } else {
        console.error('initRestrictedMedicines function not found');
    }
    
    // Handle modal close buttons
    $('.modal').on('hidden.bs.modal', function() {
        $(this).find('form')[0].reset();
    });
});

// Global function to show alerts
function showAlert(message, type = 'success') {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    const $alertContainer = $('#alertContainer');
    if ($alertContainer.length) {
        $alertContainer.html(alertHtml);
    } else {
        // If no alert container exists, prepend to the card body
        $('.card-body').first().prepend(alertHtml);
    }
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        $('.alert').alert('close');
    }, 5000);
}
