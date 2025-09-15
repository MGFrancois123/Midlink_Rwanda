(function() {
  var locationsUrlCandidates = [
    '../../rwanda-locations-json-master/locations.json',
    '../rwanda-locations-json-master/locations.json',
    'rwanda-locations-json-master/locations.json',
    '/rwanda-locations-json-master/locations.json'
  ];

  function setDisabled(select, disabled) {
    select.disabled = disabled;
    if (disabled) {
      select.innerHTML = '<option value="">Select</option>';
    }
  }

  function updateHiddenLocation() {
    var province = document.getElementById('province');
    var district = document.getElementById('district');
    var sector = document.getElementById('sector');
    var cell = document.getElementById('cell');
    var village = document.getElementById('village');
    var hidden = document.getElementById('location');
    var parts = [];
    if (province.value) parts.push(province.value);
    if (district.value) parts.push(district.value);
    if (sector.value) parts.push(sector.value);
    if (cell.value) parts.push(cell.value);
    if (village.value) parts.push(village.value);
    hidden.value = parts.join(' / ');
  }

  function tryFetchLocations(index, onSuccess, onError) {
    if (index >= locationsUrlCandidates.length) {
      onError();
      return;
    }
    fetch(locationsUrlCandidates[index])
      .then(function(r){ if (!r.ok) throw new Error('HTTP '+r.status); return r.json(); })
      .then(onSuccess)
      .catch(function(){ tryFetchLocations(index + 1, onSuccess, onError); });
  }

  function populateSelect(select, items) {
    select.innerHTML = '<option value="">'+select.options[0].text+'</option>';
    items.forEach(function(item) {
      var opt = document.createElement('option');
      opt.value = item.name;
      opt.textContent = item.name;
      select.appendChild(opt);
    });
  }

  document.addEventListener('DOMContentLoaded', function() {
    var province = document.getElementById('province');
    if (!province) return;
    // auto-generate license number and lock input
    var licInput = document.querySelector('input[name="license_number"]');
    if (licInput) {
      licInput.readOnly = true;
      fetch('php_action/generateLicense.php')
        .then(function(r){ return r.json(); })
        .then(function(d){ if (d.license) licInput.value = d.license; });
    }
    var district = document.getElementById('district');
    var sector = document.getElementById('sector');
    var cell = document.getElementById('cell');
    var village = document.getElementById('village');

    setDisabled(district, true);
    setDisabled(sector, true);
    setDisabled(cell, true);
    setDisabled(village, true);

    tryFetchLocations(0, function(data){
      populateSelect(province, data.provinces || []);

      province.addEventListener('change', function(){
        updateHiddenLocation();
        var selectedProvince = (data.provinces || []).find(function(p){ return p.name === province.value;});
        if (!selectedProvince) {
          setDisabled(district, true); setDisabled(sector, true); setDisabled(cell, true); setDisabled(village, true);
          return;
        }
        populateSelect(district, selectedProvince.districts || []);
        district.disabled = false;
        setDisabled(sector, true); setDisabled(cell, true); setDisabled(village, true);
      });

      district.addEventListener('change', function(){
        updateHiddenLocation();
        var selectedProvince = (data.provinces || []).find(function(p){ return p.name === province.value;});
        var selectedDistrict = selectedProvince ? (selectedProvince.districts || []).find(function(d){ return d.name === district.value;}) : null;
        if (!selectedDistrict) { setDisabled(sector, true); setDisabled(cell, true); setDisabled(village, true); return; }
        populateSelect(sector, selectedDistrict.sectors || []);
        sector.disabled = false;
        setDisabled(cell, true); setDisabled(village, true);
      });

      sector.addEventListener('change', function(){
        updateHiddenLocation();
        var selectedProvince = (data.provinces || []).find(function(p){ return p.name === province.value;});
        var selectedDistrict = selectedProvince ? (selectedProvince.districts || []).find(function(d){ return d.name === district.value;}) : null;
        var selectedSector = selectedDistrict ? (selectedDistrict.sectors || []).find(function(s){ return s.name === sector.value;}) : null;
        if (!selectedSector) { setDisabled(cell, true); setDisabled(village, true); return; }
        populateSelect(cell, selectedSector.cells || []);
        cell.disabled = false;
        setDisabled(village, true);
      });

      cell.addEventListener('change', function(){
        updateHiddenLocation();
        var selectedProvince = (data.provinces || []).find(function(p){ return p.name === province.value;});
        var selectedDistrict = selectedProvince ? (selectedProvince.districts || []).find(function(d){ return d.name === district.value;}) : null;
        var selectedSector = selectedDistrict ? (selectedDistrict.sectors || []).find(function(s){ return s.name === sector.value;}) : null;
        var selectedCell = selectedSector ? (selectedSector.cells || []).find(function(c){ return c.name === cell.value;}) : null;
        if (!selectedCell) { setDisabled(village, true); return; }
        populateSelect(village, selectedCell.villages || []);
        village.disabled = false;
      });

      village.addEventListener('change', updateHiddenLocation);
    }, function(){
      console.error('Failed to load Rwanda locations JSON. Ensure locations.json is accessible.');
    });

    var form = document.getElementById('registerPharmacyForm');
    if (form) {
      form.addEventListener('submit', function(){
        updateHiddenLocation();
      });
    }
  });
})();


