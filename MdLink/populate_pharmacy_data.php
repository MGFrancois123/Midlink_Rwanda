<?php
require_once './constant/connect.php';

// Sample data arrays
$categories = [
    ['name' => 'Antibiotics', 'description' => 'Medicines that fight bacterial infections'],
    ['name' => 'Pain Relief', 'description' => 'Medicines for pain and fever management'],
    ['name' => 'Cardiovascular', 'description' => 'Medicines for heart and blood pressure'],
    ['name' => 'Diabetes', 'description' => 'Medicines for diabetes management'],
    ['name' => 'Respiratory', 'description' => 'Medicines for breathing and lung conditions'],
    ['name' => 'Vitamins & Supplements', 'description' => 'Nutritional supplements and vitamins'],
    ['name' => 'Dermatology', 'description' => 'Medicines for skin conditions'],
    ['name' => 'Mental Health', 'description' => 'Medicines for psychiatric conditions'],
    ['name' => 'Pediatrics', 'description' => 'Medicines specifically for children'],
    ['name' => 'Emergency Medicine', 'description' => 'Critical care and emergency medicines']
];

$medicines = [
    // Antibiotics
    ['name' => 'Amoxicillin 500mg', 'description' => 'Broad-spectrum antibiotic for bacterial infections', 'price' => 2500, 'stock' => 150, 'expiry' => '2025-12-15', 'category' => 1, 'restricted' => '0'],
    ['name' => 'Azithromycin 250mg', 'description' => 'Macrolide antibiotic for respiratory infections', 'price' => 3200, 'stock' => 85, 'expiry' => '2025-11-20', 'category' => 1, 'restricted' => '0'],
    ['name' => 'Ciprofloxacin 500mg', 'description' => 'Fluoroquinolone antibiotic for various infections', 'price' => 2800, 'stock' => 120, 'expiry' => '2025-10-30', 'category' => 1, 'restricted' => '0'],
    ['name' => 'Doxycycline 100mg', 'description' => 'Tetracycline antibiotic for acne and infections', 'price' => 1800, 'stock' => 95, 'expiry' => '2025-09-25', 'category' => 1, 'restricted' => '0'],
    ['name' => 'Metronidazole 400mg', 'description' => 'Antibiotic for parasitic and bacterial infections', 'price' => 1200, 'stock' => 200, 'expiry' => '2025-12-10', 'category' => 1, 'restricted' => '0'],
    
    // Pain Relief
    ['name' => 'Paracetamol 500mg', 'description' => 'Pain reliever and fever reducer', 'price' => 500, 'stock' => 500, 'expiry' => '2026-03-15', 'category' => 2, 'restricted' => '0'],
    ['name' => 'Ibuprofen 400mg', 'description' => 'Anti-inflammatory pain reliever', 'price' => 800, 'stock' => 350, 'expiry' => '2026-02-20', 'category' => 2, 'restricted' => '0'],
    ['name' => 'Aspirin 100mg', 'description' => 'Pain reliever and blood thinner', 'price' => 300, 'stock' => 400, 'expiry' => '2026-01-30', 'category' => 2, 'restricted' => '0'],
    ['name' => 'Diclofenac 50mg', 'description' => 'NSAID for arthritis and pain', 'price' => 1500, 'stock' => 180, 'expiry' => '2025-11-15', 'category' => 2, 'restricted' => '0'],
    ['name' => 'Tramadol 50mg', 'description' => 'Opioid pain medication', 'price' => 2500, 'stock' => 75, 'expiry' => '2025-10-20', 'category' => 2, 'restricted' => '1'],
    
    // Cardiovascular
    ['name' => 'Amlodipine 5mg', 'description' => 'Calcium channel blocker for hypertension', 'price' => 1800, 'stock' => 120, 'expiry' => '2025-12-25', 'category' => 3, 'restricted' => '0'],
    ['name' => 'Lisinopril 10mg', 'description' => 'ACE inhibitor for blood pressure', 'price' => 2200, 'stock' => 90, 'expiry' => '2025-11-30', 'category' => 3, 'restricted' => '0'],
    ['name' => 'Atenolol 50mg', 'description' => 'Beta blocker for heart conditions', 'price' => 1600, 'stock' => 110, 'expiry' => '2025-10-15', 'category' => 3, 'restricted' => '0'],
    ['name' => 'Simvastatin 20mg', 'description' => 'Statin for cholesterol management', 'price' => 2800, 'stock' => 85, 'expiry' => '2025-09-20', 'category' => 3, 'restricted' => '0'],
    ['name' => 'Furosemide 40mg', 'description' => 'Diuretic for fluid retention', 'price' => 1200, 'stock' => 150, 'expiry' => '2025-12-05', 'category' => 3, 'restricted' => '0'],
    
    // Diabetes
    ['name' => 'Metformin 500mg', 'description' => 'First-line diabetes medication', 'price' => 800, 'stock' => 300, 'expiry' => '2026-01-15', 'category' => 4, 'restricted' => '0'],
    ['name' => 'Glibenclamide 5mg', 'description' => 'Sulfonylurea for diabetes', 'price' => 1200, 'stock' => 180, 'expiry' => '2025-11-25', 'category' => 4, 'restricted' => '0'],
    ['name' => 'Insulin Regular', 'description' => 'Short-acting insulin', 'price' => 4500, 'stock' => 60, 'expiry' => '2025-10-10', 'category' => 4, 'restricted' => '1'],
    ['name' => 'Insulin NPH', 'description' => 'Intermediate-acting insulin', 'price' => 4200, 'stock' => 45, 'expiry' => '2025-09-15', 'category' => 4, 'restricted' => '1'],
    ['name' => 'Acarbose 50mg', 'description' => 'Alpha-glucosidase inhibitor', 'price' => 2800, 'stock' => 75, 'expiry' => '2025-12-20', 'category' => 4, 'restricted' => '0'],
    
    // Respiratory
    ['name' => 'Salbutamol Inhaler', 'description' => 'Bronchodilator for asthma', 'price' => 3500, 'stock' => 80, 'expiry' => '2025-11-30', 'category' => 5, 'restricted' => '0'],
    ['name' => 'Beclomethasone Inhaler', 'description' => 'Corticosteroid for asthma', 'price' => 4200, 'stock' => 65, 'expiry' => '2025-10-25', 'category' => 5, 'restricted' => '0'],
    ['name' => 'Theophylline 200mg', 'description' => 'Bronchodilator for COPD', 'price' => 1800, 'stock' => 95, 'expiry' => '2025-09-20', 'category' => 5, 'restricted' => '0'],
    ['name' => 'Montelukast 10mg', 'description' => 'Leukotriene receptor antagonist', 'price' => 3200, 'stock' => 70, 'expiry' => '2025-12-10', 'category' => 5, 'restricted' => '0'],
    ['name' => 'Guaifenesin 400mg', 'description' => 'Expectorant for cough', 'price' => 800, 'stock' => 200, 'expiry' => '2026-02-15', 'category' => 5, 'restricted' => '0'],
    
    // Vitamins & Supplements
    ['name' => 'Vitamin C 500mg', 'description' => 'Immune system support', 'price' => 1200, 'stock' => 400, 'expiry' => '2026-06-15', 'category' => 6, 'restricted' => '0'],
    ['name' => 'Vitamin D3 1000IU', 'description' => 'Bone health and immunity', 'price' => 1800, 'stock' => 350, 'expiry' => '2026-05-20', 'category' => 6, 'restricted' => '0'],
    ['name' => 'Iron Sulfate 200mg', 'description' => 'Iron supplement for anemia', 'price' => 1500, 'stock' => 250, 'expiry' => '2026-04-30', 'category' => 6, 'restricted' => '0'],
    ['name' => 'Calcium Carbonate 500mg', 'description' => 'Calcium supplement for bones', 'price' => 1000, 'stock' => 300, 'expiry' => '2026-03-25', 'category' => 6, 'restricted' => '0'],
    ['name' => 'Folic Acid 5mg', 'description' => 'B vitamin for pregnancy', 'price' => 800, 'stock' => 180, 'expiry' => '2026-02-10', 'category' => 6, 'restricted' => '0'],
    
    // Dermatology
    ['name' => 'Betamethasone Cream', 'description' => 'Topical steroid for skin conditions', 'price' => 2200, 'stock' => 120, 'expiry' => '2025-12-30', 'category' => 7, 'restricted' => '0'],
    ['name' => 'Clotrimazole Cream', 'description' => 'Antifungal for skin infections', 'price' => 1800, 'stock' => 150, 'expiry' => '2025-11-25', 'category' => 7, 'restricted' => '0'],
    ['name' => 'Benzoyl Peroxide 5%', 'description' => 'Acne treatment gel', 'price' => 1600, 'stock' => 200, 'expiry' => '2026-01-15', 'category' => 7, 'restricted' => '0'],
    ['name' => 'Hydrocortisone 1%', 'description' => 'Mild steroid for skin irritation', 'price' => 1400, 'stock' => 180, 'expiry' => '2025-10-20', 'category' => 7, 'restricted' => '0'],
    ['name' => 'Permethrin 5%', 'description' => 'Scabies and lice treatment', 'price' => 2800, 'stock' => 80, 'expiry' => '2025-09-15', 'category' => 7, 'restricted' => '0'],
    
    // Mental Health
    ['name' => 'Fluoxetine 20mg', 'description' => 'SSRI antidepressant', 'price' => 3200, 'stock' => 90, 'expiry' => '2025-12-20', 'category' => 8, 'restricted' => '1'],
    ['name' => 'Amitriptyline 25mg', 'description' => 'Tricyclic antidepressant', 'price' => 2800, 'stock' => 75, 'expiry' => '2025-11-15', 'category' => 8, 'restricted' => '1'],
    ['name' => 'Diazepam 5mg', 'description' => 'Benzodiazepine for anxiety', 'price' => 1800, 'stock' => 60, 'expiry' => '2025-10-10', 'category' => 8, 'restricted' => '1'],
    ['name' => 'Risperidone 2mg', 'description' => 'Antipsychotic medication', 'price' => 4500, 'stock' => 45, 'expiry' => '2025-09-05', 'category' => 8, 'restricted' => '1'],
    ['name' => 'Lithium Carbonate 300mg', 'description' => 'Mood stabilizer', 'price' => 3800, 'stock' => 55, 'expiry' => '2025-12-25', 'category' => 8, 'restricted' => '1'],
    
    // Pediatrics
    ['name' => 'Amoxicillin Syrup 125mg/5ml', 'description' => 'Pediatric antibiotic suspension', 'price' => 1800, 'stock' => 100, 'expiry' => '2025-11-30', 'category' => 9, 'restricted' => '0'],
    ['name' => 'Paracetamol Syrup 120mg/5ml', 'description' => 'Pediatric pain and fever relief', 'price' => 1200, 'stock' => 150, 'expiry' => '2026-02-15', 'category' => 9, 'restricted' => '0'],
    ['name' => 'ORS Powder', 'description' => 'Oral rehydration solution', 'price' => 500, 'stock' => 300, 'expiry' => '2026-04-20', 'category' => 9, 'restricted' => '0'],
    ['name' => 'Zinc Sulfate 20mg', 'description' => 'Zinc supplement for children', 'price' => 800, 'stock' => 200, 'expiry' => '2026-03-10', 'category' => 9, 'restricted' => '0'],
    ['name' => 'Vitamin A 100,000IU', 'description' => 'Vitamin A supplement for children', 'price' => 600, 'stock' => 250, 'expiry' => '2026-01-25', 'category' => 9, 'restricted' => '0'],
    
    // Emergency Medicine
    ['name' => 'Adrenaline 1mg/ml', 'description' => 'Emergency treatment for severe allergic reactions', 'price' => 8500, 'stock' => 25, 'expiry' => '2025-08-15', 'category' => 10, 'restricted' => '1'],
    ['name' => 'Morphine 10mg/ml', 'description' => 'Strong pain relief for severe pain', 'price' => 12000, 'stock' => 15, 'expiry' => '2025-07-20', 'category' => 10, 'restricted' => '1'],
    ['name' => 'Dexamethasone 4mg/ml', 'description' => 'Emergency steroid for severe inflammation', 'price' => 6500, 'stock' => 30, 'expiry' => '2025-09-10', 'category' => 10, 'restricted' => '1'],
    ['name' => 'Atropine 1mg/ml', 'description' => 'Emergency treatment for bradycardia', 'price' => 7500, 'stock' => 20, 'expiry' => '2025-08-25', 'category' => 10, 'restricted' => '1'],
    ['name' => 'Naloxone 0.4mg/ml', 'description' => 'Emergency treatment for opioid overdose', 'price' => 9500, 'stock' => 18, 'expiry' => '2025-07-30', 'category' => 10, 'restricted' => '1']
];

