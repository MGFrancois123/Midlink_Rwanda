<?php include('./constant/layout/head.php');?>
<?php include('./constant/layout/header.php');?>
<?php include('./constant/layout/sidebar.php');?>
<?php include('./constant/connect.php');?>

<style>
.medicine-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 30px;
    text-align: center;
}

.medicine-header h3 {
    margin-bottom: 10px;
    font-weight: bold;
}

.medicine-header p {
    opacity: 0.9;
    margin-bottom: 0;
}

.medicine-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    overflow: hidden;
}

.medicine-card .card-body {
    padding: 25px;
}

.medicine-stats {
    display: flex;
    justify-content: space-around;
    margin-bottom: 25px;
    text-align: center;
}

.stat-item {
    padding: 15px;
    border-radius: 10px;
    background: #f8f9fa;
    min-width: 120px;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: bold;
    color: #007bff;
    display: block;
}

.stat-label {
    font-size: 0.8rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.add-medicine-btn {
    background: linear-gradient(135deg, #28a745, #20c997);
    border: none;
    color: white;
    padding: 12px 25px;
    border-radius: 25px;
    font-weight: 500;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.add-medicine-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
    color: white;
    text-decoration: none;
}

.table-container {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}

.table-container .table {
    margin-bottom: 0;
}

.table-container .table th {
    background: #f8f9fa;
    border: none;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 0.5px;
}

.table-container .table td {
    border: none;
    vertical-align: middle;
}

.badge-custom {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.action-btn {
    border-radius: 20px;
    padding: 6px 12px;
    font-size: 0.8rem;
    margin-right: 5px;
}
</style>

<div class="page-wrapper">
    <div class="container-fluid">
        <!-- Enhanced Header -->
        <div class="medicine-header">
            <h3><i class="fa fa-medkit"></i> Medicine Management</h3>
            <p>View, manage, and track all medicines across the pharmacy network</p>
        </div>

        <!-- Statistics Row -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-item">
                    <span class="stat-number" id="totalMedicines">0</span>
                    <span class="stat-label">Total Medicines</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-item">
                    <span class="stat-number" id="activeMedicines">0</span>
                    <span class="stat-label">Active Stock</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-item">
                    <span class="stat-number" id="lowStockMedicines">0</span>
                    <span class="stat-label">Low Stock</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-item">
                    <span class="stat-number" id="expiringMedicines">0</span>
                    <span class="stat-label">Expiring Soon</span>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="medicine-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0"><i class="fa fa-list"></i> Medicine Inventory</h4>
                    <a href="placeholder.php?title=Add%20%2F%20Update%20Medicines" class="add-medicine-btn">
                        <i class="fa fa-plus"></i> Add Medicine
                    </a>
                </div>

                <div class="table-container">
                    <div class="table-responsive">
                        <table id="medicineTable" class="table table-bordered table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th>Medicine Name</th>
                                    <th>Pharmacy</th>
                                    <th>Category</th>
                                    <th>Price (RWF)</th>
                                    <th>Stock</th>
                                    <th>Expiry</th>
                                    <th>Restricted</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('./constant/layout/footer.php');?>

<script>
(function(){
    function fmtMoney(v){
        var n = parseFloat(v || 0); 
        return 'RWF ' + n.toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 0});
    }
    
    function badgeYesNo(val){
        return (String(val).toLowerCase()==='yes' || String(val)==='1' || String(val).toLowerCase()==='true')
            ? "<span class='badge badge-danger badge-custom'>Yes</span>"
            : "<span class='badge badge-secondary badge-custom'>No</span>";
    }

    function getStockBadge(stock) {
        var stockNum = parseInt(stock);
        if (stockNum <= 5) {
            return "<span class='badge badge-danger badge-custom'>" + stock + "</span>";
        } else if (stockNum <= 10) {
            return "<span class='badge badge-warning badge-custom'>" + stock + "</span>";
        } else {
            return "<span class='badge badge-success badge-custom'>" + stock + "</span>";
        }
    }

    function getExpiryBadge(expiry) {
        if (expiry === 'N/A') return "<span class='badge badge-secondary badge-custom'>N/A</span>";
        
        var expiryDate = new Date(expiry);
        var today = new Date();
        var diffTime = expiryDate - today;
        var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays < 0) {
            return "<span class='badge badge-danger badge-custom'>Expired</span>";
        } else if (diffDays <= 30) {
            return "<span class='badge badge-warning badge-custom'>" + diffDays + " days</span>";
        } else {
            return "<span class='badge badge-success badge-custom'>" + diffDays + " days</span>";
        }
    }

    if (typeof $ === 'undefined' || !$.fn || !$.fn.DataTable) return;
    
    var table = $('#medicineTable').DataTable({
        processing: true,
        ajax: {
            url: 'php_action/getMedicines.php',
            dataSrc: function(json){
                if (!json || json.success === false) {
                    console.error('Failed to load medicines', json && json.message);
                    return [];
                }
                
                var data = json.data || [];
                
                // Update statistics
                $('#totalMedicines').text(data.length);
                $('#activeMedicines').text(data.filter(function(item) { return parseInt(item.stock_quantity) > 0; }).length);
                $('#lowStockMedicines').text(data.filter(function(item) { return parseInt(item.stock_quantity) <= 10; }).length);
                $('#expiringMedicines').text(data.filter(function(item) { 
                    if (!item.expiry_date || item.expiry_date === 'N/A') return false;
                    var expiryDate = new Date(item.expiry_date);
                    var today = new Date();
                    var diffTime = expiryDate - today;
                    var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    return diffDays <= 30 && diffDays >= 0;
                }).length);
                
                return data.map(function(r){
                    return {
                        id: r.medicine_id,
                        name: r.name,
                        pharmacy: r.pharmacy_name || 'N/A',
                        category: r.category_name || 'N/A',
                        price: r.price,
                        stock: r.stock_quantity,
                        expiry: r.expiry_date ? r.expiry_date.substring(0,10) : 'N/A',
                        restricted: r.Restricted_Medicine
                    };
                });
            }
        },
        columns: [
            { data: 'id', className:'text-center' },
            { data: 'name' },
            { data: 'pharmacy' },
            { data: 'category' },
            { data: 'price', render: function(d){ return fmtMoney(d); } },
            { data: 'stock', render: function(d){ return getStockBadge(d); } },
            { data: 'expiry', render: function(d){ return getExpiryBadge(d); } },
            { data: 'restricted', render: function(d){ return badgeYesNo(d); } },
            { data: null, orderable:false, searchable:false, render: function(row){
                return '<a href="placeholder.php?title=Add%20%2F%20Update%20Medicines&edit='+row.id+'" class="btn btn-xs btn-primary action-btn" title="Edit"><i class="fa fa-pencil"></i></a>' +
                       '<a href="javascript:void(0)" onclick="viewMedicine('+row.id+')" class="btn btn-xs btn-info action-btn" title="View"><i class="fa fa-eye"></i></a>';
              }
            }
        ],
        order: [[0,'desc']],
        lengthMenu: [[10,25,50,100],[10,25,50,100]],
        dom: 'Bfrtip',
        buttons: [
            { extend:'copy', className:'btn btn-sm btn-secondary' },
            { extend:'csv', className:'btn btn-sm btn-secondary' },
            { extend:'excel', className:'btn btn-sm btn-secondary' },
            { extend:'print', className:'btn btn-sm btn-secondary' }
        ],
        pageLength: 10,
        responsive: true
    });

    // Move buttons to the header tools container
    table.on('init', function(){
        var $btns = $('.dt-buttons').addClass('btn-group btn-group-sm');
        $('.card-body').prepend('<div id="medicineTableTools" class="mb-3"></div>');
        $('#medicineTableTools').append($btns);
    });

    // Add view medicine function
    window.viewMedicine = function(id) {
        alert('View medicine details for ID: ' + id);
        // You can implement a modal or redirect to a detailed view page
    };
})();
</script>


