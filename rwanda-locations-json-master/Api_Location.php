<?php
// Load locations.json
$locations = json_decode(file_get_contents('locations.json'), true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Rwanda Locations Cascading Dropdowns</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #f8f9fa; }
    .container { max-width: 700px; margin-top: 40px; }
    .form-label { font-weight: 500; }
    .card { box-shadow: 0 2px 8px rgba(0,0,0,0.07); }
  </style>
</head>
<body>
  <div class="container">
    <div class="card p-4">
      <h2 class="mb-4 text-center">Select Rwanda Location</h2>
      <form>
        <div class="mb-3">
          <label for="province" class="form-label">Province</label>
          <select class="form-select" id="province" required>
            <option value="">Select Province</option>
            <?php foreach ($locations['provinces'] as $province): ?>
              <option value="<?= htmlspecialchars($province['name']) ?>"><?= htmlspecialchars($province['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label for="district" class="form-label">District</label>
          <select class="form-select" id="district" disabled required>
            <option value="">Select District</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="sector" class="form-label">Sector</label>
          <select class="form-select" id="sector" disabled required>
            <option value="">Select Sector</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="cell" class="form-label">Cell</label>
          <select class="form-select" id="cell" disabled required>
            <option value="">Select Cell</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="village" class="form-label">Village</label>
          <select class="form-select" id="village" disabled required>
            <option value="">Select Village</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary w-100">Submit</button>
      </form>
    </div>
  </div>
  <script>
    // Pass PHP data to JS
    const locations = <?php echo json_encode($locations); ?>;

    // Get elements
    const provinceSelect = document.getElementById('province');
    const districtSelect = document.getElementById('district');
    const sectorSelect = document.getElementById('sector');
    const cellSelect = document.getElementById('cell');
    const villageSelect = document.getElementById('village');

    // Helper to reset and disable selects
    function resetSelect(select, placeholder) {
      select.innerHTML = `<option value="">${placeholder}</option>`;
      select.disabled = true;
    }

    // Province change
    provinceSelect.addEventListener('change', function() {
      resetSelect(districtSelect, 'Select District');
      resetSelect(sectorSelect, 'Select Sector');
      resetSelect(cellSelect, 'Select Cell');
      resetSelect(villageSelect, 'Select Village');
      if (!this.value) return;

      const province = locations.provinces.find(p => p.name === this.value);
      if (province && province.districts) {
        province.districts.forEach(d => {
          districtSelect.innerHTML += `<option value="${d.name}">${d.name}</option>`;
        });
        districtSelect.disabled = false;
      }
    });

    // District change
    districtSelect.addEventListener('change', function() {
      resetSelect(sectorSelect, 'Select Sector');
      resetSelect(cellSelect, 'Select Cell');
      resetSelect(villageSelect, 'Select Village');
      if (!this.value) return;

      const province = locations.provinces.find(p => p.name === provinceSelect.value);
      const district = province?.districts.find(d => d.name === this.value);
      if (district && district.sectors) {
        district.sectors.forEach(s => {
          sectorSelect.innerHTML += `<option value="${s.name}">${s.name}</option>`;
        });
        sectorSelect.disabled = false;
      }
    });

    // Sector change
    sectorSelect.addEventListener('change', function() {
      resetSelect(cellSelect, 'Select Cell');
      resetSelect(villageSelect, 'Select Village');
      if (!this.value) return;

      const province = locations.provinces.find(p => p.name === provinceSelect.value);
      const district = province?.districts.find(d => d.name === districtSelect.value);
      const sector = district?.sectors.find(s => s.name === this.value);
      if (sector && sector.cells) {
        sector.cells.forEach(c => {
          cellSelect.innerHTML += `<option value="${c.name}">${c.name}</option>`;
        });
        cellSelect.disabled = false;
      }
    });

    // Cell change
    cellSelect.addEventListener('change', function() {
      resetSelect(villageSelect, 'Select Village');
      if (!this.value) return;

      const province = locations.provinces.find(p => p.name === provinceSelect.value);
      const district = province?.districts.find(d => d.name === districtSelect.value);
      const sector = district?.sectors.find(s => s.name === sectorSelect.value);
      const cell = sector?.cells.find(c => c.name === this.value);
      if (cell && cell.villages) {
        cell.villages.forEach(v => {
          // Some villages are objects with a "name" property
          const vName = typeof v === 'string' ? v : v.name;
          villageSelect.innerHTML += `<option value="${vName}">${vName}</option>`;
        });
        villageSelect.disabled = false;
      }
    });
  </script>
</body>
</html> 