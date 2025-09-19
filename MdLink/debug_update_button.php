<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once './constant/connect.php';
require_once './constant/check.php';

// Check if we're in edit mode
$edit_mode = false;
$pharmacy_data = null;
$edit_pharmacy_id = null;

if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit_pharmacy_id = (int)$_GET['edit'];
    $edit_mode = true;
    
    try {
        // Fetch pharmacy data for editing
        $edit_query = "SELECT * FROM pharmacies WHERE pharmacy_id = ?";
        $edit_stmt = $connect->prepare($edit_query);
        $edit_stmt->bind_param("i", $edit_pharmacy_id);
        $edit_stmt->execute();
        $edit_result = $edit_stmt->get_result();
        
        if ($edit_result && $edit_result->num_rows > 0) {
            $pharmacy_data = $edit_result->fetch_assoc();
        } else {
            echo "Pharmacy not found!";
            exit();
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
} else {
    echo "No edit ID provided!";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Update Button - MdLink Rwanda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h1><i class="fa fa-bug"></i> Debug Update Button</h1>
                <p class="text-muted">Testing the update button functionality for pharmacy ID: <?php echo $edit_pharmacy_id; ?></p>
                
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="fa fa-edit"></i> Edit Pharmacy: <?php echo htmlspecialchars($pharmacy_data['name']); ?></h4>
                    </div>
                    <div class="card-body">
                        <div id="editPharmacyMessage"></div>
                        
                        <form id="editPharmacyForm">
                            <input type="hidden" name="pharmacy_id" value="<?php echo $pharmacy_data['pharmacy_id']; ?>">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label><i class="fa fa-hospital-o"></i> Pharmacy Name *</label>
                                        <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($pharmacy_data['name']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label><i class="fa fa-id-badge"></i> License Number *</label>
                                        <input type="text" class="form-control" name="license_number" value="<?php echo htmlspecialchars($pharmacy_data['license_number']); ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label><i class="fa fa-user-md"></i> Contact Person *</label>
                                        <input type="text" class="form-control" name="contact_person" value="<?php echo htmlspecialchars($pharmacy_data['contact_person']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label><i class="fa fa-phone"></i> Contact Phone *</label>
                                        <input type="tel" class="form-control" name="contact_phone" value="<?php echo htmlspecialchars($pharmacy_data['contact_phone']); ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label><i class="fa fa-map-marker"></i> Location *</label>
                                        <input type="text" class="form-control" name="location" value="<?php echo htmlspecialchars($pharmacy_data['location']); ?>" required>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <a href="manage_pharmacies.php" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left"></i> Cancel
                                </a>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="button" class="btn btn-primary" id="btnUpdatePharmacy">
                                    <i class="fa fa-save"></i> Update Pharmacy
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h5>Debug Information:</h5>
                    <div class="alert alert-info">
                        <strong>Pharmacy ID:</strong> <?php echo $pharmacy_data['pharmacy_id']; ?><br>
                        <strong>Current Name:</strong> <?php echo htmlspecialchars($pharmacy_data['name']); ?><br>
                        <strong>Current License:</strong> <?php echo htmlspecialchars($pharmacy_data['license_number']); ?><br>
                        <strong>Current Contact:</strong> <?php echo htmlspecialchars($pharmacy_data['contact_person']); ?><br>
                        <strong>Current Phone:</strong> <?php echo htmlspecialchars($pharmacy_data['contact_phone']); ?><br>
                        <strong>Current Location:</strong> <?php echo htmlspecialchars($pharmacy_data['location']); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        console.log('Document ready');
        console.log('Button element:', $('#btnUpdatePharmacy'));
        
        // Test button click
        $('#btnUpdatePharmacy').on('click', function() {
            console.log('Update button clicked!');
            alert('Button click detected! Check console for details.');
            
            const form = document.getElementById('editPharmacyForm');
            const formData = new FormData(form);
            const btn = this;
            
            console.log('Form data:', formData);
            console.log('Form validation:', form.checkValidity());
            
            // Validate form
            if (!form.checkValidity()) {
                console.log('Form validation failed');
                form.reportValidity();
                return;
            }
            
            console.log('Form validation passed, submitting...');
            
            // Disable button and show loading
            $(btn).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');
            
            // Submit form
            $.ajax({
                url: 'php_action/update_pharmacy.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    console.log('Success response:', response);
                    if (response.success) {
                        $('#editPharmacyMessage').html(
                            '<div class="alert alert-success"><i class="fa fa-check-circle"></i> Pharmacy updated successfully!</div>'
                        );
                        
                        setTimeout(() => {
                            window.location.href = 'manage_pharmacies.php?success=Pharmacy updated successfully';
                        }, 2000);
                    } else {
                        $('#editPharmacyMessage').html(
                            '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> ' + 
                            (response.message || 'Failed to update pharmacy') + '</div>'
                        );
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error response:', xhr, status, error);
                    $('#editPharmacyMessage').html(
                        '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> ' + 
                        'Network error. Please try again. Error: ' + error + '</div>'
                    );
                },
                complete: function() {
                    $(btn).prop('disabled', false).html('<i class="fa fa-save"></i> Update Pharmacy');
                }
            });
        });
        
        // Test if button exists
        if ($('#btnUpdatePharmacy').length === 0) {
            console.error('Update button not found!');
        } else {
            console.log('Update button found and ready');
        }
    });
    </script>
</body>
</html>
