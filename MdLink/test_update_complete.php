<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once './constant/connect.php';
require_once './constant/check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Update Test - MdLink Rwanda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h1><i class="fa fa-test-tube"></i> Complete Update Test</h1>
                <p class="text-muted">This page tests the complete update functionality with real data.</p>
                
                <?php
                // Get a pharmacy to test with
                $query = "SELECT * FROM pharmacies ORDER BY created_at DESC LIMIT 1";
                $result = $connect->query($query);
                
                if ($result && $result->num_rows > 0) {
                    $pharmacy = $result->fetch_assoc();
                    echo '<div class="alert alert-info">';
                    echo '<h5><i class="fa fa-info-circle"></i> Testing with Pharmacy:</h5>';
                    echo '<ul>';
                    echo '<li><strong>ID:</strong> ' . $pharmacy['pharmacy_id'] . '</li>';
                    echo '<li><strong>Name:</strong> ' . htmlspecialchars($pharmacy['name']) . '</li>';
                    echo '<li><strong>License:</strong> ' . htmlspecialchars($pharmacy['license_number']) . '</li>';
                    echo '<li><strong>Contact:</strong> ' . htmlspecialchars($pharmacy['contact_person']) . '</li>';
                    echo '<li><strong>Phone:</strong> ' . htmlspecialchars($pharmacy['contact_phone']) . '</li>';
                    echo '<li><strong>Location:</strong> ' . htmlspecialchars($pharmacy['location']) . '</li>';
                    echo '</ul>';
                    echo '</div>';
                    ?>
                    
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0"><i class="fa fa-edit"></i> Test Update Form</h4>
                        </div>
                        <div class="card-body">
                            <div id="testMessage"></div>
                            
                            <form id="testUpdateForm">
                                <input type="hidden" name="pharmacy_id" value="<?php echo $pharmacy['pharmacy_id']; ?>">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label><i class="fa fa-hospital-o"></i> Pharmacy Name *</label>
                                            <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($pharmacy['name']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label><i class="fa fa-id-badge"></i> License Number *</label>
                                            <input type="text" class="form-control" name="license_number" value="<?php echo htmlspecialchars($pharmacy['license_number']); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label><i class="fa fa-user-md"></i> Contact Person *</label>
                                            <input type="text" class="form-control" name="contact_person" value="<?php echo htmlspecialchars($pharmacy['contact_person']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label><i class="fa fa-phone"></i> Contact Phone *</label>
                                            <input type="tel" class="form-control" name="contact_phone" value="<?php echo htmlspecialchars($pharmacy['contact_phone']); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label><i class="fa fa-map-marker"></i> Location *</label>
                                            <input type="text" class="form-control" name="location" value="<?php echo htmlspecialchars($pharmacy['location']); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label><i class="fa fa-map-signs"></i> Additional Address Details</label>
                                            <input type="text" class="form-control" name="address_details" placeholder="Street name, building number, etc.">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-secondary" onclick="testBackend()">
                                        <i class="fa fa-server"></i> Test Backend Only
                                    </button>
                                </div>
                                <div class="col-md-6 text-right">
                                    <button type="button" class="btn btn-primary" id="testUpdateBtn">
                                        <i class="fa fa-save"></i> Test Complete Update
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h5>Test Results:</h5>
                        <div id="testResults"></div>
                    </div>
                    
                <?php } else { ?>
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i> No pharmacies found to test with.
                    </div>
                <?php } ?>
                
                <div class="mt-4">
                    <a href="create_pharmacy.php?edit=9" class="btn btn-primary">
                        <i class="fa fa-edit"></i> Test Real Edit Page
                    </a>
                    <a href="manage_pharmacies.php" class="btn btn-secondary">
                        <i class="fa fa-list"></i> Manage Pharmacies
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        console.log('Test page loaded');
        
        // Test complete update
        $('#testUpdateBtn').on('click', function() {
            console.log('Test update button clicked');
            
            const form = document.getElementById('testUpdateForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            const btn = this;
            const originalText = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Testing...';
            
            const formData = new FormData(form);
            
            // Log form data
            console.log('Form data:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }
            
            fetch('php_action/update_pharmacy.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.text();
            })
            .then(data => {
                console.log('Raw response:', data);
                
                try {
                    const response = JSON.parse(data);
                    console.log('Parsed response:', response);
                    
                    if (response.success) {
                        $('#testMessage').html(
                            '<div class="alert alert-success"><i class="fa fa-check-circle"></i> <strong>SUCCESS!</strong> ' + response.message + '</div>'
                        );
                        $('#testResults').html(
                            '<div class="alert alert-success">' +
                            '<h6>Update Successful!</h6>' +
                            '<p>Pharmacy ID: ' + response.data.pharmacy_id + '</p>' +
                            '<p>Name: ' + response.data.name + '</p>' +
                            '<p>License: ' + response.data.license_number + '</p>' +
                            '</div>'
                        );
                    } else {
                        $('#testMessage').html(
                            '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> <strong>ERROR:</strong> ' + response.message + '</div>'
                        );
                        $('#testResults').html(
                            '<div class="alert alert-danger">' +
                            '<h6>Update Failed!</h6>' +
                            '<p>Error: ' + response.message + '</p>' +
                            '</div>'
                        );
                    }
                } catch (e) {
                    console.error('JSON parse error:', e);
                    $('#testMessage').html(
                        '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> <strong>ERROR:</strong> Invalid JSON response: ' + data + '</div>'
                    );
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                $('#testMessage').html(
                    '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> <strong>ERROR:</strong> Network error: ' + error.message + '</div>'
                );
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        });
    });
    
    function testBackend() {
        window.open('test_simple_update.php', '_blank');
    }
    </script>
</body>
</html>
