<?php
require_once 'constant/connect.php';

// Add new category
if(isset($_POST['add_category'])) {
    $category_name = $connect->real_escape_string($_POST['category_name']);
    $description = $connect->real_escape_string($_POST['description']);
    
    $sql = "INSERT INTO category (category_name, description) VALUES ('$category_name', '$description')";
    
    if($connect->query($sql) === TRUE) {
        $message = "Category added successfully";
    } else {
        $error = "Error: " . $connect->error;
    }
}

// Get all categories
$categories = [];
$result = $connect->query("SELECT * FROM category ORDER BY category_name");
if($result) {
    while($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}
?>

<?php include('./constant/layout/head.php');?>
<?php include('./constant/layout/header.php');?>
<?php include('./constant/layout/sidebar.php');?>

<div class="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Add New Category</h4>
                        <?php if(isset($message)): ?>
                            <div class="alert alert-success"><?php echo $message; ?></div>
                        <?php endif; ?>
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <form method="POST" action="">
                            <div class="form-group">
                                <label>Category Name</label>
                                <input type="text" name="category_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Description (Optional)</label>
                                <textarea name="description" class="form-control" rows="3"></textarea>
                            </div>
                            <button type="submit" name="add_category" class="btn btn-primary">Add Category</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Existing Categories</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Category Name</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(count($categories) > 0): ?>
                                        <?php foreach($categories as $category): ?>
                                            <tr>
                                                <td><?php echo $category['category_id']; ?></td>
                                                <td><?php echo htmlspecialchars($category['category_name']); ?></td>
                                                <td><?php echo htmlspecialchars($category['description'] ?? 'N/A'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" class="text-center">No categories found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('./constant/layout/footer.php'); ?>
