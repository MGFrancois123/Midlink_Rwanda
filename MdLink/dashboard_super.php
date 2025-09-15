<?php include('./constant/layout/head.php');?>
<?php include('./constant/layout/header.php');?>
<?php include('./constant/layout/sidebar.php');?>
<?php include('./constant/check.php');?>
<?php if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'super_admin') { header('Location: dashboard.php'); exit; } ?>
<div class="page-wrapper">
  <div class="container-fluid">
    <div class="row page-titles">
      <div class="col-md-12 align-self-center"><h3 class="text-primary">Super Admin Dashboard</h3></div>
    </div>

    <?php
      $kpi_err = '';
      $totals = [
        'users' => 0,
        'pharmacies' => 0,
        'medicines' => 0,
        'audit_today' => 0,
        'security_today' => 0
      ];
      // helper: check if a table exists to avoid SQL errors on missing tables
      $tableExists = function($mysqli, $name) {
        if (!$mysqli) return false;
        $dbRes = $mysqli->query('SELECT DATABASE() AS d');
        $db = ($dbRes && ($row=$dbRes->fetch_assoc())) ? $row['d'] : '';
        if (!$db) return false;
        $nameSafe = $mysqli->real_escape_string($name);
        $dbSafe = $mysqli->real_escape_string($db);
        $sql = "SELECT 1 FROM information_schema.tables WHERE table_schema='{$dbSafe}' AND table_name='{$nameSafe}' LIMIT 1";
        $r = @$mysqli->query($sql);
        return ($r && $r->num_rows === 1);
      };
      try {
        $q1 = $connect->query("SELECT COUNT(*) c FROM admin_users");
        if ($q1) { $totals['users'] = (int)$q1->fetch_assoc()['c']; }
        $q2 = $connect->query("SELECT COUNT(*) c FROM pharmacies");
        if ($q2) { $totals['pharmacies'] = (int)$q2->fetch_assoc()['c']; }
        $q3 = $connect->query("SELECT COUNT(*) c FROM medicines");
        if ($q3) { $totals['medicines'] = (int)$q3->fetch_assoc()['c']; }
        // audit_logs aligned to new schema (action_time)
        if ($tableExists($connect, 'audit_logs')) {
          $q4 = $connect->query("SELECT COUNT(*) c FROM audit_logs WHERE DATE(action_time)=CURDATE()");
          if ($q4) { $totals['audit_today'] = (int)$q4->fetch_assoc()['c']; }
        }
        // security_logs might not exist in your DB; guard query
        if ($tableExists($connect, 'security_logs')) {
          $q5 = $connect->query("SELECT COUNT(*) c FROM security_logs WHERE DATE(created_at)=CURDATE()");
          if ($q5) { $totals['security_today'] = (int)$q5->fetch_assoc()['c']; }
        } else {
          $totals['security_today'] = 0;
        }
      } catch (Exception $e) { $kpi_err = $e->getMessage(); }
    ?>
    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="card" style="background:#4c51bf;color:#fff"><div class="card-body">
          <h4>Total Users</h4><p style="font-size:22px;font-weight:bold;"><?php echo (int)$totals['users']; ?></p>
        </div></div>
      </div>
      
      <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="card" style="background:#22c55e;color:#fff"><div class="card-body">
          <h4>Medicines</h4><p style="font-size:22px;font-weight:bold;"><?php echo (int)$totals['medicines']; ?></p>
        </div></div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="card" style="background:#f59e0b;color:#fff"><div class="card-body">
          <h4>Today</h4>
          <p class="mb-1"><small>Audit events</small> <span class="badge badge-light"><?php echo (int)$totals['audit_today']; ?></span></p>
          <p class="mb-0"><small>Security events</small> <span class="badge badge-light"><?php echo (int)$totals['security_today']; ?></span></p>
        </div></div>
      </div>
    </div>
    <?php if ($kpi_err) { echo '<div class="alert alert-warning">Some KPIs failed to load: '.htmlspecialchars($kpi_err).'</div>'; } ?>

    <div class="row">
      <div class="col-md-6">
        <div class="card"><div class="card-header d-flex justify-content-between align-items-center"><strong>Recent Audit Activity</strong><a href="placeholder.php?title=Audit%20Logs" class="btn btn-sm btn-outline-primary"><i class="fa fa-arrow-right"></i> View All</a></div>
          <div class="card-body">
            <?php
              $rows = [];$err='';
              $qa = $connect->query("SELECT al.action_time, al.action, al.table_name AS entity_type, al.record_id AS entity_id, al.admin_id, au.username
                                      FROM audit_logs al
                                      LEFT JOIN admin_users au ON al.admin_id = au.admin_id
                                      ORDER BY al.action_time DESC LIMIT 10");
              if ($qa) { while($r=$qa->fetch_assoc()){ $rows[]=$r; } } else { $err=$connect->error; }
            ?>
            <?php if ($err) { echo '<div class="alert alert-danger">Failed to load: '.htmlspecialchars($err).'</div>'; } ?>
            <div class="table-responsive">
              <table class="table table-sm">
                <thead><tr><th>When</th><th>User</th><th>Action</th><th>Entity</th></tr></thead>
                <tbody>
                  <?php if (empty($rows)) { echo '<tr><td colspan="4" class="text-muted">No activity.</td></tr>'; }
                  foreach($rows as $r){ ?>
                    <tr>
                      <td><?php echo date('Y-m-d H:i', strtotime($r['action_time'])); ?></td>
                      <td><?php echo htmlspecialchars($r['username'] ?: ('#'.(int)$r['admin_id'])); ?></td>
                      <td><span class="badge badge-info"><?php echo htmlspecialchars($r['action']); ?></span></td>
                      <td><?php echo htmlspecialchars($r['entity_type']).' #'.htmlspecialchars($r['entity_id']); ?></td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card"><div class="card-header d-flex justify-content-between align-items-center"><strong>Recent Security Events</strong><a href="placeholder.php?title=Security%20Logs" class="btn btn-sm btn-outline-primary"><i class="fa fa-arrow-right"></i> View All</a></div>
          <div class="card-body">
            <?php
              $rows = [];$err='';
              if ($tableExists($connect, 'security_logs')) {
                $qs = $connect->query("SELECT sl.created_at, sl.event_type, sl.severity, sl.user_id, au.username
                                        FROM security_logs sl
                                        LEFT JOIN admin_users au ON sl.user_id = au.admin_id
                                        ORDER BY sl.created_at DESC LIMIT 10");
                if ($qs) { while($r=$qs->fetch_assoc()){ $rows[]=$r; } } else { $err=$connect->error; }
              }
            ?>
            <?php if ($err) { echo '<div class="alert alert-danger">Failed to load: '.htmlspecialchars($err).'</div>'; } ?>
            <div class="table-responsive">
              <table class="table table-sm">
                <thead><tr><th>When</th><th>User</th><th>Event</th><th>Severity</th></tr></thead>
                <tbody>
                  <?php if (empty($rows)) { echo '<tr><td colspan="4" class="text-muted">No events.</td></tr>'; }
                  foreach($rows as $r){
                    $sev = strtolower((string)$r['severity']);
                    $cls = $sev==='critical'?'danger':($sev==='high'?'warning':($sev==='medium'?'info':'secondary'));
                  ?>
                    <tr>
                      <td><?php echo date('Y-m-d H:i', strtotime($r['created_at'])); ?></td>
                      <td><?php echo htmlspecialchars($r['username'] ?: ('#'.(int)$r['user_id'])); ?></td>
                      <td><?php echo htmlspecialchars($r['event_type']); ?></td>
                      <td><span class="badge badge-<?php echo $cls; ?>"><?php echo htmlspecialchars($r['severity']); ?></span></td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include('./constant/layout/footer.php');?>


