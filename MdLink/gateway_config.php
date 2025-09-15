<?php include('./constant/layout/head.php');?>
<?php include('./constant/layout/header.php');?>
<?php include('./constant/layout/sidebar.php');?>
<?php include('./constant/check.php');?>
<?php if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'super_admin') { header('Location: dashboard.php'); exit; } ?>

<div class="page-wrapper">
  <div class="container-fluid">
    <div class="row page-titles">
      <div class="col-md-12 align-self-center"><h3 class="text-primary">SMS & Payment Gateway Configuration</h3></div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header"><strong class="card-title">SMS Gateway</strong></div>
          <div class="card-body">
            <form method="post" action="#" onsubmit="alert('SMS settings saved (demo).');return false;">
              <div class="form-group">
                <label>Provider</label>
                <select class="form-control"><option>Twilio</option><option>Infobip</option><option>Kannel</option></select>
              </div>
              <div class="form-group">
                <label>API Key</label>
                <input type="text" class="form-control" placeholder="api key" required>
              </div>
              <div class="form-group">
                <label>Sender ID</label>
                <input type="text" class="form-control" placeholder="MDLINK-RW">
              </div>
              <button class="btn btn-primary">Save SMS Settings</button>
            </form>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card">
          <div class="card-header"><strong class="card-title">Payment Gateway</strong></div>
          <div class="card-body">
            <form method="post" action="#" onsubmit="alert('Payment settings saved (demo).');return false;">
              <div class="form-group">
                <label>Provider</label>
                <select class="form-control"><option>MTN MoMo</option><option>Airtel Money</option><option>Bank</option></select>
              </div>
              <div class="form-group">
                <label>Public Key</label>
                <input type="text" class="form-control" placeholder="public key" required>
              </div>
              <div class="form-group">
                <label>Secret Key</label>
                <input type="text" class="form-control" placeholder="secret" required>
              </div>
              <button class="btn btn-primary">Save Payment Settings</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include('./constant/layout/footer.php');?>


