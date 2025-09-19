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
    <title>Test Edit Pharmacy - MdLink Rwanda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h1><i class="fa fa-test-tube"></i> Test Edit Pharmacy Functionality</h1>
                <p class="text-muted">This page tests the edit pharmacy functionality by showing available pharmacies and their edit links.</p>
                
                <?php
                try {
                    // Fetch pharmacies for testing
                    $query = "SELECT pharmacy_id, name, license_number, contact_person, contact_phone, location FROM pharmacies ORDER BY created_at DESC LIMIT 5";
                    $result = $connect->query($query);
                    
                    if ($result && $result->num_rows > 0) {
                        echo '<div class="alert alert-info"><i class="fa fa-info-circle"></i> Found ' . $result->num_rows . ' pharmacies for testing</div>';
                        
                        echo '<div class="table-responsive">';
                        echo '<table class="table table-striped">';
                        echo '<thead class="table-dark">';
                        echo '<tr><th>ID</th><th>Name</th><th>License</th><th>Contact</th><th>Location</th><th>Test Edit</th></tr>';
                        echo '</thead><tbody>';
                        
                        while ($pharmacy = $result->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($pharmacy['pharmacy_id']) . '</td>';
                            echo '<td>' . htmlspecialchars($pharmacy['name']) . '</td>';
                            echo '<td>' . htmlspecialchars($pharmacy['license_number']) . '</td>';
                            echo '<td>' . htmlspecialchars($pharmacy['contact_person']) . '</td>';
                            echo '<td>' . htmlspecialchars($pharmacy['location']) . '</td>';
                            echo '<td>';
                            echo '<a href="create_pharmacy.php?edit=' . $pharmacy['pharmacy_id'] . '" class="btn btn-primary btn-sm" target="_blank">';
                            echo '<i class="fa fa-edit"></i> Test Edit';
                            echo '</a>';
                            echo '</td>';
                            echo '</tr>';
                        }
                        
                        echo '</tbody></table>';
                        echo '</div>';
                        
                        echo '<div class="alert alert-success mt-3">';
                        echo '<h5><i class="fa fa-check-circle"></i> Test Instructions:</h5>';
                        echo '<ol>';
                        echo '<li>Click any "Test Edit" button above</li>';
                        echo '<li>Verify that the edit form loads with pre-filled data</li>';
                        echo '<li>Make a change and click "Update Pharmacy"</li>';
                        echo '<li>Verify you are redirected back to manage_pharmacies.php with success message</li>';
                        echo '</ol>';
                        echo '</div>';
                        
                    } else {
                        echo '<div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> No pharmacies found. Create some pharmacies first.</div>';
                    }
                    
                } catch (Exception $e) {
                    echo '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
                }
                ?>
                
                <div class="mt-4">
                    <a href="manage_pharmacies.php" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Back to Manage Pharmacies
                    </a>
                    <a href="create_pharmacy.php" class="btn btn-primary">
                        <i class="fa fa-plus"></i> Create New Pharmacy
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
