<?php
require_once '../../database/DBConnection.php';

$db = DBConnect::getInstance();

header('Content-Type: application/json');

// Lấy data
$supplier_id = $_POST['supplier_id'];
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$address = $_POST['address'] ?? '';

$success = $db->execute('UPDATE supplier SET name = ?, email = ?, address = ? WHERE supplier_id = ?', [$name, $email, $address, $supplier_id]);

if($success) {
    echo json_encode(['success' => true, 'message' => 'Cập nhật thông tin nhà cung cấp thành công']);
}
else {
    echo json_encode(['success' => false, 'message' => 'Cập nhật thông tin nhà cung cấp không thành công!']);
}
?>