<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simple working version without complex authentication
require_once './constant/connect.php';

// Get medical staff data
$staff = [];
$query = "SELECT * FROM medical_staff ORDER BY created_at DESC";
$result = $connect->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $staff[] = $row;
    }
}

// Get statistics
$total_query = "SELECT COUNT(*) as count FROM medical_staff";
$total_result = $connect->query($total_query);
$total_staff = $total_result ? $total_result->fetch_assoc()['count'] : 0;

$doctors_query = "SELECT COUNT(*) as count FROM medical_staff WHERE role = 'doctor'";
$doctors_result = $connect->query($doctors_query);
$doctors = $doctors_result ? $doctors_result->fetch_assoc()['count'] : 0;

$nurses_query = "SELECT COUNT(*) as count FROM medical_staff WHERE role = 'nurse'";
$nurses_result = $connect->query($nurses_query);
$nurses = $nurses_result ? $nurses_result->fetch_assoc()['count'] : 0;
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
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
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
        .staff-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        .staff-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            margin-right: 1rem;
        }
        .staff-avatar.doctor { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .staff-avatar.nurse { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .staff-avatar.pharmacist { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .staff-avatar.technician { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        .staff-role {
            background: #2c5aa0;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .detail-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-item:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #6c757d;
        }
        .detail-value {
            color: #2c3e50;
        }
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #6c757d;
        }
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #e9ecef;
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
                        <div class="stat-icon" style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.5rem; color: white;">
                            <i class="fa fa-users"></i>
                        </div>
                        <h3 style="font-size: 2.5rem; font-weight: 700; margin: 0; color: #2c3e50;"><?php echo $total_staff; ?></h3>
                        <p style="font-size: 1rem; color: #6c757d; margin: 0.5rem 0 0 0; font-weight: 500;">Total Staff</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.5rem; color: white;">
                            <i class="fa fa-stethoscope"></i>
                        </div>
                        <h3 style="font-size: 2.5rem; font-weight: 700; margin: 0; color: #2c3e50;"><?php echo $doctors; ?></h3>
                        <p style="font-size: 1rem; color: #6c757d; margin: 0.5rem 0 0 0; font-weight: 500;">Doctors</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.5rem; color: white;">
                            <i class="fa fa-heartbeat"></i>
                        </div>
                        <h3 style="font-size: 2.5rem; font-weight: 700; margin: 0; color: #2c3e50;"><?php echo $nurses; ?></h3>
                        <p style="font-size: 1rem; color: #6c757d; margin: 0.5rem 0 0 0; font-weight: 500;">Nurses</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.5rem; color: white;">
                            <i class="fa fa-hospital-o"></i>
                        </div>
                        <h3 style="font-size: 2.5rem; font-weight: 700; margin: 0; color: #2c3e50;"><?php echo $total_staff; ?></h3>
                        <p style="font-size: 1rem; color: #6c757d; margin: 0.5rem 0 0 0; font-weight: 500;">Active</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Section -->
        <div class="content-section">
            <h2 style="font-size: 1.8rem; font-weight: 600; color: #2c3e50; margin-bottom: 2rem;">
                <i class="fa fa-users"></i> Medical Staff Directory
            </h2>
            
            <?php if (!empty($staff)): ?>
                <div class="row">
                    <?php foreach ($staff as $member): ?>
                        <div class="col-md-6 mb-3">
                            <div class="staff-card">
                                <div class="staff-header">
                                    <div class="staff-avatar <?php echo $member['role']; ?>">
                                        <i class="fa fa-<?php echo $member['role'] === 'doctor' ? 'stethoscope' : ($member['role'] === 'nurse' ? 'heartbeat' : ($member['role'] === 'pharmacist' ? 'pills' : 'cog')); ?>"></i>
                                    </div>
                                    <div>
                                        <h5 style="margin: 0; color: #2c3e50; font-weight: 600;"><?php echo htmlspecialchars($member['full_name']); ?></h5>
                                        <span class="staff-role"><?php echo ucfirst($member['role']); ?></span>
                                    </div>
                                </div>
                                
                                <div class="staff-details">
                                    <div class="detail-item">
                                        <span class="detail-label">ID:</span>
                                        <span class="detail-value">#<?php echo $member['staff_id']; ?></span>
                                    </div>
                                    <?php if ($member['license_number']): ?>
                                    <div class="detail-item">
                                        <span class="detail-label">License:</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($member['license_number']); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($member['specialty']): ?>
                                    <div class="detail-item">
                                        <span class="detail-label">Specialty:</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($member['specialty']); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($member['phone']): ?>
                                    <div class="detail-item">
                                        <span class="detail-label">Phone:</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($member['phone']); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($member['email']): ?>
                                    <div class="detail-item">
                                        <span class="detail-label">Email:</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($member['email']); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <div class="detail-item">
                                        <span class="detail-label">Status:</span>
                                        <span class="detail-value">
                                            <span style="background: #28a745; color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.8rem;">
                                                <?php echo ucfirst($member['status'] ?? 'Active'); ?>
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fa fa-user-md"></i>
                    <h4>No Medical Staff Found</h4>
                    <p>No medical staff members are currently in the system.</p>
                    <p><strong>Debug Info:</strong> Database connection working, but no staff records found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
