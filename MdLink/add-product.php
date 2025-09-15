<?php include('./constant/layout/head.php');?>
<?php include('./constant/layout/header.php');?>

<?php include('./constant/layout/sidebar.php');?>
<!--  Author Name: Mayuri K. 
 for any PHP, Codeignitor, Laravel OR Python work contact me at mayuri.infospace@gmail.com  
 Visit website : www.mayurik.com -->  

 
        <div class="page-wrapper">
            
            <div class="row page-titles">
                <div class="col-md-5 align-self-center">
                    <h3 class="text-primary">Add Medicine</h3> </div>
                <div class="col-md-7 align-self-center">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                        <li class="breadcrumb-item active">Add Medicine</li>
                    </ol>
                </div>
            </div>
            
            
            <div class="container-fluid">
                
                
                
                
                <div class="row">
                    <div class="col-lg-10 mx-auto">
                        <div class="card">
                            <div class="card-title">
                               
                            </div>
                            <div id="add-brand-messages"></div>
                            <div class="card-body">
                                <div class="input-states">
                                    <form class="row" method="POST" id="submitProductForm" action="php_action/create_medicine.php" enctype="multipart/form-data">

                                   <input type="hidden" name="currnt_date" class="form-control">

                                            <div class="form-group col-md-6">
                                                <label class="control-label"><i class="fa fa-capsules"></i> Medicine Name *</label>
                                                  <select class="form-control" id="name" name="name" required>
                                                    <option value="">~~ SELECT MEDICINE ~~</option>
                                                  </select>
                                                  <small class="text-muted">Select a category first to load medicines.</small>
                                                </div>
                                        <div class="form-group col-md-6">
                                                <label class="control-label"><i class="fa fa-align-left"></i> Description</label>
                                                  <select class="form-control" id="description" name="description">
                                                    <option value="">~~ SELECT DESCRIPTION ~~</option>
                                                  </select>
                                                  <small class="text-muted">Automatically populated after selecting medicine.</small>
                                        </div>
                                        <div class="form-group col-md-6">
                                                <label class="control-label"><i class="fa fa-money-bill"></i> Price (RWF) *</label>
                                                   <input type="number" step="0.01" class="form-control" id="price" placeholder="Price in RWF" name="price" autocomplete="off" required="" min="0"/>
                                        </div>
                                        <div class="form-group col-md-6">
                                                <label class="control-label"><i class="fa fa-boxes"></i> Stock Quantity *</label>
                                                  <input type="number" class="form-control" id="stock_quantity" placeholder="Stock Quantity" name="stock_quantity" autocomplete="off" required="" min="0"/>
                                        </div>
                                        <div class="form-group col-md-6">
                                                <label class="control-label"><i class="fa fa-calendar"></i> Expiry Date</label>
                                                   <input type="date" class="form-control" id="expiry_date" placeholder="Expiry Date" name="expiry_date" autocomplete="off"/>
                                        </div>
                                        <div class="form-group col-md-6">
                                                <label class="control-label">Pharmacy</label>
                                                  <select class="form-control" id="pharmacy_id" name="pharmacy_id">
                        <option value="">~~SELECT PHARMACY~~</option>
                        <?php 
                        $sql = "SELECT pharmacy_id, name FROM pharmacies ORDER BY name";
                                $result = $connect->query($sql);

                                while($row = $result->fetch_array()) {
                                    echo "<option value='".$row[0]."'>".$row[1]."</option>";
                                } // while
                                
                        ?>
                      </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                                <label class="control-label"><i class="fa fa-tags"></i> Category *</label>
                                                  <select class="form-control" id="category_id" name="category_id" required>
                        <option value="">~~SELECT CATEGORY~~</option>
                        <?php 
                        $sql = "SELECT category_id, category_name FROM category WHERE status = '1' ORDER BY category_name";
                                $result = $connect->query($sql);

                                while($row = $result->fetch_array()) {
                                    echo "<option value='".$row[0]."'>".$row[1]."</option>";
                                } // while
                                
                        ?>
                      </select>
                                    </div>
                                    <script>
                                    (function(){
                                      var cat = document.getElementById('category_id');
                                      var med = document.getElementById('name');
                                      var desc = document.getElementById('description');
                                      var KNOWN_MEDICINES = [
                                        { name: 'Paracetamol 500mg', description: 'Pain reliever and fever reducer', categories: ['Analgesics','Pain Relief','General'] },
                                        { name: 'Ibuprofen 400mg', description: 'NSAID for pain, inflammation, and fever', categories: ['Analgesics','Pain Relief'] },
                                        { name: 'Diclofenac 50mg', description: 'NSAID for moderate pain and inflammation', categories: ['Analgesics','Pain Relief'] },
                                        { name: 'Amoxicillin 500mg', description: 'Broad-spectrum penicillin antibiotic', categories: ['Antibiotics'] },
                                        { name: 'Azithromycin 500mg', description: 'Macrolide antibiotic for respiratory infections', categories: ['Antibiotics'] },
                                        { name: 'Ciprofloxacin 500mg', description: 'Fluoroquinolone antibiotic for bacterial infections', categories: ['Antibiotics'] },
                                        { name: 'Metronidazole 400mg', description: 'Antibiotic/antiprotozoal for anaerobic infections', categories: ['Antibiotics','Antiprotozoal'] },
                                        { name: 'Omeprazole 20mg', description: 'Proton pump inhibitor for acid reflux/ulcers', categories: ['Gastrointestinal','GI'] },
                                        { name: 'Salbutamol Inhaler', description: 'Bronchodilator for asthma relief', categories: ['Respiratory'] },
                                        { name: 'Oral Rehydration Salts (ORS)', description: 'Treats dehydration due to diarrhea', categories: ['Rehydration','General'] }
                                      ];
                                      function clearSelect(sel, placeholder){
                                        while (sel.firstChild) sel.removeChild(sel.firstChild);
                                        var opt = document.createElement('option');
                                        opt.value = '';
                                        opt.textContent = placeholder;
                                        sel.appendChild(opt);
                                      }
                                      function getSelectedCategoryName(){
                                        var t = '';
                                        if (!cat) return t;
                                        var i = cat.selectedIndex;
                                        if (i >= 0) { t = cat.options[i].text || ''; }
                                        return (t||'').trim();
                                      }
                                      function populateFromKnown(categoryText){
                                        clearSelect(med, '~~ SELECT MEDICINE ~~');
                                        clearSelect(desc, '~~ SELECT DESCRIPTION ~~');
                                        var list = KNOWN_MEDICINES;
                                        if (categoryText) {
                                          var ct = categoryText.toLowerCase();
                                          var filtered = list.filter(function(x){
                                            return (x.categories||[]).some(function(cn){ return (cn||'').toLowerCase() === ct; });
                                          });
                                          if (filtered.length > 0) list = filtered;
                                        }
                                        list.forEach(function(it){
                                          var o = document.createElement('option');
                                          o.value = it.name;
                                          o.textContent = it.name;
                                          o.setAttribute('data-description', it.description || '');
                                          med.appendChild(o);
                                        });
                                      }
                                      function loadMedicines(catId){
                                        clearSelect(med, '~~ SELECT MEDICINE ~~');
                                        clearSelect(desc, '~~ SELECT DESCRIPTION ~~');
                                        if(!catId) return;
                                        fetch('php_action/get_medicines_by_category.php?category_id=' + encodeURIComponent(catId))
                                          .then(function(r){ return r.json(); })
                                          .then(function(d){
                                            var items = (d && Array.isArray(d.items)) ? d.items : [];
                                            if(items.length === 0){
                                              // Fallback to built-in Rwanda medicines
                                              populateFromKnown(getSelectedCategoryName());
                                              return;
                                            }
                                            items.forEach(function(it){
                                              var o = document.createElement('option');
                                              o.value = it.name;
                                              o.textContent = it.name;
                                              o.setAttribute('data-description', it.description || '');
                                              med.appendChild(o);
                                            });
                                          }).catch(function(e){ console.error(e); });
                                      }
                                      cat && cat.addEventListener('change', function(){ loadMedicines(this.value); });
                                      med && med.addEventListener('change', function(){
                                        clearSelect(desc, '~~ SELECT DESCRIPTION ~~');
                                        var sel = this.options[this.selectedIndex];
                                        var d = sel ? sel.getAttribute('data-description') : '';
                                        var o = document.createElement('option');
                                        o.value = d;
                                        o.textContent = d || '(No description)';
                                        desc.appendChild(o);
                                        desc.value = d;
                                      });
                                      // If a category is already selected on load, populate immediately
                                      if (cat && cat.value) {
                                        loadMedicines(cat.value);
                                      }
                                    })();
                                    </script>
                                        <div class="form-group col-md-6">
                                                <label class="control-label"><i class="fa fa-ban"></i> Restricted Medicine</label>
                                                     <select class="form-control" id="restricted_medicine" name="restricted_medicine">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                      </select>
                                        </div>

                                        <div class="col-md-1 mx-auto">
                                        <button type="submit" name="create" id="createProductBtn" class="btn btn-primary btn-flat m-b-30 m-t-30">Submit</button></div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                  
                </div>
                
               


 
<script src="custom/js/product.js"></script>
<?php include('./constant/layout/footer.php');?>
<!--  Author Name: Mayuri K. 
 for any PHP, Codeignitor, Laravel OR Python work contact me at mayuri.infospace@gmail.com  
 Visit website : www.mayurik.com -->


1