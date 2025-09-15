/**
 * Restricted Medicines Management
 * Handles all functionality for the restricted medicines page
 */

// Test function to verify script loading
console.log('restricted_medicines.js loaded successfully!');

// Global variables with default values
let currentPage = 1;
let itemsPerPage = 10;
let totalItems = 0;
let allMedicines = [];
let currentFilters = {};
let isInitialized = false;
let selectedMedicines = [];

// Main initialization function
function initRestrictedMedicines() {
    console.log('Initializing restricted medicines functionality...');
    
    try {
        // Check if required elements exist
        if ($('#restricted_search').length === 0) {
            console.error('Required elements not found on the page');
            return;
        }
        
        console.log('Required elements found, initializing...');
    // Initialize Select2 for regular dropdowns
    if (typeof $.fn.select2 === 'function') {
      $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: 'Select an option'
      });
    }
    
    // Initialize Select2 with AJAX for medicine search
    if (typeof $.fn.select2 === 'function') {
      $('#restricted_search').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: 'Search for a medicine...',
        allowClear: true,
        minimumInputLength: 2,
        ajax: {
            url: 'php_action/searchMedicines.php',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term, // search term
                    page: params.page || 1
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results,
                    pagination: {
                        more: (params.page * 10) < data.total_count
                    }
                };
            },
            cache: true
        }
      });
    }

    // Load restricted medicines when page loads
    loadRestrictedMedicines();

    // Handle search select change with debounce
    let searchTimer;
    $('#restricted_search').on('change', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            currentPage = 1;
            loadRestrictedMedicines();
        }, 500);
    });
    
    // Handle reset filters button
    $('#resetFiltersBtn').on('click', function() {
        $('#restricted_search').val('').trigger('change');
        $('#description_filter').val('');
        $('#restricted_category_filter').val('').trigger('change');
        $('#restricted_pharmacy_filter').val('').trigger('change');
        currentPage = 1;
        loadRestrictedMedicines();
    });

    // Handle export to Excel
    $('#exportExcelBtn').on('click', exportToExcel);
    
    // Handle export to PDF
    $('#exportPdfBtn').on('click', exportToPdf);
    
    // Handle print
    $('#printBtn').on('click', printTable);
    
    // Handle bulk action button
    $('#bulkActionBtn').on('click', showBulkActions);
    
    // Handle select all checkbox
    $('#selectAllCheckbox').on('change', function() {
        const isChecked = $(this).prop('checked');
        $('.medicine-checkbox').prop('checked', isChecked);
        updateSelectedCount();
    });
    
    // Handle individual checkbox changes
    $(document).on('change', '.medicine-checkbox', function() {
        updateSelectedCount();
    });
    
    // Add description filter to the change event
    $('#description_filter, #restricted_category_filter, #restricted_pharmacy_filter, #itemsPerPage').on('change', function() {
        currentPage = 1;
        loadRestrictedMedicines();
    });
    
        // Populate filter options
        populateFilterOptions();

        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
        
        // Load initial data
        loadRestrictedMedicines();
        
        console.log('Restricted medicines functionality initialized successfully');
    } catch (error) {
        console.error('Error initializing restricted medicines:', error);
        showAlert('danger', 'Failed to initialize page. Please check console for details.');
    }
}

// Initialize when document is ready
$(document).ready(function() {
    console.log('restricted_medicines.js: Document ready');
    
    // Make initRestrictedMedicines available globally
    window.initRestrictedMedicines = initRestrictedMedicines;
    
    // Check if we should auto-initialize
    if (typeof window.autoInitRestrictedMedicines === 'undefined' || window.autoInitRestrictedMedicines !== false) {
        console.log('Auto-initializing restricted medicines...');
        initRestrictedMedicines();
    } else {
        console.log('Waiting for manual initialization...');
    }
});

