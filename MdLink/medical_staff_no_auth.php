<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Bypass authentication for testing
// require_once './constant/check.php';

require_once './constant/connect.php';

// Get existing medical staff data
$existingStaff = [];
$staffQuery = "SELECT ms.*, p.name as pharmacy_name 
               FROM medical_staff ms 
               LEFT JOIN pharmacies p ON ms.pharmacy_id = p.pharmacy_id 
               ORDER BY ms.created_at DESC";
$staffResult = $connect->query($staffQuery);

if ($staffResult && $staffResult->num_rows > 0) {
    while ($row = $staffResult->fetch_assoc()) {
        $existingStaff[] = $row;
    }
}

// Get statistics
$stats = [
    'total_staff' => 0,
    'doctors' => 0,
    'nurses' => 0,
    'assigned_staff' => 0
];

$totalQuery = "SELECT COUNT(*) as count FROM medical_staff";
$totalResult = $connect->query($totalQuery);
if ($totalResult) {
    $stats['total_staff'] = $totalResult->fetch_assoc()['count'];
}

$doctorsQuery = "SELECT COUNT(*) as count FROM medical_staff WHERE role = 'doctor'";
$doctorsResult = $connect->query($doctorsQuery);
if ($doctorsResult) {
    $stats['doctors'] = $doctorsResult->fetch_assoc()['count'];
}

$nursesQuery = "SELECT COUNT(*) as count FROM medical_staff WHERE role = 'nurse'";
$nursesResult = $connect->query($nursesQuery);
if ($nursesResult) {
    $stats['nurses'] = $nursesResult->fetch_assoc()['count'];
}

$assignedQuery = "SELECT COUNT(*) as count FROM medical_staff WHERE pharmacy_id IS NOT NULL";
$assignedResult = $connect->query($assignedQuery);
if ($assignedResult) {
    $stats['assigned_staff'] = $assignedResult->fetch_assoc()['count'];
}

// Get pharmacies for dropdown
$pharmacies = [];
$pharmacyQuery = "SELECT pharmacy_id, name FROM pharmacies ORDER BY name";
$pharmacyResult = $connect->query($pharmacyQuery);
if ($pharmacyResult && $pharmacyResult->num_rows > 0) {
    while ($row = $pharmacyResult->fetch_assoc()) {
        $pharmacies[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Staff Management - MdLink Rwanda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding: 20px;
        }
        .main-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            overflow: hidden;
        }
        .page-header {
            background: linear-gradient(135deg, #2c5aa0 0%, #4facfe 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .stats-container {
            padding: 2rem;
            background: #f8f9fa;
        }
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
        }
        .content-section {
            padding: 2rem;
        }
        .staff-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }
        .staff-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            border-color: #2c5aa0;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="fa fa-user-md"></i> Medical Staff Management</h1>
            <p>Manage doctors, nurses, and medical professionals across all pharmacies</p>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-container">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="stat-card">
                        <h3><?php echo $stats['total_staff']; ?></h3>
                        <p>Total Staff</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stat-card">
                        <h3><?php echo $stats['doctors']; ?></h3>
                        <p>Doctors</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stat-card">
                        <h3><?php echo $stats['nurses']; ?></h3>
                        <p>Nurses</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stat-card">
                        <h3><?php echo $stats['assigned_staff']; ?></h3>
                        <p>Assigned</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Section -->
        <div class="content-section">
            <h2>Medical Staff Directory</h2>
            
            <?php if (!empty($existingStaff)): ?>
                <div class="row">
                    <?php foreach ($existingStaff as $staff): ?>
                        <div class="col-md-6 mb-3">
                            <div class="staff-card">
                                <h5><?php echo htmlspecialchars($staff['full_name']); ?></h5>
                                <p><strong>Role:</strong> <?php echo ucfirst($staff['role']); ?></p>
                                <p><strong>Specialty:</strong> <?php echo htmlspecialchars($staff['specialty'] ?? 'Not specified'); ?></p>
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($staff['phone'] ?? 'Not provided'); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($staff['email'] ?? 'Not provided'); ?></p>
                                <p><strong>Pharmacy:</strong> <?php echo $staff['pharmacy_name'] ? htmlspecialchars($staff['pharmacy_name']) : 'Not assigned'; ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center">
                    <h4>No Medical Staff Found</h4>
                    <p>No medical staff members are currently in the system.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
