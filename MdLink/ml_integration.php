<?php include('./constant/layout/head.php');?>
<?php include('./constant/layout/header.php');?>
<?php include('./constant/layout/sidebar.php');?>
<?php include('./constant/check.php');?>
<?php if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'super_admin') { header('Location: dashboard.php'); exit; } ?>

<div class="page-wrapper">
  <div class="container-fluid">
    <div class="row page-titles">
      <div class="col-md-12 align-self-center"><h3 class="text-primary">ML Integration Settings</h3></div>
    </div>

    <div class="row">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header"><strong class="card-title">Model / API Configuration</strong></div>
          <div class="card-body">
            <form method="post" action="#" onsubmit="alert('Settings saved (demo).');return false;">
              <div class="form-group">
                <label>API Base URL</label>
                <input type="url" class="form-control" placeholder="https://ml.example.com/api" required>
              </div>
              <div class="form-group">
                <label>API Key / Token</label>
                <input type="text" class="form-control" placeholder="paste secret token" required>
              </div>
              <div class="form-group">
                <label>Default Prediction Use-case</label>
                <select class="form-control">
                  <option value="demand">Medicine Demand Forecast</option>
                  <option value="risk">Payment Risk / Fraud</option>
                </select>
              </div>
              <div class="form-group">
                <label>Enable Real-time Predictions</label>
                <select class="form-control">
                  <option value="1">Enabled</option>
                  <option value="0">Disabled</option>
                </select>
              </div>
              <button class="btn btn-primary">Save Settings</button>
            </form>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card">
          <div class="card-header"><strong class="card-title">Test Endpoint</strong></div>
          <div class="card-body">
            <p>Use sample input to verify the model connection.</p>
            <button class="btn btn-success" onclick="alert('Test sent (demo).');">Send Test</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include('./constant/layout/footer.php');?>


