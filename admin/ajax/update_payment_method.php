<?php
require_once '../../database/DBConnection.php';

$db = DBConnect::getInstance();

header('Content-Type: application/json');

// Lấy data
$payment_method_id = $_POST['payment_method_id'];
$name = $_POST['name'] ?? '';

$success = $db->execute('UPDATE payment_method SET name = ? WHERE payment_method_id = ?', [$name, $payment_method_id]);

if($success) {
    echo json_encode(['success' => true, 'message' => 'Cập nhật thông tin phương thức thanh toán thành công']);
}
else {
    echo json_encode(['success' => false, 'message' => 'Cập nhật thông tin phương thức thanh toán không thành công!']);
}
?>