$pharmacies = [
    ['name' => 'KEZA PHARMACY', 'location' => 'Umujyi wa Kigali / Gasabo / Kacyiru / Kamutwa / Umuco', 'license' => 'MDLink-5083-0166', 'contact' => 'Dr Frederic', 'phone' => '+250791905996'],
    ['name' => 'KIMIRONKO PHARMACY', 'location' => 'Umujyi wa Kigali / Gasabo / Kimironko / Kimironko', 'license' => 'MDLink-5083-0167', 'contact' => 'Dr Eric', 'phone' => '+250792021423'],
    ['name' => 'REMEZO PHARMACY', 'location' => 'Umujyi wa Kigali / Kicukiro / Remezo / Remezo', 'license' => 'MDLink-5083-0168', 'contact' => 'Dr Francois', 'phone' => '+250792021423'],
    ['name' => 'NYARUGENGE PHARMACY', 'location' => 'Umujyi wa Kigali / Nyarugenge / Nyamirambo / Nyamirambo', 'license' => 'MDLink-5083-0169', 'contact' => 'Dr Jean', 'phone' => '+250788765432'],
    ['name' => 'KANOMBE PHARMACY', 'location' => 'Umujyi wa Kigali / Kicukiro / Kanombe / Kanombe', 'license' => 'MDLink-5083-0170', 'contact' => 'Dr Marie', 'phone' => '+250789876543']
];

