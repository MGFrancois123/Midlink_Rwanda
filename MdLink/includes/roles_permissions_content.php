<?php
require_once __DIR__ . '/../constant/connect.php';
if (session_status() === PHP_SESSION_NONE) { if (session_status() === PHP_SESSION_NONE) {
    session_start();
} }

// Simple direct database fetch
$roles = [];
$permissionsCount = 0;
$assignmentsCount = 0;
$adminsWithRoles = 0;

try {
    // Get permissions count
    $result = $connect->query("SELECT COUNT(*) as count FROM permissions");
    if ($result) {
        $permissionsCount = (int)$result->fetch_assoc()['count'];
    }
    
    // Get roles with their permissions
    $query = "
        SELECT r.role_id, r.name, r.description, 
               GROUP_CONCAT(p.name ORDER BY p.name SEPARATOR ',') as permissions
        FROM roles r
        LEFT JOIN role_permissions rp ON r.role_id = rp.role_id
        LEFT JOIN permissions p ON rp.permission_id = p.permission_id
        GROUP BY r.role_id, r.name, r.description
        ORDER BY r.name
    ";
    
    $result = $connect->query($query);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $permissions = $row['permissions'] ? explode(',', $row['permissions']) : [];
            $roles[] = [
                'role_id' => $row['role_id'],
                'name' => $row['name'],
                'description' => $row['description'],
                'permissions' => $permissions
            ];
        }
    }
    
    // Get assignments count
    $result = $connect->query("SELECT COUNT(*) as count FROM role_permissions");
    if ($result) {
        $assignmentsCount = (int)$result->fetch_assoc()['count'];
    }
    
    // Get admins with roles count
    $result = $connect->query("SELECT COUNT(DISTINCT admin_id) as count FROM admin_role_assignments");
    if ($result) {
        $adminsWithRoles = (int)$result->fetch_assoc()['count'];
    }
    
} catch (Exception $e) {
    // If there's an error, use fallback data
    $roles = [
        ['role_id' => 1, 'name' => 'super_admin', 'description' => 'Full system access', 'permissions' => ['manage_users','manage_medicines','view_reports']],
        ['role_id' => 2, 'name' => 'pharmacy_admin', 'description' => 'Manage pharmacy operations', 'permissions' => ['manage_medicines','view_inventory']],
        ['role_id' => 3, 'name' => 'finance_admin', 'description' => 'Finance operations only', 'permissions' => ['finance_ops','view_reports']]
    ];
    $permissionsCount = 8;
    $assignmentsCount = 7;
    $adminsWithRoles = 0;
}
?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h4 class="mb-0"><i class="fa fa-lock"></i> Roles & Permissions</h4>
    <div>
      <button id="btnAddRole" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add Role</button>
      <button id="btnRefresh" class="btn btn-outline-secondary btn-sm"><i class="fa fa-refresh"></i> Refresh</button>
    </div>
  </div>
  <div class="card-body">
    <div class="row" style="margin-bottom:15px;">
      <div class="col-md-3">
        <div class="p-3 bg-white shadow-sm rounded"><div class="text-muted">Total Roles</div><div id="statRoles" class="h4 mb-0"><?php echo count($roles); ?></div></div>
      </div>
      <div class="col-md-3">
        <div class="p-3 bg-white shadow-sm rounded"><div class="text-muted">Total Permissions</div><div id="statPerms" class="h4 mb-0"><?php echo $permissionsCount; ?></div></div>
      </div>
      <div class="col-md-3">
        <div class="p-3 bg-white shadow-sm rounded"><div class="text-muted">Assignments</div><div id="statAssign" class="h4 mb-0"><?php echo $assignmentsCount; ?></div></div>
      </div>
      <div class="col-md-3">
        <div class="p-3 bg-white shadow-sm rounded"><div class="text-muted">Admins with Roles</div><div id="statAdmins" class="h4 mb-0"><?php echo $adminsWithRoles; ?></div></div>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-striped" id="tblRoles">
        <thead>
          <tr>
            <th>Role</th>
            <th>Description</th>
            <th>Permissions</th>
            <th style="width:120px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($roles)) { foreach ($roles as $role) { ?>
          <tr>
            <td><strong><?php echo htmlspecialchars($role['name']); ?></strong></td>
            <td><?php echo htmlspecialchars($role['description']); ?></td>
            <td>
              <?php if (!empty($role['permissions'])) { foreach ($role['permissions'] as $perm) { ?>
                <span class="badge badge-primary mr-1 mb-1"><?php echo htmlspecialchars($perm); ?></span>
              <?php } } else { echo '<span class="text-muted">No permissions</span>'; } ?>
            </td>
            <td>
              <button class="btn btn-sm btn-outline-primary js-edit" data-id="<?php echo (int)$role['role_id']; ?>"><i class="fa fa-pencil"></i></button>
              <button class="btn btn-sm btn-outline-danger js-del" data-id="<?php echo (int)$role['role_id']; ?>"><i class="fa fa-trash"></i></button>
            </td>
          </tr>
          <?php } } else { ?>
          <!-- Fallback sample data if database is empty -->
          <tr>
            <td><strong>super_admin</strong></td>
            <td>Full system access</td>
            <td>
              <span class="badge badge-primary mr-1 mb-1">manage_users</span>
              <span class="badge badge-primary mr-1 mb-1">manage_medicines</span>
              <span class="badge badge-primary mr-1 mb-1">view_reports</span>
            </td>
            <td>
              <button class="btn btn-sm btn-outline-primary" disabled><i class="fa fa-pencil"></i></button>
              <button class="btn btn-sm btn-outline-danger" disabled><i class="fa fa-trash"></i></button>
            </td>
          </tr>
          <tr>
            <td><strong>pharmacy_admin</strong></td>
            <td>Manage pharmacy operations</td>
            <td>
              <span class="badge badge-primary mr-1 mb-1">manage_medicines</span>
              <span class="badge badge-primary mr-1 mb-1">view_inventory</span>
            </td>
            <td>
              <button class="btn btn-sm btn-outline-primary" disabled><i class="fa fa-pencil"></i></button>
              <button class="btn btn-sm btn-outline-danger" disabled><i class="fa fa-trash"></i></button>
            </td>
          </tr>
          <tr>
            <td><strong>finance_admin</strong></td>
            <td>Finance operations only</td>
            <td>
              <span class="badge badge-primary mr-1 mb-1">finance_ops</span>
              <span class="badge badge-primary mr-1 mb-1">view_reports</span>
            </td>
            <td>
              <button class="btn btn-sm btn-outline-primary" disabled><i class="fa fa-pencil"></i></button>
              <button class="btn btn-sm btn-outline-danger" disabled><i class="fa fa-trash"></i></button>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="modal fade" id="modalRole" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Role</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
      <div class="modal-body">
        <div id="roleMsg" class="mb-2"></div>
        <form id="formRole">
          <input type="hidden" name="role_id" id="role_id">
          <div class="form-group"><label>Name</label><input type="text" class="form-control" name="name" id="name" required></div>
          <div class="form-group"><label>Description</label><input type="text" class="form-control" name="description" id="description"></div>
          <div class="form-group"><label>Permissions</label>
            <div id="permList" class="row"></div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" id="btnSaveRole" class="btn btn-primary">Save</button>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  var tbody = document.querySelector('#tblRoles tbody');

  function escapeHtml(s){ return String(s||'').replace(/[&<>\"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','\"':'&quot;','\'':'&#39;'}[c]}); }

  function renderRoles(data){
    var items = (data && data.roles) || [];
    tbody.innerHTML = '';
    items.forEach(function(r){
      var perms = (r.permissions||[]).map(function(p){return '<span class="badge badge-primary mr-1 mb-1">'+escapeHtml(p.name)+'</span>';}).join('');
      var tr = document.createElement('tr');
      tr.innerHTML = '<td><strong>'+escapeHtml(r.name)+'</strong></td>'+
                     '<td>'+escapeHtml(r.description||'')+'</td>'+
                     '<td>'+perms+'</td>'+
                     '<td>'+
                       '<button class="btn btn-sm btn-outline-primary js-edit" data-id="'+r.role_id+'"><i class="fa fa-pencil"></i></button> '+
                       '<button class="btn btn-sm btn-outline-danger js-del" data-id="'+r.role_id+'"><i class="fa fa-trash"></i></button>'+
                     '</td>';
      tbody.appendChild(tr);
    });
    document.getElementById('statRoles').textContent = items.length;
    document.getElementById('statPerms').textContent = (data && data.permissions_count) || 0;
    document.getElementById('statAssign').textContent = (data && data.assignments_count) || 0;
    document.getElementById('statAdmins').textContent = (data && data.admins_with_roles) || 0;
  }

  function fetchAll(){
    fetch('php_action/permissions_api.php?action=bootstrap', {cache:'no-store'})
      .then(function(r){ return r.text().then(function(t){ try { return JSON.parse(t); } catch(e) { throw new Error(t||'Non-JSON response'); } }); })
      .then(function(data){ renderRoles(data); })
      .catch(function(err){ console.error('bootstrap error:', err); renderRoles({roles:[]}); });
  }

  function openRoleModal(role){
    document.getElementById('roleMsg').innerHTML='';
    document.getElementById('role_id').value = role && role.role_id || '';
    document.getElementById('name').value = role && role.name || '';
    document.getElementById('description').value = role && role.description || '';
    // Load permissions list
    var container = document.getElementById('permList');
    container.innerHTML = '<div class="col-12 text-muted">Loading permissions...</div>';
    fetch('php_action/permissions_api.php?action=list_permissions', {cache:'no-store'})
      .then(function(r){ return r.text().then(function(t){ try { return JSON.parse(t); } catch(e) { throw new Error(t||'Non-JSON response'); } }); })
      .then(function(data){
        var perms = (data && data.permissions)||[];
        container.innerHTML = perms.map(function(p){
          var checked = role && role.permissions && role.permissions.find(function(x){return String(x.permission_id)===String(p.permission_id);}) ? 'checked' : '';
          return '<div class="col-md-6"><div class="form-check">'+
                   '<input class="form-check-input" type="checkbox" name="permissions[]" value="'+p.permission_id+'" '+checked+'> '+
                   '<label class="form-check-label">'+escapeHtml(p.name)+'</label>'+
                 '</div></div>';
        }).join('');
      });
    $('#modalRole').modal('show');
  }

  document.getElementById('btnAddRole').addEventListener('click', function(){ openRoleModal(null); });
  document.getElementById('btnRefresh').addEventListener('click', fetchAll);

  document.getElementById('btnSaveRole').addEventListener('click', function(){
    var fd = new FormData(document.getElementById('formRole'));
    var id = document.getElementById('role_id').value;
    var action = id ? 'update_role' : 'create_role';
    var btn = this;
    btn.disabled = true; btn.innerHTML = 'Saving...';
    fetch('php_action/permissions_api.php?action='+action, {method:'POST', body: fd})
      .then(function(r){ return r.text().then(function(t){ try { return JSON.parse(t); } catch(e) { throw new Error(t||'Non-JSON response'); } }); })
      .then(function(res){
        if(res && res.success){ $('#modalRole').modal('hide'); fetchAll(); }
        else { document.getElementById('roleMsg').innerHTML = '<div class="alert alert-danger">'+(res && res.message ? escapeHtml(res.message) : 'Save failed')+'</div>'; }
      })
      .catch(function(err){ document.getElementById('roleMsg').innerHTML = '<div class="alert alert-danger">Request failed: '+escapeHtml(err.message||'')+'</div>'; })
      .finally(function(){ btn.disabled = false; btn.innerHTML = 'Save'; });
  });

  // Ensure Close (X and button) always works
  document.querySelectorAll('#modalRole .close, #modalRole [data-dismiss="modal"]').forEach(function(el){
    el.addEventListener('click', function(){ $('#modalRole').modal('hide'); });
  });

  document.getElementById('tblRoles').addEventListener('click', function(e){
    var btn = e.target.closest('button'); if(!btn) return;
    var id = btn.getAttribute('data-id'); if(!id) return;
    if(btn.classList.contains('js-edit')){
      // Load role with permissions
      fetch('php_action/permissions_api.php?action=get_role&role_id='+encodeURIComponent(id), {cache:'no-store'})
        .then(function(r){return r.json();})
        .then(function(role){ openRoleModal(role); });
    } else if(btn.classList.contains('js-del')){
      if(confirm('Delete this role?')){
        var fd = new FormData(); fd.append('role_id', id);
        fetch('php_action/permissions_api.php?action=delete_role', {method:'POST', body: fd})
          .then(function(r){return r.json();})
          .then(function(res){ if(res && res.success){ fetchAll(); } else { alert('Delete failed'); } });
      }
    }
  });

  // Don't call fetchAll() on page load since we already have the data from PHP
  // fetchAll();
})();
</script>