function loadRestrictedMedicines() {
    // Update current filters from the form
    updateCurrentFilters();
    
    // Show loading state
    const tbody = $('#restrictedMedicinesTableBody');
    tbody.html(`
        <tr>
            <td colspan="10" class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p>Loading restricted medicines...</p>
            </td>
        </tr>
    `);
    
    // Add loading class to table
    $('#restrictedMedicinesTable').addClass('table-loading');
    
    // Prepare request data with current filters and pagination
    const params = {
        ...currentFilters,
        page: currentPage,
        per_page: itemsPerPage,
        _: new Date().getTime() // Cache buster
    };
    
    // Make AJAX request
    $.ajax({
        url: 'php_action/getRestrictedMedicines.php',
        type: 'GET',
        data: params,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                allMedicines = response.data || [];
                totalItems = response.meta?.total || 0;
                
                // Update pagination info
                if (response.meta) {
                    currentPage = response.meta.current_page;
                    itemsPerPage = response.meta.per_page;
                }
                
                displayRestrictedMedicines(allMedicines);
                updatePagination();
                updatePageInfo(response.meta);
                
                // Update URL with current filters
                updateUrlWithFilters();
                
                // Initialize only once
                if (!isInitialized) {
                    initializeEventListeners();
                    isInitialized = true;
                }
            } else {
                showAlert('danger', response.message || 'Failed to load restricted medicines');
            }
        },
        error: function(xhr, status, error) {
            const errorMessage = xhr.responseJSON?.message || 
                               (xhr.responseText || 'An unknown error occurred');
            showAlert('danger', 'Error: ' + errorMessage);
            console.error('Error loading restricted medicines:', error, xhr);
            showAlert('error', errorMessage);
            console.error('AJAX Error:', error);
        },
        complete: function() {
            // Re-initialize tooltips after content load
            $('[data-toggle="tooltip"]').tooltip();
        }
    });
}

/**
 * Display restricted medicines in the table
 */