echo "<h2>Populating Pharmacy Database with Sample Data</h2>";

// Insert categories
echo "<h3>Inserting Categories...</h3>";
foreach ($categories as $cat) {
    $stmt = $connect->prepare("INSERT INTO category (category_name, description, status) VALUES (?, ?, '1')");
    $stmt->bind_param("ss", $cat['name'], $cat['description']);
    if ($stmt->execute()) {
        echo "✓ Added category: " . $cat['name'] . "<br>";
    } else {
        echo "✗ Failed to add category: " . $cat['name'] . " - " . $stmt->error . "<br>";
    }
}

// Insert pharmacies
echo "<h3>Inserting Pharmacies...</h3>";
foreach ($pharmacies as $pharm) {
    $stmt = $connect->prepare("INSERT INTO pharmacies (name, location, license_number, contact_person, contact_phone) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $pharm['name'], $pharm['location'], $pharm['license'], $pharm['contact'], $pharm['phone']);
    if ($stmt->execute()) {
        echo "✓ Added pharmacy: " . $pharm['name'] . "<br>";
    } else {
        echo "✗ Failed to add pharmacy: " . $pharm['name'] . " - " . $stmt->error . "<br>";
    }
}

// Insert medicines
echo "<h3>Inserting Medicines...</h3>";
foreach ($medicines as $med) {
    $pharmacy_id = rand(1, 5); // Random pharmacy assignment
    $stmt = $connect->prepare("INSERT INTO medicines (pharmacy_id, name, description, price, stock_quantity, expiry_date, Restricted_Medicine, category_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issdsssi", $pharmacy_id, $med['name'], $med['description'], $med['price'], $med['stock'], $med['expiry'], $med['restricted'], $med['category']);
    if ($stmt->execute()) {
        echo "✓ Added medicine: " . $med['name'] . "<br>";
    } else {
        echo "✗ Failed to add medicine: " . $med['name'] . " - " . $stmt->error . "<br>";
    }
}

// Generate stock movements
echo "<h3>Generating Stock Movements...</h3>";
$movement_types = ['IN', 'OUT', 'ADJUSTMENT'];
$movement_reasons = [
    'IN' => ['New Stock Arrival', 'Return from Customer', 'Transfer from Other Branch'],
    'OUT' => ['Sale to Customer', 'Expired Stock Removal', 'Transfer to Other Branch'],
    'ADJUSTMENT' => ['Stock Count Correction', 'Damage Adjustment', 'Theft Loss']
];

// Get all medicines
$medicines_result = $connect->query("SELECT medicine_id, stock_quantity FROM medicines");
while ($medicine = $medicines_result->fetch_assoc()) {
    $num_movements = rand(5, 15); // Random number of movements per medicine
    
    for ($i = 0; $i < $num_movements; $i++) {
        $movement_type = $movement_types[array_rand($movement_types)];
        $reasons = $movement_reasons[$movement_type];
        $reason = $reasons[array_rand($reasons)];
        
        $quantity = rand(1, 50);
        $previous_stock = rand(0, 200);
        $new_stock = $movement_type == 'IN' ? $previous_stock + $quantity : max(0, $previous_stock - $quantity);
        
        // Random date within last 30 days
        $movement_date = date('Y-m-d H:i:s', strtotime('-' . rand(0, 30) . ' days'));
        
        $stmt = $connect->prepare("INSERT INTO stock_movements (medicine_id, movement_type, quantity, previous_stock, new_stock, reference_number, notes, admin_id, movement_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $ref_number = 'REF-' . strtoupper(substr(md5(rand()), 0, 8));
        $admin_id = rand(1, 3);
        
        $stmt->bind_param("isiiissis", $medicine['medicine_id'], $movement_type, $quantity, $previous_stock, $new_stock, $ref_number, $reason, $admin_id, $movement_date);
        if ($stmt->execute()) {
            echo "✓ Added movement for medicine ID " . $medicine['medicine_id'] . "<br>";
        }
    }
}

echo "<h3>Database Population Complete!</h3>";
echo "<p>✓ Categories: " . count($categories) . "</p>";
echo "<p>✓ Pharmacies: " . count($pharmacies) . "</p>";
echo "<p>✓ Medicines: " . count($medicines) . "</p>";
echo "<p>✓ Stock movements generated for all medicines</p>";

echo "<br><a href='dashboard_pharmacy.php' class='btn btn-primary'>View Pharmacy Dashboard</a>";
?>
