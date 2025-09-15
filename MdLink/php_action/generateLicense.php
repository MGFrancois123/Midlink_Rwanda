<?php 
require_once 'core.php';
header('Content-Type: application/json');

function generateCode() {
    $a = str_pad(strval(random_int(0, 9999)), 4, '0', STR_PAD_LEFT);
    $b = str_pad(strval(random_int(0, 9999)), 4, '0', STR_PAD_LEFT);
    return "MDLink-$a-$b";
}

for ($i = 0; $i < 10; $i++) {
    $code = generateCode();
    $esc = mysqli_real_escape_string($connect, $code);
    $q = $connect->query("SELECT 1 FROM pharmacies WHERE license_number='$esc' LIMIT 1");
    if ($q && $q->num_rows === 0) {
        echo json_encode(array('license' => $code));
        exit;
    }
}

echo json_encode(array('error' => 'Unable to generate unique license'));


