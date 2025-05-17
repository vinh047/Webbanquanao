<?php
require_once '../../database/DBConnection.php';

$db = DBConnect::getInstance();

header('Content-Type: application/json');

// Láy data
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$address = $_POST['address'] ?? '';

$success = $db->execute('INSERT INTO supplier (name, email, address) VALUES (?, ?, ?)', [$name, $email, $address]);

if($success) {
    echo json_encode(['success' => true, 'message' => 'Thêm nhà cung cấp thành công']);
}
else {
    echo json_encode(['success' => false, 'message' => 'Không thể thêm nhà cung cấp.']);
}


?>