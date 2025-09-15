<?php
require_once '../constant/connect.php';
header('Content-Type: application/json');

// Simple test data to verify DataTables is working
$testData = [
    [
        'DT_RowId' => 'row_1',
        'medicine_id' => 1,
        'name' => 'Test Medicine 1',
        'category_name' => 'Test Category',
        'pharmacy_name' => 'Test Pharmacy',
        'price' => '10.50',
        'stock_quantity' => 100,
        'expiry_date' => '2024-12-31',
        'Restricted_Medicine' => 0,
        'status' => 1
    ],
    [
        'DT_RowId' => 'row_2',
        'medicine_id' => 2,
        'name' => 'Test Medicine 2',
        'category_name' => 'Test Category',
        'pharmacy_name' => 'Test Pharmacy',
        'price' => '15.75',
        'stock_quantity' => 50,
        'expiry_date' => '2025-06-30',
        'Restricted_Medicine' => 1,
        'status' => 1
    ]
];

$response = [
    'draw' => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
    'recordsTotal' => count($testData),
    'recordsFiltered' => count($testData),
    'data' => $testData
];

echo json_encode($response);
?>
