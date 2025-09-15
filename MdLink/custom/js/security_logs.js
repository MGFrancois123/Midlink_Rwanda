$(document).ready(function() {
    // Initialize DataTable
    var securityTable = $('#securityLogsTable').DataTable({
        "processing": true,
        "serverSide": false,
        "responsive": true,
        "pageLength": 10,
        "order": [[0, "desc"]],
        "columnDefs": [
            {
                "targets": [1], // Severity column
                "render": function(data, type, row) {
                    if (type === 'display') {
                        return getSeverityBadge(data);
                    }
                    return data;
                }
            },
            {
                "targets": [6], // Actions column
                "orderable": false,
                "render": function(data, type, row) {
                    return '<button class="btn btn-sm btn-info view-details" data-log-id="' + row[7] + '">' +
                           '<i class="fa fa-eye"></i> Details</button>';
                }
            }
        ],
        "language": {
            "emptyTable": "No security logs found",
            "processing": "Loading security logs..."
        }
    });

    // Load initial data
    loadSecurityLogs();
    loadSecurityStats();

    // Auto-refresh stats every 30 seconds
    setInterval(loadSecurityStats, 30000);

    // Filter button click
    $('#filter_security_logs').click(function() {
        loadSecurityLogs();
    });

    // Export buttons
    $('#export_security_csv').click(function() {
        exportSecurityLogs('csv');
    });

    $('#export_security_pdf').click(function() {
        exportSecurityLogs('pdf');
    });

    $('#generate_security_report').click(function() {
        generateSecurityReport();
    });

    // View details button click
    $(document).on('click', '.view-details', function() {
        var logId = $(this).data('log-id');
        viewLogDetails(logId);
    });

    // Set default date range (last 7 days)
    var today = new Date();
    var sevenDaysAgo = new Date(today.getTime() - (7 * 24 * 60 * 60 * 1000));
    
    $('#security_date_to').val(today.toISOString().split('T')[0]);
    $('#security_date_from').val(sevenDaysAgo.toISOString().split('T')[0]);

    function loadSecurityLogs() {
        var formData = {
            action: 'fetch',
            date_from: $('#security_date_from').val(),
            date_to: $('#security_date_to').val(),
            severity_filter: $('#security_severity_filter').val(),
            event_filter: $('#security_event_filter').val(),
            limit: 1000
        };

        $.ajax({
            url: 'php_action/security_logs.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend: function() {
                $('#filter_security_logs').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Loading...');
            },
            success: function(response) {
                if (response.success) {
                    // Clear existing data
                    securityTable.clear();
                    
                    // Add new data
                    response.data.forEach(function(log) {
                        var severityBadge = getSeverityBadge(log.severity);
                        var eventTypeBadge = getEventTypeBadge(log.event_type);
                        
                        securityTable.row.add([
                            log.created_at,
                            log.severity,
                            eventTypeBadge,
                            log.user,
                            '<div class="text-truncate" style="max-width: 300px;" title="' + 
                            log.description.replace(/"/g, '&quot;') + '">' + log.description + '</div>',
                            log.ip_address || '-',
                            '', // Actions column - will be rendered by columnDefs
                            log.log_id // Hidden column for actions
                        ]);
                    });
                    
                    // Redraw table
                    securityTable.draw();
                    
                    // Update info
                    showAlert('Loaded ' + response.data.length + ' security log entries', 'success');
                } else {
                    showAlert(response.message || 'Failed to load security logs', 'danger');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                showAlert('Error loading security logs: ' + error, 'danger');
            },
            complete: function() {
                $('#filter_security_logs').prop('disabled', false).html('<i class="fa fa-filter"></i> Filter');
            }
        });
    }

    function loadSecurityStats() {
        $.ajax({
            url: 'php_action/security_logs.php',
            type: 'POST',
            data: { action: 'fetch_stats' },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#total_events').text(response.stats.total_events);
                    $('#high_severity').text(response.stats.high_severity);
                    $('#failed_logins').text(response.stats.failed_logins);
                    $('#blocked_attempts').text(response.stats.blocked_attempts);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading security stats:', error);
            }
        });
    }

    function exportSecurityLogs(format) {
        var formData = {
            action: 'export_' + format,
            date_from: $('#security_date_from').val(),
            date_to: $('#security_date_to').val(),
            severity_filter: $('#security_severity_filter').val(),
            event_filter: $('#security_event_filter').val()
        };

        if (format === 'csv') {
            // Create a form and submit it to trigger download
            var form = $('<form>', {
                'method': 'POST',
                'action': 'php_action/security_logs.php'
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
                url: 'php_action/security_logs.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
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

    function generateSecurityReport() {
        var formData = {
            action: 'generate_report',
            date_from: $('#security_date_from').val(),
            date_to: $('#security_date_to').val()
        };

        $.ajax({
            url: 'php_action/security_logs.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend: function() {
                $('#generate_security_report').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Generating...');
            },
            success: function(response) {
                if (response.success) {
                    displaySecurityReport(response.report);
                } else {
                    showAlert(response.message || 'Failed to generate report', 'danger');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                showAlert('Error generating report: ' + error, 'danger');
            },
            complete: function() {
                $('#generate_security_report').prop('disabled', false).html('<i class="fa fa-file-text-o"></i> Generate Report');
            }
        });
    }

    function displaySecurityReport(report) {
        var reportHtml = '<div class="modal fade" id="securityReportModal" tabindex="-1" role="dialog">' +
                        '<div class="modal-dialog modal-lg" role="document">' +
                        '<div class="modal-content">' +
                        '<div class="modal-header">' +
                        '<h5 class="modal-title">Security Report (' + report.period.from + ' to ' + report.period.to + ')</h5>' +
                        '<button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>' +
                        '</div>' +
                        '<div class="modal-body">' +
                        '<div class="row">' +
                        '<div class="col-md-6">' +
                        '<h6>Summary</h6>' +
                        '<ul class="list-group">' +
                        '<li class="list-group-item d-flex justify-content-between">' +
                        '<span>Total Events</span><span class="badge badge-primary">' + report.summary.total_events + '</span>' +
                        '</li>' +
                        '<li class="list-group-item d-flex justify-content-between">' +
                        '<span>Critical Events</span><span class="badge badge-danger">' + report.summary.critical_events + '</span>' +
                        '</li>' +
                        '<li class="list-group-item d-flex justify-content-between">' +
                        '<span>High Severity</span><span class="badge badge-warning">' + report.summary.high_events + '</span>' +
                        '</li>' +
                        '</ul>' +
                        '</div>' +
                        '<div class="col-md-6">' +
                        '<h6>Event Types</h6>' +
                        '<ul class="list-group">';

        report.event_types.forEach(function(eventType) {
            reportHtml += '<li class="list-group-item d-flex justify-content-between">' +
                         '<span>' + eventType.event_type.replace(/_/g, ' ').toUpperCase() + '</span>' +
                         '<span class="badge badge-info">' + eventType.count + '</span>' +
                         '</li>';
        });

        reportHtml += '</ul></div></div></div>' +
                     '<div class="modal-footer">' +
                     '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>' +
                     '</div></div></div></div>';

        // Remove existing modal if any
        $('#securityReportModal').remove();
        
        // Add and show new modal
        $('body').append(reportHtml);
        $('#securityReportModal').modal('show');
    }

    function viewLogDetails(logId) {
        // This would show detailed information about a specific log entry
        // For now, we'll show a simple alert
        showAlert('Log details view - Feature to be implemented', 'info');
    }

    function getSeverityBadge(severity) {
        var badgeClass = '';
        switch (severity.toLowerCase()) {
            case 'critical':
                badgeClass = 'badge-danger';
                break;
            case 'high':
                badgeClass = 'badge-warning';
                break;
            case 'medium':
                badgeClass = 'badge-info';
                break;
            case 'low':
                badgeClass = 'badge-secondary';
                break;
            default:
                badgeClass = 'badge-light';
        }
        return '<span class="badge ' + badgeClass + '">' + severity.toUpperCase() + '</span>';
    }

    function getEventTypeBadge(eventType) {
        var badgeClass = '';
        switch (eventType.toLowerCase()) {
            case 'failed login':
                badgeClass = 'badge-warning';
                break;
            case 'suspicious activity':
                badgeClass = 'badge-danger';
                break;
            case 'unauthorized access':
                badgeClass = 'badge-danger';
                break;
            case 'data breach':
                badgeClass = 'badge-danger';
                break;
            case 'system intrusion':
                badgeClass = 'badge-danger';
                break;
            default:
                badgeClass = 'badge-info';
        }
        return '<span class="badge ' + badgeClass + '">' + eventType + '</span>';
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