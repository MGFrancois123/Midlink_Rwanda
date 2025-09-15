(function(){
  document.addEventListener('DOMContentLoaded', function(){
    var form = document.getElementById('verifyLicenseForm');
    var input = document.getElementById('license_query');
    var result = document.getElementById('licenseResult');
    var datalist = document.getElementById('license_suggestions');
    var select = document.getElementById('license_select');
    if (!form || !input || !result) return;

    // populate select with all pharmacies
    if (select) {
      fetch('php_action/verifyLicense.php?all=1')
        .then(function(r){ return r.json(); })
        .then(function(d){
          var items = d.items || [];
          select.innerHTML = '';
          var placeholder = document.createElement('option');
          placeholder.value = '';
          placeholder.textContent = 'Choose Pharmacy Name';
          placeholder.disabled = false;
          placeholder.selected = true;
          select.appendChild(placeholder);
          items.forEach(function(it){
            var opt = document.createElement('option');
            opt.value = it.license_number;
            opt.textContent = it.name.toUpperCase();
            select.appendChild(opt);
          });

          function updateInputState() {
            var hasSelection = !!select.value;
            if (hasSelection) {
              // show selected license, enable and make readonly with highlight
              input.disabled = false;
              input.readOnly = true;
              input.value = select.value;
              input.classList.add('bg-warning');
              input.classList.add('c-white');
            } else {
              // no selection: disable and clear, remove highlight
              input.readOnly = false;
              input.disabled = true;
              input.value = '';
              input.classList.remove('bg-warning');
              input.classList.remove('c-white');
            }
          }
          updateInputState();

          select.addEventListener('change', function(){
            updateInputState();
            // trigger verification directly using the selected license
            var license = select.value;
            if (!license) return;
            result.innerHTML = '<div class="alert alert-info">Verifying license...</div>';
            fetch('php_action/verifyLicense.php?q=' + encodeURIComponent(license))
              .then(function(r){ return r.json(); })
              .then(function(data){
                if (data.found && data.pharmacy) {
                  var p = data.pharmacy;
                  result.innerHTML = '<div class="alert alert-success"><strong>Valid License</strong><br>'+
                    'Pharmacy: ' + (p.name || '') + '<br>'+
                    'License: ' + (p.license_number || '') + '<br>'+
                    'Location: ' + (p.location || '') + '<br>'+
                    'Contact: ' + (p.contact_person || '') + ' (' + (p.contact_phone || '') + ')' +
                    '</div>';
                } else {
                  result.innerHTML = '<div class="alert alert-danger">No pharmacy found with that license number.</div>';
                }
              })
              .catch(function(){
                result.innerHTML = '<div class="alert alert-warning">Verification failed. Try again.</div>';
              });
          });
        });
    }

    // prevent manual entry when not selected
    if (input) {
      input.addEventListener('input', function(){
        if (select && !select.value) {
          input.value = '';
        }
      });
    }

    var debounceTimer = null;
    input.addEventListener('input', function(){
      var q = (input.value || '').trim();
      clearTimeout(debounceTimer);
      if (!q) { if (datalist) datalist.innerHTML = ''; return; }
      debounceTimer = setTimeout(function(){
        fetch('php_action/verifyLicense.php?search=1&q=' + encodeURIComponent(q))
          .then(function(r){ return r.json(); })
          .then(function(d){
            if (!datalist) return;
            datalist.innerHTML = '';
            (d.suggestions || []).forEach(function(s){
              var opt = document.createElement('option');
              opt.value = s.license_number;
              opt.label = s.name + ' (' + s.license_number + ')';
              datalist.appendChild(opt);
            });
          });
      }, 250);
    });

    form.addEventListener('submit', function(e){
      e.preventDefault();
      var q = (input.value || '').trim();
      if (!q && select && select.value) { q = select.value; }
      if (!q) return;
      result.innerHTML = '<div class="alert alert-info">Verifying license...</div>';
      fetch('php_action/verifyLicense.php?q=' + encodeURIComponent(q))
        .then(function(r){ return r.json(); })
        .then(function(data){
          if (data.found && data.pharmacy) {
            var p = data.pharmacy;
            result.innerHTML = '<div class="alert alert-success"><strong>Valid License</strong><br>'+
              'Pharmacy: ' + (p.name || '') + '<br>'+
              'License: ' + (p.license_number || '') + '<br>'+
              'Location: ' + (p.location || '') + '<br>'+
              'Contact: ' + (p.contact_person || '') + ' (' + (p.contact_phone || '') + ')' +
              '</div>';
          } else {
            result.innerHTML = '<div class="alert alert-danger">No pharmacy found with that license number.</div>';
          }
        })
        .catch(function(){
          result.innerHTML = '<div class="alert alert-warning">Verification failed. Try again.</div>';
        });
    });
  });
})();


