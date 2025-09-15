<?php include('./constant/layout/head.php');?>
<?php include('./constant/layout/header.php');?>

<?php include('./constant/layout/sidebar.php');?>
<!--  Author Name: Mayuri K. 
 for any PHP, Codeignitor, Laravel OR Python work contact me at mayuri.infospace@gmail.com  
 Visit website : www.mayurik.com -->   
<?php include('./constant/connect.php');
$sql = "SELECT category_id, category_name, description, status, created_at, updated_at FROM category ORDER BY category_id DESC";
$result = $connect->query($sql);

// Get statistics
$stats = [];
$stats['total'] = $result->num_rows;
$stats['active'] = $connect->query("SELECT COUNT(*) as count FROM category WHERE status = '1'")->fetch_assoc()['count'];
$stats['inactive'] = $connect->query("SELECT COUNT(*) as count FROM category WHERE status = '2'")->fetch_assoc()['count'];
?>

<style>
.page-wrapper { width: 100%; }
.page-wrapper .container-fluid { width: 100%; max-width: 100%; padding-left: 15px; padding-right: 15px; }
.card { border: none; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); }
.card-header { background: linear-gradient(135deg, #2e7d32, #4caf50); color: #fff; border:0; padding: 1rem 1.25rem; }
.stats-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px; padding: 20px; margin-bottom: 20px; }
.stat-item { text-align: center; }
.stat-number { font-size: 2rem; font-weight: bold; margin-bottom: 5px; }
.stat-label { font-size: 0.9rem; opacity: 0.9; }
</style>

<div class="page-wrapper">
    <div class="container-fluid py-4">
        <div class="row align-items-center mb-3">
            <div class="col-md-8">
                <h3 class="mb-0"><i class="fa fa-tags"></i> Medicine Categories</h3>
                <small class="text-muted">Manage medicine categories and their status</small>
            </div>
            <div class="col-md-4 text-end">
                <a href="add-category.php" class="btn btn-primary">
                    <i class="fa fa-plus"></i> Add Category
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo $stats['total']; ?></div>
                        <div class="stat-label">Total Categories</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo $stats['active']; ?></div>
                        <div class="stat-label">Active Categories</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo $stats['inactive']; ?></div>
                        <div class="stat-label">Inactive Categories</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <strong><i class="fa fa-list"></i> Categories List</strong>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="myTable" class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">#</th>
                                <th>Category Name</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Updated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows == 0) { ?>
                                <tr><td colspan="7" class="text-center text-muted py-4">No categories found</td></tr>
                            <?php } else { 
                                $counter = 1;
                                while ($row = $result->fetch_assoc()) { ?>
                                <tr>
                                    <td class="text-center"><?php echo $counter++; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($row['category_name']); ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['description'] ?? 'No description'); ?></td>
                                    <td>
                                        <?php if ($row['status'] == '1') { ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php } else { ?>
                                            <span class="badge bg-secondary">Inactive</span>
                                        <?php } ?>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($row['created_at'])); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($row['updated_at'])); ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="editcategory.php?id=<?php echo $row['category_id']; ?>" 
                                               class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <a href="php_action/removeCategories.php?id=<?php echo $row['category_id']; ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Are you sure to delete this category?')" 
                                               title="Delete">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php } } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

 
<?php include('./constant/layout/footer.php');?>
<!--  Author Name: Mayuri K. 
 for any PHP, Codeignitor, Laravel OR Python work contact me at mayuri.infospace@gmail.com  
 Visit website : www.mayurik.com -->


