<?php
session_start();
require_once './constant/connect.php';
require_once './constant/check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include './constant/layout/head.php'; ?>
    <title>Manage Pharmacies - MdLink Rwanda</title>
    <style>
        .page-wrapper { width: 100%; }
        .page-wrapper .container-fluid { width: 100%; max-width: 100%; padding-left: 15px; padding-right: 15px; }
        .card { border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.06); border-radius: 12px; }
        .card-header { background: #f8f9fa; border-bottom: 1px solid #eef0f2; border-radius: 12px 12px 0 0; }
        .card-header h4 { margin: 0; color: #2d2d2d; }
        .form-control { border: 2px solid #e5e7eb; border-radius: 10px; }
        .form-control:focus { border-color: #2f855a; box-shadow: 0 0 0 0.2rem rgba(47,133,90,0.15); }
        .btn-primary { background: #2f855a; border-color: #2f855a; border-radius: 10px; }
        .btn-primary:hover { background: #276749; border-color: #276749; }
        .table thead th { background: #f1f3f5; color: #2d2d2d; border: none; }
        .table tbody td { color: #2d2d2d; vertical-align: middle; }
        .badge { background: #e6f4ea; color: #2f855a; border-radius: 12px; font-weight: 600; }
        .actions .btn { border-color: #2f855a; color: #2f855a; }
        .actions .btn:hover { background: #2f855a; color: #fff; }
    </style>
</head>
<body>
    <?php include './constant/layout/header.php'; ?>
    <?php include './constant/layout/sidebar.php'; ?>

    <div class="page-wrapper">
        <div class="container-fluid py-4">
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h4 class="d-flex align-items-center" style="gap:10px;"><i class="fa fa-building"></i> Manage Pharmacies <span id="pharmacyCount" class="badge" style="font-size:12px;">0</span></h4>
                            <div class="d-flex" style="gap:10px;">
                                <input type="text" id="search" class="form-control" placeholder="Search by name, license, location" style="min-width:280px;" />
                                <a href="create_pharmacy.php" class="btn btn-primary"><i class="fa fa-plus"></i> Create Pharmacy</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="pharmMessage"></div>
<?php
$pharmacies = [];
$dbError = '';
$sql = "SELECT pharmacy_id, name, license_number, location, contact_person, contact_phone, created_at FROM pharmacies ORDER BY created_at DESC";
$result = $connect->query($sql);
if ($result instanceof mysqli_result) {
    while ($row = $result->fetch_assoc()) { $pharmacies[] = $row; }
} else if ($result === false) {
    $dbError = $connect->error;
}
?>
<?php if ($dbError !== '') { ?>
                            <div class="alert alert-danger" role="alert" style="border:none;border-left:4px solid #dc3545;border-radius:8px;">
                                <strong><i class="fa fa-exclamation-triangle"></i> Database error:</strong> <?php echo htmlspecialchars($dbError); ?>
                            </div>

                            <!-- Create Pharmacy (inline) -->
                            <div class="mt-4 p-3" style="background:#f9fafb;border:1px solid #eef0f2;border-radius:12px;">
                                <h5 style="margin-bottom:15px;">Quick Create</h5>
                                <form id="quickCreatePharmacy">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Pharmacy Name *</label>
                                                <input type="text" name="name" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>License Number *</label>
                                                <input type="text" name="license_number" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Contact Person *</label>
                                                <input type="text" name="contact_person" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Contact Phone *</label>
                                                <input type="tel" name="contact_phone" class="form-control" placeholder="+250 7XX XXX XXX" pattern="^[+]?[0-9\s\-()]{10,}$" required>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label>Location *</label>
                                                <div class="row">
                                                    <div class="col-md-6 mb-2">
                                                        <select class="form-control" id="qc_province" required>
                                                            <option value="">Province</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 mb-2">
                                                        <select class="form-control" id="qc_district" disabled required>
                                                            <option value="">District</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <select class="form-control" id="qc_sector" disabled required>
                                                            <option value="">Sector</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <select class="form-control" id="qc_cell" disabled required>
                                                            <option value="">Cell</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <select class="form-control" id="qc_village" disabled required>
                                                            <option value="">Village</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="location" id="qc_location" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <button type="submit" class="btn btn-primary" id="qc_btn"><i class="fa fa-plus"></i> Save Pharmacy</button>
                                    </div>
                                </form>
                            </div>
<?php } ?>
                            <div class="table-responsive">
                                <table class="table table-hover" id="pharmaciesTable">
                                    <thead>
                                        <tr>
                                            <th>Pharmacy</th>
                                            <th>License</th>
                                            <th>Location</th>
                                            <th>Contact</th>
                                            <th>Created</th>
                                            <th class="text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
<?php if (!empty($pharmacies)) { foreach ($pharmacies as $p) { ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($p['name'] ?? ''); ?></strong></td>
                                            <td><?php echo htmlspecialchars($p['license_number'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($p['location'] ?? ''); ?></td>
                                            <td>
                                                <?php if (!empty($p['contact_person'])) { echo '<i class="fa fa-user"></i> '.htmlspecialchars($p['contact_person']).'<br>'; }
                                                if (!empty($p['contact_phone'])) { echo '<i class="fa fa-phone"></i> '.htmlspecialchars($p['contact_phone']); } ?>
                                            </td>
                                            <td><?php echo !empty($p['created_at']) ? date('M d, Y', strtotime($p['created_at'])) : '-'; ?></td>
                                            <td class="text-right actions">
                                                <a class="btn btn-sm btn-outline-primary" title="View"><i class="fa fa-eye"></i></a>
                                                <a class="btn btn-sm btn-outline-primary" title="Edit"><i class="fa fa-pencil"></i></a>
                                                <a class="btn btn-sm btn-outline-primary" title="Deactivate"><i class="fa fa-ban"></i></a>
                                            </td>
                                        </tr>
<?php } } else { ?>
                                        <tr><td colspan="6" class="text-center text-muted">No pharmacies found</td></tr>
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

    <?php include './constant/layout/footer.php'; ?>

    <script>
    $(function(){
        // Set count badge
        var count = $('#pharmaciesTable tbody tr').length;
        var emptyRows = $('#pharmaciesTable tbody tr td').length === 1; // single cell row => empty message
        $('#pharmacyCount').text(emptyRows ? '0' : String(count));

        $('#search').on('input', function(){
            const q = $(this).val().toLowerCase();
            $('#pharmaciesTable tbody tr').each(function(){
                const rowText = $(this).text().toLowerCase();
                $(this).toggle(rowText.indexOf(q) !== -1);
            });
            // update count after filtering
            var visible = $('#pharmaciesTable tbody tr:visible').length;
            $('#pharmacyCount').text(String(visible));
        });

        // Load cascading location options
        initializeQuickLocation();

        // Submit quick create
        $('#quickCreatePharmacy').on('submit', function(e){
            e.preventDefault();
            const btn = $('#qc_btn');
            // Ensure cascading location is fully selected
            if (!$('#qc_village').val()) {
                showMsg('Please select Province, District, Sector, Cell and Village.', 'danger');
                return;
            }
            if (!$('#qc_location').val()) {
                showMsg('Location is incomplete. Please complete all selections.', 'danger');
                return;
            }
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
            $.ajax({
                url: 'php_action/create_pharmacy.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json'
            }).done(function(resp){
                if (resp && resp.success) {
                    showMsg('Pharmacy created successfully!', 'success');
                    setTimeout(function(){ location.reload(); }, 1200);
                } else {
                    showMsg(resp && resp.message ? resp.message : 'Failed to create pharmacy', 'danger');
                }
            }).fail(function(){
                showMsg('Network error. Please try again.', 'danger');
            }).always(function(){
                btn.prop('disabled', false).html('<i class="fa fa-plus"></i> Save Pharmacy');
            });
        });

        function showMsg(text, type){
            const cls = type === 'success' ? 'alert-success' : 'alert-danger';
            $('#pharmMessage').html('<div class="alert '+cls+'" style="border:none;border-left:4px solid '+(type==='success'?'#28a745':'#dc3545')+';border-radius:8px;">'+text+'</div>');
            setTimeout(function(){ $('#pharmMessage').html(''); }, 5000);
        }
    });

    // Quick create cascading locations using existing API
    function initializeQuickLocation(){
        const province = $('#qc_province');
        const district = $('#qc_district');
        const sector = $('#qc_sector');
        const cell = $('#qc_cell');
        const village = $('#qc_village');
        const hiddenLocation = $('#qc_location');

        function setHidden(){
            const parts = [];
            if (village.val()) parts.push(village.val());
            if (cell.val()) parts.push(cell.val());
            if (sector.val()) parts.push(sector.val());
            if (district.val()) parts.push(district.val());
            if (province.val()) parts.push(province.val());
            hiddenLocation.val(parts.join(', '));
        }

        // provinces
        $.getJSON('php_action/get_locations.php?action=provinces', function(r){
            if (r && r.success) {
                province.html('<option value="">Province</option>');
                r.data.forEach(function(p){ province.append('<option value="'+p.name+'">'+p.name+'</option>'); });
            }
        });

        province.on('change', function(){
            const p = $(this).val();
            district.prop('disabled', !p).html('<option value="">District</option>');
            sector.prop('disabled', true).html('<option value="">Sector</option>');
            cell.prop('disabled', true).html('<option value="">Cell</option>');
            village.prop('disabled', true).html('<option value="">Village</option>');
            setHidden();
            if (!p) return;
            $.getJSON('php_action/get_locations.php?action=districts&province='+encodeURIComponent(p), function(r){
                if (r && r.success) r.data.forEach(function(d){ district.append('<option value="'+d.name+'">'+d.name+'</option>'); });
            });
        });

        district.on('change', function(){
            const p = province.val();
            const d = $(this).val();
            sector.prop('disabled', !d).html('<option value="">Sector</option>');
            cell.prop('disabled', true).html('<option value="">Cell</option>');
            village.prop('disabled', true).html('<option value="">Village</option>');
            setHidden();
            if (!d) return;
            $.getJSON('php_action/get_locations.php?action=sectors&province='+encodeURIComponent(p)+'&district='+encodeURIComponent(d), function(r){
                if (r && r.success) r.data.forEach(function(s){ sector.append('<option value="'+s.name+'">'+s.name+'</option>'); });
            });
        });

        sector.on('change', function(){
            const p = province.val();
            const d = district.val();
            const s = $(this).val();
            cell.prop('disabled', !s).html('<option value="">Cell</option>');
            village.prop('disabled', true).html('<option value="">Village</option>');
            setHidden();
            if (!s) return;
            $.getJSON('php_action/get_locations.php?action=cells&province='+encodeURIComponent(p)+'&district='+encodeURIComponent(d)+'&sector='+encodeURIComponent(s), function(r){
                if (r && r.success) r.data.forEach(function(c){ cell.append('<option value="'+c.name+'">'+c.name+'</option>'); });
            });
        });

        cell.on('change', function(){
            const p = province.val();
            const d = district.val();
            const s = sector.val();
            const c = $(this).val();
            village.prop('disabled', !c).html('<option value="">Village</option>');
            setHidden();
            if (!c) return;
            $.getJSON('php_action/get_locations.php?action=villages&province='+encodeURIComponent(p)+'&district='+encodeURIComponent(d)+'&sector='+encodeURIComponent(s)+'&cell='+encodeURIComponent(c), function(r){
                if (r && r.success) {
                    r.data.forEach(function(v){ var name = typeof v === 'string' ? v : v.name; village.append('<option value="'+name+'">'+name+'</option>'); });
                }
            });
        });

        village.on('change', setHidden);
    }
    </script>
</body>
</html>


