$(document).ready(function() {
    // Initialize DataTable
    var auditTable = $('#auditLogsTable').DataTable({
        "processing": true,
        "serverSide": false,
        "responsive": true,
        "pageLength": 10,
        "order": [[0, "desc"]],
        "columnDefs": [
            {
                "targets": [5], // Changes column
                "orderable": false,
                "render": function(data, type, row) {
                    if (type === 'display' && data.length > 100) {
                        return '<div class="text-truncate" style="max-width: 200px;" title="' + 
                               data.replace(/"/g, '&quot;') + '">' + data.substring(0, 100) + '...</div>';
                    }
                    return data;
                }
            }
        ],
        "language": {
            "emptyTable": "No audit logs found",
            "processing": "Loading audit logs..."
        }
    });

    // Load initial data
    loadAuditLogs();

    // Filter button click
    $('#filter_audit_logs').click(function() {
        loadAuditLogs();
    });

    // Export CSV button
    $('#export_audit_csv').click(function() {
        exportAuditLogs('csv');
    });

    // Export PDF button
    $('#export_audit_pdf').click(function() {
        exportAuditLogs('pdf');
    });

    // Set default date range (last 30 days)
    var today = new Date();
    var thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));
    
    $('#audit_date_to').val(today.toISOString().split('T')[0]);
    $('#audit_date_from').val(thirtyDaysAgo.toISOString().split('T')[0]);

    function loadAuditLogs() {
        var formData = {
            action: 'fetch',
            date_from: $('#audit_date_from').val(),
            date_to: $('#audit_date_to').val(),
            action_filter: $('#audit_action_filter').val(),
            entity_filter: $('#audit_entity_filter').val(),
            limit: 1000
        };

        $.ajax({
            url: 'php_action/audit_logs.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend: function() {
                $('#filter_audit_logs').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Loading...');
            },
            success: function(response) {
                if (response.success) {
                    // Clear existing data
                    auditTable.clear();
                    
                    // Add new data
                    response.data.forEach(function(log) {
                        var actionBadge = getActionBadge(log.action);
                        var entityBadge = getEntityBadge(log.entity_type);
                        
                        auditTable.row.add([
                            log.created_at,
                            log.user,
                            actionBadge,
                            entityBadge,
                            log.entity_id || '-',
                            log.changes || '-',
                            log.ip_address || '-'
                        ]);
                    });
                    
                    // Redraw table
                    auditTable.draw();
                    
                    // Update info
                    showAlert('Loaded ' + response.data.length + ' audit log entries', 'success');
                } else {
                    showAlert(response.message || 'Failed to load audit logs', 'danger');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                showAlert('Error loading audit logs: ' + error, 'danger');
            },
            complete: function() {
                $('#filter_audit_logs').prop('disabled', false).html('<i class="fa fa-filter"></i> Filter');
            }
        });
    }

    function exportAuditLogs(format) {
        var formData = {
            action: 'export_' + format,
            date_from: $('#audit_date_from').val(),
            date_to: $('#audit_date_to').val(),
            action_filter: $('#audit_action_filter').val(),
            entity_filter: $('#audit_entity_filter').val()
        };

        if (format === 'csv') {
            // Create a form and submit it to trigger download
            var form = $('<form>', {
                'method': 'POST',
                'action': 'php_action/audit_logs.php'
            });

            $.each(formData, function(key, value) {
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': key,
                    'value': value
                }));
            });

            $('body').append(form);
            form.submit();
            form.remove();
        } else if (format === 'pdf') {
            $.ajax({
                url: 'php_action/audit_logs.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Handle PDF download
                        window.open(response.pdf_url, '_blank');
                    } else {
                        showAlert(response.message || 'PDF export failed', 'warning');
                    }
                },
                error: function() {
                    showAlert('Error exporting to PDF', 'danger');
                }
            });
        }
    }

    function getActionBadge(action) {
        var badgeClass = '';
        switch (action.toLowerCase()) {
            case 'create':
                badgeClass = 'badge-success';
                break;
            case 'update':
                badgeClass = 'badge-warning';
                break;
            case 'delete':
                badgeClass = 'badge-danger';
                break;
            case 'login':
                badgeClass = 'badge-info';
                break;
            case 'logout':
                badgeClass = 'badge-secondary';
                break;
            default:
                badgeClass = 'badge-primary';
        }
        return '<span class="badge ' + badgeClass + '">' + action + '</span>';
    }

    function getEntityBadge(entity) {
        var badgeClass = '';
        switch (entity.toLowerCase()) {
            case 'user':
                badgeClass = 'badge-primary';
                break;
            case 'medicine':
                badgeClass = 'badge-success';
                break;
            case 'pharmacy':
                badgeClass = 'badge-info';
                break;
            case 'order':
                badgeClass = 'badge-warning';
                break;
            case 'category':
                badgeClass = 'badge-secondary';
                break;
            default:
                badgeClass = 'badge-light';
        }
        return '<span class="badge ' + badgeClass + '">' + entity + '</span>';
    }

    function showAlert(message, type) {
        var alertClass = 'alert-' + type;
        var alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
                       message +
                       '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                       '<span aria-hidden="true">&times;</span>' +
                       '</button>' +
                       '</div>';
        
        // Remove existing alerts
        $('.alert').remove();
        
        // Add new alert at the top of the card body
        $('.card-body').prepend(alertHtml);
        
        // Auto-hide success alerts after 5 seconds
        if (type === 'success') {
            setTimeout(function() {
                $('.alert-success').fadeOut();
            }, 5000);
        }
    }
});