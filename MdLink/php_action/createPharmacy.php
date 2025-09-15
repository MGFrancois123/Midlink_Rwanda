<?php 

require_once 'core.php';

$response = array('success' => false, 'messages' => array());

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    // Always override license with system-generated unique value
    $licenseNumber = '';
    function gen_code() {
        $a = str_pad(strval(random_int(0, 9999)), 4, '0', STR_PAD_LEFT);
        $b = str_pad(strval(random_int(0, 9999)), 4, '0', STR_PAD_LEFT);
        return "MDLink-$a-$b";
    }
    for ($i = 0; $i < 10; $i++) {
        $candidate = gen_code();
        $esc = mysqli_real_escape_string($connect, $candidate);
        $dupe = $connect->query("SELECT 1 FROM pharmacies WHERE license_number='$esc' LIMIT 1");
        if ($dupe && $dupe->num_rows === 0) { $licenseNumber = $candidate; break; }
    }
    $contactPerson = isset($_POST['contact_person']) ? trim($_POST['contact_person']) : '';
    $contactPhone = isset($_POST['contact_phone_e164']) && trim($_POST['contact_phone_e164']) !== ''
        ? trim($_POST['contact_phone_e164'])
        : (isset($_POST['contact_phone']) ? trim($_POST['contact_phone']) : '');
    $location = isset($_POST['location']) ? trim($_POST['location']) : '';

    if ($name === '' || $licenseNumber === '' || $contactPerson === '' || $contactPhone === '' || $location === '') {
        $response['messages'] = 'All fields are required.';
        echo json_encode($response);
        exit;
    }

    $nameEsc = mysqli_real_escape_string($connect, $name);
    $licenseEsc = mysqli_real_escape_string($connect, $licenseNumber);
    $personEsc = mysqli_real_escape_string($connect, $contactPerson);
    $phoneEsc = mysqli_real_escape_string($connect, $contactPhone);
    $locationEsc = mysqli_real_escape_string($connect, $location);

    $sql = "INSERT INTO pharmacies (name, location, license_number, contact_person, contact_phone) VALUES ('$nameEsc', '$locationEsc', '$licenseEsc', '$personEsc', '$phoneEsc')";

    if ($connect->query($sql) === TRUE) {
        $response['success'] = true;
        $response['messages'] = 'Pharmacy registered successfully';
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
        if (!$isAjax) {
            header('Location: ../placeholder.php?title=Register%20Pharmacy');
            exit;
        }
    } else {
        if ($connect->errno == 1062) {
            $response['messages'] = 'License number already exists.';
        } else {
            $response['messages'] = 'Error while registering pharmacy.';
        }
    }
}

$connect->close();

echo json_encode($response);
 

