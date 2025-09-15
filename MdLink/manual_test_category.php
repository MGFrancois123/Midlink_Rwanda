<?php
// Manual test form for category insertion
require_once 'php_action/core.php';

$message = '';
$messageType = '';

if ($_POST) {
    $categoriesName = trim($_POST['categoriesName'] ?? '');
    $categoriesStatus = $_POST['categoriesStatus'] ?? '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : 'Category created via admin panel';
    
    if (!empty($categoriesName) && !empty($categoriesStatus)) {
        $sql = "INSERT INTO category (category_name, description, status) VALUES (?, ?, ?)";
        $stmt = $connect->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("sss", $categoriesName, $description, $categoriesStatus);
            
            if ($stmt->execute()) {
                $message = "✅ Category added successfully! Insert ID: " . $stmt->insert_id;
                $messageType = 'success';
            } else {
                $message = "❌ Error: " . $stmt->error;
                $messageType = 'error';
            }
            $stmt->close();
        } else {
            $message = "❌ Prepare error: " . $connect->error;
            $messageType = 'error';
        }
    } else {
        $message = "❌ Please fill in all required fields";
        $messageType = 'error';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manual Category Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select, textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .success { color: green; background: #d4edda; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .error { color: red; background: #f8d7da; padding: 10px; border-radius: 4px; margin: 10px 0; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Manual Category Insertion Test</h1>
    
    <?php if ($message): ?>
        <div class="<?php echo $messageType; ?>"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="form-group">
            <label>Category Name *</label>
            <input type="text" name="categoriesName" required>
        </div>
        
        <div class="form-group">
            <label>Status *</label>
            <select name="categoriesStatus" required>
                <option value="">Select Status</option>
                <option value="1">Available</option>
                <option value="2">Not Available</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="3"></textarea>
        </div>
        
        <button type="submit">Add Category</button>
    </form>
    
    <h2>Current Categories</h2>
    <?php
    $result = $connect->query("SELECT * FROM category ORDER BY category_id DESC LIMIT 10");
    if ($result && $result->num_rows > 0): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Status</th>
                <th>Created</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['category_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><?php echo $row['status']; ?></td>
                    <td><?php echo $row['created_at']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No categories found.</p>
    <?php endif; ?>
    
    <p><a href="placeholder.php?title=Categories">← Back to Categories Page</a></p>
</body>
</html>
