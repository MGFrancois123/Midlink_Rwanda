$(document).ready(function() {
    // Initialize DataTable
    var submissionsTable = $('#submissionsTable').DataTable({
        "processing": true,
        "serverSide": false,
        "responsive": true,
        "pageLength": 10,
        "order": [[4, "desc"]], // Order by submission date
        "columnDefs": [
            {
                "targets": [6], // Status column
                "render": function(data, type, row) {
                    if (type === 'display') {
                        return getStatusBadge(data);
                    }
                    return data;
                }
            },
            {
                "targets": [7], // Actions column
                "orderable": false,
                "render": function(data, type, row) {
                    var submissionId = row[8]; // Hidden column with submission ID
                    return '<div class="btn-group" role="group">' +
                           '<button class="btn btn-sm btn-info view-submission" data-id="' + submissionId + '">' +
                           '<i class="fa fa-eye"></i></button>' +
                           '<button class="btn btn-sm btn-warning edit-submission" data-id="' + submissionId + '">' +
                           '<i class="fa fa-edit"></i></button>' +
                           '<button class="btn btn-sm btn-danger delete-submission" data-id="' + submissionId + '">' +
                           '<i class="fa fa-trash"></i></button>' +
                           '</div>';
                }
            }
        ],
        "language": {
            "emptyTable": "No regulatory submissions found",
            "processing": "Loading submissions..."
        }
    });

    // Load initial data
    loadSubmissions();
    loadSubmissionStats();

    // Filter button click
    $('#filter_submissions').click(function() {
        loadSubmissions();
    });

    // Clear filters button
    $('#clear_filters').click(function() {
        $('#submission_status_filter').val('');
        $('#submission_type_filter').val('');
        $('#submission_date_from').val('');
        $('#submission_date_to').val('');
        loadSubmissions();
    });

    // Export and report buttons
    $('#export_submissions_csv').click(function() {
        exportSubmissions();
    });

    $('#generate_compliance_report').click(function() {
        generateComplianceReport();
    });

    // Modal form submission
    $('#save_submission').click(function() {
        saveSubmission();
    });

    // Table action buttons
    $(document).on('click', '.view-submission', function() {
        var submissionId = $(this).data('id');
        viewSubmission(submissionId);
    });

    $(document).on('click', '.edit-submission', function() {
        var submissionId = $(this).data('id');
        editSubmission(submissionId);
    });

    $(document).on('click', '.delete-submission', function() {
        var submissionId = $(this).data('id');
        deleteSubmission(submissionId);
    });

    // Modal events
    $('#submissionModal').on('show.bs.modal', function() {
        // Set default submission date to today
        if (!$('#submission_date').val()) {
            $('#submission_date').val(new Date().toISOString().split('T')[0]);
        }
    });

    $('#submissionModal').on('hidden.bs.modal', function() {
        resetForm();
    });

    function loadSubmissions() {
        var formData = {
            action: 'fetch',
            status_filter: $('#submission_status_filter').val(),
            type_filter: $('#submission_type_filter').val(),
            date_from: $('#submission_date_from').val(),
            date_to: $('#submission_date_to').val(),
            limit: 1000
        };

        $.ajax({
            url: 'php_action/regulatory_submissions.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend: function() {
                $('#filter_submissions').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Loading...');
            },
            success: function(response) {
                if (response.success) {
                    // Clear existing data
                    submissionsTable.clear();
                    
                    // Add new data
                    response.data.forEach(function(submission) {
                        var statusBadge = getStatusBadge(submission.status);
                        var dueDateDisplay = submission.due_date || '-';
                        
                        // Highlight overdue submissions
                        if (submission.is_overdue) {
                            dueDateDisplay = '<span class="text-danger font-weight-bold">' + dueDateDisplay + ' (OVERDUE)</span>';
                        }
                        
                        submissionsTable.row.add([
                            submission.reference_number,
                            '<div class="text-truncate" style="max-width: 200px;" title="' + 
                            submission.title.replace(/"/g, '&quot;') + '">' + submission.title + '</div>',
                            submission.submission_type,
                            submission.submitted_by,
                            submission.submission_date,
                            dueDateDisplay,
                            submission.status,
                            '', // Actions column - will be rendered by columnDefs
                            submission.submission_id // Hidden column for actions
                        ]);
                    });
                    
                    // Redraw table
                    submissionsTable.draw();
                    
                    // Update info
                    showAlert('Loaded ' + response.data.length + ' submissions', 'success');
                } else {
                    showAlert(response.message || 'Failed to load submissions', 'danger');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                showAlert('Error loading submissions: ' + error, 'danger');
            },
            complete: function() {
                $('#filter_submissions').prop('disabled', false).html('<i class="fa fa-filter"></i> Filter');
            }
        });
    }

    function loadSubmissionStats() {
        $.ajax({
            url: 'php_action/regulatory_submissions.php',
            type: 'POST',
            data: { action: 'fetch_stats' },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#total_submissions').text(response.stats.total);
                    $('#pending_submissions').text(response.stats.pending);
                    $('#under_review_submissions').text(response.stats.under_review);
                    $('#approved_submissions').text(response.stats.approved);
                    $('#rejected_submissions').text(response.stats.rejected);
                    $('#overdue_submissions').text(response.stats.overdue);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading submission stats:', error);
            }
        });
    }

    function saveSubmission() {
        var formData = new FormData($('#submissionForm')[0]);
        var submissionId = $('#submission_id').val();
        
        if (submissionId) {
            formData.set('action', 'update');
        } else {
            formData.set('action', 'create');
        }

        $.ajax({
            url: 'php_action/regulatory_submissions.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function() {
                $('#save_submission').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
            },
            success: function(response) {
                if (response.success) {
                    showAlert(response.message, 'success');
                    $('#submissionModal').modal('hide');
                    loadSubmissions();
                    loadSubmissionStats();
                } else {
                    showAlert(response.message || 'Failed to save submission', 'danger');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                showAlert('Error saving submission: ' + error, 'danger');
            },
            complete: function() {
                $('#save_submission').prop('disabled', false).html('<i class="fa fa-save"></i> Save Submission');
            }
        });
    }

    function viewSubmission(submissionId) {
        $.ajax({
            url: 'php_action/regulatory_submissions.php',
            type: 'GET',
            data: { 
                action: 'fetch_single',
                submission_id: submissionId 
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    displaySubmissionDetails(response.data);
                } else {
                    showAlert(response.message || 'Failed to load submission details', 'danger');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                showAlert('Error loading submission details: ' + error, 'danger');
            }
        });
    }

    function editSubmission(submissionId) {
        $.ajax({
            url: 'php_action/regulatory_submissions.php',
            type: 'GET',
            data: { 
                action: 'fetch_single',
                submission_id: submissionId 
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    populateForm(response.data);
                    $('#submissionModalTitle').text('Edit Regulatory Submission');
                    $('#submissionModal').modal('show');
                } else {
                    showAlert(response.message || 'Failed to load submission for editing', 'danger');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                showAlert('Error loading submission for editing: ' + error, 'danger');
            }
        });
    }

    function deleteSubmission(submissionId) {
        if (confirm('Are you sure you want to delete this submission? This action cannot be undone.')) {
            $.ajax({
                url: 'php_action/regulatory_submissions.php',
                type: 'POST',
                data: { 
                    action: 'delete',
                    submission_id: submissionId 
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showAlert(response.message, 'success');
                        loadSubmissions();
                        loadSubmissionStats();
                    } else {
                        showAlert(response.message || 'Failed to delete submission', 'danger');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    showAlert('Error deleting submission: ' + error, 'danger');
                }
            });
        }
    }

    function exportSubmissions() {
        var formData = {
            action: 'export_csv',
            status_filter: $('#submission_status_filter').val(),
            type_filter: $('#submission_type_filter').val(),
            date_from: $('#submission_date_from').val(),
            date_to: $('#submission_date_to').val()
        };

        // Create a form and submit it to trigger download
        var form = $('<form>', {
            'method': 'POST',
            'action': 'php_action/regulatory_submissions.php'
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
    }

    function generateComplianceReport() {
        var formData = {
            action: 'generate_compliance_report',
            date_from: $('#submission_date_from').val() || new Date(new Date().setFullYear(new Date().getFullYear() - 1)).toISOString().split('T')[0],
            date_to: $('#submission_date_to').val() || new Date().toISOString().split('T')[0]
        };

        $.ajax({
            url: 'php_action/regulatory_submissions.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend: function() {
                $('#generate_compliance_report').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Generating...');
            },
            success: function(response) {
                if (response.success) {
                    displayComplianceReport(response.report);
                } else {
                    showAlert(response.message || 'Failed to generate compliance report', 'danger');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                showAlert('Error generating compliance report: ' + error, 'danger');
            },
            complete: function() {
                $('#generate_compliance_report').prop('disabled', false).html('<i class="fa fa-file-text-o"></i> Compliance Report');
            }
        });
    }

    function displaySubmissionDetails(submission) {
        var detailsHtml = '<div class="modal fade" id="submissionDetailsModal" tabindex="-1" role="dialog">' +
                         '<div class="modal-dialog modal-lg" role="document">' +
                         '<div class="modal-content">' +
                         '<div class="modal-header">' +
                         '<h5 class="modal-title">Submission Details - ' + submission.reference_number + '</h5>' +
                         '<button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>' +
                         '</div>' +
                         '<div class="modal-body">' +
                         '<div class="row">' +
                         '<div class="col-md-6"><strong>Title:</strong> ' + submission.title + '</div>' +
                         '<div class="col-md-6"><strong>Type:</strong> ' + submission.submission_type.replace(/_/g, ' ').toUpperCase() + '</div>' +
                         '</div><br>' +
                         '<div class="row">' +
                         '<div class="col-md-6"><strong>Status:</strong> ' + getStatusBadge(submission.status) + '</div>' +
                         '<div class="col-md-6"><strong>Submitted By:</strong> ' + submission.submitted_by_name + '</div>' +
                         '</div><br>' +
                         '<div class="row">' +
                         '<div class="col-md-6"><strong>Submission Date:</strong> ' + submission.submission_date + '</div>' +
                         '<div class="col-md-6"><strong>Due Date:</strong> ' + (submission.due_date || 'Not set') + '</div>' +
                         '</div><br>';

        if (submission.description) {
            detailsHtml += '<div class="row"><div class="col-md-12"><strong>Description:</strong><br>' + 
                          submission.description + '</div></div><br>';
        }

        if (submission.attachments && submission.attachments.length > 0) {
            detailsHtml += '<div class="row"><div class="col-md-12"><strong>Attachments:</strong><ul>';
            submission.attachments.forEach(function(attachment) {
                detailsHtml += '<li>' + attachment.original_name + ' (' + formatFileSize(attachment.file_size) + ')</li>';
            });
            detailsHtml += '</ul></div></div><br>';
        }

        if (submission.notes) {
            detailsHtml += '<div class="row"><div class="col-md-12"><strong>Notes:</strong><br>' + 
                          submission.notes + '</div></div>';
        }

        detailsHtml += '</div>' +
                      '<div class="modal-footer">' +
                      '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>' +
                      '</div></div></div></div>';

        // Remove existing modal if any
        $('#submissionDetailsModal').remove();
        
        // Add and show new modal
        $('body').append(detailsHtml);
        $('#submissionDetailsModal').modal('show');
    }

    function displayComplianceReport(report) {
        var reportHtml = '<div class="modal fade" id="complianceReportModal" tabindex="-1" role="dialog">' +
                        '<div class="modal-dialog modal-lg" role="document">' +
                        '<div class="modal-content">' +
                        '<div class="modal-header">' +
                        '<h5 class="modal-title">Compliance Report (' + report.period.from + ' to ' + report.period.to + ')</h5>' +
                        '<button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>' +
                        '</div>' +
                        '<div class="modal-body">' +
                        '<div class="row">' +
                        '<div class="col-md-6">' +
                        '<h6>Summary Statistics</h6>' +
                        '<ul class="list-group">' +
                        '<li class="list-group-item d-flex justify-content-between">' +
                        '<span>Total Submissions</span><span class="badge badge-primary">' + report.summary.total_submissions + '</span>' +
                        '</li>' +
                        '<li class="list-group-item d-flex justify-content-between">' +
                        '<span>Approved</span><span class="badge badge-success">' + report.summary.approved + '</span>' +
                        '</li>' +
                        '<li class="list-group-item d-flex justify-content-between">' +
                        '<span>Rejected</span><span class="badge badge-danger">' + report.summary.rejected + '</span>' +
                        '</li>' +
                        '<li class="list-group-item d-flex justify-content-between">' +
                        '<span>Overdue</span><span class="badge badge-warning">' + report.summary.overdue + '</span>' +
                        '</li>' +
                        '</ul>' +
                        '</div>' +
                        '<div class="col-md-6">' +
                        '<h6>By Submission Type</h6>' +
                        '<ul class="list-group">';

        report.by_type.forEach(function(type) {
            reportHtml += '<li class="list-group-item d-flex justify-content-between">' +
                         '<span>' + type.submission_type.replace(/_/g, ' ').toUpperCase() + '</span>' +
                         '<span class="badge badge-info">' + type.count + '</span>' +
                         '</li>';
        });

        reportHtml += '</ul></div></div></div>' +
                     '<div class="modal-footer">' +
                     '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>' +
                     '</div></div></div></div>';

        // Remove existing modal if any
        $('#complianceReportModal').remove();
        
        // Add and show new modal
        $('body').append(reportHtml);
        $('#complianceReportModal').modal('show');
    }

    function populateForm(submission) {
        $('#submission_id').val(submission.submission_id);
        $('#submission_type').val(submission.submission_type);
        $('#reference_number').val(submission.reference_number);
        $('#submission_title').val(submission.title);
        $('#submission_description').val(submission.description);
        $('#submission_date').val(submission.submission_date);
        $('#due_date').val(submission.due_date);
        $('#submission_status').val(submission.status);
        $('#submission_notes').val(submission.notes);
    }

    function resetForm() {
        $('#submissionForm')[0].reset();
        $('#submission_id').val('');
        $('#submissionModalTitle').text('New Regulatory Submission');
    }

    function getStatusBadge(status) {
        var badgeClass = '';
        switch (status.toLowerCase()) {
            case 'approved':
                badgeClass = 'badge-success';
                break;
            case 'rejected':
                badgeClass = 'badge-danger';
                break;
            case 'under_review':
                badgeClass = 'badge-info';
                break;
            case 'submitted':
                badgeClass = 'badge-warning';
                break;
            case 'draft':
                badgeClass = 'badge-secondary';
                break;
            case 'withdrawn':
                badgeClass = 'badge-dark';
                break;
            default:
                badgeClass = 'badge-light';
        }
        return '<span class="badge ' + badgeClass + '">' + status.toUpperCase() + '</span>';
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        var k = 1024;
        var sizes = ['Bytes', 'KB', 'MB', 'GB'];
        var i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
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