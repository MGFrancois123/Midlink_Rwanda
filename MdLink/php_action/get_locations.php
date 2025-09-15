<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

try {
    // Load the Rwanda locations data
    $locations_file = dirname(__FILE__) . '/../../rwanda-locations-json-master/locations.json';
    
    if (!file_exists($locations_file)) {
        throw new Exception('Locations file not found');
    }
    
    $locations = json_decode(file_get_contents($locations_file), true);
    
    if (!$locations) {
        throw new Exception('Invalid locations data');
    }
    
    // Handle different API endpoints
    $action = $_GET['action'] ?? 'all';
    
    switch ($action) {
        case 'provinces':
            $response = [
                'success' => true,
                'data' => $locations['provinces']
            ];
            break;
            
        case 'districts':
            $province_name = $_GET['province'] ?? '';
            if (empty($province_name)) {
                throw new Exception('Province name is required');
            }
            
            $province = null;
            foreach ($locations['provinces'] as $p) {
                if ($p['name'] === $province_name) {
                    $province = $p;
                    break;
                }
            }
            
            if (!$province) {
                throw new Exception('Province not found');
            }
            
            $response = [
                'success' => true,
                'data' => $province['districts'] ?? []
            ];
            break;
            
        case 'sectors':
            $province_name = $_GET['province'] ?? '';
            $district_name = $_GET['district'] ?? '';
            
            if (empty($province_name) || empty($district_name)) {
                throw new Exception('Province and district names are required');
            }
            
            $province = null;
            foreach ($locations['provinces'] as $p) {
                if ($p['name'] === $province_name) {
                    $province = $p;
                    break;
                }
            }
            
            if (!$province) {
                throw new Exception('Province not found');
            }
            
            $district = null;
            foreach ($province['districts'] as $d) {
                if ($d['name'] === $district_name) {
                    $district = $d;
                    break;
                }
            }
            
            if (!$district) {
                throw new Exception('District not found');
            }
            
            $response = [
                'success' => true,
                'data' => $district['sectors'] ?? []
            ];
            break;
            
        case 'cells':
            $province_name = $_GET['province'] ?? '';
            $district_name = $_GET['district'] ?? '';
            $sector_name = $_GET['sector'] ?? '';
            
            if (empty($province_name) || empty($district_name) || empty($sector_name)) {
                throw new Exception('Province, district, and sector names are required');
            }
            
            $province = null;
            foreach ($locations['provinces'] as $p) {
                if ($p['name'] === $province_name) {
                    $province = $p;
                    break;
                }
            }
            
            if (!$province) {
                throw new Exception('Province not found');
            }
            
            $district = null;
            foreach ($province['districts'] as $d) {
                if ($d['name'] === $district_name) {
                    $district = $d;
                    break;
                }
            }
            
            if (!$district) {
                throw new Exception('District not found');
            }
            
            $sector = null;
            foreach ($district['sectors'] as $s) {
                if ($s['name'] === $sector_name) {
                    $sector = $s;
                    break;
                }
            }
            
            if (!$sector) {
                throw new Exception('Sector not found');
            }
            
            $response = [
                'success' => true,
                'data' => $sector['cells'] ?? []
            ];
            break;
            
        case 'villages':
            $province_name = $_GET['province'] ?? '';
            $district_name = $_GET['district'] ?? '';
            $sector_name = $_GET['sector'] ?? '';
            $cell_name = $_GET['cell'] ?? '';
            
            if (empty($province_name) || empty($district_name) || empty($sector_name) || empty($cell_name)) {
                throw new Exception('Province, district, sector, and cell names are required');
            }
            
            $province = null;
            foreach ($locations['provinces'] as $p) {
                if ($p['name'] === $province_name) {
                    $province = $p;
                    break;
                }
            }
            
            if (!$province) {
                throw new Exception('Province not found');
            }
            
            $district = null;
            foreach ($province['districts'] as $d) {
                if ($d['name'] === $district_name) {
                    $district = $d;
                    break;
                }
            }
            
            if (!$district) {
                throw new Exception('District not found');
            }
            
            $sector = null;
            foreach ($district['sectors'] as $s) {
                if ($s['name'] === $sector_name) {
                    $sector = $s;
                    break;
                }
            }
            
            if (!$sector) {
                throw new Exception('Sector not found');
            }
            
            $cell = null;
            foreach ($sector['cells'] as $c) {
                if ($c['name'] === $cell_name) {
                    $cell = $c;
                    break;
                }
            }
            
            if (!$cell) {
                throw new Exception('Cell not found');
            }
            
            $response = [
                'success' => true,
                'data' => $cell['villages'] ?? []
            ];
            break;
            
        default:
            $response = [
                'success' => true,
                'data' => $locations
            ];
            break;
    }
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);
?>

