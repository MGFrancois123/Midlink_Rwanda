<?php 
// Debug version of placeholder.php
include('./constant/layout/head.php');
include('./constant/layout/header.php');
include('./constant/layout/sidebar.php');
include('./constant/check.php');

$title = isset($_GET['title']) ? htmlspecialchars($_GET['title']) : 'Coming Soon';
?>

<div class="page-wrapper">
  <div class="container-fluid">
    <div class="row page-titles">
      <div class="col-md-12 align-self-center">
        <h3 class="text-primary"><?php echo $title; ?> (Debug Mode)</h3>
      </div>
    </div>

    <?php if (strcasecmp($title, 'Add / Update Medicines') === 0) { ?>
    
    <!-- Debug Info -->
    <div class="alert alert-info">
      <h4>Debug Information</h4>
      <p><strong>Page:</strong> Add/Update Medicines</p>
      <p><strong>JavaScript Files:</strong> medicine_catalog.js should be loaded</p>
      <p><strong>API Endpoints:</strong> actions/fetch_data.php and php_action/manageMedicine.php</p>
    </div>

    <div class="row">
      <div class="col-lg-10 mx-auto">
        <div class="card">
          <div class="card-title">
            <h4>Add / Update Medicines</h4>
          </div>
          <div id="medicine-messages"></div>
          <div class="card-body">
            <div class="input-states">
              <form class="row" id="medicineForm" method="POST" action="php_action/manageMedicine.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="medicine_id" id="medicine_id" value="">
                
                <div class="form-group col-md-6">
                  <label><i class="fa fa-medkit"></i> Medicine Name</label>
                  <input type="text" class="form-control" name="name" id="medicine_name" placeholder="Enter medicine name" required>
                </div>

                <div class="form-group col-md-6">
                  <label><i class="fa fa-hospital-o"></i> Pharmacy</label>
                  <select class="form-control" name="pharmacy_id" id="medicine_pharmacy" required>
                    <option value="">Loading pharmacies...</option>
                  </select>
                </div>

                <div class="form-group col-md-6">
                  <label><i class="fa fa-tags"></i> Category</label>
                  <select class="form-control" name="category_id" id="medicine_category" required>
                    <option value="">Loading categories...</option>
                  </select>
                </div>

                <div class="form-group col-md-12">
                  <label><i class="fa fa-file-text"></i> Description</label>
                  <textarea class="form-control" name="description" id="medicine_description" rows="3" placeholder="Enter medicine description"></textarea>
                </div>

                <div class="form-group col-md-6">
                  <label><i class="fa fa-money"></i> Price (RWF)</label>
                  <input type="number" class="form-control" name="price" id="medicine_price" placeholder="0.00" step="0.01" min="0" required>
                </div>

                <div class="form-group col-md-6">
                  <label><i class="fa fa-cubes"></i> Stock Quantity</label>
                  <input type="number" class="form-control" name="stock_quantity" id="medicine_stock" placeholder="0" min="0" required>
                </div>

                <div class="form-group col-md-6">
                  <label><i class="fa fa-calendar"></i> Expiry Date</label>
                  <input type="date" class="form-control" name="expiry_date" id="medicine_expiry" required>
                </div>

                <div class="form-group col-md-6">
                  <label><i class="fa fa-exclamation-triangle"></i> Restricted Medicine</label>
                  <select class="form-control" name="Restricted_Medicine" id="medicine_restricted">
                    <option value="0">No</option>
                    <option value="1">Yes</option>
                  </select>
                </div>

                <div class="form-group col-md-12">
                  <button type="submit" class="btn btn-success" id="submitBtn"><i class="fa fa-save"></i> Add Medicine</button>
                  <button type="button" class="btn btn-secondary" id="resetBtn" style="display:none;"><i class="fa fa-refresh"></i> Reset</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row" style="margin-top: 20px;">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-title">
            <h4>Existing Medicines</h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped" id="medicinesTable">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Pharmacy</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Expiry</th>
                    <th>Restricted</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody id="medicinesTableBody">
                  <tr><td colspan="9" class="text-center">Loading medicines...</td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script>
      // Debug console logs
      console.log('Debug: Page loaded');
      console.log('Debug: jQuery version:', $.fn.jquery);
      
      // Test if medicine_catalog.js is loaded
      if (typeof loadPharmacies === 'function') {
        console.log('Debug: loadPharmacies function found');
      } else {
        console.log('Debug: loadPharmacies function NOT found - JavaScript file may not be loaded');
      }
    </script>
    
    <script src="custom/js/medicine_catalog.js"></script>

    <?php } elseif (strcasecmp($title, 'Categories') === 0) { ?>
    
    <!-- Debug Info -->
    <div class="alert alert-info">
      <h4>Debug Information</h4>
      <p><strong>Page:</strong> Categories Management</p>
      <p><strong>JavaScript Files:</strong> category_management.js should be loaded</p>
      <p><strong>API Endpoints:</strong> php_action/manageCategory.php</p>
    </div>

    <div class="row">
      <div class="col-lg-8 mx-auto">
        <div class="card">
          <div class="card-title">
            <h4>Manage Medicine Categories</h4>
          </div>
          <div id="category-messages"></div>
          <div class="card-body">
            <div class="input-states">
              <form class="row" id="categoryForm" method="POST" action="php_action/manageCategory.php">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="category_id" id="category_id" value="">
                
                <div class="form-group col-md-6">
                  <label><i class="fa fa-tags"></i> Category Name</label>
                  <input type="text" class="form-control" name="category_name" id="category_name" placeholder="Enter category name" required>
                </div>

                <div class="form-group col-md-6">
                  <label><i class="fa fa-toggle-on"></i> Status</label>
                  <select class="form-control" name="is_active" id="category_status">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                  </select>
                </div>

                <div class="form-group col-md-12">
                  <label><i class="fa fa-file-text"></i> Description</label>
                  <textarea class="form-control" name="description" id="category_description" rows="2" placeholder="Enter category description"></textarea>
                </div>

                <div class="form-group col-md-12">
                  <button type="submit" class="btn btn-success" id="categorySubmitBtn"><i class="fa fa-save"></i> Add Category</button>
                  <button type="button" class="btn btn-secondary" id="categoryResetBtn" style="display:none;"><i class="fa fa-refresh"></i> Reset</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row" style="margin-top: 20px;">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-title">
            <h4>Existing Categories</h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped" id="categoriesTable">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Category Name</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody id="categoriesTableBody">
                  <tr><td colspan="6" class="text-center">Loading categories...</td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script>
      // Debug console logs
      console.log('Debug: Categories page loaded');
      console.log('Debug: jQuery version:', $.fn.jquery);
      
      // Test if category_management.js is loaded
      if (typeof loadCategories === 'function') {
        console.log('Debug: loadCategories function found');
      } else {
        console.log('Debug: loadCategories function NOT found - JavaScript file may not be loaded');
      }
    </script>
    
    <script src="custom/js/category_management.js"></script>

    <?php } else { ?>
    <div class="card">
      <div class="card-body">
        <p>This section is being prepared. Check back soon.</p>
        <p><strong>Debug:</strong> Title = "<?php echo $title; ?>"</p>
      </div>
    </div>
    <?php } ?>

  </div>
</div>

<?php include('./constant/layout/footer.php'); ?>