function displayRestrictedMedicines(medicines) {
    const tbody = $('#restrictedMedicinesTableBody');
    tbody.empty();
    
    if (!medicines || medicines.length === 0) {
        tbody.append(`
            <tr>
                <td colspan="10" class="text-center py-4">
                    <div class="text-muted">
                        <i class="fa fa-inbox fa-3x mb-3"></i>
                        <h5>No restricted medicines found</h5>
                        <p class="mb-0">Try adjusting your search or filter criteria</p>
                    </div>
                </td>
            </tr>
        `);
        return;
    }
    
    medicines.forEach(function(medicine) {
        const restrictionLevel = getRestrictionLevel(medicine.Restricted_Medicine);
        const restrictionClass = getRestrictionClass(medicine.Restricted_Medicine);
        const expiryDate = medicine.expiry_date ? formatDate(medicine.expiry_date) : 'N/A';
        const isExpired = isDateExpired(medicine.expiry_date);
        const expiryClass = isExpired ? 'text-danger' : '';
        const stockClass = medicine.stock_quantity <= 10 ? 'text-warning' : '';
        
        const row = `
            <tr>
                <td>${medicine.medicine_id}</td>
                <td>
                    <strong>${escapeHtml(medicine.name)}</strong>
                    ${medicine.description ? `<br><small class="text-muted">${truncateText(medicine.description, 50)}</small>` : ''}
                </td>
                <td>${medicine.category_name ? `<span class="badge badge-info">${escapeHtml(medicine.category_name)}</span>` : 'N/A'}</td>
                <td>${medicine.pharmacy_name ? escapeHtml(medicine.pharmacy_name) : 'N/A'}</td>
                <td class="${stockClass}">
                    <i class="fa fa-cubes"></i> ${medicine.stock_quantity}
                </td>
                <td class="${expiryClass}">
                    ${expiryDate} 
                    ${isExpired ? '<i class="fa fa-exclamation-triangle ml-1" data-toggle="tooltip" title="Expired"></i>' : ''}
                </td>
                <td>
                    <span class="badge ${medicine.status === '1' ? 'badge-success' : 'badge-secondary'}">
                        ${medicine.status === '1' ? 'Active' : 'Inactive'}
                    </span>
                </td>
                <td>
                    <span class="badge ${restrictionClass} p-2">
                        <i class="fa fa-shield"></i> ${restrictionLevel}
                    </span>
                </td>
                <td class="text-center">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                onclick="viewRestrictionDetails(${medicine.medicine_id})" 
                                data-toggle="tooltip" title="View Details">
                            <i class="fa fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-info" 
                                onclick="editRestriction(${medicine.medicine_id})"
                                data-toggle="tooltip" title="Edit">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" 
                                onclick="printLabel(${medicine.medicine_id})"
                                data-toggle="tooltip" title="Print Label">
                            <i class="fa fa-print"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
    
    // Update selected medicines count
    updateSelectedCount();
    
    // Initialize tooltips for new elements
    $('[data-toggle="tooltip"]').tooltip();
}

function getRestrictionLevel(restricted) {
    switch(parseInt(restricted)) {
        case 1: return 'Level 1';
        case 2: return 'Level 2';
        case 3: return 'Level 3';
        default: return 'Unknown';
    }
}

function getRestrictionClass(restricted) {
    switch(parseInt(restricted)) {
        case 1: return 'badge-warning';
        case 2: return 'badge-danger';
        case 3: return 'badge-dark';
        default: return 'badge-secondary';
    }
}

// Format date to readable format
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Check if date is expired
function isDateExpired(dateString) {
    if (!dateString) return false;
    const expiryDate = new Date(dateString);
    const today = new Date();
    return expiryDate < today;
}

// Truncate text with ellipsis
function truncateText(text, maxLength) {
    if (!text) return '';
    return text.length > maxLength 
        ? text.substring(0, maxLength) + '...' 
        : text;
}

// Escape HTML to prevent XSS
function escapeHtml(unsafe) {
    if (!unsafe) return '';
    return unsafe
        .toString()
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// Show alert message
function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
    
    // Remove any existing alerts
    $('.alert-dismissible').alert('close');
    
    // Add new alert
    $('.card-body').prepend(alertHtml);
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        $('.alert-dismissible').alert('close');
    }, 5000);
}

// Update pagination controls
function updatePagination() {
    const totalPages = Math.ceil(totalItems / itemsPerPage);
    const pagination = $('#pagination');
    pagination.empty();
    
    if (totalPages <= 1) return;
    
    // Previous button
    const prevDisabled = currentPage === 1 ? 'disabled' : '';
    pagination.append(`
        <li class="page-item ${prevDisabled}">
            <a class="page-link" href="#" onclick="changePage(${currentPage - 1}); return false;">
                <i class="fa fa-chevron-left"></i>
            </a>
        </li>
    `);
    
    // Page numbers
    const maxVisiblePages = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
    
    if (endPage - startPage + 1 < maxVisiblePages) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }
    
    if (startPage > 1) {
        pagination.append(`
            <li class="page-item">
                <a class="page-link" href="#" onclick="changePage(1); return false;">1</a>
            </li>
        `);
        if (startPage > 2) {
            pagination.append('<li class="page-item disabled"><span class="page-link">...</span></li>');
        }
    }
    
    for (let i = startPage; i <= endPage; i++) {
        const active = i === currentPage ? 'active' : '';
        pagination.append(`
            <li class="page-item ${active}">
                <a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a>
            </li>
        `);
    }
    
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            pagination.append('<li class="page-item disabled"><span class="page-link">...</span></li>');
        }
        pagination.append(`
            <li class="page-item">
                <a class="page-link" href="#" onclick="changePage(${totalPages}); return false;">${totalPages}</a>
            </li>
        `);
    }
    
    // Next button
    const nextDisabled = currentPage === totalPages ? 'disabled' : '';
    pagination.append(`
        <li class="page-item ${nextDisabled}">
            <a class="page-link" href="#" onclick="changePage(${currentPage + 1}); return false;">
                <i class="fa fa-chevron-right"></i>
            </a>
        </li>
    `);
}

// Change current page
function changePage(page) {
    if (page < 1 || page > Math.ceil(totalItems / itemsPerPage)) return;
    currentPage = page;
    loadRestrictedMedicines();
    // Scroll to top of table
    $('html, body').animate({
        scrollTop: $('#restrictedMedicinesTable').offset().top - 100
    }, 500);
}

// Update page info text
function updatePageInfo() {
    const start = (currentPage - 1) * itemsPerPage + 1;
    const end = Math.min(currentPage * itemsPerPage, totalItems);
    $('#pageInfo').text(`Showing ${start} to ${end} of ${totalItems} entries`);
}

// Update selected medicines count
function updateSelectedCount() {
    const selectedCount = $('.medicine-checkbox:checked').length;
    const bulkBtn = $('#bulkActionBtn');
    
    if (selectedCount > 0) {
        bulkBtn.prop('disabled', false);
        bulkBtn.html(`<i class="fa fa-cog"></i> ${selectedCount} Selected`);
    } else {
        bulkBtn.prop('disabled', true);
        bulkBtn.html('<i class="fa fa-cog"></i> Bulk Actions');
    }
}

// Export to Excel
function updateCurrentFilters() {
    const selected = $('#restricted_search').select2('data');
    currentFilters = {
        search: selected && selected[0] ? selected[0].text : '',
        category: $('#restricted_category_filter').val() || 0,
        pharmacy: $('#restricted_pharmacy_filter').val() || 0
    };
}

// Load categories and pharmacies into filters
function populateFilterOptions() {
    // Categories
    $.getJSON('php_action/getCategories.php')
      .done(function(res) {
        const $cat = $('#restricted_category_filter');
        const current = $cat.val();
        $cat.empty().append('<option value="">All</option>');
        if (res && res.success && Array.isArray(res.data)) {
          res.data.forEach(function(c){
            $cat.append('<option value="'+ (c.category_id || '') +'">'+ (c.category_name || '') +'</option>');
          });
        } else {
          console.warn('getCategories returned no data');
        }
        if (current) { $cat.val(current); }
        if (typeof $.fn.select2 === 'function') { $cat.trigger('change.select2'); }
      })
      .fail(function(xhr){ console.error('Failed to load categories', xhr && xhr.responseText); });
    // Pharmacies
    $.getJSON('php_action/getPharmacies.php')
      .done(function(res) {
        const $ph = $('#restricted_pharmacy_filter');
        const current = $ph.val();
        $ph.empty().append('<option value="">All</option>');
        if (res && res.success && Array.isArray(res.data)) {
          res.data.forEach(function(p){
            $ph.append('<option value="'+ (p.pharmacy_id || '') +'">'+ (p.name || '') +'</option>');
          });
        } else {
          console.warn('getPharmacies returned no data');
        }
        if (current) { $ph.val(current); }
        if (typeof $.fn.select2 === 'function') { $ph.trigger('change.select2'); }
      })
      .fail(function(xhr){ console.error('Failed to load pharmacies', xhr && xhr.responseText); });
}

function exportToExcel() {
    const search = currentFilters.search;
    const category = $('#restricted_category_filter').val();
    const pharmacy = $('#restricted_pharmacy_filter').val();
    
    showAlert('info', 'Preparing Excel export...');
    console.log('Exporting to Excel with filters - Search:', search, 'Category:', category, 'Pharmacy:', pharmacy);
    
    // In a real implementation, you would use something like:
    // window.location.href = `export_excel.php?search=${encodeURIComponent(search)}&category=${encodeURIComponent(category)}&pharmacy=${encodeURIComponent(pharmacy)}`;
}

// Export to PDF
function exportToPdf() {
    const search = $('#restricted_search').val();
    const category = $('#restricted_category_filter').val();
    const pharmacy = $('#restricted_pharmacy_filter').val();
    
    showAlert('info', 'Preparing PDF export...');
    console.log('Exporting to PDF with filters - Search:', search, 'Category:', category, 'Pharmacy:', pharmacy);
    
    // In a real implementation, you would use something like:
    // window.open(`export_pdf.php?search=${encodeURIComponent(search)}&category=${encodeURIComponent(category)}&pharmacy=${encodeURIComponent(pharmacy)}`, '_blank');
}

// Print table
function printTable() {
    showAlert('info', 'Opening print dialog...');
    
    // In a real implementation, you would use something like:
    // window.print();
    
    // Or for a custom print view:
    /*
    const printWindow = window.open('', '_blank');
    const tableHtml = $('#restrictedMedicinesTable').clone();
    printWindow.document.write(`
        <html>
            <head>
                <title>Restricted Medicines Report</title>
                <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
                <style>
                    @media print {
                        @page { size: landscape; }
                        body { padding: 20px; }
                        .no-print { display: none !important; }
                        table { font-size: 12px; }
                    }
                </style>
            </head>
            <body>
                <h2>Restricted Medicines Report</h2>
                <p>Generated on: ${new Date().toLocaleString()}</p>
                ${tableHtml.prop('outerHTML')}
                <div class="text-muted text-center mt-4 no-print">
                    <p>--- End of Report ---</p>
                    <p>Generated by MdLink Rwanda System</p>
                </div>
                <script>
                    window.onload = function() {
                        window.print();
                        setTimeout(function() { window.close(); }, 1000);
                    };
                <\/script>
            </body>
        </html>
    `);
    */
}

// Show bulk actions modal
function showBulkActions() {
    const selectedIds = [];
    $('.medicine-checkbox:checked').each(function() {
        selectedIds.push($(this).val());
    });
    
    if (selectedIds.length === 0) {
        showAlert('warning', 'Please select at least one medicine');
        return;
    }
    
    const modalHtml = `
        <div class="modal fade" id="bulkActionsModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fa fa-tasks"></i> Bulk Actions (${selectedIds.length} items)
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Select Action</label>
                            <select class="form-control" id="bulkActionSelect">
                                <option value="">-- Select Action --</option>
                                <option value="print_labels">Print Labels</option>
                                <option value="export">Export Selected</option>
                                <option value="update_status">Update Status</option>
                                <option value="update_restriction">Update Restriction Level</option>
                                <option value="delete" class="text-danger">Delete Selected</option>
                            </select>
                        </div>
                        
                        <div id="bulkActionOptions" class="mt-3">
                            <!-- Dynamic options will be inserted here -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fa fa-times"></i> Cancel
                        </button>
                        <button type="button" class="btn btn-primary" id="applyBulkAction">
                            <i class="fa fa-check"></i> Apply
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    $('#bulkActionsModal').remove();
    
    // Add new modal to body
    $('body').append(modalHtml);
    
    // Show modal
    const modal = $('#bulkActionsModal');
    modal.modal('show');
    
    // Handle action selection
    $('#bulkActionSelect').on('change', function() {
        const action = $(this).val();
        let optionsHtml = '';
        
        switch(action) {
            case 'update_status':
                optionsHtml = `
                    <div class="form-group">
                        <label>New Status</label>
                        <select class="form-control" id="newStatus">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                `;
                break;
                
            case 'update_restriction':
                optionsHtml = `
                    <div class="form-group">
                        <label>Restriction Level</label>
                        <select class="form-control" id="newRestrictionLevel">
                            <option value="1">Level 1 - Pharmacist Approval</option>
                            <option value="2">Level 2 - Doctor's Prescription</option>
                            <option value="3">Level 3 - Controlled Substance</option>
                        </select>
                    </div>
                `;
                break;
                
            case 'print_labels':
                optionsHtml = `
                    <div class="form-group">
                        <label>Label Format</label>
                        <select class="form-control" id="labelFormat">
                            <option value="small">Small (30 per sheet)</option>
                            <option value="medium">Medium (20 per sheet)</option>
                            <option value="large">Large (10 per sheet)</option>
                        </select>
                    </div>
                `;
                break;
                
            case 'export':
                optionsHtml = `
                    <div class="form-group">
                        <label>Export Format</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="exportFormat" id="exportExcel" value="excel" checked>
                            <label class="form-check-label" for="exportExcel">
                                <i class="fa fa-file-excel-o text-success"></i> Excel (.xlsx)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="exportFormat" id="exportPdf" value="pdf">
                            <label class="form-check-label" for="exportPdf">
                                <i class="fa fa-file-pdf-o text-danger"></i> PDF (.pdf)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="exportFormat" id="exportCsv" value="csv">
                            <label class="form-check-label" for="exportCsv">
                                <i class="fa fa-file-text-o text-info"></i> CSV (.csv)
                            </label>
                        </div>
                    </div>
                `;
                break;
                
            case 'delete':
                optionsHtml = `
                    <div class="alert alert-danger">
                        <i class="fa fa-exclamation-triangle"></i>
                        <strong>Warning:</strong> This action cannot be undone. Are you sure you want to delete the selected items?
                    </div>
                `;
                break;
        }
        
        $('#bulkActionOptions').html(optionsHtml);
    });
    
    // Handle apply action
    $('#applyBulkAction').on('click', function() {
        const action = $('#bulkActionSelect').val();
        
        if (!action) {
            showAlert('warning', 'Please select an action');
            return;
        }
        
        // Get selected medicine IDs
        const selectedIds = [];
        $('.medicine-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });
        
        // Perform action based on selection
        switch(action) {
            case 'print_labels':
                const format = $('#labelFormat').val();
                printLabels(selectedIds, format);
                break;
                
            case 'export':
                const exportFormat = $('input[name="exportFormat"]:checked').val();
                exportSelected(selectedIds, exportFormat);
                break;
                
            case 'update_status':
                const newStatus = $('#newStatus').val();
                updateStatus(selectedIds, newStatus);
                break;
                
            case 'update_restriction':
                const newLevel = $('#newRestrictionLevel').val();
                updateRestrictionLevel(selectedIds, newLevel);
                break;
                
            case 'delete':
                if (confirm('Are you sure you want to delete the selected items? This action cannot be undone.')) {
                    deleteSelected(selectedIds);
                }
                break;
        }
        
        // Close modal
        modal.modal('hide');
    });
}

// Print labels for selected medicines
function printLabels(ids, format) {
    showAlert('info', `Preparing to print ${ids.length} labels in ${format} format...`);
    console.log('Printing labels for IDs:', ids, 'Format:', format);
    
    // In a real implementation, you would use something like:
    // window.open(`print_labels.php?ids=${ids.join(',')}&format=${format}`, '_blank');
}

// Export selected items
function exportSelected(ids, format) {
    showAlert('info', `Exporting ${ids.length} items as ${format.toUpperCase()}...`);
    console.log('Exporting IDs:', ids, 'Format:', format);
    
    // In a real implementation, you would use something like:
    // window.location.href = `export.php?ids=${ids.join(',')}&format=${format}`;
}

// Update status of selected items
function updateStatus(ids, status) {
    const statusText = status === '1' ? 'Active' : 'Inactive';
    showAlert('success', `Updating ${ids.length} items to status: ${statusText}...`);
    console.log('Updating status for IDs:', ids, 'New status:', status);
    
    // In a real implementation, you would use something like:
    /*
    $.post('update_status.php', {
        ids: ids,
        status: status
    }, function(response) {
        if (response.success) {
            showAlert('success', 'Status updated successfully');
            loadRestrictedMedicines();
        } else {
            showAlert('error', 'Failed to update status: ' + response.message);
        }
    }, 'json');
    */
}

// Update restriction level of selected items
function updateRestrictionLevel(ids, level) {
    const levelText = getRestrictionLevel(level);
    showAlert('success', `Updating ${ids.length} items to restriction level: ${levelText}...`);
    console.log('Updating restriction level for IDs:', ids, 'New level:', level);
    
    // In a real implementation, you would use something like:
    /*
    $.post('update_restriction.php', {
        ids: ids,
        level: level
    }, function(response) {
        if (response.success) {
            showAlert('success', 'Restriction level updated successfully');
            loadRestrictedMedicines();
        } else {
            showAlert('error', 'Failed to update restriction level: ' + response.message);
        }
    }, 'json');
    */
}

// Delete selected items
function deleteSelected(ids) {
    showAlert('warning', `Deleting ${ids.length} items...`);
    console.log('Deleting IDs:', ids);
    
    // In a real implementation, you would use something like:
    /*
    if (confirm('Are you sure you want to delete the selected items? This action cannot be undone.')) {
        $.post('delete_items.php', {
            ids: ids
        }, function(response) {
            if (response.success) {
                showAlert('success', 'Items deleted successfully');
                loadRestrictedMedicines();
            } else {
                showAlert('error', 'Failed to delete items: ' + response.message);
            }
        }, 'json');
    }
    */
}

function viewRestrictionDetails(medicineId) {
    $.ajax({
        url: 'php_action/getMedicine.php',
        type: 'GET',
        data: { medicine_id: medicineId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showRestrictionModal(response.data);
            } else {
                alert('Error loading medicine details: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            alert('An error occurred while loading medicine details.');
        }
    });
}

function showRestrictionModal(medicine) {
    let modalHtml = `
        <div class="modal fade" id="restrictionModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Restricted Medicine Details</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Medicine Information</h6>
                                <p><strong>Name:</strong> ${medicine.name}</p>
                                <p><strong>Category:</strong> ${medicine.category_name || 'N/A'}</p>
                                <p><strong>Description:</strong> ${medicine.description || 'N/A'}</p>
                                <p><strong>Price:</strong> ${medicine.price} RWF</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Restriction Details</h6>
                                <p><strong>Restriction Level:</strong> ${getRestrictionLevel(medicine.Restricted_Medicine)}</p>
                                <p><strong>Stock Quantity:</strong> ${medicine.stock_quantity}</p>
                                <p><strong>Expiry Date:</strong> ${medicine.expiry_date}</p>
                                <p><strong>Pharmacy:</strong> ${medicine.pharmacy_name || 'N/A'}</p>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <h6>Restriction Guidelines</h6>
                                <div class="alert alert-warning">
                                    <i class="fa fa-exclamation-triangle"></i>
                                    <strong>Important:</strong> This medicine requires special authorization and monitoring.
                                    <ul class="mt-2 mb-0">
                                        <li>Prescription required from authorized healthcare provider</li>
                                        <li>Patient identification and documentation mandatory</li>
                                        <li>Quantity limits may apply</li>
                                        <li>Regular monitoring and reporting required</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    $('#restrictionModal').remove();
    
    // Add new modal to body
    $('body').append(modalHtml);
    
    // Show modal
    $('#restrictionModal').modal('show');
}

function editRestriction(medicineId) {
    // Redirect to medicine edit page
    window.location.href = 'placeholder.php?title=Add%20%2F%20Update%20Medicines&edit=' + medicineId;
}

// Auto-refresh restricted medicines every 30 seconds
setInterval(function() {
    let search = $('#restricted_search').val();
    let category = $('#restricted_category_filter').val();
    if (!search && !category) { // Only auto-refresh if no filters are applied
        loadRestrictedMedicines();
    }
}, 30000);

// Initialize Select2 for the search dropdown
$('#restricted_search').select2({
    theme: 'bootstrap4',
    placeholder: 'Select a medicine...',
    allowClear: true
});
